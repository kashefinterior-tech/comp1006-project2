<?php
require_once __DIR__ . '/../includes/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id) {
  $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
  $stmt->execute([':id' => $id]);
}

header("Location: index.php");
exit;
