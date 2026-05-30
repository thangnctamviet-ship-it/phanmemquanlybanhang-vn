<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_debts — tổng hợp công nợ KH/NCC + ghi phiếu thu/chi.
 */
class Model_debts extends CI_Model
{
    public function __construct() { parent::__construct(); }

    /** Danh sách KH còn nợ */
    public function customersWithDebt()
    {
        return $this->db->query(
            "SELECT id, name, phone, debt FROM `customers` WHERE debt > 0 ORDER BY debt DESC"
        )->result_array();
    }

    /** Danh sách NCC còn nợ */
    public function suppliersWithDebt()
    {
        return $this->db->query(
            "SELECT id, name, phone, debt FROM `suppliers` WHERE debt > 0 ORDER BY debt DESC"
        )->result_array();
    }

    public function totalCustomerDebt()
    {
        $r = $this->db->query("SELECT COALESCE(SUM(debt),0) AS t FROM `customers` WHERE debt > 0")->row_array();
        return (float)$r['t'];
    }

    public function totalSupplierDebt()
    {
        $r = $this->db->query("SELECT COALESCE(SUM(debt),0) AS t FROM `suppliers` WHERE debt > 0")->row_array();
        return (float)$r['t'];
    }

    /**
     * Ghi 1 phiếu thu/chi.
     * $data: [kind, party_type, party_id, amount, reference, note, user_id, cash_account_id]
     */
    public function recordPayment($data)
    {
        $this->load->model('model_customers');
        $this->load->model('model_suppliers');

        $kind = $data['kind'];
        $party_type = $data['party_type'];
        $party_id = (int)$data['party_id'];
        $amount = (float)$data['amount'];
        if ($amount <= 0) return false;

        $row = array(
            'kind'       => $kind,
            'party_type' => $party_type,
            'party_id'   => $party_id,
            'amount'     => $amount,
            'reference'  => $data['reference'] ?? '',
            'note'       => $data['note'] ?? '',
            'user_id'    => (int)$data['user_id'],
        );
        $this->db->insert('cash_payments', $row);
        $payment_id = $this->db->insert_id();

        // Adjust party debt
        if ($kind === 'receive_customer' && $party_type === 'customer' && $party_id > 0) {
            $this->model_customers->adjustDebt($party_id, -$amount); // giảm nợ KH
        } elseif ($kind === 'pay_supplier' && $party_type === 'supplier' && $party_id > 0) {
            $this->model_suppliers->adjustDebt($party_id, -$amount); // giảm nợ NCC
        }

        // Cập nhật số dư quỹ
        if (!empty($data['cash_account_id'])) {
            $delta = in_array($kind, ['receive_customer','other_in']) ? +$amount : -$amount;
            $this->db->query(
                "UPDATE `cash_accounts` SET balance = balance + ? WHERE id = ?",
                array($delta, (int)$data['cash_account_id'])
            );
        }

        return $payment_id;
    }

    /** Lịch sử phiếu thu/chi */
    public function getPayments($limit = 100)
    {
        return $this->db->query(
            "SELECT cp.*,
                CASE
                  WHEN cp.party_type='customer' THEN (SELECT name FROM customers WHERE id=cp.party_id)
                  WHEN cp.party_type='supplier' THEN (SELECT name FROM suppliers WHERE id=cp.party_id)
                  ELSE NULL
                END AS party_name
             FROM `cash_payments` cp
             ORDER BY cp.id DESC LIMIT " . (int)$limit
        )->result_array();
    }
}
