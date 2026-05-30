<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Nhà cung cấp';
        $this->load->model('model_suppliers');
    }

    public function index()
    {
        $this->data['results'] = $this->model_suppliers->getAll();
        $this->render_template('suppliers/index', $this->data);
    }

    public function fetchData()
    {
        $result = array('data' => array());
        foreach ($this->model_suppliers->getAll() as $k => $r) {
            $buttons = '<button type="button" class="btn btn-default" onclick="editSupplier('.$r['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button> '
                     . '<button type="button" class="btn btn-default" onclick="removeSupplier('.$r['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            $debt = number_format((float)$r['debt'], 0, ',', '.') . 'đ';
            $debt_html = ((float)$r['debt'] > 0) ? '<span class="label label-warning">'.$debt.'</span>' : '<span class="text-muted">'.$debt.'</span>';
            $result['data'][$k] = array(
                htmlspecialchars($r['name']),
                htmlspecialchars($r['phone'] ?: '—'),
                htmlspecialchars($r['email'] ?: '—'),
                $debt_html,
                $buttons,
            );
        }
        echo json_encode($result);
    }

    public function getById($id)
    {
        echo json_encode($this->model_suppliers->getAll($id));
    }

    public function create()
    {
        $resp = array('success' => false);
        $this->form_validation->set_rules('name', 'Tên NCC', 'trim|required');
        if ($this->form_validation->run()) {
            $data = array(
                'name'    => $this->input->post('name'),
                'phone'   => $this->input->post('phone'),
                'email'   => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'note'    => $this->input->post('note'),
                'active'  => 1,
            );
            try {
                $id = $this->model_suppliers->create($data);
                if ($id) {
                    $this->audit->log('create', 'suppliers', (int)$id, null, $data);
                    $resp['success'] = true; $resp['messages'] = 'Đã thêm NCC.';
                } else $resp['messages'] = 'Lỗi khi tạo';
            } catch (Exception $e) {
                $resp['messages'] = stripos($e->getMessage(),'Duplicate')!==false
                    ? 'NCC đã tồn tại (trùng thông tin unique).'
                    : 'Lỗi: '.$e->getMessage();
            }
        } else $resp['messages'] = validation_errors();
        echo json_encode($resp);
    }

    public function update($id)
    {
        $resp = array('success' => false);
        $this->form_validation->set_rules('edit_name', 'Tên NCC', 'trim|required');
        if ($this->form_validation->run()) {
            $data = array(
                'name'    => $this->input->post('edit_name'),
                'phone'   => $this->input->post('edit_phone'),
                'email'   => $this->input->post('edit_email'),
                'address' => $this->input->post('edit_address'),
                'note'    => $this->input->post('edit_note'),
            );
            $old_row = $this->db->get_where('suppliers', array('id'=>$id))->row_array();
            $ok = $this->model_suppliers->update($id, $data);
            if ($ok) $this->audit->log('update', 'suppliers', (int)$id, $old_row, $data);
            $resp['success'] = (bool)$ok;
            $resp['messages'] = $ok ? 'Đã cập nhật.' : 'Lỗi cập nhật.';
        } else $resp['messages'] = validation_errors();
        echo json_encode($resp);
    }

    public function remove()
    {
        $id = (int)$this->input->post('supplier_id');
        $old_row = $this->db->get_where('suppliers', array('id'=>$id))->row_array();
        $ok = $this->model_suppliers->remove($id);
        if ($ok) $this->audit->log('delete', 'suppliers', $id, $old_row, null);
        echo json_encode(array('success' => (bool)$ok, 'messages' => $ok ? 'Đã xóa.' : 'Lỗi xóa.'));
    }
}
