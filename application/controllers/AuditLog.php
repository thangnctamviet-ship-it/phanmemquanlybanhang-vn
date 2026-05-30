<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuditLog extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Chỉ admin được xem audit log
        if (!$this->ion_auth->is_admin()) {
            show_error('Bạn không có quyền xem nhật ký hệ thống', 403);
        }
    }

    public function index() {
        if (!$this->db->table_exists('audit_log')) {
            show_error('Bảng audit_log chưa tồn tại. Hãy chạy migration 002.', 500);
        }

        $action = $this->input->get('action');
        $entity = $this->input->get('entity');
        $user_id = (int)$this->input->get('user_id');
        $date_from = $this->input->get('from');
        $date_to   = $this->input->get('to');

        $this->db->select('al.*, u.username, u.firstname, u.lastname')
                 ->from('audit_log al')
                 ->join('users u', 'u.id = al.user_id', 'left')
                 ->order_by('al.id', 'DESC')
                 ->limit(200);
        if ($action) $this->db->where('al.action', $action);
        if ($entity) $this->db->where('al.entity_type', $entity);
        if ($user_id > 0) $this->db->where('al.user_id', $user_id);
        if ($date_from) $this->db->where('al.created_at >=', $date_from.' 00:00:00');
        if ($date_to)   $this->db->where('al.created_at <=', $date_to.' 23:59:59');

        $this->data['rows'] = $this->db->get()->result_array();
        $this->data['filters'] = compact('action','entity','user_id','date_from','date_to');

        // Distinct values cho filter dropdown
        $this->data['actions']  = $this->db->distinct()->select('action')->order_by('action')->get('audit_log')->result_array();
        $this->data['entities'] = $this->db->distinct()->select('entity_type')->order_by('entity_type')->get('audit_log')->result_array();

        $this->load->view('audit_log/index', $this->data);
    }
}
