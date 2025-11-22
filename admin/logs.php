<?php
// /admin/logs.php — 編集ログ一覧
session_start();
require_once __DIR__ . '/../lib/auth.php';
check_auth();

$SITE_TITLE = 'Edit-Logs';
$PAGE_TITLE = '(管理画面)編集ログ一覧';
$PAGE_ID = 'admin-logs';

$logFile = __DIR__ . '/../data/logs/edit.log';
$logs = [];
if (file_exists($logFile)) {
    $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logs = array_reverse($logs);
}

ob_start();
?>
<h2 class="wiki-section-title">編集ログ一覧</h2>
<div class="wiki-admin-section">
    <ul>
    <?php foreach($logs as $l): ?>
        <li><?php echo htmlspecialchars($l); ?></li>
    <?php endforeach; ?>
    </ul>
    <a href="index.php" class="wiki-button wiki-button-secondary">管理トップに戻る</a>
</div>
<?php
$PAGE_CONTENT = ob_get_clean();

$template = file_get_contents(__DIR__ . '/../template/layout.html');
$html = str_replace([
    '{{PAGE_TITLE}}',
    '{{SITE_TITLE}}',
    '{{PAGE_ID}}',
    '{{PAGE_CONTENT}}'
], [
    $PAGE_TITLE,
    $SITE_TITLE,
    $PAGE_ID,
    $PAGE_CONTENT
], $template);

echo $html;
