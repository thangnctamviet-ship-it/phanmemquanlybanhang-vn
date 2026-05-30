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
            $data = array(
                'name'     => $this->input->post('name'),
                'phone'    => $this->input->post('phone'),
                'email'    => $this->input->post('email'),
                'address'  => $this->input->post('address'),
                'birthday' => $this->input->post('birthday') ?: null,
                'note'     => $this->input->post('note'),
            );
            $id = $this->model_customers->create($data);
            if ($id) $this->audit->log('create', 'customers', (int)$id, null, $data);
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
            $data = array(
                'name'     => $this->input->post('edit_name'),
                'phone'    => $this->input->post('edit_phone'),
                'email'    => $this->input->post('edit_email'),
                'address'  => $this->input->post('edit_address'),
                'birthday' => $this->input->post('edit_birthday') ?: null,
                'note'     => $this->input->post('edit_note'),
            );
            $old_row = $this->db->get_where('customers', array('id'=>$id))->row_array();
            $ok = $this->model_customers->update($id, $data);
            if ($ok) $this->audit->log('update', 'customers', (int)$id, $old_row, $data);
            $resp['success'] = (bool)$ok;
            $resp['messages'] = $ok ? 'Đã cập nhật.' : 'Lỗi cập nhật.';
        } else $resp['messages'] = validation_errors();
        echo json_encode($resp);
    }

    public function remove()
    {
        $id = (int)$this->input->post('customer_id');
        $old_row = $this->db->get_where('customers', array('id'=>$id))->row_array();
        $ok = $this->model_customers->remove($id);
        if ($ok) $this->audit->log('delete', 'customers', $id, $old_row, null);
        echo json_encode(array('success' => (bool)$ok, 'messages' => $ok ? 'Đã xóa.' : 'Lỗi xóa.'));
    }

    /** 2.3: Top KH + sinh nhật tháng */
    public function loyalty()
    {
        // Top KH theo loyalty_points
        $this->data['top_points'] = $this->db->query(
            "SELECT id, name, phone, loyalty_points, debt FROM `customers`
             WHERE loyalty_points > 0 ORDER BY loyalty_points DESC LIMIT 20"
        )->result_array();

        // Top KH theo tổng chi tiêu
        $hasCustomer = $this->db->field_exists('customer_id', 'orders');
        $this->data['top_spent'] = $hasCustomer ? $this->db->query(
            "SELECT c.id, c.name, c.phone,
                    COUNT(o.id) AS order_count,
                    COALESCE(SUM(CAST(o.net_amount AS DECIMAL(15,2))),0) AS total_spent
             FROM `customers` c
             JOIN `orders` o ON o.customer_id = c.id AND o.paid_status = 1
             GROUP BY c.id, c.name, c.phone
             ORDER BY total_spent DESC LIMIT 20"
        )->result_array() : array();

        // Sinh nhật tháng này
        $month = (int)date('n');
        $this->data['birthdays'] = $this->db->query(
            "SELECT id, name, phone, birthday, loyalty_points
             FROM `customers`
             WHERE birthday IS NOT NULL AND MONTH(birthday) = ?
             ORDER BY DAY(birthday)", array($month)
        )->result_array();

        // Sinh nhật hôm nay
        $this->data['birthdays_today'] = array_filter($this->data['birthdays'], function($c){
            return $c['birthday'] && date('m-d', strtotime($c['birthday'])) === date('m-d');
        });

        // Setting điểm
        $rate = 1;
        if ($this->db->table_exists('settings')) {
            $r = $this->db->query("SELECT `value` FROM `settings` WHERE `key`='loyalty_points_per_1000' LIMIT 1")->row_array();
            if ($r) $rate = (float)$r['value'];
        }
        $this->data['loyalty_rate'] = $rate;

        $this->data['page_title'] = 'Khách hàng thân thiết';
        $this->render_template('customers/loyalty', $this->data);
    }

    /** Cập nhật tỷ lệ tích điểm */
    public function setLoyaltyRate()
    {
        $rate = (float)$this->input->post('rate');
        if ($rate < 0) $rate = 0;
        if (!$this->db->table_exists('settings')) {
            echo json_encode(array('ok'=>false,'error'=>'Cần chạy migration 002')); return;
        }
        $this->db->query("INSERT INTO `settings`(`key`,`value`) VALUES('loyalty_points_per_1000', ?)
                          ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)", array($rate));
        echo json_encode(array('ok'=>true));
    }
}
