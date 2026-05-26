<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Onboarding extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Chào mừng';
    }

    private function shop_name()
    {
        if ($this->license && $this->license->hasTenant()) {
            $t = $this->license->getTenant();
            return $t['shop_name'] ?? 'cửa hàng của bạn';
        }
        return 'cửa hàng của bạn';
    }

    private function done()
    {
        setcookie('qlbh_onboarded', '1', time() + 365*86400, '/');
        redirect('dashboard');
    }

    public function index() { redirect('onboarding/step1'); }

    public function step1()
    {
        $this->data['shop_name'] = $this->shop_name();
        $this->render_template('onboarding/step1', $this->data);
    }

    public function step2()
    {
        if ($this->input->method() === 'post') {
            $data = [
                'company_name' => $this->input->post('company_name'),
                'address'      => $this->input->post('address'),
                'phone'        => $this->input->post('phone'),
                'vat_charge_value' => (string)(float)$this->input->post('vat'),
            ];
            $row = $this->db->select('id')->from('company')->limit(1)->get()->row_array();
            if ($row) {
                $this->db->where('id', $row['id'])->update('company', $data);
            } else {
                $this->db->insert('company', $data);
            }
            redirect('onboarding/step3');
            return;
        }
        $row = $this->db->get('company')->row_array() ?: [];
        $this->data['company'] = $row;
        $this->data['default_name'] = $row['company_name'] ?? $this->shop_name();
        $this->render_template('onboarding/step2', $this->data);
    }

    public function step3()
    {
        if ($this->input->method() === 'post') { $this->done(); return; }
        $this->render_template('onboarding/step3', $this->data);
    }

    public function skip() { $this->done(); }

    public function finish() { $this->done(); }
}
