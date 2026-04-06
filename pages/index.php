<?php
$pageTitle = "All Posts";
// connect to database
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">All Posts</h1>

  <?php if (isLoggedIn()): ?>
    <a class="btn btn-primary" href="create.php">+ Add New Post</a>
  <?php endif; ?>
</div>

<?php
// added 'image_path' to the SELECT statement
$stmt = $pdo->query("SELECT id, title, post_date, category, image_path, created_at FROM posts ORDER BY id DESC");
$posts = $stmt->fetchAll();
?>

<?php if (!$posts): ?>
  <div class="alert alert-info">
    No posts yet. Click “Add New Post” to create your first one.
  </div>
<?php else: ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered bg-white align-middle">
      <thead class="table-dark">
        <tr>
          <th style="width: 100px;">Image</th> <th>Title</th>
          <th>Date</th>
          <th>Category</th>
          <th style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($posts as $p): ?>
          <tr>
            <td>
              <?php if (!empty($p['image_path'])): ?>
                <img src="../uploads/<?= htmlspecialchars($p['image_path']) ?>" 
                     alt="Post Image" 
                     class="img-thumbnail" 
                     style="width: 80px; height: 60px; object-fit: cover;">
              <?php else: ?>
                <span class="text-muted" style="font-size: 0.8rem;">No Image</span>
              <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['post_date']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td>
              <a class="btn btn-sm btn-outline-secondary" href="edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?= (int)$p['id'] ?>" onclick="return confirm('Delete this post?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>

      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>