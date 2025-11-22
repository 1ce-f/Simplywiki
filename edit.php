<?php
// edit.php — 記事編集画面

require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/parsedown.php';

$SITE_TITLE = 'SimplyWiki';
$PAGE_TITLE = '記事編集';

// -----------------------------
// 記事ID取得
// -----------------------------
$id = isset($_GET['id']) ? preg_replace('/[^0-9]/', '', $_GET['id']) : '';
if ($id === '') {
    http_response_code(400);
    echo "記事IDが指定されていません";
    exit;
}

$PAGE_ID = $id;
$md_path = __DIR__ . '/data/articles/' . $id . '.md';

// 新規作成
if (!file_exists($md_path)) {
    $title = "新規記事 ($id)";
    $content = "";
} else {
    $raw = file_get_contents($md_path);
    $content = $raw;
    $lines = explode("\n", $raw);
    $title = trim($lines[0]) ?: "無題の記事";
}

// -----------------------------
// ページ内容HTML生成
// -----------------------------
ob_start();
?>
<h2 class="wiki-edit-title">記事編集：<?php echo htmlspecialchars($id); ?></h2>

<form action="save.php" method="post" class="wiki-edit-form">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

    <textarea name="content" class="wiki-edit-textarea" rows="25"><?php echo htmlspecialchars($content); ?></textarea>

    <div class="wiki-edit-buttons">
        <button type="submit" class="wiki-button wiki-button-primary">保存</button>
        <a href="/articles/<?php echo htmlspecialchars($id); ?>" class="wiki-button wiki-button-secondary">キャンセル</a>
    </div>
</form>
<?php
$PAGE_CONTENT = ob_get_clean();

// -----------------------------
// テンプレート適用
// -----------------------------
$template = file_get_contents(__DIR__ . '/template/layout.html');
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
