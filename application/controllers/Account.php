<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Tài khoản';
        $this->load->model('model_stores');
    }

    public function index()
    {
        $tenant = $this->license ? $this->license->getTenant() : null;
        $payments = [];
        if ($tenant) {
            $env = TenantLicense::loadEnv();
            try {
                $pdo = new PDO(
                    "mysql:host={$env['MASTER_DB_HOST']};dbname={$env['MASTER_DB_NAME']};charset=utf8mb4",
                    $env['MASTER_DB_USER'],
                    $env['MASTER_DB_PASS'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $stmt = $pdo->prepare("SELECT * FROM payments WHERE tenant_id=? ORDER BY id DESC LIMIT 30");
                $stmt->execute([$tenant['id']]);
                $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $payments = [];
            }
        }

        $this->data['tenant'] = $tenant;
        $this->data['payments'] = $payments;
        $this->data['used_branches'] = $this->model_stores->countTotalStores();
        $this->data['max_branches'] = $this->license ? $this->license->maxBranches() : 999;
        $this->render_template('account/index', $this->data);
    }
}
