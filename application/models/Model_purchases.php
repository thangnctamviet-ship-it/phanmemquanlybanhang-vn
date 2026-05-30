<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_purchases extends CI_Model
{
    public function __construct() { parent::__construct(); }

    public function getAll($id = null)
    {
        if ($id) {
            $row = $this->db->query(
                "SELECT p.*, s.name AS supplier_name, st.name AS store_name
                 FROM `purchases` p
                 LEFT JOIN `suppliers` s ON s.id = p.supplier_id
                 LEFT JOIN `stores` st ON st.id = p.store_id
                 WHERE p.id = ?", array((int)$id)
            )->row_array();
            return $row;
        }
        return $this->db->query(
            "SELECT p.*, s.name AS supplier_name, st.name AS store_name
             FROM `purchases` p
             LEFT JOIN `suppliers` s ON s.id = p.supplier_id
             LEFT JOIN `stores` st ON st.id = p.store_id
             ORDER BY p.id DESC"
        )->result_array();
    }

    public function getItems($purchase_id)
    {
        return $this->db->query(
            "SELECT pi.*, p.name AS product_name, p.sku
             FROM `purchases_item` pi
             LEFT JOIN `products` p ON p.id = pi.product_id
             WHERE pi.purchase_id = ?", array((int)$purchase_id)
        )->result_array();
    }

    /**
     * Tạo phiếu nhập + insert items + cộng kho (qua Model_stock) + cập nhật cost_price + cộng công nợ NCC.
     * $data = [supplier_id, store_id, items=[[product_id,qty,cost_price]], paid_amount, note, user_id]
     */
    public function create($data)
    {
        $this->load->model('model_stock');
        $this->load->model('model_suppliers');

        $items = $data['items'] ?? array();
        if (empty($items)) return false;

        $total = 0; $clean = array();
        foreach ($items as $it) {
            $pid = (int)($it['product_id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            $cost = (float)($it['cost_price'] ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;
            $amount = $qty * $cost;
            $total += $amount;
            $clean[] = array('product_id'=>$pid,'qty'=>$qty,'cost_price'=>$cost,'amount'=>$amount);
        }
        if (empty($clean)) return false;

        $paid = (float)($data['paid_amount'] ?? 0);
        $debt = max(0, $total - $paid);
        $code = 'NH-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

        $this->db->insert('purchases', array(
            'code'         => $code,
            'supplier_id'  => (int)$data['supplier_id'],
            'store_id'     => (int)$data['store_id'],
            'total_amount' => $total,
            'paid_amount'  => $paid,
            'debt_amount'  => $debt,
            'note'         => $data['note'] ?? '',
            'user_id'      => (int)$data['user_id'],
        ));
        $pid = $this->db->insert_id();

        foreach ($clean as $it) {
            $this->db->insert('purchases_item', array(
                'purchase_id' => $pid,
                'product_id'  => $it['product_id'],
                'qty'         => $it['qty'],
                'cost_price'  => $it['cost_price'],
                'amount'      => $it['amount'],
            ));
            // Cộng tồn
            $this->model_stock->adjust($it['product_id'], (int)$data['store_id'], +$it['qty']);
            // Cập nhật giá vốn mới (nếu có cột cost_price)
            if ($this->db->field_exists('cost_price', 'products')) {
                $this->db->where('id', $it['product_id'])->update('products', array('cost_price' => $it['cost_price']));
            }
        }

        // Cộng công nợ NCC nếu còn nợ
        if ($debt > 0 && (int)$data['supplier_id'] > 0) {
            $this->model_suppliers->adjustDebt((int)$data['supplier_id'], +$debt);
        }

        return $pid;
    }
}
