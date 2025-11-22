<?php
// /admin/backup.php — 手動バックアップ作成
session_start();
require_once __DIR__ . '/../lib/auth.php';
check_auth();

$SITE_TITLE = 'Admin-Backup';
$PAGE_TITLE = '(管理画面)バックアップ作成';
$PAGE_ID = 'admin-backup';

$articleDir = __DIR__ . '/../data/articles';
$backupDir = __DIR__ . '/../admin-backup';
$message = '';

if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $timestamp = date('Ymd-His');
    $backupPath = "$backupDir/full-backup-$timestamp.tar.gz";
    try {
        $tar = new PharData(str_replace('.gz','',$backupPath));
        $tar->buildFromDirectory($articleDir);
        $tar->compress(Phar::GZ);
        $message = "バックアップを作成しました: full-backup-$timestamp.tar.gz";
    } catch (Exception $e) {
        $message = "バックアップ作成に失敗しました: " . $e->getMessage();
    }
}

ob_start();
?>
<h2 class="wiki-section-title">手動バックアップ作成</h2>
<?php if($message): ?>
<p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<form method="post">
    <button type="submit" class="wiki-button wiki-button-primary">バックアップ作成</button>
</form>
<a href="index.php" class="wiki-button wiki-button-secondary">管理トップに戻る</a>
<?php
$PAGE_CONTENT = ob_get_clean();

$template = file_get_contents(__DIR__ . '/../template/layout.html');
$html = str_replace([
    '{{PAGE_TITLE}}','{{SITE_TITLE}}','{{PAGE_ID}}','{{PAGE_CONTENT}}'
], [$PAGE_TITLE, $SITE_TITLE, $PAGE_ID, $PAGE_CONTENT], $template);

echo $html;
