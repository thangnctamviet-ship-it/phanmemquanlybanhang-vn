<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_transfers extends CI_Model
{
    public function __construct() { parent::__construct(); }

    public function getAll($id = null)
    {
        if ($id) {
            return $this->db->query(
                "SELECT t.*, sf.name AS from_name, st.name AS to_name
                 FROM `stock_transfers` t
                 LEFT JOIN `stores` sf ON sf.id = t.from_store_id
                 LEFT JOIN `stores` st ON st.id = t.to_store_id
                 WHERE t.id = ?", array((int)$id)
            )->row_array();
        }
        return $this->db->query(
            "SELECT t.*, sf.name AS from_name, st.name AS to_name
             FROM `stock_transfers` t
             LEFT JOIN `stores` sf ON sf.id = t.from_store_id
             LEFT JOIN `stores` st ON st.id = t.to_store_id
             ORDER BY t.id DESC"
        )->result_array();
    }

    public function getItems($transfer_id)
    {
        return $this->db->query(
            "SELECT ti.*, p.name AS product_name, p.sku
             FROM `stock_transfers_item` ti
             LEFT JOIN `products` p ON p.id = ti.product_id
             WHERE ti.transfer_id = ?", array((int)$transfer_id)
        )->result_array();
    }

    /** Tạo phiếu chuyển: trừ from_store, cộng to_store. */
    public function create($data)
    {
        $this->load->model('model_stock');
        $from = (int)($data['from_store_id'] ?? 0);
        $to   = (int)($data['to_store_id'] ?? 0);
        $items = $data['items'] ?? array();
        if ($from <= 0 || $to <= 0 || $from === $to || empty($items)) return false;

        $code = 'CV-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $this->db->insert('stock_transfers', array(
            'code' => $code,
            'from_store_id' => $from,
            'to_store_id' => $to,
            'status' => 'completed',
            'note' => $data['note'] ?? '',
            'user_id' => (int)$data['user_id'],
        ));
        $tid = $this->db->insert_id();

        foreach ($items as $it) {
            $pid = (int)($it['product_id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;
            $this->db->insert('stock_transfers_item', array(
                'transfer_id' => $tid, 'product_id' => $pid, 'qty' => $qty,
            ));
            $this->model_stock->adjust($pid, $from, -$qty);
            $this->model_stock->adjust($pid, $to,   +$qty);
        }
        return $tid;
    }
}
