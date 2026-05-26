<?php
function mailer_env_load() {
    static $env = null;
    if ($env !== null) return $env;
    $path = dirname(__DIR__, 2) . '/.env';
    $env = file_exists($path) ? (parse_ini_file($path) ?: []) : [];
    $env += [
        'OWNER_EMAIL' => '',
        'SMTP_FROM_NAME' => 'Quản lý bán hàng',
    ];
    return $env;
}

function mailer_log($message) {
    @file_put_contents(dirname(__DIR__, 2) . '/provision.log', '[' . date('Y-m-d H:i:s') . '] MAIL ' . $message . PHP_EOL, FILE_APPEND);
}

function smtp_read($fp) {
    $data = '';
    while (($line = fgets($fp, 515)) !== false) {
        $data .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') break;
    }
    return $data;
}

function smtp_cmd($fp, $cmd, array $expect) {
    if ($cmd !== null) {
        fwrite($fp, $cmd . "\r\n");
    }
    $response = smtp_read($fp);
    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $expect, true)) {
        throw new RuntimeException(trim($response));
    }
    return $response;
}

function smtp_send_mail($to, $subject, $body_html, array $headers, array $env, $from_email) {
    $host = $env['SMTP_HOST'] ?? '';
    $port = (int)($env['SMTP_PORT'] ?? 587);
    $user = $env['SMTP_USER'] ?? '';
    $pass = $env['SMTP_PASS'] ?? '';
    if (!$host || !$user || !$pass) return false;

    $fp = @fsockopen($host, $port, $errno, $errstr, 20);
    if (!$fp) {
        throw new RuntimeException($errstr ?: ('SMTP connect failed #' . $errno));
    }
    stream_set_timeout($fp, 30);

    smtp_cmd($fp, null, [220]);
    smtp_cmd($fp, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), [250]);
    smtp_cmd($fp, 'STARTTLS', [220]);
    if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        throw new RuntimeException('STARTTLS failed');
    }
    smtp_cmd($fp, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), [250]);
    smtp_cmd($fp, 'AUTH LOGIN', [334]);
    smtp_cmd($fp, base64_encode($user), [334]);
    smtp_cmd($fp, base64_encode($pass), [235]);
    smtp_cmd($fp, 'MAIL FROM:<' . $from_email . '>', [250]);
    smtp_cmd($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
    smtp_cmd($fp, 'DATA', [354]);

    $message = implode("\r\n", $headers)
        . "\r\nSubject: " . $subject
        . "\r\nTo: <" . $to . ">"
        . "\r\n\r\n" . str_replace("\n.", "\n..", $body_html)
        . "\r\n.";
    smtp_cmd($fp, $message, [250]);
    smtp_cmd($fp, 'QUIT', [221]);
    fclose($fp);
    return true;
}

function send_mail($to, $subject, $body_html, $from_name = null) {
    $env = mailer_env_load();
    $from_email = $env['OWNER_EMAIL'] ?: ('no-reply@' . ($env['BASE_DOMAIN'] ?? 'quanlybanhang.shop'));
    $name = $from_name ?: ($env['SMTP_FROM_NAME'] ?? 'Quản lý bán hàng');
    $encoded_name = '=?UTF-8?B?' . base64_encode($name) . '?=';
    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [
        'From: ' . $encoded_name . ' <' . $from_email . '>',
        'Reply-To: ' . $from_email,
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=utf-8',
    ];

    // TODO: switch to PHPMailer when composer available.
    if (!empty($env['SMTP_HOST'])) {
        try {
            if (smtp_send_mail($to, $encoded_subject, $body_html, $headers, $env, $from_email)) {
                return true;
            }
        } catch (Exception $e) {
            mailer_log("SMTP FAIL to={$to} subject={$subject} error=" . $e->getMessage());
        }
    }

    $ok = @mail($to, $encoded_subject, $body_html, implode("\r\n", $headers));
    if (!$ok) {
        mailer_log("FAIL to={$to} subject={$subject}");
    }
    return $ok;
}
