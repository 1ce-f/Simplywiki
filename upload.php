<?php
// /upload.php — 画像アップロード

$id = isset($_GET['id']) ? preg_replace('/[^0-9]/', '', $_GET['id']) : '';
if ($id === '') {
    echo "記事IDが指定されていません";
    exit;
}

$uploadDir = __DIR__ . "/data/uploads/$id";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$message = '';
$linkMarkdown = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $tmp = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']);

        // 拡張子チェック（画像のみ）
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['png','jpg','jpeg','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $message = "アップロードできるのは画像のみです。";
        } else {

            // 重複ファイル名を避ける
            $newName = $name;
            $i = 1;
            while (file_exists("$uploadDir/$newName")) {
                $newName = pathinfo($name, PATHINFO_FILENAME) . "_$i." . $ext;
                $i++;
            }

            move_uploaded_file($tmp, "$uploadDir/$newName");

            $url = "/data/uploads/$id/$newName";
            $linkMarkdown = "![image]($url)";
            $message = "アップロードしました！";
        }

    } else {
        $message = "ファイルが選択されていません。";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>画像アップロード</title>
<link rel="stylesheet" href="/style.css">
</head>
<body class="wiki-body">
<div class="wiki-container">

<h1>画像アップロード (記事ID: <?php echo $id; ?>)</h1>

<?php if($message): ?>
<p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="wiki-edit-form">
    <input type="file" name="image" class="wiki-input">
    <button type="submit" class="wiki-button wiki-button-primary">アップロード</button>
</form>

<?php if ($linkMarkdown): ?>
<h3>Markdown 用リンク</h3>
<input type="text" value="<?php echo htmlspecialchars($linkMarkdown); ?>" class="wiki-input" onclick="this.select();">
<?php endif; ?>

<p>
    <a href="/edit.php?id=<?php echo $id; ?>" class="wiki-button wiki-button-secondary">記事編集へ戻る</a>
</p>

</div>
</body>
</html>
