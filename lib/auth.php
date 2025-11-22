<?php
// lib/auth.php — 管理者認証処理
session_start();

// 管理者パスワードハッシュ（ここを変更して独自パスワード設定可能）
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_DEFAULT));

function auth_check_password($password) {
    return password_verify($password, ADMIN_PASSWORD_HASH);
}

function check_auth() {
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    if (empty($_SESSION['admin'])) {
        header('Location: login.php');
        exit;
    }
}
