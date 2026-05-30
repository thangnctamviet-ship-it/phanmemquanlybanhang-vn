<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Thùng rác — xem + khôi phục các record đã soft delete
 * Chỉ admin được phép thao tác (force delete).
 */
class Trash extends Admin_Controller {

    private $allowed = array(
        'products'  => array('name'=>'Sản phẩm',      'cols'=>array('name','sku','barcode','price')),
        'customers' => array('name'=>'Khách hàng',    'cols'=>array('name','phone','email','address')),
        'suppliers' => array('name'=>'Nhà cung cấp',  'cols'=>array('name','phone','email','address')),
        'orders'    => array('name'=>'Hoá đơn bán',   'cols'=>array('bill_no','customer_name','net_amount','paid_status')),
        'purchases' => array('name'=>'Hoá đơn nhập',  'cols'=>array('supplier_id','total_amount','status')),
    );

    public function __construct() {
        parent::__construct();
        if (empty($this->session->userdata('logged_in'))) redirect('auth','refresh');
        $uid = (int)$this->session->userdata('id');
        $uname = strtolower((string)$this->session->userdata('username'));
        $perms = is_array($this->permission ?? null) ? $this->permission : array();
        if ($uid !== 1 && $uname !== 'admin' && !in_array('user_create', $perms, true)) {
            show_error('Chỉ admin được vào Thùng rác', 403);
        }
    }

    public function index($table = 'products') {
        if (!isset($this->allowed[$table])) {
            show_error('Bảng không hợp lệ', 404);
        }
        $this->data['table'] = $table;
        $this->data['table_meta'] = $this->allowed[$table];
        $this->data['tables'] = $this->allowed;
        $this->data['rows'] = $this->soft_delete->trashed($table);
        $this->load->view('trash/index', $this->data);
    }

    public function restore($table, $id) {
        if (!isset($this->allowed[$table])) show_error('Bảng không hợp lệ', 404);
        $ok = $this->soft_delete->restore($table, (int)$id);
        $this->session->set_flashdata($ok?'success':'errors', $ok ? 'Đã khôi phục.' : 'Không khôi phục được.');
        redirect('Trash/index/'.$table, 'refresh');
    }

    public function forceDelete($table, $id) {
        if (!isset($this->allowed[$table])) show_error('Bảng không hợp lệ', 404);
        $ok = $this->soft_delete->forceDelete($table, (int)$id);
        $this->session->set_flashdata($ok?'success':'errors', $ok ? 'Đã xoá vĩnh viễn.' : 'Không xoá được (có thể đang được tham chiếu).');
        redirect('Trash/index/'.$table, 'refresh');
    }
}
