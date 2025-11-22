<?php
// save.php — 記事保存処理（バックアップ・ログ対応）

require_once __DIR__ . '/lib/auth.php';

$id = isset($_POST['id']) ? preg_replace('/[^0-9]/', '', $_POST['id']) : '';
$content = isset($_POST['content']) ? $_POST['content'] : '';

if ($id === '') {
    http_response_code(400);
    header("Location: /articles/index.php?id=$id");
    echo "記事IDが指定されていません";
    exit;
}

$articleDir = __DIR__ . '/data/articles';
$logDir = __DIR__ . '/data/logs';
$backupDir = __DIR__ . '/admin-backup';

if (!is_dir($articleDir)) mkdir($articleDir, 0777, true);
if (!is_dir($logDir)) mkdir($logDir, 0777, true);
if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

$mdPath = "$articleDir/$id.md";
$bakPath = "$articleDir/$id.md.bak";

// -----------------------------
// バックアップ: その日の最初の編集時
// -----------------------------
$today = date('Ymd');
$fullBackupPath = "$backupDir/full-backup-$today.tar.gz";
if (!file_exists($fullBackupPath) && file_exists($mdPath)) {
    // 既存記事を tar.gz にまとめる
    $tar = new PharData(str_replace('.gz','',$fullBackupPath));
    $tar->buildFromDirectory($articleDir);
    $tar->compress(Phar::GZ);
}

// -----------------------------
// 記事個別バックアップ
// -----------------------------
if (file_exists($mdPath)) {
    $timestamp = date('Ymd-His');
    $bakFile = $articleDir . "/{$id}-{$timestamp}.md.bak";
    copy($mdPath, $bakFile);
}

// -----------------------------
// 記事保存
// -----------------------------
file_put_contents($mdPath, $content);

// -----------------------------
// 編集ログ追記
// -----------------------------
$logLine = sprintf(
    "[%s] ID=%s IP=%s SIZE=%d\n",
    date('Y-m-d H:i:s'),
    $id,
    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    strlen($content)
);
file_put_contents("$logDir/edit.log", $logLine, FILE_APPEND);

// 保存完了後、記事ページへリダイレクト
header("Location: /articles/index.php?id=$id");
exit;
