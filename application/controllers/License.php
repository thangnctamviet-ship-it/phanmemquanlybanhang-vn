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
            'monthly'    => ['amount'=>120000, 'months'=>1,  'branches'=>0],
            'semiannual' => ['amount'=>600000, 'months'=>6,  'branches'=>0],
            'annual'     => ['amount'=>1100000,'months'=>12, 'branches'=>0],
            'extra_branch'=>['amount'=>50000,  'months'=>0,  'branches'=>1],
        ];
        if (!isset($plans[$plan])) { redirect('license'); }
        $info = $plans[$plan];

        // Lưu pending payment vào master DB
        $sub = $this->license ? $this->license->getSubdomain() : null;
        $tenant = $this->license ? $this->license->getTenant() : null;
        $env = TenantLicense::loadEnv();
        $ref = '';
        if ($tenant) {
            try {
                $pdo = new PDO("mysql:host={$env['MASTER_DB_HOST']};dbname={$env['MASTER_DB_NAME']};charset=utf8mb4",
                    $env['MASTER_DB_USER'], $env['MASTER_DB_PASS']);
                $ref = $sub.' '.$ref_suffix;
                $pdo->prepare("INSERT INTO payments (tenant_id,plan,amount,months_added,branches_added,bank_ref,status) VALUES (?,?,?,?,?,?,'pending')")
                    ->execute([$tenant['id'],$plan_save,$info['amount'],$info['months'],$info['branches'],$ref]);
            } catch (Exception $e) {}
        }

        $this->data['plan'] = $plan;
        $this->data['info'] = $info;
        $this->data['ref']  = $ref ?: ($sub.' '.$plan);
        $this->data['bank'] = [
            'name' => $env['BANK_NAME'] ?? '',
            'account' => $env['BANK_ACCOUNT'] ?? '',
            'holder' => $env['BANK_HOLDER'] ?? '',
            'owner_email' => $env['OWNER_EMAIL'] ?? '',
        ];
        $this->render_template('license/payment', $this->data);
    }
}
lan,amount,months_added,branches_added,bank_ref,status) VALUES (?,?,?,?,?,?,'pending')")
                    ->execute([$tenant['id'],$plan,$info['amount'],$info['months'],$info['branches'],$ref]);
            } catch (Exception $e) {}
        }

        $this->data['plan'] = $plan;
        $this->data['info'] = $info;
        $this->data['ref']  = $ref ?: ($sub.' '.$plan);
        $this->data['bank'] = [
            'name' => $env['BANK_NAME'] ?? '',
            'account' => $env['BANK_ACCOUNT'] ?? '',
            'holder' => $env['BANK_HOLDER'] ?? '',
            'owner_email' => $env['OWNER_EMAIL'] ?? '',
        ];
        $this->render_template('license/payment', $this->data);
    }
}
