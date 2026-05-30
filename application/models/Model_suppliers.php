<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_suppliers extends CI_Model
{
    public function __construct() { parent::__construct(); }

    public function getAll($id = null)
    {
        if ($id) {
            return $this->db->query("SELECT * FROM `suppliers` WHERE id = ? AND deleted_at IS NULL", array((int)$id))->row_array();
        }
        return $this->db->query("SELECT * FROM `suppliers` WHERE deleted_at IS NULL ORDER BY id DESC")->result_array();
    }

    public function create($data)
    {
        return $this->db->insert('suppliers', $data) ? $this->db->insert_id() : false;
    }

    public function update($id, $data)
    {
        return $this->db->where('id', (int)$id)->update('suppliers', $data);
    }

    public function remove($id)
    {
        return $this->db->where('id', (int)$id)
                        ->update('suppliers', array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function adjustDebt($id, $delta)
    {
        $row = $this->db->query("SELECT debt FROM `suppliers` WHERE id = ?", array((int)$id))->row_array();
        if (!$row) return false;
        $new = (float)$row['debt'] + (float)$delta;
        return $this->db->where('id', (int)$id)->update('suppliers', array('debt' => $new));
    }
}
