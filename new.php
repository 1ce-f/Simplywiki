<?php
// new.php — 新規記事作成
session_start();
require_once __DIR__ . '/lib/auth.php';

$SITE_TITLE = 'Create-New';
$PAGE_TITLE = '新規記事作成';
$PAGE_ID = 'new';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = preg_replace('/[^0-9]/', '', $_POST['id'] ?? '');
    $content = $_POST['content'] ?? '';
    $mdPath = __DIR__ . "/data/articles/$id.md";

    if ($id === '') {
        $error = '記事IDを入力してください。';
    } elseif (file_exists($mdPath)) {
        $error = "ID $id は既に使用されています。別のIDを指定してください。";
    } else {
        // 新規作成はそのまま save.php に POST
        $_POST['content'] = $content;
        $_POST['id'] = $id;
        header('Location: save.php');
        exit;
    }
}

ob_start();
?>
<h2 class="wiki-section-title">新規記事作成</h2>
<?php if($error): ?>
<p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form action="new.php" method="post" class="wiki-edit-form">
    <label>記事ID（数字のみ）</label>
    <input type="text" name="id" class="wiki-edit-textarea" style="height:auto;" value="<?php echo htmlspecialchars($_POST['id'] ?? ''); ?>">
    <label>記事内容（Markdown）</label>
    <textarea name="content" class="wiki-edit-textarea" rows="20"><?php echo htmlspecialchars($_POST['content'] ?? "# タイトルをここに入力"); ?></textarea>
    <div class="wiki-edit-buttons">
        <button type="submit" class="wiki-button wiki-button-primary">作成</button>
        <a href="/index.php" class="wiki-button wiki-button-secondary">キャンセル</a>
    </div>
</form>
<?php
$PAGE_CONTENT = ob_get_clean();
$template = file_get_contents(__DIR__ . '/template/layout.html');
$html = str_replace([
    '{{PAGE_TITLE}}','{{SITE_TITLE}}','{{PAGE_ID}}','{{PAGE_CONTENT}}'
], [$PAGE_TITLE,$SITE_TITLE,$PAGE_ID,$PAGE_CONTENT], $template);

echo $html;
