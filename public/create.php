<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/secrets.php';

$errors = [];
$title = '';
$post_date = '';
$body = '';
$category = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // 1) Get form data (server-side)
  $title     = trim($_POST['title'] ?? '');
  $post_date = trim($_POST['post_date'] ?? '');
  $body      = trim($_POST['body'] ?? '');
  $category  = trim($_POST['category'] ?? '');

  // 2) Validate normal fields (server-side)
  if ($title === '' || strlen($title) < 3) $errors[] = "Title must be at least 3 characters.";
  if ($post_date === '') $errors[] = "Date is required.";
  if ($body === '' || strlen($body) < 20) $errors[] = "Body must be at least 20 characters.";
  if ($category === '') $errors[] = "Category is required.";

  // 3) reCAPTCHA server-side verification (must pass before saving)
  $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

  if ($recaptchaResponse === '') {
    $errors[] = "Please complete the reCAPTCHA.";
  } else {
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";

    $data = [
      'secret'   => RECAPTCHA_SECRET_KEY,
      'response' => $recaptchaResponse,
      'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
    ];

    $options = [
      'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'timeout' => 10
      ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($verifyUrl, false, $context);
    $resultJson = json_decode($result, true);

    if (empty($resultJson['success'])) {
      $errors[] = "reCAPTCHA verification failed. Please try again.";
    }
  }

  // 4) If no errors -> insert
  if (!$errors) {
    $sql = "INSERT INTO posts (title, post_date, body, category)
            VALUES (:title, :post_date, :body, :category)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':title' => $title,
      ':post_date' => $post_date,
      ':body' => $body,
      ':category' => $category
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- reCAPTCHA script (needed to show the checkbox) -->
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
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="needs-validation" novalidate>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input name="title" class="form-control" required minlength="3"
             value="<?= htmlspecialchars($title) ?>">
      <div class="invalid-feedback">Please enter a title (min 3 characters).</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Date</label>
      <input type="date" name="post_date" class="form-control" required
             value="<?= htmlspecialchars($post_date) ?>">
      <div class="invalid-feedback">Please select a date.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Category</label>
      <input name="category" class="form-control" required
             value="<?= htmlspecialchars($category) ?>">
      <div class="invalid-feedback">Please enter a category.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Body</label>
      <textarea name="body" class="form-control" rows="6" required minlength="20"><?= htmlspecialchars($body) ?></textarea>
      <div class="invalid-feedback">Please write at least 20 characters.</div>
    </div>

    <!-- reCAPTCHA checkbox -->
    <div class="mb-3">
      <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
    </div>

    <button class="btn btn-primary">Save Post</button>
  </form>

</div>

<script>
// Bootstrap client-side validation
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
