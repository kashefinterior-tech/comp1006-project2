<!doctype html>
<html lang="en">
<head>

  <!-- Basic character encoding -->
  <meta charset="utf-8">

  <!-- Makes the page responsive on mobile devices -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Dynamic page title (comes from each page) -->
  <title><?= htmlspecialchars($pageTitle ?? 'Blog CMS') ?></title>

  <!-- Bootstrap CSS for styling and layout -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<!-- Light background for the whole app -->
<body class="bg-light">

  <!-- Navigation bar at top of application -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <!-- Clicking this returns user to homepage -->
      <a class="navbar-brand" href="index.php">Blog CMS</a>
    </div>
  </nav>

  <!-- Main content container (all page content goes inside this) -->
  <div class="container py-4">
