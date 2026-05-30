<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Soft delete + audit helper
 *
 *   $this->soft_delete->trash('products', $id);
 *   $this->soft_delete->restore('products', $id);
 *   $this->soft_delete->forceDelete('products', $id);
 *   $rows = $this->soft_delete->trashed('products');
 *
 * Idempotent với bảng chưa có `deleted_at` (silent fallback hard delete).
 */
class Soft_delete {
    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('audit');
    }

    /** Soft delete: SET deleted_at = NOW(). Fallback hard delete nếu cột không tồn tại. */
    public function trash($table, $id) {
        $id = (int)$id;
        if ($id <= 0) return false;
        $old = $this->CI->db->get_where($table, array('id'=>$id))->row_array();
        if (!$old) return false;

        $ok = false;
        if ($this->CI->db->field_exists('deleted_at', $table)) {
            $ok = $this->CI->db->where('id', $id)
                                ->update($table, array('deleted_at' => date('Y-m-d H:i:s')));
        } else {
            $ok = $this->CI->db->where('id', $id)->delete($table);
        }

        if ($ok) {
            $this->CI->audit->log('trash', $table, $id, $old, null);
        }
        return $ok;
    }

    /** Khôi phục từ thùng rác */
    public function restore($table, $id) {
        $id = (int)$id;
        if (!$this->CI->db->field_exists('deleted_at', $table)) return false;
        $old = $this->CI->db->get_where($table, array('id'=>$id))->row_array();
        if (!$old || !$old['deleted_at']) return false;

        $ok = $this->CI->db->where('id', $id)
                            ->update($table, array('deleted_at' => null));
        if ($ok) {
            $this->CI->audit->log('restore', $table, $id, null, $old);
        }
        return $ok;
    }

    /** Xoá vĩnh viễn (kể cả đã trong thùng rác) */
    public function forceDelete($table, $id) {
        $id = (int)$id;
        $old = $this->CI->db->get_where($table, array('id'=>$id))->row_array();
        if (!$old) return false;

        $ok = $this->CI->db->where('id', $id)->delete($table);
        if ($ok) {
            $this->CI->audit->log('force_delete', $table, $id, $old, null);
        }
        return $ok;
    }

    /** Liệt kê các record đã trash */
    public function trashed($table, $limit = 200) {
        if (!$this->CI->db->field_exists('deleted_at', $table)) return array();
        return $this->CI->db->where('deleted_at IS NOT NULL', null, false)
                            ->order_by('deleted_at', 'DESC')
                            ->limit($limit)
                            ->get($table)
                            ->result_array();
    }
}
