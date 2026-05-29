<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Khách hàng';
        $this->load->model('model_customers');
    }

    public function index()
    {
        $this->render_template('customers/index', $this->data);
    }

    public function fetchData()
    {
        $result = array('data' => array());
        foreach ($this->model_customers->getAll() as $k => $r) {
            $buttons = '<button type="button" class="btn btn-default" onclick="editCustomer('.$r['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button> '
                     . '<button type="button" class="btn btn-default" onclick="removeCustomer('.$r['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            $debt = number_format((float)$r['debt'], 0, ',', '.') . 'đ';
            $debt_html = ((float)$r['debt'] > 0) ? '<span class="label label-warning">'.$debt.'</span>' : '<span class="text-muted">'.$debt.'</span>';
            $result['data'][$k] = array(
                htmlspecialchars($r['name']),
                htmlspecialchars($r['phone'] ?: '—'),
                (int)$r['loyalty_points'],
                $debt_html,
                $buttons,
            );
        }
        echo json_encode($result);
    }

    public function getById($id) { echo json_encode($this->model_customers->getAll($id)); }

    public function create()
    {
        $resp = array('success' => false);
        $this->form_validation->set_rules('name', 'Tên KH', 'trim|required');
        if ($this->form_validation->run()) {
            $id = $this->model_customers->create(array(
                'name'     => $this->input->post('name'),
                'phone'    => $this->input->post('phone'),
                'email'    => $this->input->post('email'),
                'address'  => $this->input->post('address'),
                'birthday' => $this->input->post('birthday') ?: null,
                'note'     => $this->input->post('note'),
            ));
            $resp['success'] = (bool)$id;
            $resp['messages'] = $id ? 'Đã thêm khách hàng.' : 'Lỗi.';
        } else $resp['messages'] = validation_errors();
        echo json_encode($resp);
    }

    public function update($id)
    {
        $resp = array('success' => false);
        $this->form_validation->set_rules('edit_name', 'Tên KH', 'trim|required');
        if ($this->form_validation->run()) {
            $ok = $this->model_customers->update($id, array(
                'name'     => $this->input->post('edit_name'),
                'phone'    => $this->input->post('edit_phone'),
                'email'    => $this->input->post('edit_email'),
                'address'  => $this->input->post('edit_address'),
                'birthday' => $this->input->post('edit_birthday') ?: null,
                'note'     => $this->input->post('edit_note'),
            ));
            $resp['success'] = (bool)$ok;
            $resp['messages'] = $ok ? 'Đã cập nhật.' : 'Lỗi cập nhật.';
        } else $resp['messages'] = validation_errors();
        echo json_encode($resp);
    }

    public function remove()
    {
        $id = (int)$this->input->post('customer_id');
        $ok = $this->model_customers->remove($id);
        echo json_encode(array('success' => (bool)$ok, 'messages' => $ok ? 'Đã xóa.' : 'Lỗi xóa.'));
    }
}
