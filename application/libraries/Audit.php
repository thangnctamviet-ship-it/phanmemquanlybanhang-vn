<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Audit log helper — ghi mọi hành động CRUD quan trọng vào bảng `audit_log`.
 * Idempotent: nếu bảng chưa tồn tại (tenant chưa migrate 002) thì silent skip.
 *
 * Cách dùng:
 *   $this->audit->log('create', 'products', $product_id, null, $data);
 *   $this->audit->log('update', 'products', $product_id, $old_data, $new_data);
 *   $this->audit->log('delete', 'orders', $order_id, $old_data, null);
 *   $this->audit->log('void',   'orders', $order_id);
 */
class Audit {
    private $CI;
    private $table_checked = false;
    private $table_exists  = false;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function log($action, $entity_type, $entity_id = 0, $old = null, $new = null) {
        if (!$this->_ensureTable()) return false;

        $user_id = 0;
        if ($this->CI->session && $this->CI->session->userdata('user_id')) {
            $user_id = (int)$this->CI->session->userdata('user_id');
        }

        $data = array(
            'user_id'     => $user_id,
            'action'      => substr((string)$action, 0, 32),
            'entity_type' => substr((string)$entity_type, 0, 32),
            'entity_id'   => (int)$entity_id,
            'old_data'    => $old === null ? null : (is_string($old) ? $old : json_encode($old, JSON_UNESCAPED_UNICODE)),
            'new_data'    => $new === null ? null : (is_string($new) ? $new : json_encode($new, JSON_UNESCAPED_UNICODE)),
            'ip'          => $this->CI->input->ip_address(),
            'user_agent'  => substr((string)$this->CI->input->user_agent(), 0, 255),
        );

        try {
            return $this->CI->db->insert('audit_log', $data);
        } catch (Exception $e) {
            // Audit không bao giờ được làm gãy main flow
            log_message('error', 'Audit log fail: '.$e->getMessage());
            return false;
        }
    }

    private function _ensureTable() {
        if ($this->table_checked) return $this->table_exists;
        $this->table_checked = true;
        try { $this->table_exists = $this->CI->db->table_exists('audit_log'); }
        catch (Exception $e) { $this->table_exists = false; }
        return $this->table_exists;
    }
}
