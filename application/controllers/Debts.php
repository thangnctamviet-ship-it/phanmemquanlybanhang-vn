<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debts extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Công nợ';
        $this->load->model('model_debts');
    }

    public function index()
    {
        $this->data['customers'] = $this->model_debts->customersWithDebt();
        $this->data['suppliers'] = $this->model_debts->suppliersWithDebt();
        $this->data['total_customer_debt'] = $this->model_debts->totalCustomerDebt();
        $this->data['total_supplier_debt'] = $this->model_debts->totalSupplierDebt();
        $this->data['payments'] = $this->model_debts->getPayments(50);

        // Quỹ tiền
        $this->data['cash_accounts'] = $this->db->query(
            "SELECT id, name, type, balance FROM `cash_accounts` WHERE active=1 ORDER BY id"
        )->result_array();

        $this->render_template('debts/index', $this->data);
    }

    /** Ajax: ghi 1 phiếu thu/chi */
    public function record()
    {
        $kind = $this->input->post('kind');                  // receive_customer | pay_supplier | other_in | other_out
        $party_type = $this->input->post('party_type');      // customer | supplier | other
        $party_id = (int)$this->input->post('party_id');
        $amount = (float)$this->input->post('amount');
        $cash_account_id = (int)$this->input->post('cash_account_id');
        $reference = $this->input->post('reference');
        $note = $this->input->post('note');

        if ($amount <= 0) { echo json_encode(['ok'=>false,'error'=>'Số tiền phải > 0']); return; }
        if (!in_array($kind, ['receive_customer','pay_supplier','other_in','other_out'])) {
            echo json_encode(['ok'=>false,'error'=>'Loại phiếu không hợp lệ']); return;
        }

        $id = $this->model_debts->recordPayment([
            'kind' => $kind,
            'party_type' => $party_type,
            'party_id' => $party_id,
            'amount' => $amount,
            'reference' => $reference,
            'note' => $note,
            'cash_account_id' => $cash_account_id,
            'user_id' => (int)$this->session->userdata('id'),
        ]);
        echo json_encode($id ? ['ok'=>true,'id'=>$id] : ['ok'=>false,'error'=>'Lỗi ghi phiếu']);
    }
}
