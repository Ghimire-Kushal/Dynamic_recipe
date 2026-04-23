<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    $_SESSION['flash']['danger'] = 'Admin access required.';
    header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
function csrf_field(){ echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">'; }
function check_csrf(){ if(($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')){ http_response_code(403); exit('Invalid CSRF token'); } }

function countTable($pdo, $table) {
    try { return (int)$pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn(); }
    catch(Throwable $e) { return 0; }
}

$users = countTable($pdo,'users');
$recipesCount = countTable($pdo,'recipes');
$comments = countTable($pdo,'comments');

// Load recipes for inline management
$q = trim($_GET['q'] ?? '');
$sql = "SELECT r.*, u.username AS author FROM recipes r LEFT JOIN users u ON u.id=r.author_id";
$params = [];
if ($q !== '') { $sql .= " WHERE r.title LIKE :q"; $params[':q'] = "%$q%"; }
$sql .= " ORDER BY r.created_at DESC LIMIT 50";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>
<div class="py-4">
  <h1 class="mb-4">Admin Dashboard</h1>
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Users</h5>
          <p class="display-6"><?= $users ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Recipes</h5>
          <p class="display-6"><?= $recipesCount ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Comments</h5>
          <p class="display-6"><?= $comments ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h4 mb-0">Manage Recipes</h2>
    <form class="d-flex" method="get">
      <input class="form-control me-2" name="q" placeholder="Search..." value="<?= htmlspecialchars($q) ?>">
      <button class="btn btn-outline-secondary">Search</button>
    </form>
  </div>

  <div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Title</th><th>Category</th><th>Author</th><th>Created</th><th style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($recipes as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><a href="<?= url('recipe.php?id='.$r['id']) ?>" target="_blank"><?= htmlspecialchars($r['title']) ?></a></td>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <td><?= htmlspecialchars($r['author'] ?? '—') ?></td>
          <td><small class="text-muted"><?= htmlspecialchars($r['created_at']) ?></small></td>
          <td>
            <a href="<?= url('admin/comments.php') ?>" class="btn btn-outline-secondary">Manage Comments</a>

            <a class="btn btn-sm btn-primary" href="<?= url('admin/edit_recipe.php?id='.$r['id']) ?>">Edit</a>
            <form class="d-inline" method="post" action="<?= url('admin/delete_recipe.php') ?>" onsubmit="return confirm('Delete this recipe?');">
              <?php csrf_field(); ?>
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
