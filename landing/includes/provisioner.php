<?php

require_once __DIR__ . '/db.php';
require_once dirname(__DIR__, 2) . '/tenant-shared/CpanelApi.php';

function provision_log($message) {
    @file_put_contents(dirname(__DIR__, 2) . '/provision.log', '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

function provision_normalize_subdomain($sub) {
    $sub = strtolower(trim((string)$sub));
    $sub = preg_replace('/[^a-z0-9-]/', '-', $sub);
    $sub = trim($sub, '-');
    return $sub;
}

function provision_short_name($base, $max = 23) {
    $base = preg_replace('/[^a-z0-9_]/', '_', strtolower($base));
    if (strlen($base) <= $max) return $base;
    $suffix = '_' . bin2hex(random_bytes(2));
    return substr($base, 0, $max - strlen($suffix)) . $suffix;
}

function provision_split_sql($sql) {
    $sql = preg_replace('/^\s*(CREATE\s+DATABASE|DROP\s+DATABASE|USE)\b.*?;\s*$/mi', '', $sql);
    $parts = preg_split('/;\s*\R/', $sql);
    return array_values(array_filter(array_map('trim', $parts), function ($stmt) {
        return $stmt !== '' && $stmt !== ';';
    }));
}

function provision_rrmdir($dir) {
    if (!$dir || !file_exists($dir)) return;
    if (is_link($dir) || is_file($dir)) {
        @unlink($dir);
        return;
    }
    $real = realpath($dir);
    $root = realpath(dirname(__DIR__, 2) . '/tenants');
    if ($root && $real && strpos($real, $root . DIRECTORY_SEPARATOR) !== 0) {
        throw new RuntimeException('Refusing to delete outside tenants directory: ' . $dir);
    }
    foreach (scandir($dir) ?: [] as $item) {
        if ($item === '.' || $item === '..') continue;
        provision_rrmdir($dir . DIRECTORY_SEPARATOR . $item);
    }
    @rmdir($dir);
}

function provision_symlink($target, $link) {
    if (file_exists($link) || is_link($link)) {
        if (is_dir($link) && !is_link($link)) {
            provision_rrmdir($link);
        } else {
            @unlink($link);
        }
    }
    if (!@symlink($target, $link) && !file_exists($link)) {
        throw new RuntimeException('Cannot create symlink: ' . $link);
    }
}

function provision_update_php_array_value($file, $key, $value) {
    $contents = file_get_contents($file);
    $replacement = "'" . $key . "' => '" . str_replace("'", "\\'", $value) . "'";
    $contents = preg_replace("/'" . preg_quote($key, '/') . "'\s*=>\s*'[^']*'/", $replacement, $contents, 1);
    file_put_contents($file, $contents);
}

function provision_update_config_base_url($file, $url) {
    $contents = file_get_contents($file);
    $contents = preg_replace("/\\\$config\\['base_url'\\]\s*=\s*'[^']*';/", "\$config['base_url'] = '" . str_replace("'", "\\'", $url) . "';", $contents, 1);
    file_put_contents($file, $contents);
}

function provision_tenant_cpanel(array $opts): array {
    $env = env_load();
    foreach (['subdomain', 'shop_name', 'email', 'password'] as $key) {
        if (empty($opts[$key])) {
            throw new InvalidArgumentException('Missing option: ' . $key);
        }
    }

    $sub = provision_normalize_subdomain($opts['subdomain']);
    if ($err = validate_subdomain($sub)) {
        throw new InvalidArgumentException($err);
    }

    $prefix = $env['CPANEL_DB_PREFIX'] ?? (substr($env['CPANEL_USER'] ?? 'iqosvnsh', 0, 8) . '_');
    $shortDb = provision_short_name('tenant_' . $sub);
    $shortUser = provision_short_name('t_' . $sub);
    $dbPass = bin2hex(random_bytes(10));
    $fullDb = $prefix . $shortDb;
    $fullUser = $prefix . $shortUser;
    $root = dirname(__DIR__, 2);
    $dir = $root . '/tenants/' . $sub;
    $created = ['db' => null, 'user' => null, 'sub' => null, 'dir' => null];
    $cpanel = CpanelApi::fromEnv($env);

    try {
        $db = $cpanel->createDatabase($shortDb);
        $fullDb = $db['db_name'];
        $created['db'] = $fullDb;

        $user = $cpanel->createDbUser($shortUser, $dbPass);
        $fullUser = $user['db_user'];
        $created['user'] = $fullUser;

        $cpanel->grantPrivileges($fullUser, $fullDb);

        $host = $env['DB_HOST_TENANT'] ?? 'localhost';
        $pdo = new PDO("mysql:host={$host};dbname={$fullDb};charset=utf8mb4", $fullUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
        ]);

        $schema = file_get_contents($root . '/stock.sql');
        foreach (provision_split_sql($schema) as $stmt) {
            $pdo->exec($stmt);
        }
        $pdo->exec('SET AUTOCOMMIT=1');

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach (['products', 'orders', 'orders_item', 'attributes', 'attribute_value', 'brands', 'categories', 'stores', 'users', 'user_group', 'company'] as $table) {
            $pdo->exec('TRUNCATE TABLE `' . $table . '`');
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

        $hash = password_hash($opts['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (id, username, password, email, firstname, lastname, phone, gender) VALUES (1, 'admin', ?, ?, 'Admin', ?, '', 1)");
        $stmt->execute([$hash, $opts['email'], $sub]);
        $pdo->exec("INSERT INTO user_group (id, user_id, group_id) VALUES (1, 1, 1)");
        $stmt = $pdo->prepare("INSERT INTO company (id, company_name, service_charge_value, vat_charge_value, address, phone, country, message, currency) VALUES (1, ?, '0', '10', '', '', 'Vietnam', '', 'VND')");
        $stmt->execute([$opts['shop_name']]);

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new RuntimeException('Cannot create tenant directory');
        }
        $created['dir'] = $dir;
        foreach (['application/cache', 'application/logs', 'application/config'] as $path) {
            if (!is_dir($dir . '/' . $path) && !mkdir($dir . '/' . $path, 0775, true)) {
                throw new RuntimeException('Cannot create tenant subdirectory: ' . $path);
            }
            @chmod($dir . '/' . $path, 0775);
        }

        foreach (['index.php', 'system', 'assets', 'tenant-shared'] as $item) {
            if (file_exists($root . '/' . $item)) {
                provision_symlink('../../' . $item, $dir . '/' . $item);
            }
        }
        // .htaccess riêng cho tenant (KHÔNG symlink về root - root htaccess rewrite về landing)
        file_put_contents($dir . '/.htaccess',
            "RewriteEngine On\n"
            ."RewriteCond %{HTTPS} !=on\n"
            ."RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]\n\n"
            ."RewriteCond %{REQUEST_FILENAME} !-f\n"
            ."RewriteCond %{REQUEST_FILENAME} !-d\n"
            ."RewriteRule ^(.*)$ index.php/$1 [L,QSA]\n");
        foreach (['controllers', 'models', 'views', 'core', 'helpers', 'hooks', 'libraries', 'third_party', 'language'] as $item) {
            if (file_exists($root . '/application/' . $item)) {
                provision_symlink('../../../application/' . $item, $dir . '/application/' . $item);
            }
        }

        foreach (glob($root . '/application/config/*') ?: [] as $src) {
            if (is_file($src)) {
                copy($src, $dir . '/application/config/' . basename($src));
            }
        }
        provision_update_php_array_value($dir . '/application/config/database.php', 'hostname', $host);
        provision_update_php_array_value($dir . '/application/config/database.php', 'username', $fullUser);
        provision_update_php_array_value($dir . '/application/config/database.php', 'password', $dbPass);
        provision_update_php_array_value($dir . '/application/config/database.php', 'database', $fullDb);
        provision_update_config_base_url($dir . '/application/config/config.php', 'https://' . $sub . '.' . $env['BASE_DOMAIN'] . '/');

        $docroot = ($env['CPANEL_DOCROOT_PREFIX'] ?? ($env['BASE_DOMAIN'] . '/tenants')) . '/' . $sub;
        $cpanel->createSubdomain($sub, $env['BASE_DOMAIN'], $docroot);
        $created['sub'] = $sub . '.' . $env['BASE_DOMAIN'];

        $master = master_pdo();
        $stmt = $master->prepare("UPDATE tenants SET db_name=?, db_user=?, db_pass=?, status='trial' WHERE subdomain=?");
        $stmt->execute([$fullDb, $fullUser, $dbPass, $sub]);
        if ($stmt->rowCount() === 0) {
            $now = date('Y-m-d H:i:s');
            $exp = date('Y-m-d H:i:s', strtotime('+7 days'));
            $stmt = $master->prepare("INSERT INTO tenants (subdomain, shop_name, owner_email, db_name, db_user, db_pass, status, plan, paid_branches, trial_started_at, expires_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$sub, $opts['shop_name'], $opts['email'], $fullDb, $fullUser, $dbPass, 'trial', 'trial', 2, $now, $exp]);
        }

        return [
            'status' => 'ok',
            'db_name' => $fullDb,
            'db_user' => $fullUser,
            'db_pass' => $dbPass,
            'tenant_dir' => $dir,
            'url' => 'https://' . $sub . '.' . $env['BASE_DOMAIN'] . '/',
        ];
    } catch (Exception $e) {
        provision_log('Provision failed for ' . $sub . ': ' . $e->getMessage());
        if ($created['sub']) {
            try { $cpanel->deleteSubdomain($created['sub']); } catch (Exception $ignored) {}
        }
        if ($created['db']) {
            try { $cpanel->deleteDatabase($created['db']); } catch (Exception $ignored) {}
        }
        if ($created['user']) {
            try { $cpanel->deleteDbUser($created['user']); } catch (Exception $ignored) {}
        }
        if ($created['dir']) {
            try { provision_rrmdir($created['dir']); } catch (Exception $ignored) {}
        }
        throw $e;
    }
}
