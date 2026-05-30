<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_reports2 — báo cáo nâng cao 2.2
 * Lưu ý: orders.date_time là string unix timestamp → cast.
 */
class Model_reports2 extends CI_Model
{
    public function __construct() { parent::__construct(); }

    private function range($from, $to)
    {
        return array(
            (int)strtotime($from . ' 00:00:00'),
            (int)strtotime($to   . ' 23:59:59'),
        );
    }

    /** Tổng quan kỳ. */
    public function summary($from, $to)
    {
        list($f, $t) = $this->range($from, $to);
        $sql = "SELECT
                    COUNT(*) AS order_count,
                    COALESCE(SUM(CAST(net_amount AS DECIMAL(15,2))),0) AS revenue,
                    COALESCE(SUM(CAST(gross_amount AS DECIMAL(15,2))),0) AS gross,
                    COALESCE(SUM(CAST(discount AS DECIMAL(15,2))),0) AS discount
                FROM `orders`
                WHERE CAST(date_time AS UNSIGNED) BETWEEN ? AND ? AND paid_status = 1";
        $r = $this->db->query($sql, array($f, $t))->row_array();
        // Lợi nhuận tạm tính = SUM(orders_item.amount - cost_price * qty)
        $hasCost = $this->db->field_exists('cost_price', 'products');
        if ($hasCost) {
            $profit_sql = "SELECT COALESCE(SUM(
                            CAST(oi.amount AS DECIMAL(15,2))
                            - COALESCE(p.cost_price, 0) * CAST(oi.qty AS UNSIGNED)
                          ),0) AS profit
                          FROM `orders_item` oi
                          JOIN `orders` o ON o.id = oi.order_id
                          LEFT JOIN `products` p ON p.id = oi.product_id
                          WHERE CAST(o.date_time AS UNSIGNED) BETWEEN ? AND ?
                            AND o.paid_status = 1";
            $p = $this->db->query($profit_sql, array($f, $t))->row_array();
            $r['profit'] = (float)($p['profit'] ?? 0);
        } else $r['profit'] = 0;
        return $r;
    }

    /** Doanh thu theo ngày (cho chart). */
    public function dailyRevenue($from, $to)
    {
        $result = array();
        $f = strtotime($from . ' 00:00:00');
        $t = strtotime($to   . ' 23:59:59');
        for ($d = $f; $d <= $t; $d += 86400) {
            $dayEnd = $d + 86399;
            $r = $this->db->query(
                "SELECT COALESCE(SUM(CAST(net_amount AS DECIMAL(15,2))),0) AS rev, COUNT(*) AS cnt
                 FROM `orders` WHERE CAST(date_time AS UNSIGNED) BETWEEN ? AND ? AND paid_status=1",
                array($d, $dayEnd)
            )->row_array();
            $result[] = array(
                'date' => date('d/m', $d),
                'revenue' => (float)$r['rev'],
                'orders' => (int)$r['cnt'],
            );
        }
        return $result;
    }

    /** Top SP theo qty + revenue. */
    public function topProducts($from, $to, $limit = 20)
    {
        list($f, $t) = $this->range($from, $to);
        $sql = "SELECT p.id, p.name, p.sku,
                       SUM(CAST(oi.qty AS UNSIGNED)) AS qty,
                       SUM(CAST(oi.amount AS DECIMAL(15,2))) AS revenue
                FROM `orders_item` oi
                JOIN `orders` o ON o.id = oi.order_id
                LEFT JOIN `products` p ON p.id = oi.product_id
                WHERE CAST(o.date_time AS UNSIGNED) BETWEEN ? AND ? AND o.paid_status=1
                GROUP BY p.id, p.name, p.sku
                ORDER BY qty DESC LIMIT " . (int)$limit;
        return $this->db->query($sql, array($f, $t))->result_array();
    }

    /** Doanh số theo nhân viên. */
    public function byEmployee($from, $to)
    {
        list($f, $t) = $this->range($from, $to);
        $sql = "SELECT u.id, u.username, u.firstname, u.lastname,
                       COUNT(o.id) AS order_count,
                       COALESCE(SUM(CAST(o.net_amount AS DECIMAL(15,2))),0) AS revenue,
                       COALESCE(SUM(CAST(o.discount AS DECIMAL(15,2))),0) AS discount
                FROM `orders` o
                LEFT JOIN `users` u ON u.id = o.user_id
                WHERE CAST(o.date_time AS UNSIGNED) BETWEEN ? AND ? AND o.paid_status=1
                GROUP BY u.id, u.username, u.firstname, u.lastname
                ORDER BY revenue DESC";
        return $this->db->query($sql, array($f, $t))->result_array();
    }

    /** Doanh số theo cửa hàng. */
    public function byStore($from, $to)
    {
        list($f, $t) = $this->range($from, $to);
        $hasStore = $this->db->field_exists('store_id', 'orders');
        if (!$hasStore) return array();
        $sql = "SELECT s.id, s.name,
                       COUNT(o.id) AS order_count,
                       COALESCE(SUM(CAST(o.net_amount AS DECIMAL(15,2))),0) AS revenue
                FROM `orders` o
                LEFT JOIN `stores` s ON s.id = o.store_id
                WHERE CAST(o.date_time AS UNSIGNED) BETWEEN ? AND ? AND o.paid_status=1
                GROUP BY s.id, s.name
                ORDER BY revenue DESC";
        return $this->db->query($sql, array($f, $t))->result_array();
    }

    /** Tồn lâu (SP có tồn > 0 nhưng chưa bán trong N ngày qua). */
    public function slowMoving($days = 90, $limit = 50)
    {
        $cut = time() - $days * 86400;
        $sql = "SELECT p.id, p.name, p.sku, CAST(p.qty AS SIGNED) AS qty,
                       (SELECT MAX(CAST(o.date_time AS UNSIGNED))
                        FROM `orders_item` oi
                        JOIN `orders` o ON o.id = oi.order_id
                        WHERE oi.product_id = p.id) AS last_sold_ts
                FROM `products` p
                WHERE p.availability = 1 AND CAST(p.qty AS SIGNED) > 0
                HAVING (last_sold_ts IS NULL OR last_sold_ts < ?)
                ORDER BY qty DESC LIMIT " . (int)$limit;
        return $this->db->query($sql, array($cut))->result_array();
    }

    /** Giá trị tồn kho hiện tại theo cửa hàng. */
    public function inventoryValue()
    {
        $hasCost = $this->db->field_exists('cost_price', 'products');
        $hasStock = $this->db->table_exists('product_stock');
        if (!$hasCost || !$hasStock) {
            // fallback chỉ tổng theo products
            $r = $this->db->query(
                "SELECT COALESCE(SUM(CAST(qty AS SIGNED) * " . ($hasCost ? "COALESCE(cost_price,0)" : "0") . "),0) AS value,
                        SUM(CAST(qty AS SIGNED)) AS total_qty
                 FROM `products` WHERE availability = 1"
            )->row_array();
            return array(array('store_name' => 'Tất cả', 'qty' => (int)$r['total_qty'], 'value' => (float)$r['value']));
        }
        return $this->db->query(
            "SELECT s.id AS store_id, COALESCE(s.name,'(Không xác định)') AS store_name,
                    SUM(ps.qty) AS qty,
                    SUM(ps.qty * COALESCE(p.cost_price,0)) AS value
             FROM `product_stock` ps
             LEFT JOIN `stores` s ON s.id = ps.store_id
             LEFT JOIN `products` p ON p.id = ps.product_id
             GROUP BY s.id, s.name
             ORDER BY value DESC"
        )->result_array();
    }
}
