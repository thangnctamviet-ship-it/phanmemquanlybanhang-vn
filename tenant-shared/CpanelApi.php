<?php

class CpanelApi
{
    private $host;
    private $user;
    private $token;
    private $root;

    public function __construct(string $host, string $user, string $token)
    {
        $host = preg_replace('#^https?://#', '', trim($host));
        $this->host = rtrim($host, '/');
        $this->user = $user;
        $this->token = $token;
        $this->root = dirname(__DIR__);
    }

    public static function fromEnv(array $env): self
    {
        return new self(
            $env['CPANEL_HOST'] ?? '',
            $env['CPANEL_USER'] ?? '',
            $env['CPANEL_TOKEN'] ?? ''
        );
    }

    public function call(string $endpoint, array $params = []): array
    {
        $url = 'https://' . $this->host . '/execute/' . ltrim($endpoint, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        try {
            $json = $this->request($url);
        } catch (Exception $e) {
            $this->logCall($endpoint, $params, 0, [$e->getMessage()]);
            throw $e;
        }
        $status = (int)($json['status'] ?? 0);
        $errors = $this->normalizeErrors($json['errors'] ?? []);
        $this->logCall($endpoint, $params, $status, $errors);

        if ($status !== 1) {
            throw new RuntimeException('cPanel UAPI ' . $endpoint . ' failed: ' . implode('; ', $errors));
        }

        return $json;
    }

    public function callApi2(string $module, string $func, array $params = []): array
    {
        $base = [
            'cpanel_jsonapi_user' => $this->user,
            'cpanel_jsonapi_apiversion' => 2,
            'cpanel_jsonapi_module' => $module,
            'cpanel_jsonapi_func' => $func,
        ];
        $url = 'https://' . $this->host . '/json-api/cpanel?' . http_build_query($base + $params);
        $endpoint = $module . '::' . $func;
        try {
            $json = $this->request($url);
        } catch (Exception $e) {
            $this->logCall($endpoint, $params, 0, [$e->getMessage()]);
            throw $e;
        }

        $result = 0;
        $errors = [];
        $event = $json['cpanelresult']['event'] ?? null;
        if (is_array($event) && (int)($event['result'] ?? 0) === 1) {
            $result = 1;
        }
        foreach (($json['cpanelresult']['data'] ?? []) as $row) {
            if (is_array($row) && isset($row['event']) && (int)($row['event']['result'] ?? 0) === 1) {
                $result = 1;
            }
            if (is_array($row) && !empty($row['reason'])) {
                $errors[] = $row['reason'];
            }
        }
        if (!$errors && !empty($json['cpanelresult']['error'])) {
            $errors[] = $json['cpanelresult']['error'];
        }
        if (!$errors && $result !== 1) {
            $errors[] = 'Unknown API2 error';
        }

        $this->logCall($endpoint, $params, $result, $errors);
        if ($result !== 1) {
            throw new RuntimeException('cPanel API2 ' . $endpoint . ' failed: ' . implode('; ', $errors));
        }

        return $json;
    }

    public function createDatabase(string $shortName): array
    {
        $shortName = $this->fitShortName($shortName);
        $dbName = $this->user . '_' . $shortName;
        $this->call('Mysql/create_database', ['name' => $dbName]);
        return ['db_name' => $dbName];
    }

    public function createDbUser(string $shortName, string $password): array
    {
        $shortName = $this->fitShortName($shortName);
        $dbUser = $this->user . '_' . $shortName;
        $this->call('Mysql/create_user', ['name' => $dbUser, 'password' => $password]);
        return ['db_user' => $dbUser];
    }

    public function grantPrivileges(string $userFull, string $dbFull): bool
    {
        $this->call('Mysql/set_privileges_on_database', [
            'user' => $userFull,
            'database' => $dbFull,
            'privileges' => 'ALL PRIVILEGES',
        ]);
        return true;
    }

    public function createSubdomain(string $subdomain, string $rootDomain, string $dir): bool
    {
        $this->call('SubDomain/addsubdomain', [
            'domain' => $subdomain,
            'rootdomain' => $rootDomain,
            'dir' => $dir,
        ]);
        return true;
    }

    public function deleteSubdomain(string $fullDomain): bool
    {
        $this->callApi2('SubDomain', 'delsubdomain', ['domain' => $fullDomain]);
        return true;
    }

    public function deleteDatabase(string $dbFull): bool
    {
        $this->call('Mysql/delete_database', ['name' => $dbFull]);
        return true;
    }

    public function deleteDbUser(string $userFull): bool
    {
        $this->call('Mysql/delete_user', ['name' => $userFull]);
        return true;
    }

    private function request(string $url): array
    {
        if (!$this->host || !$this->user || !$this->token) {
            throw new RuntimeException('Missing cPanel API credentials');
        }

        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => ['Authorization: cpanel ' . $this->user . ':' . $this->token],
        ];
        // Loopback (127.0.0.1 / localhost) → bỏ verify SSL vì cert của cPanel
        // được phát hành cho hostname public, không khớp IP loopback.
        // Gọi loopback an toàn vì traffic không rời máy.
        if (preg_match('#^(127\.0\.0\.1|localhost|\[::1\])(:\d+)?$#i', $this->host)) {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        curl_setopt_array($ch, $opts);
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            throw new RuntimeException('cPanel curl error: ' . $error);
        }
        if ($code < 200 || $code >= 300) {
            throw new RuntimeException('cPanel HTTP ' . $code . ': ' . substr((string)$body, 0, 500));
        }

        $json = json_decode((string)$body, true);
        if (!is_array($json)) {
            throw new RuntimeException('Invalid cPanel JSON response');
        }
        return $json;
    }

    private function fitShortName(string $shortName): string
    {
        $shortName = preg_replace('/[^a-zA-Z0-9_]/', '_', $shortName);
        $max = 32 - strlen($this->user . '_');
        if (strlen($shortName) > $max) {
            $suffix = '_' . bin2hex(random_bytes(2));
            $shortName = substr($shortName, 0, max(1, $max - strlen($suffix))) . $suffix;
        }
        if (strlen($this->user . '_' . $shortName) > 32) {
            throw new RuntimeException('cPanel database/user name is longer than 32 chars');
        }
        return $shortName;
    }

    private function normalizeErrors($errors): array
    {
        if (!$errors) return [];
        if (is_string($errors)) return [$errors];
        if (is_array($errors)) return array_map('strval', $errors);
        return [strval($errors)];
    }

    private function logCall(string $endpoint, array $params, int $status, array $errors): void
    {
        $safe = $params;
        foreach ($safe as $key => $value) {
            if (stripos((string)$key, 'pass') !== false || stripos((string)$key, 'password') !== false) {
                $safe[$key] = '***';
            }
        }
        $line = json_encode([
            'ts' => date('c'),
            'endpoint' => $endpoint,
            'params' => $safe,
            'status' => $status,
            'errors' => $errors,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @file_put_contents($this->root . '/provision.log', $line . PHP_EOL, FILE_APPEND);
    }
}
