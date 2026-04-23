<?php
// admin/recipes.php — compact with serial numbers (fixed join)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';

// Admin gate
if (empty($_SESSION['user']) || (($_SESSION['user']['role'] ?? '') !== 'admin')) {
  $_SESSION['flash']['danger'] = 'Admin access required.';
  header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php');
  exit;
}

// Search
$q = trim($_GET['q'] ?? '');
$sql = "SELECT r.*, u.username AS author
        FROM recipes r
        LEFT JOIN users u ON r.author_id = u.id";
$params = [];
if ($q !== '') {
  $sql .= " WHERE r.title LIKE :q OR r.category LIKE :q OR u.username LIKE :q";
  $params[':q'] = "%$q%";
}
$sql .= " ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>
<div class="container py-4" style="max-width:1100px;">
  <div class="card shadow-sm p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h4 mb-0">Manage Recipes</h1>
      <form class="d-flex" method="get">
        <input class="form-control form-control-sm me-2" name="q" placeholder="Search..." value="<?= htmlspecialchars($q) ?>" style="max-width:220px;">
        <button class="btn btn-sm btn-outline-secondary">Search</button>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Category</th>
            <th>Author</th>
            <th>Created</th>
            <th style="width:220px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!$recipes): ?>
            <tr><td colspan="6" class="text-center text-muted">No recipes found.</td></tr>
          <?php else: $i = 1; foreach($recipes as $r): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><a href="<?= url('recipe.php?id='.$r['id']) ?>" target="_blank"><?= htmlspecialchars($r['title']) ?></a></td>
            <td><?= htmlspecialchars($r['category']) ?></td>
            <td><?= htmlspecialchars($r['author'] ?? '—') ?></td>
            <td><small class="text-muted"><?= htmlspecialchars($r['created_at']) ?></small></td>
            <td>
              <a class="btn btn-sm btn-primary" href="<?= url('admin/edit_recipe.php?id='.$r['id']) ?>">Edit</a>
              <form class="d-inline" method="post" action="<?= url('admin/delete_recipe.php') ?>" onsubmit="return confirm('Delete this recipe?');">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
              <a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/comments.php?q='.urlencode($r['title'])) ?>">Manage Comments</a>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
