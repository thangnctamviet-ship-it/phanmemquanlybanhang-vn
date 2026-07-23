<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chốt ca / kết ca tiền mặt.
 * Mở ca (đếm tiền đầu) → bán hàng → đóng ca (đếm tiền cuối) → chênh lệch.
 */
class Shifts extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->not_logged_in();
		$this->data['page_title'] = 'Chốt ca';
		$this->load->model('model_shifts');
		$this->load->model('model_stores');
	}

	private function _json($payload)
	{
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($payload));
	}

	/** Quyền: cho phép ai bán hàng (createOrder) được dùng chốt ca. */
	private function _canUse()
	{
		return in_array('createOrder', $this->permission) || in_array('viewReports', $this->permission);
	}

	public function index()
	{
		if (!$this->_canUse()) { redirect('dashboard', 'refresh'); }
		$this->data['stores']  = $this->model_stores->getActiveStore();
		$this->data['history'] = $this->model_shifts->getHistory(50);
		$this->render_template('shifts/index', $this->data);
	}

	/** Trạng thái ca đang mở của 1 cửa hàng (AJAX cho POS + trang chốt ca). */
	public function status()
	{
		$store_id = (int)$this->input->get('store_id');
		if ($store_id <= 0) { $this->_json(array('open'=>false)); return; }
		$shift = $this->model_shifts->getOpenShiftByStore($store_id);
		if (!$shift) { $this->_json(array('open'=>false)); return; }
		$sales = $this->model_shifts->calcCashSales($store_id, $shift['check_in'], null);
		$this->_json(array(
			'open'         => true,
			'shift_id'     => (int)$shift['id'],
			'check_in'     => $shift['check_in'],
			'opening_cash' => (float)$shift['opening_cash'],
			'cash_sales'   => $sales['revenue'],
			'order_count'  => $sales['order_count'],
			'expected'     => (float)$shift['opening_cash'] + $sales['revenue'],
		));
	}

	public function open()
	{
		if (!$this->_canUse()) { $this->_json(array('ok'=>false,'error'=>'Không có quyền')); return; }
		$store_id = (int)$this->input->post('store_id');
		$opening  = (float)$this->input->post('opening_cash');
		if ($store_id <= 0) { $this->_json(array('ok'=>false,'error'=>'Chưa chọn cửa hàng')); return; }
		if ($this->model_shifts->getOpenShiftByStore($store_id)) {
			$this->_json(array('ok'=>false,'error'=>'Cửa hàng này đang có ca mở. Hãy đóng ca cũ trước.')); return;
		}
		$user_id = (int)$this->session->userdata('id');
		$id = $this->model_shifts->openShift($user_id, $store_id, $opening, trim((string)$this->input->post('note')));
		if (!$id) { $this->_json(array('ok'=>false,'error'=>'Lỗi khi mở ca')); return; }
		$this->_json(array('ok'=>true,'shift_id'=>$id));
	}

	public function close()
	{
		if (!$this->_canUse()) { $this->_json(array('ok'=>false,'error'=>'Không có quyền')); return; }
		$shift_id = (int)$this->input->post('shift_id');
		$closing  = (float)$this->input->post('closing_cash');
		if ($shift_id <= 0) { $this->_json(array('ok'=>false,'error'=>'Thiếu mã ca')); return; }
		$res = $this->model_shifts->closeShift($shift_id, $closing, trim((string)$this->input->post('note')));
		if (!$res) { $this->_json(array('ok'=>false,'error'=>'Ca không tồn tại hoặc đã đóng')); return; }
		$res['ok'] = true;
		$this->_json($res);
	}
}
