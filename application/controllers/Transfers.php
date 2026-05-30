<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfers extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Chuyển kho';
        $this->load->model('model_transfers');
        $this->load->model('model_stores');
        $this->load->model('model_products');
        $this->load->model('model_stock');
    }

    public function index()
    {
        $this->render_template('transfers/index', $this->data);
    }

    public function fetchData()
    {
        $result = array('data' => array());
        foreach ($this->model_transfers->getAll() as $k => $r) {
            $buttons = '<a href="'.base_url('transfers/view/'.$r['id']).'" class="btn btn-default"><i class="fa fa-eye"></i></a>';
            $status_html = '<span class="label label-success">Hoàn tất</span>';
            $result['data'][$k] = array(
                $r['code'],
                htmlspecialchars($r['from_name'] ?: '—'),
                htmlspecialchars($r['to_name'] ?: '—'),
                date('d/m/Y H:i', strtotime($r['created_at'])),
                $status_html,
                $buttons,
            );
        }
        echo json_encode($result);
    }

    public function create()
    {
        $this->data['stores']   = $this->model_stores->getStoresData();
        $this->data['products'] = $this->model_products->getActiveProductData();
        $this->render_template('transfers/create', $this->data);
    }

    public function save()
    {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) { echo json_encode(['ok'=>false,'error'=>'Dữ liệu không hợp lệ']); return; }
        $data = array(
            'from_store_id' => (int)($payload['from_store_id'] ?? 0),
            'to_store_id'   => (int)($payload['to_store_id'] ?? 0),
            'items'         => $payload['items'] ?? array(),
            'note'          => trim($payload['note'] ?? ''),
            'user_id'       => (int)$this->session->userdata('id'),
        );
        $id = $this->model_transfers->create($data);
        if ($id) {
            $this->audit->log('create', 'stock_transfers', (int)$id, null, $data);
            echo json_encode(['ok'=>true,'id'=>$id,'redirect'=>base_url('transfers/view/'.$id)]);
        } else {
            echo json_encode(['ok'=>false,'error'=>'Lỗi (kiểm tra cửa hàng nguồn/đích phải khác nhau và có item)']);
        }
    }

    public function view($id)
    {
        $t = $this->model_transfers->getAll($id);
        if (!$t) { show_404(); return; }
        $this->data['transfer'] = $t;
        $this->data['items'] = $this->model_transfers->getItems($id);
        $this->render_template('transfers/view', $this->data);
    }

    /** API: trả tồn của 1 SP theo store_id */
    public function stock($product_id, $store_id)
    {
        $this->output->set_content_type('application/json');
        echo json_encode(array('qty' => $this->model_stock->getQty($product_id, $store_id)));
    }
}
