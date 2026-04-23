<?php
// admin/comments.php — Manage & edit comments (compact, CSRF-protected)

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';

// --- Admin-only gate
if (empty($_SESSION['user']) || (($_SESSION['user']['role'] ?? '') !== 'admin')) {
  $_SESSION['flash']['danger'] = 'Admin access required.';
  header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php');
  exit;
}

// --- CSRF helpers
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_field() {
  echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">';
}
function check_csrf() {
  if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token');
  }
}

// --- Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  check_csrf();
  $id = (int)$_POST['delete_id'];
  $stmt = $pdo->prepare("DELETE FROM comments WHERE id=?");
  $stmt->execute([$id]);
  $_SESSION['flash']['success'] = 'Comment deleted.';
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

// --- Handle Edit (inline modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
  check_csrf();
  $id = (int)$_POST['edit_id'];
  $text = trim($_POST['comment_text'] ?? '');
  if ($text !== '') {
    $stmt = $pdo->prepare("UPDATE comments SET comment=? WHERE id=?");
    $stmt->execute([$text, $id]);
    $_SESSION['flash']['success'] = 'Comment updated.';
  }
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

// --- Fetch comments (optional recipe filter)
$q = trim($_GET['q'] ?? '');
$sql = "SELECT c.id, c.comment, c.created_at, r.title AS recipe, u.username AS author
        FROM comments c
        LEFT JOIN recipes r ON c.recipe_id = r.id
        LEFT JOIN users u ON c.user_id = u.id";
$params = [];

if ($q !== '') {
  $sql .= " WHERE r.title LIKE :q";
  $like = "%".$q."%";       // avoid \"%$q%\" interpolation issues
  $params[':q'] = $like;
}

$sql .= " ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>
<div class="container py-4" style="max-width:1000px;">
  <div class="card shadow-sm p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h4 mb-0">Manage Comments</h1>
      <form class="d-flex" method="get">
        <input class="form-control form-control-sm me-2"
               name="q" placeholder="Filter by recipe..."
               value="<?= htmlspecialchars($q) ?>" style="max-width:220px;">
        <button class="btn btn-sm btn-outline-secondary">Search</button>
      </form>
    </div>

    <?php if (!empty($_SESSION['flash']['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['success']) ?>
        <?php unset($_SESSION['flash']['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-sm table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Recipe</th>
            <th>Author</th>
            <th>Comment</th>
            <th>Posted</th>
            <th style="width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$comments): ?>
            <tr><td colspan="6" class="text-center text-muted">No comments found.</td></tr>
          <?php else: $i=1; foreach ($comments as $c): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($c['recipe']) ?></td>
              <td><?= htmlspecialchars($c['author'] ?? 'Guest') ?></td>
              <td class="text-truncate" style="max-width:300px;" title="<?= htmlspecialchars($c['comment']) ?>">
                <?= htmlspecialchars($c['comment']) ?>
              </td>
              <td><small class="text-muted"><?= htmlspecialchars($c['created_at']) ?></small></td>
              <td>
                <!-- Edit (modal) -->
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= (int)$c['id'] ?>">Edit</button>

                <!-- Delete -->
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this comment?');">
                  <?php csrf_field(); ?>
                  <input type="hidden" name="delete_id" value="<?= (int)$c['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= (int)$c['id'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form method="post">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Comment</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <?php csrf_field(); ?>
                      <input type="hidden" name="edit_id" value="<?= (int)$c['id'] ?>">
                      <textarea name="comment_text" class="form-control" rows="3"><?= htmlspecialchars($c['comment']) ?></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary btn-sm">Save</button>
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
