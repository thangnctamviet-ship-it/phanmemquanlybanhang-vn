<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->data['page_title'] = 'Cửa hàng';
		$this->load->model('model_reports');
	}

	/*
    * It redirects to the report page
    * and based on the year, all the orders data are fetch from the database.
    */
	public function index()
	{
		if(!in_array('viewReports', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$today_year = date('Y');

		if($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		$parking_data = $this->model_reports->getOrderData($today_year);
		$this->data['report_years'] = $this->model_reports->getOrderYear();


		$final_parking_data = array();
		foreach ($parking_data as $k => $v) {

			if(count($v) > 1) {
				$total_amount_earned = array();
				foreach ($v as $k2 => $v2) {
					if($v2) {
						$total_amount_earned[] = $v2['gross_amount'];
					}
				}
				$final_parking_data[$k] = array_sum($total_amount_earned);
			}
			else {
				$final_parking_data[$k] = 0;
			}

		}

		$this->data['selected_year'] = $today_year;
		$this->data['company_currency'] = $this->company_currency();
		$this->data['results'] = $final_parking_data;

		$this->render_template('reports/index', $this->data);
	}

	/* ===== 2.2 Báo cáo nâng cao ===== */
	public function advanced()
	{
		if (!in_array('viewReports', $this->permission)) { redirect('dashboard','refresh'); }
		$this->load->model('model_reports2');

		$from = $this->input->get('from') ?: date('Y-m-01');
		$to   = $this->input->get('to')   ?: date('Y-m-d');
		$tab  = $this->input->get('tab')  ?: 'overview';

		$m = $this->model_reports2;
		$this->data['from'] = $from;
		$this->data['to'] = $to;
		$this->data['tab'] = $tab;
		$this->data['summary']       = $m->summary($from, $to);
		$this->data['daily']         = $m->dailyRevenue($from, $to);
		$this->data['top_products']  = $m->topProducts($from, $to, 20);
		$this->data['by_employee']   = $m->byEmployee($from, $to);
		$this->data['by_store']      = $m->byStore($from, $to);
		$this->data['slow_moving']   = $m->slowMoving(90, 50);
		$this->data['inventory']     = $m->inventoryValue();
		$this->data['page_title']    = 'Báo cáo nâng cao';

		$this->render_template('reports/advanced', $this->data);
	}

	/** Export CSV (Excel mở được trực tiếp) */
	public function exportCsv($section = 'top_products')
	{
		if (!in_array('viewReports', $this->permission)) { redirect('dashboard','refresh'); }
		$this->load->model('model_reports2');
		$from = $this->input->get('from') ?: date('Y-m-01');
		$to   = $this->input->get('to')   ?: date('Y-m-d');

		$rows = array();
		$header = array();
		switch ($section) {
			case 'top_products':
				$header = array('Tên SP','SKU','SL bán','Doanh thu');
				foreach ($this->model_reports2->topProducts($from,$to,500) as $r) {
					$rows[] = array($r['name'], $r['sku'], $r['qty'], $r['revenue']);
				}
				break;
			case 'by_employee':
				$header = array('Nhân viên','Số đơn','Doanh thu','Giảm giá');
				foreach ($this->model_reports2->byEmployee($from,$to) as $r) {
					$name = trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? '')) ?: $r['username'];
					$rows[] = array($name, $r['order_count'], $r['revenue'], $r['discount']);
				}
				break;
			case 'by_store':
				$header = array('Cửa hàng','Số đơn','Doanh thu');
				foreach ($this->model_reports2->byStore($from,$to) as $r) {
					$rows[] = array($r['name'] ?? '(N/A)', $r['order_count'], $r['revenue']);
				}
				break;
			case 'daily':
				$header = array('Ngày','Doanh thu','Số đơn');
				foreach ($this->model_reports2->dailyRevenue($from,$to) as $r) {
					$rows[] = array($r['date'], $r['revenue'], $r['orders']);
				}
				break;
			case 'slow_moving':
				$header = array('Tên SP','SKU','Tồn','Lần bán gần nhất');
				foreach ($this->model_reports2->slowMoving(90,500) as $r) {
					$last = $r['last_sold_ts'] ? date('Y-m-d', $r['last_sold_ts']) : 'Chưa bao giờ';
					$rows[] = array($r['name'], $r['sku'], $r['qty'], $last);
				}
				break;
			case 'inventory':
				$header = array('Cửa hàng','Tổng SL','Giá trị tồn');
				foreach ($this->model_reports2->inventoryValue() as $r) {
					$rows[] = array($r['store_name'], $r['qty'], $r['value']);
				}
				break;
			default: show_404(); return;
		}

		$filename = 'bao-cao-' . $section . '-' . date('Ymd-His') . '.csv';
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		// BOM để Excel hiểu UTF-8
		echo "\xEF\xBB\xBF";
		$out = fopen('php://output', 'w');
		fputcsv($out, $header);
		foreach ($rows as $r) fputcsv($out, $r);
		fclose($out);
	}
}