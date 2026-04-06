<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/secrets.php';

$errors = [];
$title = '';
$post_date = '';
$body = '';
$category = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $title     = trim($_POST['title'] ?? '');
  $post_date = trim($_POST['post_date'] ?? '');
  $body      = trim($_POST['body'] ?? '');
  $category  = trim($_POST['category'] ?? '');

  // Validation
  if ($title === '' || strlen($title) < 3) $errors[] = "Title must be at least 3 characters.";
  if ($post_date === '') $errors[] = "Date is required.";
  if ($body === '' || strlen($body) < 20) $errors[] = "Body must be at least 20 characters.";
  if ($category === '') $errors[] = "Category is required.";

  // reCAPTCHA verification
  $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
  if ($recaptchaResponse === '') {
    $errors[] = "Please complete the reCAPTCHA.";
  } else {
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
    $data = ['secret' => RECAPTCHA_SECRET_KEY, 'response' => $recaptchaResponse];
    $options = ['http' => ['method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n", 'content' => http_build_query($data)]];
    $context = stream_context_create($options);
    $resultJson = json_decode(file_get_contents($verifyUrl, false, $context), true);
    if (empty($resultJson['success'])) { $errors[] = "reCAPTCHA verification failed."; }
  }

  //image upload
  $image_name = null;
  if (!empty($_FILES['image']['name']) && !$errors) {
    $targetDir = "../uploads/";
    
    // making sure folder exists
    if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }

    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $image_name;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $errors[] = "Failed to upload image.";
    }
  }

  // If there is no errors it will insert image and user_id
  if (!$errors) {
    $sql = "INSERT INTO posts (title, post_date, body, category, image_path, user_id)
            VALUES (:title, :post_date, :body, :category, :image_path, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':title'      => $title,
      ':post_date'  => $post_date,
      ':body'       => $body,
      ':category'   => $category,
      ':image_path' => $image_name,
      ':user_id'    => $_SESSION['user_id'] // From  auth.php
    ]);

    header("Location: index.php");
    exit;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 800px;">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Add New Post</h1>
    <a class="btn btn-outline-secondary" href="index.php">Back</a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input name="title" class="form-control" required value="<?= htmlspecialchars($title) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Date</label>
      <input type="date" name="post_date" class="form-control" required value="<?= htmlspecialchars($post_date) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Category</label>
      <input name="category" class="form-control" required value="<?= htmlspecialchars($category) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Body</label>
      <textarea name="body" class="form-control" rows="6" required><?= htmlspecialchars($body) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Upload Image</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <div class="mb-3">
      <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
    </div>

    <button class="btn btn-primary w-100">Save Post</button>
  </form>
</div>

<script>
// Bootstrap validation
(() => {
  const form = document.querySelector('.needs-validation');
  form.addEventListener('submit', (event) => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  });
})();
</script>
</body>
</html>