<?php

class Dashboard extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Bảng điều khiển';

		$this->load->model('model_products');
		$this->load->model('model_orders');
		$this->load->model('model_users');
		$this->load->model('model_stores');
		$this->load->model('model_dashboard');
	}

	/*
	* It only redirects to the manage category page
	* It passes the total product, total paid orders, total users, and total stores information
	into the frontend.
	*/
	public function index()
	{
		if (empty($_COOKIE['qlbh_onboarded']) && $this->session->userdata('logged_in')) {
			redirect('onboarding/step1');
			return;
		}
		$this->data['total_products']     = $this->model_products->countTotalProducts();
		$this->data['total_paid_orders']  = $this->model_orders->countTotalPaidOrders();
		$this->data['total_users']        = $this->model_users->countTotalUsers();
		$this->data['total_stores']       = $this->model_stores->countTotalStores();

		// ===== Widgets mới (1.4 Dashboard) =====
		$m = $this->model_dashboard;
		$rev_today      = $m->revenueToday();
		$rev_yesterday  = $m->revenueYesterday();
		$diff_pct = 0;
		if ($rev_yesterday > 0) {
			$diff_pct = (($rev_today - $rev_yesterday) / $rev_yesterday) * 100;
		} elseif ($rev_today > 0) {
			$diff_pct = 100;
		}

		$this->data['rev_today']        = $rev_today;
		$this->data['rev_yesterday']    = $rev_yesterday;
		$this->data['rev_diff_pct']     = $diff_pct;
		$this->data['orders_today']     = $m->ordersToday();
		$this->data['rev_this_month']   = $m->revenueThisMonth();
		$this->data['cash_on_hand']     = $m->cashOnHand();
		$this->data['top_products']     = $m->topProducts(7, 5);
		$this->data['low_stock']        = $m->lowStockProducts(5);
		$this->data['low_stock_count']  = $m->lowStockCount();
		$this->data['chart_30d']        = $m->revenueLast30Days();
		$this->data['customer_debt']    = $m->totalCustomerDebt();
		$this->data['supplier_debt']    = $m->totalSupplierDebt();

		$user_id = $this->session->userdata('id');
		$is_admin = ($user_id == 1) ? true :false;

		$this->data['is_admin'] = $is_admin;
		$this->render_template('dashboard', $this->data);
	}
}