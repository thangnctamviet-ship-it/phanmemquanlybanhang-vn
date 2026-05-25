<?php
/**
 * TenantLicense - kiểm tra license tenant từ Master DB
 * Sử dụng: $lic = new TenantLicense($subdomain);
 */
class TenantLicense {
    private $tenant = null;
    private $subdomain;

    public function __construct($subdomain) {
        $this->subdomain = $subdomain;
        $env = self::loadEnv();
        if (!$subdomain) return;
        try {
            $dsn = "mysql:host={$env['MASTER_DB_HOST']};dbname={$env['MASTER_DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $env['MASTER_DB_USER'], $env['MASTER_DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
                PDO::ATTR_TIMEOUT => 2,
            ]);
            $stmt = $pdo->prepare("SELECT * FROM tenants WHERE subdomain = ? LIMIT 1");
            $stmt->execute([$subdomain]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) $this->tenant = $row;
        } catch (Exception $e) {
            // Master DB không sẵn sàng → coi như không có license (cho phép chạy bình thường để dev)
        }
    }

    public function hasTenant()  { return $this->tenant !== null; }
    public function isExpired()  { return $this->tenant && strtotime($this->tenant['expires_at']) < time(); }
    public function daysLeft()   { return $this->tenant ? max(0, (int)ceil((strtotime($this->tenant['expires_at']) - time())/86400)) : 0; }
    public function isTrial()    { return $this->tenant && $this->tenant['status'] === 'trial'; }
    public function isSuspended(){ return $this->tenant && $this->tenant['status'] === 'suspended'; }
    public function maxBranches(){ return $this->tenant ? (int)$this->tenant['paid_branches'] : 999; }
    public function canCreateOrder() { return !$this->tenant || (!$this->isExpired() && !$this->isSuspended()); }
    public function canCreateBranch($current_count) { return $current_count < $this->maxBranches(); }
    public function getTenant()  { return $this->tenant; }
    public function getPlan()    { return $this->tenant ? $this->tenant['plan'] : null; }
    public function getExpires() { return $this->tenant ? $this->tenant['expires_at'] : null; }
    public function getSubdomain(){ return $this->subdomain; }

    /** Đọc .env (đơn giản, dùng parse_ini_file) */
    public static function loadEnv() {
        static $env = null;
        if ($env !== null) return $env;
        $paths = [
            __DIR__ . '/../.env',
            __DIR__ . '/../../.env',
            '/var/www/html/.env',
        ];
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $env = @parse_ini_file($p) ?: [];
                break;
            }
        }
        if ($env === null) $env = [];
        // defaults
        $env += [
            'MASTER_DB_HOST' => 'db',
            'MASTER_DB_NAME' => 'master_quanlybanhang',
            'MASTER_DB_USER' => 'root',
            'MASTER_DB_PASS' => 'root',
            'MODE' => 'local',
            'BASE_DOMAIN' => 'quanlybanhang.shop',
            'LOCAL_BASE_DOMAIN' => 'localhost:8080',
            'BANK_NAME' => '', 'BANK_ACCOUNT' => '', 'BANK_HOLDER' => '',
        ];
        return $env;
    }

    /** Trích subdomain từ HTTP_HOST */
    public static function parseSubdomain($host) {
        if (!$host) return null;
        $host = preg_replace('/:\d+$/', '', $host);
        $parts = explode('.', $host);
        // Bỏ qua các host không phải tenant
        $reserved = ['www','admin','localhost','quanlybanhang','127','0'];
        if (count($parts) < 2) return null;
        $sub = $parts[0];
        if (in_array($sub, $reserved)) return null;
        if (!preg_match('/^[a-z0-9][a-z0-9-]{1,61}[a-z0-9]$/', $sub)) return null;
        return $sub;
    }
}
