<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Nhập hàng';
        $this->load->model('model_purchases');
        $this->load->model('model_suppliers');
        $this->load->model('model_stores');
        $this->load->model('model_products');
    }

    public function index()
    {
        $this->render_template('purchases/index', $this->data);
    }

    public function fetchData()
    {
        $result = array('data' => array());
        foreach ($this->model_purchases->getAll() as $k => $r) {
            $total = number_format((float)$r['total_amount'], 0, ',', '.') . 'đ';
            $debt = (float)$r['debt_amount'] > 0
                ? '<span class="label label-warning">'.number_format($r['debt_amount'],0,',','.').'đ</span>'
                : '<span class="label label-success">Đã trả</span>';
            $buttons = '<a href="'.base_url('purchases/view/'.$r['id']).'" class="btn btn-default"><i class="fa fa-eye"></i></a>';
            $result['data'][$k] = array(
                $r['code'],
                htmlspecialchars($r['supplier_name'] ?: '—'),
                htmlspecialchars($r['store_name'] ?: '—'),
                date('d/m/Y H:i', strtotime($r['created_at'])),
                $total,
                $debt,
                $buttons,
            );
        }
        echo json_encode($result);
    }

    public function create()
    {
        $this->data['suppliers'] = $this->model_suppliers->getAll();
        $this->data['stores']    = $this->model_stores->getStoresData();
        $this->data['products']  = $this->model_products->getActiveProductData();
        $this->render_template('purchases/create', $this->data);
    }

    public function save()
    {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            echo json_encode(array('ok'=>false,'error'=>'Dữ liệu không hợp lệ')); return;
        }
        $data = array(
            'supplier_id' => (int)($payload['supplier_id'] ?? 0),
            'store_id'    => (int)($payload['store_id'] ?? 0),
            'items'       => $payload['items'] ?? array(),
            'paid_amount' => (float)($payload['paid_amount'] ?? 0),
            'note'        => trim($payload['note'] ?? ''),
            'user_id'     => (int)$this->session->userdata('id'),
        );
        $id = $this->model_purchases->create($data);
        if ($id) {
            $this->audit->log('create', 'purchases', (int)$id, null, $data);
            echo json_encode(array('ok'=>true,'id'=>$id,'redirect'=>base_url('purchases/view/'.$id)));
        } else {
            echo json_encode(array('ok'=>false,'error'=>'Lỗi tạo phiếu nhập (kiểm tra cửa hàng / item)'));
        }
    }

    public function view($id)
    {
        $purchase = $this->model_purchases->getAll($id);
        if (!$purchase) { show_404(); return; }
        $this->data['purchase'] = $purchase;
        $this->data['items'] = $this->model_purchases->getItems($id);
        $this->render_template('purchases/view', $this->data);
    }
}
