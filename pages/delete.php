<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id) {
  $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
  $stmt->execute([':id' => $id]);
}

header("Location: index.php");
exit;
