<?php
require_once __DIR__ . '/../includes/config.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
  header("Location: index.php");
  exit;
}

// Get existing post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
  header("Location: index.php");
  exit;
}

$errors = [];
$title = $post['title'];
$post_date = $post['post_date'];
$body = $post['body'];
$category = $post['category'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $title = trim($_POST['title'] ?? '');
  $post_date = trim($_POST['post_date'] ?? '');
  $body = trim($_POST['body'] ?? '');
  $category = trim($_POST['category'] ?? '');

  // Server-side validation
  if ($title === '' || strlen($title) < 3) $errors[] = "Title must be at least 3 characters.";
  if ($post_date === '') $errors[] = "Date is required.";
  if ($body === '' || strlen($body) < 20) $errors[] = "Body must be at least 20 characters.";
  if ($category === '') $errors[] = "Category is required.";

  if (!$errors) {
    $sql = "UPDATE posts 
            SET title = :title,
                post_date = :post_date,
                body = :body,
                category = :category
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':title' => $title,
      ':post_date' => $post_date,
      ':body' => $body,
      ':category' => $category,
      ':id' => $id
    ]);

    header("Location: index.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 800px;">

<h1 class="h3 mb-3">Edit Post</h1>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <ul>
    <?php foreach ($errors as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post">
  <div class="mb-3">
    <label>Title</label>
    <input name="title" class="form-control"
           value="<?= htmlspecialchars($title) ?>" required>
  </div>

  <div class="mb-3">
    <label>Date</label>
    <input type="date" name="post_date" class="form-control"
           value="<?= htmlspecialchars($post_date) ?>" required>
  </div>

  <div class="mb-3">
    <label>Category</label>
    <input name="category" class="form-control"
           value="<?= htmlspecialchars($category) ?>" required>
  </div>

  <div class="mb-3">
    <label>Body</label>
    <textarea name="body" class="form-control" rows="6" required><?= htmlspecialchars($body) ?></textarea>
  </div>

  <button class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</body>
</html>
