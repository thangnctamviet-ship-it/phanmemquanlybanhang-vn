<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_dashboard — các query tổng hợp cho trang Bảng điều khiển.
 *
 * Lưu ý schema cũ:
 *  - orders.date_time là string unix timestamp (do strtotime() lưu)
 *  - các field số (net_amount, qty, amount, ...) đều varchar → phải CAST khi SUM
 */
class Model_dashboard extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /* ---------- Helpers ---------- */

    private function tsRange($from, $to)
    {
        // orders.date_time là string timestamp → so sánh bằng CAST
        return array('from' => $from, 'to' => $to);
    }

    private function todayRange()
    {
        $from = strtotime('today');
        $to   = strtotime('tomorrow') - 1;
        return $this->tsRange($from, $to);
    }

    private function yesterdayRange()
    {
        $from = strtotime('yesterday');
        $to   = strtotime('today') - 1;
        return $this->tsRange($from, $to);
    }

    /* ---------- Doanh thu / Đơn hàng ---------- */

    /** Tổng doanh thu (net_amount) trong khoảng timestamp */
    public function revenueInRange($from, $to, $paidOnly = true)
    {
        $sql = "SELECT COALESCE(SUM(CAST(net_amount AS DECIMAL(15,2))), 0) AS total
                FROM `orders`
                WHERE CAST(date_time AS UNSIGNED) BETWEEN ? AND ?";
        if ($paidOnly) {
            $sql .= " AND paid_status = 1";
        }
        $row = $this->db->query($sql, array($from, $to))->row_array();
        return (float) ($row['total'] ?? 0);
    }

    /** Số đơn trong khoảng timestamp */
    public function ordersCountInRange($from, $to, $paidOnly = false)
    {
        $sql = "SELECT COUNT(*) AS c
                FROM `orders`
                WHERE CAST(date_time AS UNSIGNED) BETWEEN ? AND ?";
        if ($paidOnly) {
            $sql .= " AND paid_status = 1";
        }
        $row = $this->db->query($sql, array($from, $to))->row_array();
        return (int) ($row['c'] ?? 0);
    }

    public function revenueToday()      { $r = $this->todayRange();     return $this->revenueInRange($r['from'], $r['to']); }
    public function revenueYesterday()  { $r = $this->yesterdayRange(); return $this->revenueInRange($r['from'], $r['to']); }
    public function ordersToday()       { $r = $this->todayRange();     return $this->ordersCountInRange($r['from'], $r['to']); }

    /** Doanh thu tháng hiện tại */
    public function revenueThisMonth()
    {
        $from = strtotime(date('Y-m-01 00:00:00'));
        $to   = strtotime(date('Y-m-t 23:59:59'));
        return $this->revenueInRange($from, $to);
    }

    /** Tiền mặt = tổng net_amount của đơn đã thanh toán (paid_status=1) toàn thời gian.
     *  Tạm thời chưa có module thu/chi nên dùng proxy này. Sẽ thay khi xong 1.3.
     */
    public function cashOnHand()
    {
        $sql = "SELECT COALESCE(SUM(CAST(net_amount AS DECIMAL(15,2))), 0) AS total
                FROM `orders` WHERE paid_status = 1";
        $row = $this->db->query($sql)->row_array();
        return (float) ($row['total'] ?? 0);
    }

    /* ---------- Biểu đồ 30 ngày ---------- */

    /** Trả mảng [['date'=>'2026-05-01','revenue'=>...], ...] cho 30 ngày gần nhất */
    public function revenueLast30Days()
    {
        $result = array();
        $from = strtotime('today -29 days');
        for ($i = 0; $i < 30; $i++) {
            $dayStart = $from + $i * 86400;
            $dayEnd   = $dayStart + 86399;
            $result[] = array(
                'date'    => date('d/m', $dayStart),
                'revenue' => $this->revenueInRange($dayStart, $dayEnd),
            );
        }
        return $result;
    }

    /* ---------- Top sản phẩm ---------- */

    /** Top 5 sản phẩm bán chạy nhất trong N ngày (theo SL) */
    public function topProducts($days = 7, $limit = 5)
    {
        $from = strtotime("today -" . (int)$days . " days");
        $to   = strtotime('tomorrow') - 1;

        $sql = "SELECT p.id, p.name, p.sku,
                       SUM(CAST(oi.qty AS UNSIGNED)) AS total_qty,
                       SUM(CAST(oi.amount AS DECIMAL(15,2))) AS total_revenue
                FROM `orders_item` oi
                JOIN `orders` o ON o.id = oi.order_id
                JOIN `products` p ON p.id = oi.product_id
                WHERE CAST(o.date_time AS UNSIGNED) BETWEEN ? AND ?
                  AND o.paid_status = 1
                GROUP BY p.id, p.name, p.sku
                ORDER BY total_qty DESC
                LIMIT " . (int)$limit;

        return $this->db->query($sql, array($from, $to))->result_array();
    }

    /* ---------- Sắp hết hàng ---------- */

    /** Sản phẩm có tồn kho thấp. Ngưỡng mặc định 5 (sẽ thay bằng products.min_stock sau migration M4). */
    public function lowStockProducts($threshold = 5, $limit = 5)
    {
        $sql = "SELECT id, name, sku, qty
                FROM `products`
                WHERE availability = 1
                  AND CAST(qty AS SIGNED) <= ?
                ORDER BY CAST(qty AS SIGNED) ASC
                LIMIT " . (int)$limit;
        return $this->db->query($sql, array((int)$threshold))->result_array();
    }

    public function lowStockCount($threshold = 5)
    {
        $sql = "SELECT COUNT(*) AS c FROM `products`
                WHERE availability = 1 AND CAST(qty AS SIGNED) <= ?";
        $row = $this->db->query($sql, array((int)$threshold))->row_array();
        return (int) ($row['c'] ?? 0);
    }
}
