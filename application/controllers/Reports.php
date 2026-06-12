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
		$this->data['by_payment']    = $m->byPaymentMethod($from, $to);
		$this->data['slow_moving']   = $m->slowMoving(90, 50);
		$this->data['inventory']     = $m->inventoryValue();
		$this->load->model('model_stores');
		$this->data['store_list']    = $this->model_stores->getActiveStore();
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
					$name = trim(($r['firstname'] ?? '').' '.($r['lastname'] ?? '')) ?: $r['username'];
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
			case 'by_payment':
				$header = array('Nguồn tiền','Số đơn','Doanh thu');
				foreach ($this->model_reports2->byPaymentMethod($from,$to) as $r) {
					$rows[] = array($r['method'], $r['order_count'], $r['revenue']);
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

	/**
	 * Xuất Sổ chi tiết doanh thu bán hàng hóa, dịch vụ — Mẫu S1-HKD
	 * (Thông tư 88/2021/TT-BTC). Doanh thu gộp theo ngày, ghi vào cột 1
	 * (Phân phối, cung cấp hàng hóa). Xuất .xls (HTML table) — Excel mở tốt,
	 * không cần thư viện.
	 */
	public function exportS1HKD()
	{
		if (!in_array('viewReports', $this->permission)) { redirect('dashboard','refresh'); }
		$this->load->model('model_reports2');
		$this->load->model('model_company');

		$from     = $this->input->get('from') ?: date('Y-m-01');
		$to       = $this->input->get('to')   ?: date('Y-m-d');
		$store_id = (int)($this->input->get('store_id') ?: 0);

		$company  = $this->model_company->getCompanyData(1);
		$shop     = $company['company_name'] ?? '';
		$address  = $company['address'] ?? '';

		$store_name = '';
		if ($store_id > 0) {
			$this->load->model('model_stores');
			$st = $this->model_stores->getStoresData($store_id);
			$store_name = $st['name'] ?? '';
		}

		$rows  = $this->model_reports2->s1hkdDaily($from, $to, $store_id);
		$total = 0;
		foreach ($rows as $r) { $total += (float)$r['revenue']; }

		$year = date('Y', strtotime($to));
		$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
		$money = function ($n) { return number_format((float)$n, 0, ',', '.'); };

		$filename = 'So-S1-HKD-' . date('Ymd-His') . '.xls';
		header('Content-Type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		echo "\xEF\xBB\xBF";
		?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="utf-8"></head>
<body>
<table border="0" cellspacing="0" cellpadding="3">
  <tr><td colspan="4"><b>HỘ, CÁ NHÂN KINH DOANH:</b> <?= $h($shop) ?></td><td colspan="2" style="text-align:right;"><b>Mẫu số S1-HKD</b></td></tr>
  <tr><td colspan="4"><b>Địa chỉ:</b> <?= $h($address) ?></td><td colspan="2" style="text-align:right;">(Ban hành kèm theo Thông tư số 88/2021/TT-BTC ngày 11/10/2021 của Bộ Tài chính)</td></tr>
  <tr><td colspan="6">&nbsp;</td></tr>
  <tr><td colspan="6" style="text-align:center;font-size:16px;"><b>SỔ CHI TIẾT DOANH THU BÁN HÀNG HÓA, DỊCH VỤ</b></td></tr>
  <?php if ($store_name): ?>
  <tr><td colspan="6" style="text-align:center;">Tên địa điểm kinh doanh: <?= $h($store_name) ?></td></tr>
  <?php endif; ?>
  <tr><td colspan="6" style="text-align:center;">Từ ngày <?= $h(date('d/m/Y', strtotime($from))) ?> đến ngày <?= $h(date('d/m/Y', strtotime($to))) ?> — Năm: <?= $h($year) ?></td></tr>
  <tr><td colspan="6">&nbsp;</td></tr>
  <tr style="background:#dbeafe;font-weight:bold;text-align:center;">
    <td rowspan="2" style="border:1px solid #000;">Ngày, tháng ghi sổ</td>
    <td colspan="2" style="border:1px solid #000;">Chứng từ</td>
    <td rowspan="2" style="border:1px solid #000;">Diễn giải</td>
    <td colspan="2" style="border:1px solid #000;">Doanh thu bán hàng hóa, dịch vụ</td>
  </tr>
  <tr style="background:#dbeafe;font-weight:bold;text-align:center;">
    <td style="border:1px solid #000;">Số hiệu</td>
    <td style="border:1px solid #000;">Ngày, tháng</td>
    <td style="border:1px solid #000;">Phân phối, cung cấp hàng hóa</td>
    <td style="border:1px solid #000;">Hoạt động khác</td>
  </tr>
  <tr style="background:#f1f5f9;text-align:center;font-style:italic;">
    <td style="border:1px solid #000;">A</td><td style="border:1px solid #000;">B</td><td style="border:1px solid #000;">C</td>
    <td style="border:1px solid #000;">D</td><td style="border:1px solid #000;">1</td><td style="border:1px solid #000;">2</td>
  </tr>
  <?php foreach ($rows as $r):
    $d = date('d/m/Y', strtotime($r['sale_date'])); ?>
  <tr>
    <td style="border:1px solid #000;text-align:center;"><?= $h($d) ?></td>
    <td style="border:1px solid #000;text-align:center;">BH<?= $h(date('Ymd', strtotime($r['sale_date']))) ?></td>
    <td style="border:1px solid #000;text-align:center;"><?= $h($d) ?></td>
    <td style="border:1px solid #000;">Doanh thu bán hàng ngày <?= $h($d) ?> (<?= (int)$r['order_count'] ?> đơn)</td>
    <td style="border:1px solid #000;text-align:right;"><?= $money($r['revenue']) ?></td>
    <td style="border:1px solid #000;text-align:right;">0</td>
  </tr>
  <?php endforeach; ?>
  <tr style="font-weight:bold;background:#fef9c3;">
    <td colspan="4" style="border:1px solid #000;text-align:center;">Tổng cộng</td>
    <td style="border:1px solid #000;text-align:right;"><?= $money($total) ?></td>
    <td style="border:1px solid #000;text-align:right;">0</td>
  </tr>
  <tr><td colspan="6">&nbsp;</td></tr>
  <tr><td colspan="6">- Sổ này có ... trang, đánh số từ trang 01 đến trang ...</td></tr>
  <tr><td colspan="6">- Ngày mở sổ: ......................</td></tr>
  <tr><td colspan="6">&nbsp;</td></tr>
  <tr style="text-align:center;">
    <td colspan="3"><b>NGƯỜI LẬP BIỂU</b><br>(Ký, họ tên)</td>
    <td colspan="3"><i>Ngày .... tháng .... năm <?= $h($year) ?></i><br><b>NGƯỜI ĐẠI DIỆN HỘ KINH DOANH</b><br>(Ký, họ tên)</td>
  </tr>
</table>
</body></html>
		<?php
	}
}