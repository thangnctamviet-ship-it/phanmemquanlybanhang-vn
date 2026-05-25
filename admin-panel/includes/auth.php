<?php
session_start();
require_once __DIR__.'/../../landing/includes/db.php';

function require_login() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php'); exit;
    }
}

function admin_login($email, $password) {
    $env = env_load();
    if (strcasecmp($email, $env['OWNER_EMAIL'] ?? '') !== 0) return false;
    $hash = $env['OWNER_PASSWORD_HASH'] ?? '';
    if (!$hash) return false;
    if (!password_verify($password, $hash)) return false;
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_email'] = $email;
    return true;
}

function admin_logout() { session_destroy(); header('Location: login.php'); exit; }
