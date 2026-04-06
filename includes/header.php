<?php
require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Blog CMS') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Blog CMS</a>

    <div class="ms-auto">
      <?php if (isLoggedIn()): ?>
        <span class="text-white me-3">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a class="btn btn-sm btn-outline-light" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-sm btn-outline-light me-2" href="login.php">Login</a>
        <a class="btn btn-sm btn-light" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-4">