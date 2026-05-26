<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class License extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Gia hạn / Mua thêm';
    }

    public function index()
    {
        $this->render_template('license/index', $this->data);
    }

    public function buy()
    {
        $plan = $this->input->post('plan');
        $plans = [
            'monthly'    => ['amount' => 120000,  'months' => 1,  'branches' => 0],
            'semiannual' => ['amount' => 600000,  'months' => 6,  'branches' => 0],
            'annual'     => ['amount' => 1100000, 'months' => 12, 'branches' => 0],
        ];

        if ($plan === 'extra_branch') {
            $qty = max(1, min(50, (int) $this->input->post('qty')));
            $dur = (int) $this->input->post('duration');
            if (!in_array($dur, array(1, 6, 12), true)) { $dur = 1; }
            $discount = ($dur === 6) ? 0.17 : ($dur === 12 ? 0.25 : 0);
            $base   = 50000 * $qty * $dur;
            $amount = (int) (round(($base * (1 - $discount)) / 1000) * 1000);
            $info = ['amount' => $amount, 'months' => $dur, 'branches' => $qty];
            $plan_save  = 'extra_branch';
            $ref_suffix = 'extra' . $qty . 'cn-' . $dur . 'th';
        } else {
            if (!isset($plans[$plan])) {
                redirect('license');
            }
            $info = $plans[$plan];
            $plan_save  = $plan;
            $ref_suffix = $plan;
        }

        $sub    = $this->license ? $this->license->getSubdomain() : null;
        $tenant = $this->license ? $this->license->getTenant() : null;
        $env    = TenantLicense::loadEnv();
        $ref    = '';

        if ($tenant) {
            try {
                $pdo = new PDO(
                    "mysql:host={$env['MASTER_DB_HOST']};dbname={$env['MASTER_DB_NAME']};charset=utf8mb4",
                    $env['MASTER_DB_USER'],
                    $env['MASTER_DB_PASS']
                );
                $ref = $sub . ' ' . $ref_suffix;
                $pdo->prepare("INSERT INTO payments (tenant_id, plan, amount, months_added, branches_added, bank_ref, status) VALUES (?,?,?,?,?,?,'pending')")
                    ->execute([
                        $tenant['id'],
                        $plan_save,
                        $info['amount'],
                        $info['months'],
                        $info['branches'],
                        $ref,
                    ]);
            } catch (Exception $e) {
                // bỏ qua lỗi master DB, vẫn cho khách thấy thông tin CK
            }
        }

        $this->data['plan'] = $plan_save;
        $this->data['info'] = $info;
        $this->data['ref']  = $ref ?: ($sub . ' ' . $ref_suffix);
        $this->data['bank'] = [
            'name'        => isset($env['BANK_NAME'])    ? $env['BANK_NAME']    : '',
            'account'     => isset($env['BANK_ACCOUNT']) ? $env['BANK_ACCOUNT'] : '',
            'holder'      => isset($env['BANK_HOLDER'])  ? $env['BANK_HOLDER']  : '',
            'owner_email' => isset($env['OWNER_EMAIL'])  ? $env['OWNER_EMAIL']  : '',
        ];
        $this->render_template('license/payment', $this->data);
    }
}
