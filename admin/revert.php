<?php
// /admin/revert.php — 記事巻き戻し（元ファイルに上書き）

session_start();
require_once __DIR__ . '/../lib/auth.php';
check_auth();

$SITE_TITLE = '(管理画面)記事巻き戻し';
$PAGE_TITLE = '記事巻き戻し';
$PAGE_ID = 'admin-revert';

$articleDir = __DIR__ . '/../data/articles';
$message = '';

// バックアップ一覧を生成
$backups = [];
foreach (scandir($articleDir) as $file) {
    if (preg_match('/^(\d+)-(\d{8}-\d{6})\.md\.bak$/', $file, $m)) {
        $id = $m[1];
        $backups[$id][] = $file;
    }
}

// POST で復元
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['backup_file'] ?? '';
    if (file_exists("$articleDir/$file")) {
        // 元の ID を抽出して上書き先パスを決定
        preg_match('/^(\d+)-/', $file, $m);
        $id = $m[1];
        $mdPath = "$articleDir/$id.md";

        copy("$articleDir/$file", $mdPath);
        $message = "バックアップ {$file} から記事 {$id}.md に復元しました。";
    } else {
        $message = "選択されたバックアップファイルが存在しません。";
    }
}

ob_start();
?>
<h2 class="wiki-section-title">記事巻き戻し</h2>
<?php if($message): ?>
<p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post" class="wiki-edit-form">
    <label>復元するバックアップを選択:</label>
    <select name="backup_file" class="wiki-edit-textarea" style="height:auto;">
        <?php foreach($backups as $id => $files): ?>
            <?php foreach($files as $f): ?>
                <option value="<?php echo $f; ?>"><?php echo $f; ?></option>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="wiki-button wiki-button-primary">復元</button>
</form>
<a href="index.php" class="wiki-button wiki-button-secondary">管理トップに戻る</a>
<?php
$PAGE_CONTENT = ob_get_clean();
$template = file_get_contents(__DIR__ . '/../template/layout.html');
$html = str_replace(['{{PAGE_TITLE}}','{{SITE_TITLE}}','{{PAGE_ID}}','{{PAGE_CONTENT}}'], [$PAGE_TITLE,$SITE_TITLE,$PAGE_ID,$PAGE_CONTENT], $template);

echo $html;
