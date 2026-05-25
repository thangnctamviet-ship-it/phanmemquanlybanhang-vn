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
    $dir = dirname(__DIR__, 2) . '/tenants';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    @file_put_contents($dir . '/mail.log', '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
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

    // Khi cần gửi ổn định hơn trên production, nâng cấp sang PHPMailer SMTP
    // và dùng SMTP_HOST/SMTP_PORT/SMTP_USER/SMTP_PASS trong .env.
    $ok = @mail($to, $encoded_subject, $body_html, implode("\r\n", $headers));
    if (!$ok) {
        mailer_log("FAIL to={$to} subject={$subject}");
    }
    return $ok;
}
