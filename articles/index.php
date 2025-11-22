<?php
// /articles/index.php — 記事IDに応じて /data/articles/XXXX.md を読み込んで表示

require_once __DIR__ . '/../lib/parsedown.php';
require_once __DIR__ . '/../lib/auth.php';

$SITE_TITLE = 'SimplyWiki';

// -----------------------------
// 記事ID取得
// -----------------------------
$id = isset($_GET['id']) ? preg_replace('/[^0-9]/', '', $_GET['id']) : '';
if ($id === '') {
    http_response_code(404);
    echo "記事IDが指定されていません。";
    exit;
}

$PAGE_ID = $id;
$md_path = __DIR__ . '/../data/articles/' . $id . '.md';

if (!file_exists($md_path)) {
    http_response_code(404);
    echo "指定された記事は存在しません";
    exit;
}

// -----------------------------
// Markdown 読み込み
// -----------------------------
$raw = file_get_contents($md_path);
$lines = explode("\n", $raw);
$title = trim($lines[0]) ?: "無題の記事";
$PAGE_TITLE = $title;

$markdown = implode("\n", array_slice($lines, 1));
$Parsedown = new Parsedown();
$html_body = $Parsedown->text($markdown);

// -----------------------------
// テンプレート反映
// -----------------------------
ob_start();
?>
<h2 class="wiki-article-title"><?php echo htmlspecialchars($title); ?></h2>
<div class="wiki-article-body"><?php echo $html_body; ?></div>
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
