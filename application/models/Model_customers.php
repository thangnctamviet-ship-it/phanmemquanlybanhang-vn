<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_customers extends CI_Model
{
    public function __construct() { parent::__construct(); }

    public function getAll($id = null)
    {
        if ($id) return $this->db->query("SELECT * FROM `customers` WHERE id = ? AND deleted_at IS NULL", array((int)$id))->row_array();
        return $this->db->query("SELECT * FROM `customers` WHERE deleted_at IS NULL ORDER BY id DESC")->result_array();
    }

    public function findByPhone($phone)
    {
        $phone = trim($phone);
        if (!$phone) return null;
        return $this->db->query("SELECT * FROM `customers` WHERE phone = ? AND deleted_at IS NULL LIMIT 1", array($phone))->row_array();
    }

    public function create($data)
    {
        return $this->db->insert('customers', $data) ? $this->db->insert_id() : false;
    }

    public function update($id, $data)
    {
        return $this->db->where('id', (int)$id)->update('customers', $data);
    }

    public function remove($id)
    {
        // Soft delete: chỉ SET deleted_at, không xoá thật → có thể restore
        return $this->db->where('id', (int)$id)
                        ->update('customers', array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function addPoints($id, $delta)
    {
        $row = $this->db->query("SELECT loyalty_points FROM `customers` WHERE id = ?", array((int)$id))->row_array();
        if (!$row) return false;
        $new = max(0, (int)$row['loyalty_points'] + (int)$delta);
        return $this->db->where('id', (int)$id)->update('customers', array('loyalty_points' => $new));
    }

    public function adjustDebt($id, $delta)
    {
        $row = $this->db->query("SELECT debt FROM `customers` WHERE id = ?", array((int)$id))->row_array();
        if (!$row) return false;
        $new = (float)$row['debt'] + (float)$delta;
        return $this->db->where('id', (int)$id)->update('customers', array('debt' => $new));
    }
}
