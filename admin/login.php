<?php
// /admin/login.php — 管理者ログイン画面
session_start();
require_once __DIR__ . '/../lib/auth.php';

$SITE_TITLE = 'Admin-login';
$PAGE_TITLE = '(管理画面)ログイン';
$PAGE_ID = 'admin-login';

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (auth_check_password($password)) {
        $_SESSION['admin'] = true;
        header(header: 'Location: index.php');
        exit;
    } else {
        $loginError = 'パスワードが間違っています';
    }
}

ob_start();
?>
<h2 class="wiki-section-title">管理者ログイン</h2>
<?php if ($loginError): ?>
<p style="color:red;"><?php echo htmlspecialchars($loginError); ?></p>
<?php endif; ?>
<form method="post" class="wiki-edit-form">
    <label>パスワード</label>
    <input type="password" name="password" class="wiki-edit-textarea" style="height:auto;">
    <button type="submit" class="wiki-button wiki-button-primary">ログイン</button>
</form>
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
