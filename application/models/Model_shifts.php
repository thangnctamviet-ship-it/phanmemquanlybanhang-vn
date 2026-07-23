<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Quản lý ca làm việc / chốt ca tiền mặt.
 * Bảng employee_shifts: user_id, store_id, check_in, check_out,
 * opening_cash, closing_cash, total_sales, order_count, note.
 */
class Model_shifts extends CI_Model
{
	public function __construct() { parent::__construct(); }

	/** Ca đang mở của 1 cửa hàng (check_out IS NULL). Trả row hoặc null. */
	public function getOpenShiftByStore($store_id)
	{
		$store_id = (int)$store_id;
		$sql = "SELECT * FROM `employee_shifts`
		        WHERE store_id = ? AND check_out IS NULL AND deleted_at IS NULL
		        ORDER BY id DESC LIMIT 1";
		return $this->db->query($sql, array($store_id))->row_array();
	}

	/** Ca đang mở bất kỳ của user (mọi cửa hàng). */
	public function getOpenShiftByUser($user_id)
	{
		$user_id = (int)$user_id;
		$sql = "SELECT * FROM `employee_shifts`
		        WHERE user_id = ? AND check_out IS NULL AND deleted_at IS NULL
		        ORDER BY id DESC LIMIT 1";
		return $this->db->query($sql, array($user_id))->row_array();
	}

	/** Mở ca mới. Trả id ca hoặc false. */
	public function openShift($user_id, $store_id, $opening_cash, $note = '')
	{
		$data = array(
			'user_id'      => (int)$user_id,
			'store_id'     => (int)$store_id,
			'check_in'     => date('Y-m-d H:i:s'),
			'opening_cash' => (float)$opening_cash,
			'note'         => $note,
		);
		if (!$this->db->insert('employee_shifts', $data)) return false;
		return (int)$this->db->insert_id();
	}

	/**
	 * Tính tiền mặt bán ra trong khoảng ca (theo cửa hàng).
	 * Chỉ đơn cash (cash_account_id = 1) đã thanh toán (paid_status=1).
	 * @return array [revenue, order_count]
	 */
	public function calcCashSales($store_id, $check_in_datetime, $check_out_datetime = null)
	{
		$store_id = (int)$store_id;
		$from = strtotime($check_in_datetime);
		$to   = $check_out_datetime ? strtotime($check_out_datetime) : time();
		// date_time trong orders lưu chuỗi unix timestamp
		$sql = "SELECT COALESCE(SUM(CAST(net_amount AS DECIMAL(15,2))),0) rev, COUNT(*) oc
		        FROM `orders`
		        WHERE store_id = ?
		          AND paid_status = 1
		          AND (cash_account_id = 1)
		          AND CAST(date_time AS UNSIGNED) BETWEEN ? AND ?";
		if ($this->db->field_exists('deleted_at', 'orders')) $sql .= " AND deleted_at IS NULL";
		$r = $this->db->query($sql, array($store_id, $from, $to))->row_array();
		return array('revenue' => (float)($r['rev'] ?? 0), 'order_count' => (int)($r['oc'] ?? 0));
	}

	/** Đóng ca: ghi tiền cuối ca + tổng bán + chênh lệch. Trả mảng kết quả. */
	public function closeShift($shift_id, $closing_cash, $note = '')
	{
		$shift_id = (int)$shift_id;
		$shift = $this->db->get_where('employee_shifts', array('id'=>$shift_id))->row_array();
		if (!$shift || $shift['check_out'] !== null) return false;

		$sales = $this->calcCashSales($shift['store_id'], $shift['check_in'], null);
		$expected = (float)$shift['opening_cash'] + $sales['revenue']; // tiền đáng lẽ có trong két
		$diff = (float)$closing_cash - $expected;                      // >0 thừa, <0 thiếu

		$upd = array(
			'check_out'    => date('Y-m-d H:i:s'),
			'closing_cash' => (float)$closing_cash,
			'total_sales'  => $sales['revenue'],
			'order_count'  => $sales['order_count'],
		);
		if ($note !== '') $upd['note'] = trim(($shift['note'] ? $shift['note']."\n" : '').$note);
		$this->db->where('id', $shift_id)->update('employee_shifts', $upd);

		return array(
			'opening_cash' => (float)$shift['opening_cash'],
			'cash_sales'   => $sales['revenue'],
			'order_count'  => $sales['order_count'],
			'expected'     => $expected,
			'closing_cash' => (float)$closing_cash,
			'difference'   => $diff,
		);
	}

	/** Lịch sử ca gần đây (kèm tên NV + cửa hàng). */
	public function getHistory($limit = 50, $store_id = 0)
	{
		$limit = (int)$limit; $store_id = (int)$store_id;
		$where = "es.deleted_at IS NULL";
		if ($store_id > 0) $where .= " AND es.store_id = ".$store_id;
		$sql = "SELECT es.*, u.username, s.name AS store_name
		        FROM `employee_shifts` es
		        LEFT JOIN `users` u ON u.id = es.user_id
		        LEFT JOIN `stores` s ON s.id = es.store_id
		        WHERE $where
		        ORDER BY es.id DESC LIMIT $limit";
		return $this->db->query($sql)->result_array();
	}
}
