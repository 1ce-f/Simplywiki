<?php
// /admin/index.php — 管理者トップ
session_start();
require_once __DIR__ . '/../lib/auth.php';
check_auth(); // ログインチェック

$SITE_TITLE = 'Admin-Top';
$PAGE_TITLE = '(管理画面)トップ';
$PAGE_ID = 'admin-index';

// 編集ログの最新5件表示
$logFile = __DIR__ . '/../data/logs/edit.log';
$logs = [];
if (file_exists($logFile)) {
    $allLogs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logs = array_slice(array_reverse($allLogs), 0, 5);
}

ob_start();
?>
<h2 class="wiki-section-title">管理者トップ</h2>
<div class="wiki-admin-section">
    <h3>編集ログ（最新5件）</h3>
    <ul>
    <?php foreach($logs as $l): ?>
        <li><?php echo htmlspecialchars($l); ?></li>
    <?php endforeach; ?>
    </ul>

    <h3>管理メニュー</h3>
    <ul>
        <li><a href="logs.php">編集ログ一覧</a></li>
        <li><a href="revert.php">記事巻き戻し</a></li>
        <li><a href="backup.php">手動バックアップ作成</a></li>
        <li><a href="login.php?logout=1">ログアウト</a></li>
    </ul>
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
