<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Settings — cấu hình tenant: thiết bị (máy in/quét), industry preset, feature flags.
 * Đọc/ghi vào bảng `settings` (key-value).
 */
class Settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Cấu hình';
    }

    private function getAll()
    {
        if (!$this->db->table_exists('settings')) return array();
        $rows = $this->db->query("SELECT `key`,`value` FROM `settings`")->result_array();
        $out = array();
        foreach ($rows as $r) $out[$r['key']] = $r['value'];
        return $out;
    }

    private function set($key, $value)
    {
        $this->db->query("INSERT INTO `settings`(`key`,`value`) VALUES(?,?)
                          ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)", array($key, (string)$value));
    }

    public function index() { redirect('settings/devices', 'refresh'); }

    /** Cấu hình máy in / máy quét + Industry preset + feature flags */
    public function devices()
    {
        $this->data['s'] = $this->getAll();
        $this->data['page_title'] = 'Cấu hình thiết bị &amp; hệ thống';
        $this->render_template('settings/devices', $this->data);
    }

    public function save()
    {
        if (!$this->db->table_exists('settings')) {
            $this->session->set_flashdata('errors', 'Cần chạy migration 002');
            redirect('settings/devices'); return;
        }
        $keys = array(
            'industry_preset', 'low_stock_threshold', 'loyalty_points_per_1000',
            'enable_batches', 'enable_combos', 'enable_variants', 'enable_wholesale',
            'enable_returns', 'enable_loyalty', 'enable_multi_unit', 'enable_promotions',
            'enable_employee_shift',
            'print_bill_width', 'print_bill_open_method', 'print_auto',
            'barcode_prefix', 'barcode_check_digit',
            'pos_bank_name', 'pos_bank_account', 'pos_bank_holder',
        );
        foreach ($keys as $k) {
            $v = $this->input->post($k);
            // Checkbox không gửi nếu unchecked → set 0
            if (in_array($k, ['enable_batches','enable_combos','enable_variants','enable_wholesale','enable_returns','enable_loyalty','enable_multi_unit','enable_promotions','enable_employee_shift','print_auto','barcode_check_digit'])) {
                $v = $v ? 1 : 0;
            }
            if ($v !== null) $this->set($k, $v);
        }
        // Audit log: settings updated
        $this->audit->log('update', 'settings', 0, null, $this->input->post());
        $this->session->set_flashdata('success', 'Đã lưu cấu hình.');
        redirect('settings/devices');
    }
}
