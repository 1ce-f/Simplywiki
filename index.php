<?php
// index.php — 記事一覧を表示するトップページ

require_once __DIR__ . '/lib/parsedown.php';
require_once __DIR__ . '/lib/auth.php';

$SITE_TITLE = 'SimplyWiki';
$PAGE_TITLE = 'ホーム';
$PAGE_ID = 'index';

// 記事一覧取得
$articles = [];
$dir = __DIR__ . '/data/articles';
if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        if (preg_match('/^(\d+)\.md$/', $file, $m)) {
            $id = $m[1];
            $path = $dir . '/' . $file;
            $title = trim(strtok(file_get_contents($path), "\n"));
            $articles[] = [
                'id' => $id,
                'title' => $title ?: '無題の記事'
            ];
        }
    }
}

// 記事番号昇順ソート
usort($articles, function($a, $b){ return intval($a['id']) - intval($b['id']); });

// ページ内容生成
ob_start();
?>
<div class="wiki-list">
    <h2 class="wiki-section-title">記事一覧</h2>
    <ul class="wiki-article-list">
        <?php foreach ($articles as $a): ?>
            <li class="wiki-article-item">
                <a href="/articles/index.php?id=<?php echo $a['id']; ?>" class="wiki-article-link">
                    <?php echo htmlspecialchars($a['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
$PAGE_CONTENT = ob_get_clean();

// テンプレート読み込み
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
