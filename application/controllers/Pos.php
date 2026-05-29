<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * POS — màn hình bán hàng nhanh (1.1)
 *
 * - Route: /pos (fullscreen, không sidebar)
 * - API JSON: /pos/products  /pos/checkout
 */
class Pos extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('model_products');
        $this->load->model('model_orders');
        $this->load->model('model_stores');
        $this->load->model('model_company');
        $this->load->model('model_stock');
    }

    /** Helper: gắn tồn theo store cho mỗi SP. */
    private function attachStock(&$rows, $store_id)
    {
        if (!$store_id || empty($rows)) return;
        foreach ($rows as &$r) {
            $r['stock'] = $this->model_stock->getQty((int)$r['id'], (int)$store_id);
            // ghi đè qty để frontend dùng nhất quán
            $r['qty'] = $r['stock'];
        }
        unset($r);
    }

    public function index()
    {
        if (!in_array('createOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        if ($this->license && !$this->license->canCreateOrder()) {
            $this->session->set_flashdata('errors', 'Tài khoản đã hết hạn dùng thử.');
            redirect('dashboard', 'refresh');
        }

        $data = array(
            'page_title' => 'Bán hàng nhanh',
            'stores'     => $this->model_stores->getStoresData(),
            'company'    => $this->model_company->getCompanyData(1),
        );
        $this->load->view('pos/index', $data);
    }

    /** API: trả danh sách SP còn bán (JSON). Hỗ trợ ?q=keyword */
    public function products()
    {
        if (!in_array('createOrder', $this->permission)) {
            show_error('forbidden', 403); return;
        }
        $q = trim((string)$this->input->get('q'));
        $store_id = (int)$this->input->get('store_id');
        $sql = "SELECT id, name, sku, price, qty, image FROM `products` WHERE availability = 1";
        $params = array();
        if ($q !== '') {
            $sql .= " AND (name LIKE ? OR sku LIKE ?)";
            $like = "%{$q}%";
            $params = array($like, $like);
        }
        $sql .= " ORDER BY id DESC LIMIT 200";
        $rows = $this->db->query($sql, $params)->result_array();
        $this->attachStock($rows, $store_id);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($rows));
    }

    /** Tìm sản phẩm theo barcode/SKU chính xác. */
    public function lookup()
    {
        if (!in_array('createOrder', $this->permission)) { show_error('forbidden', 403); return; }
        $code = trim((string)$this->input->get('code'));
        $store_id = (int)$this->input->get('store_id');
        $row = null;
        if ($code !== '') {
            $sql = "SELECT id, name, sku, price, qty, image FROM `products`
                    WHERE availability = 1 AND (sku = ? OR id = ?) LIMIT 1";
            $row = $this->db->query($sql, array($code, (int)$code))->row_array();
            if ($row && $store_id) {
                $rows = array($row);
                $this->attachStock($rows, $store_id);
                $row = $rows[0];
            }
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($row ?: null));
    }

    /** Tạo đơn từ giỏ hàng POS. Body JSON: { items:[{id,qty,price}], discount, paid_amount, customer_name, customer_phone } */
    public function checkout()
    {
        if (!in_array('createOrder', $this->permission)) { show_error('forbidden', 403); return; }
        if ($this->license && !$this->license->canCreateOrder()) {
            $this->_json(array('ok' => false, 'error' => 'Hết hạn dùng thử'));
            return;
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload) || empty($payload['items'])) {
            $this->_json(array('ok' => false, 'error' => 'Giỏ hàng trống'));
            return;
        }

        $user_id = $this->session->userdata('id');
        $items = $payload['items'];
        $discount = isset($payload['discount']) ? (float)$payload['discount'] : 0;
        $paid_amount = isset($payload['paid_amount']) ? (float)$payload['paid_amount'] : 0;
        $customer_name = isset($payload['customer_name']) ? trim($payload['customer_name']) : '';
        $customer_phone = isset($payload['customer_phone']) ? trim($payload['customer_phone']) : '';
        $store_id = isset($payload['store_id']) ? (int)$payload['store_id'] : 0;

        // Tính tổng
        $gross = 0;
        $clean_items = array();
        foreach ($items as $it) {
            $pid = (int)($it['id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            $price = (float)($it['price'] ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;
            $amount = $qty * $price;
            $gross += $amount;
            $clean_items[] = array('id' => $pid, 'qty' => $qty, 'price' => $price, 'amount' => $amount);
        }
        if (empty($clean_items)) {
            $this->_json(array('ok' => false, 'error' => 'Giỏ hàng không hợp lệ'));
            return;
        }
        $net = max(0, $gross - $discount);
        $paid_status = ($paid_amount >= $net) ? 1 : 2;

        // Bill no
        $bill_no = 'POS-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

        $order = array(
            'bill_no' => $bill_no,
            'customer_name' => $customer_name,
            'customer_address' => '',
            'customer_phone' => $customer_phone,
            'date_time' => time(),
            'gross_amount' => $gross,
            'service_charge_rate' => 0,
            'service_charge' => 0,
            'vat_charge_rate' => 0,
            'vat_charge' => 0,
            'net_amount' => $net,
            'discount' => $discount,
            'paid_status' => $paid_status,
            'user_id' => $user_id,
        );
        // Cột store_id/paid_amount/debt_amount đã có sau migration 001 (tránh fail nếu chưa migrate)
        if ($this->db->field_exists('store_id', 'orders'))     $order['store_id']     = $store_id;
        if ($this->db->field_exists('paid_amount', 'orders'))  $order['paid_amount']  = $paid_amount;
        if ($this->db->field_exists('debt_amount', 'orders'))  $order['debt_amount']  = max(0, $net - $paid_amount);

        $this->db->insert('orders', $order);
        $order_id = $this->db->insert_id();

        // Insert items + trừ kho qua Model_stock (sẽ tự sync products.qty)
        foreach ($clean_items as $it) {
            $this->db->insert('orders_item', array(
                'order_id' => $order_id,
                'product_id' => $it['id'],
                'qty' => $it['qty'],
                'rate' => $it['price'],
                'amount' => $it['amount'],
            ));
            if ($store_id) {
                $this->model_stock->adjust($it['id'], $store_id, -$it['qty']);
            } else {
                // Fallback: trừ thẳng products.qty
                $row = $this->db->query("SELECT qty FROM `products` WHERE id = ?", array($it['id']))->row_array();
                $new_qty = (int)$row['qty'] - $it['qty'];
                $this->db->where('id', $it['id'])->update('products', array('qty' => $new_qty));
            }
        }

        $this->_json(array(
            'ok' => true,
            'order_id' => $order_id,
            'bill_no' => $bill_no,
            'print_url' => base_url('orders/printDiv/' . $order_id),
        ));
    }

    private function _json($payload)
    {
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($payload));
    }
}
