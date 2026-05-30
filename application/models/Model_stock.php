<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_stock — quản lý tồn kho theo cửa hàng (product_stock).
 * Có fallback về products.qty nếu chưa có row product_stock (tương thích ngược).
 */
class Model_stock extends CI_Model
{
    public function __construct() { parent::__construct(); }

    /** Lấy tồn của 1 SP tại 1 store. Fallback về products.qty nếu chưa có. */
    public function getQty($product_id, $store_id)
    {
        $row = $this->db->query(
            "SELECT qty FROM `product_stock` WHERE product_id = ? AND store_id = ? LIMIT 1",
            array((int)$product_id, (int)$store_id)
        )->row_array();
        if ($row) return (int)$row['qty'];
        // Fallback
        $p = $this->db->query("SELECT qty, store_id FROM `products` WHERE id = ?", array((int)$product_id))->row_array();
        if ($p && (int)$p['store_id'] === (int)$store_id) return (int)$p['qty'];
        return 0;
    }

    /** Tổng tồn của 1 SP qua tất cả cửa hàng. */
    public function getTotalQty($product_id)
    {
        $row = $this->db->query(
            "SELECT COALESCE(SUM(qty),0) AS q FROM `product_stock` WHERE product_id = ?",
            array((int)$product_id)
        )->row_array();
        if ($row && (int)$row['q'] > 0) return (int)$row['q'];
        // Fallback
        $p = $this->db->query("SELECT qty FROM `products` WHERE id = ?", array((int)$product_id))->row_array();
        return $p ? (int)$p['qty'] : 0;
    }

    /** Cộng/trừ tồn (delta có dấu). Tự tạo row nếu chưa có. Đồng bộ products.qty = tổng tồn. */
    public function adjust($product_id, $store_id, $delta)
    {
        $product_id = (int)$product_id; $store_id = (int)$store_id; $delta = (int)$delta;
        if ($product_id <= 0 || $store_id <= 0) return false;

        $row = $this->db->query(
            "SELECT id, qty FROM `product_stock` WHERE product_id = ? AND store_id = ? LIMIT 1",
            array($product_id, $store_id)
        )->row_array();

        if ($row) {
            $new = max(0, (int)$row['qty'] + $delta);
            $this->db->where('id', $row['id'])->update('product_stock', array('qty' => $new));
        } else {
            // Tạo mới. Lấy fallback từ products.qty nếu store trùng.
            $p = $this->db->query("SELECT qty, store_id FROM `products` WHERE id = ?", array($product_id))->row_array();
            $base = ($p && (int)$p['store_id'] === $store_id) ? (int)$p['qty'] : 0;
            $new = max(0, $base + $delta);
            $this->db->insert('product_stock', array(
                'product_id' => $product_id, 'store_id' => $store_id, 'qty' => $new,
            ));
        }

        // Sync products.qty = tổng tồn
        $total = $this->db->query("SELECT COALESCE(SUM(qty),0) AS t FROM `product_stock` WHERE product_id = ?", array($product_id))->row_array();
        $this->db->where('id', $product_id)->update('products', array('qty' => (int)$total['t']));
        return true;
    }

    /** Trả mảng tồn theo từng cửa hàng cho 1 SP. */
    public function getByProduct($product_id)
    {
        return $this->db->query(
            "SELECT ps.store_id, s.name AS store_name, ps.qty
             FROM `product_stock` ps
             LEFT JOIN `stores` s ON s.id = ps.store_id
             WHERE ps.product_id = ? ORDER BY s.name",
            array((int)$product_id)
        )->result_array();
    }
}
