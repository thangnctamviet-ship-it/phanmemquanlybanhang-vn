<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Đổi mật khẩu';
    }

    public function index()
    {
        $this->change_password();
    }

    public function change_password()
    {
        if ($this->input->method() === 'post') {
            $old = (string)$this->input->post('old_password');
            $new = (string)$this->input->post('new_password');
            $confirm = (string)$this->input->post('confirm_password');

            if (strlen($new) < 6) {
                $this->session->set_flashdata('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
            } elseif ($new !== $confirm) {
                $this->session->set_flashdata('error', 'Xác nhận mật khẩu không khớp.');
            } else {
                $uid = (int)$this->session->userdata('id');
                $row = $this->db->select('id,password')->from('users')->where('id', $uid)->get()->row_array();
                if (!$row || !password_verify($old, $row['password'])) {
                    $this->session->set_flashdata('error', 'Mật khẩu cũ không đúng.');
                } else {
                    $hash = password_hash($new, PASSWORD_BCRYPT);
                    $this->db->where('id', $uid)->update('users', ['password' => $hash]);
                    $this->session->set_flashdata('success', 'Đổi mật khẩu thành công.');
                    redirect('profile');
                    return;
                }
            }
        }
        $this->render_template('profile/change_password', $this->data);
    }
}
