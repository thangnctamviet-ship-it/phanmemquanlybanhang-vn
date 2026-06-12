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

    /* ============ NHẬP HÀNG LOẠT TỪ EXCEL ============ */

    public function import()
    {
        $this->render_template('customers/import', $this->data);
    }

    public function importTemplate()
    {
        $cols = array(
            array('Tên khách hàng *', true), array('Số điện thoại', false),
            array('Email', false), array('Địa chỉ', false),
            array('Ngày sinh (YYYY-MM-DD)', false), array('Ghi chú', false)
        );
        $samples = array(
            array('Nguyễn Văn A','0901234567','a@gmail.com','12 Lê Lợi, Q1','1990-05-20','Khách VIP'),
            array('Trần Thị B','0987654321','','45 Trần Hưng Đạo','',''),
        );
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="mau-nhap-khach-hang.xls"');
        echo "\xEF\xBB\xBF";
        $h = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body>';
        echo '<table border="1" cellspacing="0" cellpadding="4"><tr style="font-weight:bold;">';
        foreach ($cols as $c) {
            $req=!empty($c[1]); $bg=$req?'#ffe4e6':'#dbeafe'; $fg=$req?'#dd0000':'#1e293b';
            echo '<td style="background:'.$bg.';color:'.$fg.';">'.$h($c[0]).'</td>';
        }
        echo '</tr>';
        foreach ($samples as $row){ echo '<tr style="color:#94a3b8;">'; foreach ($row as $cell) echo '<td>'.$h($cell).'</td>'; echo '</tr>'; }
        $ncol=count($cols);
        for($i=0;$i<20;$i++){ echo '<tr>'; for($j=0;$j<$ncol;$j++) echo '<td></td>'; echo '</tr>'; }
        echo '</table>';
        echo '<p style="color:#dd0000;font-size:13px;font-weight:bold;">Cột chữ ĐỎ = bắt buộc phải điền.</p>';
        echo '<p style="color:#64748b;font-size:12px;">Xóa 2 dòng ví dụ (chữ xám) trước khi nhập thật. Đã có sẵn 20 dòng trống — cần thêm thì bôi đen dòng trống rồi kéo xuống.</p></body></html>';
    }

    public function importBulk()
    {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload) || empty($payload['rows'])) { echo json_encode(array('ok'=>false,'error'=>'Không có dữ liệu')); return; }
        $rows = $payload['rows'];
        if (count($rows) > 5000) { echo json_encode(array('ok'=>false,'error'=>'Tối đa 5000 dòng mỗi lần.')); return; }
        $added=0; $skipped=0; $errors=array(); $seen=array();
        foreach ($rows as $i=>$r) {
            $line=$i+2;
            $name=trim((string)($r['name']??''));
            $phone=trim((string)($r['phone']??''));
            if ($name==='') { $errors[]="Dòng $line: thiếu Tên"; continue; }
            if ($phone!=='') {
                if (isset($seen[$phone])) { $skipped++; continue; }
                $seen[$phone]=true;
                $dup=$this->db->get_where('customers', array('phone'=>$phone,'deleted_at'=>null))->row_array();
                if ($dup) { $skipped++; continue; }
            }
            $data=array(
                'name'=>$name,'phone'=>($phone?:null),
                'email'=>trim((string)($r['email']??''))?:null,
                'address'=>trim((string)($r['address']??''))?:null,
                'birthday'=>trim((string)($r['birthday']??''))?:null,
                'note'=>trim((string)($r['note']??''))?:null,
            );
            if ($this->model_customers->create($data)) $added++;
            else $errors[]="Dòng $line: lỗi khi lưu";
        }
        echo json_encode(array('ok'=>true,'added'=>$added,'skipped'=>$skipped,'errors'=>$errors));
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
            // Pre-check duplicate phone (UNIQUE constraint trong DB sẽ chặn lần 2 — nhưng check trước để báo lỗi đẹp)
            if (!empty($data['phone'])) {
                $dup = $this->db->get_where('customers', array('phone'=>$data['phone'], 'deleted_at'=>null))->row_array();
                if ($dup) {
                    $resp['messages'] = 'Số điện thoại này đã tồn tại (KH: '.htmlspecialchars($dup['name']).')';
                    echo json_encode($resp); return;
                }
            }
            $id = $this->model_customers->create($data);
            if ($id) $this->audit->log('create', 'customers', (int)$id, null, $data);
            $resp['success'] = (bool)$id;
            $resp['messages'] = $id ? 'Đã thêm khách hàng.' : 'Lỗi khi tạo. Có thể trùng dữ liệu hoặc tham chiếu sai.';
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
