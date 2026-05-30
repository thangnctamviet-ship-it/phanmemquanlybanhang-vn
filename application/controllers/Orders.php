<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Đơn hàng';

		$this->load->model('model_orders');
		$this->load->model('model_products');
		$this->load->model('model_company');
	}

	/*
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->data['page_title'] = 'Quản lý đơn hàng';
		$this->render_template('orders/index', $this->data);
	}

	/*
	* Fetches the orders data from the orders table
	* this function is called from the datatable ajax function
	*/
	public function fetchOrdersData()
	{
		$result = array('data' => array());

		$data = $this->model_orders->getOrdersData();

		foreach ($data as $key => $value) {

			$count_total_item = $this->model_orders->countOrderItem($value['id']);
			$date = date('d-m-Y', $value['date_time']);
			$time = date('h:i a', $value['date_time']);

			$date_time = $date . ' ' . $time;

			// button
			$buttons = '';

			if(in_array('viewOrder', $this->permission)) {
				$buttons .= '<a target="__blank" href="'.base_url('orders/printDiv/'.$value['id']).'" class="btn btn-default"><i class="fa fa-print"></i></a>';
			}

			if(in_array('updateOrder', $this->permission)) {
				$buttons .= ' <a href="'.base_url('orders/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			}

			if(in_array('deleteOrder', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}

			if($value['paid_status'] == 1) {
				$paid_status = '<span class="label label-success">Đã thanh toán</span>';
			}
			else {
				$paid_status = '<span class="label label-warning">Chưa thanh toán</span>';
			}

			$result['data'][$key] = array(
				$value['bill_no'],
				$value['customer_name'],
				$value['customer_phone'],
				$date_time,
				$count_total_item,
				$value['net_amount'],
				$paid_status,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{
		if(!in_array('createOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if ($this->license && !$this->license->canCreateOrder()) {
			$this->session->set_flashdata('errors', 'Tài khoản đã hết hạn dùng thử. Vui lòng gia hạn để tiếp tục tạo đơn hàng.');
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Thêm đơn hàng';

		$this->form_validation->set_rules('product[]', 'Tên sản phẩm', 'trim|required');


        if ($this->form_validation->run() == TRUE) {

	$order_id = $this->model_orders->create();

	if($order_id) {
		$this->audit->log('create', 'orders', (int)$order_id, null, $this->input->post());
		$this->session->set_flashdata('success', 'Tạo thành công');
		redirect('orders/update/'.$order_id, 'refresh');
	}
	else {
		$this->session->set_flashdata('errors', 'Đã xảy ra lỗi!!');
		redirect('orders/create/', 'refresh');
	}
        }
        else {
            // false case
	$company = $this->model_company->getCompanyData(1);
	$this->data['company_data'] = $company;
	$this->data['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
	$this->data['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

	$this->data['products'] = $this->model_products->getActiveProductData();

            $this->render_template('orders/create', $this->data);
        }
	}

	/*
	* It gets the product id passed from the ajax method.
	* It checks retrieves the particular product data from the product id
	* and return the data into the json format.
	*/
	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}

	/*
	* It gets the all the active product inforamtion from the product table
	* This function is used in the order page, for the product selection in the table
	* The response is return on the json format.
	*/
	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	/*
	* If the validation is not valid, then it redirects to the edit orders page
	* If the validation is successfully then it updates the data into the database
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function update($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Cập nhật đơn hàng';

		$this->form_validation->set_rules('product[]', 'Tên sản phẩm', 'trim|required');


        if ($this->form_validation->run() == TRUE) {

	$update = $this->model_orders->update($id);

	if($update == true) {
		$this->session->set_flashdata('success', 'Cập nhật thành công');
		redirect('orders/update/'.$id, 'refresh');
	}
	else {
		$this->session->set_flashdata('errors', 'Đã xảy ra lỗi!!');
		redirect('orders/update/'.$id, 'refresh');
	}
        }
        else {
            // false case
	$company = $this->model_company->getCompanyData(1);
	$this->data['company_data'] = $company;
	$this->data['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
	$this->data['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

	$result = array();
	$orders_data = $this->model_orders->getOrdersData($id);

		$result['order'] = $orders_data;
		$orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);

		foreach($orders_item as $k => $v) {
			$result['order_item'][] = $v;
		}

		$this->data['order_data'] = $result;

	$this->data['products'] = $this->model_products->getActiveProductData();

            $this->render_template('orders/edit', $this->data);
        }
	}

	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if(!in_array('deleteOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$order_id = $this->input->post('order_id');

        $response = array();
        if($order_id) {
            $old_row = $this->db->get_where('orders', array('id'=>$order_id))->row_array();
            $delete = $this->model_orders->remove($order_id);
            if($delete == true) {
                $this->audit->log('delete', 'orders', (int)$order_id, $old_row, null);
                $response['success'] = true;
                $response['messages'] = "Xoá thành công";
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Lỗi cơ sở dữ liệu khi xoá thông tin sản phẩm";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refersh the page again!!";
        }

        echo json_encode($response);
	}

	/*
	* It gets the product id and fetch the order data.
	* The order print logic is done here
	*/
	public function printDiv($id)
	{
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y', $order_data['date_time']);
			$paid_status = ($order_data['paid_status'] == 1) ? "Đã thanh toán" : "Chưa thanh toán";

			$company_addr = isset($company_info['address']) ? $company_info['address'] : '';
			$company_phone = isset($company_info['phone']) ? $company_info['phone'] : '';

			$html = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Hoá đơn '.$order_data['bill_no'].'</title>
<style>
@page { size: 80mm auto; margin: 2mm; }
* { box-sizing: border-box; }
body { font-family: -apple-system, "Segoe UI", Tahoma, Arial, sans-serif; font-size: 11px; line-height: 1.35; margin: 0; padding: 0; width: 76mm; color:#000; }
.center { text-align: center; }
.right { text-align: right; }
.bold { font-weight: bold; }
.company { font-size: 14px; font-weight: bold; }
.small { font-size: 10px; }
.title { font-size: 12px; font-weight: bold; margin: 2px 0; }
hr { border: none; border-top: 1px dashed #000; margin: 4px 0; }
.row { display: flex; justify-content: space-between; }
.item-name { font-weight: bold; }
.item-line { display: flex; justify-content: space-between; }
.total-line { font-size: 13px; font-weight: bold; }
.thanks { font-style: italic; margin-top: 4px; }
table { width: 100%; border-collapse: collapse; }
@media print { body { width: 76mm; } }
</style>
</head>
<body onload="window.print();">
<div class="center company">'.htmlspecialchars($company_info['company_name']).'</div>';
			if ($company_addr) $html .= '<div class="center small">'.htmlspecialchars($company_addr).'</div>';
			if ($company_phone) $html .= '<div class="center small">ĐT: '.htmlspecialchars($company_phone).'</div>';
			$html .= '<hr>
<div class="center title">HOÁ ĐƠN BÁN HÀNG</div>
<div>Số: <b>'.$order_data['bill_no'].'</b></div>
<div>Ngày: '.$order_date.'</div>';
			if (!empty($order_data['customer_name'])) $html .= '<div>KH: '.htmlspecialchars($order_data['customer_name']).'</div>';
			if (!empty($order_data['customer_phone'])) $html .= '<div>ĐT: '.htmlspecialchars($order_data['customer_phone']).'</div>';
			if (!empty($order_data['customer_address'])) $html .= '<div>ĐC: '.htmlspecialchars($order_data['customer_address']).'</div>';
			$html .= '<hr>';

			foreach ($orders_items as $k => $v) {
				$product_data = $this->model_products->getProductData($v['product_id']);
				$html .= '<div class="item-name">'.htmlspecialchars($product_data['name']).'</div>'
					.'<div class="item-line"><span>'.$v['qty'].' x '.format_vnd($v['rate']).'</span><span>'.format_vnd($v['amount']).'</span></div>';
			}

			$html .= '<hr>';
			$html .= '<div class="row"><span>Tổng tiền hàng:</span><span>'.format_vnd($order_data['gross_amount']).'</span></div>';
			if ($order_data['vat_charge'] > 0) {
				$html .= '<div class="row"><span>VAT ('.$order_data['vat_charge_rate'].'%):</span><span>'.format_vnd($order_data['vat_charge']).'</span></div>';
			}
			if ($order_data['service_charge'] > 0) {
				$html .= '<div class="row"><span>Phí DV ('.$order_data['service_charge_rate'].'%):</span><span>'.format_vnd($order_data['service_charge']).'</span></div>';
			}
			if ($order_data['discount'] > 0) {
				$html .= '<div class="row"><span>Giảm giá:</span><span>-'.format_vnd($order_data['discount']).'</span></div>';
			}
			$html .= '<hr>';
			$html .= '<div class="row total-line"><span>THÀNH TIỀN:</span><span>'.format_vnd($order_data['net_amount']).'</span></div>';
			$html .= '<div>Thanh toán: '.$paid_status.'</div>';
			$html .= '<hr>';
			$html .= '<div class="center thanks">Cảm ơn quý khách! Hẹn gặp lại.</div>';
			$html .= '</body></html>';

			echo $html;
		}
	}

}