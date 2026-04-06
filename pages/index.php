<?php

// Page title for header
$pageTitle = "All Posts";

// Connect to database
require_once __DIR__ . '/../includes/config.php';

// Include header layout (navbar + opening HTML)
require_once __DIR__ . '/../includes/header.php';

?>

<!-- Page heading + Add button -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">All Posts</h1>
  <a class="btn btn-primary" href="create.php">+ Add New Post</a>
</div>

<?php
// Get all posts from database (newest first)
$stmt = $pdo->query("SELECT id, title, post_date, category, created_at FROM posts ORDER BY id DESC");

// Convert result into array
$posts = $stmt->fetchAll();
?>

<?php if (!$posts): ?>
  <!-- If there are no posts yet -->
  <div class="alert alert-info">
    No posts yet. Click “Add New Post” to create your first one.
  </div>
<?php else: ?>

  <!-- If posts exist, show them in a table -->
  <div class="table-responsive">
    <table class="table table-striped table-bordered bg-white align-middle">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Date</th>
          <th>Category</th>
          <th>Created</th>
          <th style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($posts as $p): ?>
          <tr>
            <!-- htmlspecialchars protects from XSS -->
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['post_date']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
            <td>
              <!-- Edit button sends id in URL -->
              <a class="btn btn-sm btn-outline-secondary"
                href="edit.php?id=<?= (int)$p['id'] ?>">
                Edit
              </a>

              <!-- Delete button with confirmation -->
              <a class="btn btn-sm btn-outline-danger"
                href="delete.php?id=<?= (int)$p['id'] ?>"
                onclick="return confirm('Delete this post?');">
                Delete
              </a>
            </td>
          </tr>
        <?php endforeach; ?>

      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php
// Include footer layout (closing div/body/html)
require_once __DIR__ . '/../includes/footer.php';
?>