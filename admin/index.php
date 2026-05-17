<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    $_SESSION['flash']['danger'] = 'Admin access required.';
    header('Location: ' . url('index.php'));
    exit;
}

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
function csrf_field(){ echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">'; }
function check_csrf(){ if(($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')){ http_response_code(403); exit('Invalid CSRF token'); } }

function countTable($pdo, $table) {
    try { return (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn(); }
    catch(Throwable $e) { return 0; }
}

$totalUsers    = countTable($pdo, 'users');
$totalRecipes  = countTable($pdo, 'recipes');
$totalComments = countTable($pdo, 'comments');

// Latest 5 recipes for the "recent" mini-list
$recentRecipes = $pdo->query("
    SELECT r.id, r.title, r.category, r.created_at, u.username AS author
    FROM recipes r LEFT JOIN users u ON u.id = r.author_id
    ORDER BY r.created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// All recipes with optional search
$q = trim($_GET['q'] ?? '');
$sql = "SELECT r.*, u.username AS author FROM recipes r LEFT JOIN users u ON u.id = r.author_id";
$params = [];
if ($q !== '') { $sql .= " WHERE r.title LIKE :q OR r.category LIKE :q"; $params[':q'] = "%$q%"; }
$sql .= " ORDER BY r.created_at DESC LIMIT 50";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<!-- Admin page intro -->
<section class="page-intro">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
      <div>
        <h1 style="margin:0 0 6px;"><i class="fa-solid fa-gauge me-2" style="opacity:.85;"></i>Admin Dashboard</h1>
        <p style="margin:0;opacity:.85;">Manage all recipes, users and comments</p>
      </div>
      <a href="<?= url('add_recipe.php') ?>" class="btn-secondary-custom" style="background:rgba(255,255,255,0.15);border-color:rgba(255,255,255,0.3);color:white;">
        <i class="fa-solid fa-plus"></i> Add Recipe
      </a>
    </div>
  </div>
</section>

<div class="container" style="padding-top:48px;padding-bottom:80px;">

  <!-- ── Stat cards ── -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:48px;">

    <div class="detail-card" style="margin-bottom:0;display:flex;align-items:center;gap:20px;">
      <div style="width:56px;height:56px;border-radius:var(--r-lg);background:hsl(239,80%,57%,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fa-solid fa-users" style="color:hsl(239,80%,57%);font-size:1.4rem;"></i>
      </div>
      <div>
        <div style="font-size:2rem;font-weight:800;color:var(--text-main);line-height:1;"><?= $totalUsers ?></div>
        <div style="font-size:.82rem;color:var(--text-muted);font-weight:600;margin-top:4px;">Total Users</div>
      </div>
    </div>

    <div class="detail-card" style="margin-bottom:0;display:flex;align-items:center;gap:20px;">
      <div style="width:56px;height:56px;border-radius:var(--r-lg);background:hsl(142,65%,39%,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fa-solid fa-bowl-food" style="color:hsl(142,65%,39%);font-size:1.4rem;"></i>
      </div>
      <div>
        <div style="font-size:2rem;font-weight:800;color:var(--text-main);line-height:1;"><?= $totalRecipes ?></div>
        <div style="font-size:.82rem;color:var(--text-muted);font-weight:600;margin-top:4px;">Total Recipes</div>
      </div>
    </div>

    <div class="detail-card" style="margin-bottom:0;display:flex;align-items:center;gap:20px;">
      <div style="width:56px;height:56px;border-radius:var(--r-lg);background:hsl(25,95%,53%,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fa-regular fa-comments" style="color:hsl(25,95%,53%);font-size:1.4rem;"></i>
      </div>
      <div>
        <div style="font-size:2rem;font-weight:800;color:var(--text-main);line-height:1;"><?= $totalComments ?></div>
        <div style="font-size:.82rem;color:var(--text-muted);font-weight:600;margin-top:4px;">Comments</div>
      </div>
    </div>

    <div class="detail-card" style="margin-bottom:0;display:flex;align-items:center;gap:20px;">
      <div style="width:56px;height:56px;border-radius:var(--r-lg);background:hsl(270,75%,65%,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fa-solid fa-tags" style="color:hsl(270,75%,65%);font-size:1.4rem;"></i>
      </div>
      <div>
        <?php $cats = (int)$pdo->query("SELECT COUNT(DISTINCT category) FROM recipes WHERE category IS NOT NULL AND category <> ''")->fetchColumn(); ?>
        <div style="font-size:2rem;font-weight:800;color:var(--text-main);line-height:1;"><?= $cats ?></div>
        <div style="font-size:.82rem;color:var(--text-muted);font-weight:600;margin-top:4px;">Categories</div>
      </div>
    </div>

  </div>

  <!-- ── Two-column: Quick links + Recent activity ── -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:48px;">

    <!-- Quick links -->
    <div class="detail-card" style="margin-bottom:0;">
      <h3><i class="fa-solid fa-bolt"></i> Quick Actions</h3>
      <div style="display:flex;flex-direction:column;gap:10px;">
        <?php
        $actions = [
          [url('add_recipe.php'),      'fa-plus',          'Add New Recipe',     'Create a new recipe'],
          [url('admin/recipes.php'),   'fa-bowl-food',     'Manage Recipes',     'Edit or delete recipes'],
          [url('admin/comments.php'),  'fa-comments',      'Manage Comments',    'Review all comments'],
          [url('index.php'),           'fa-eye',           'View Site',          'See the live site'],
        ];
        foreach ($actions as [$href, $icon, $label, $desc]):
        ?>
        <a href="<?= $href ?>"
           style="display:flex;align-items:center;gap:14px;padding:12px 16px;background:var(--bg-body);border:1px solid var(--divider);border-radius:var(--r-lg);color:var(--text-main);text-decoration:none;transition:all var(--t-fast);"
           onmouseover="this.style.borderColor='var(--clr-400)'"
           onmouseout="this.style.borderColor='var(--divider)'">
          <div style="width:38px;height:38px;background:var(--clr-50);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:var(--clr-600);flex-shrink:0;">
            <i class="fa-solid <?= $icon ?>"></i>
          </div>
          <div>
            <div style="font-size:.88rem;font-weight:700;"><?= $label ?></div>
            <div style="font-size:.75rem;color:var(--text-light);"><?= $desc ?></div>
          </div>
          <i class="fa-solid fa-chevron-right ms-auto" style="color:var(--text-light);font-size:.75rem;"></i>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Recent recipes -->
    <div class="detail-card" style="margin-bottom:0;">
      <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Recipes</h3>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php foreach ($recentRecipes as $rec): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
          <div style="min-width:0;">
            <div style="font-size:.88rem;font-weight:700;color:var(--text-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              <?= e($rec['title']) ?>
            </div>
            <div style="font-size:.75rem;color:var(--text-light);">
              <?= e($rec['author'] ?? '—') ?> · <?= date('M j', strtotime($rec['created_at'])) ?>
            </div>
          </div>
          <div style="display:flex;gap:6px;flex-shrink:0;">
            <?php if ($rec['category']): ?>
            <span style="background:var(--clr-50);color:var(--clr-700);padding:3px 10px;border-radius:var(--r-full);font-size:.72rem;font-weight:700;">
              <?= e(ucfirst($rec['category'])) ?>
            </span>
            <?php endif; ?>
            <a href="<?= url('admin/edit_recipe.php?id=' . $rec['id']) ?>"
               style="background:var(--bg-body);border:1px solid var(--divider);color:var(--text-muted);padding:3px 10px;border-radius:var(--r-full);font-size:.72rem;font-weight:700;text-decoration:none;">
              Edit
            </a>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$recentRecipes): ?>
          <p style="font-size:.88rem;color:var(--text-light);margin:0;">No recipes yet.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- ── Recipes table ── -->
  <div class="detail-card" style="margin-bottom:0;">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:20px;">
      <h3 style="margin:0;border:none;padding:0;">
        <i class="fa-solid fa-table-list"></i> All Recipes
        <span style="font-size:.78rem;font-weight:600;color:var(--text-light);margin-left:8px;"><?= count($recipes) ?> shown</span>
      </h3>
      <form method="get" style="display:flex;gap:8px;align-items:center;">
        <div class="nav-search-wrap" style="max-width:280px;">
          <input type="search" name="q" placeholder="Search recipes…" value="<?= e($q) ?>" style="width:200px;">
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        <?php if ($q): ?>
          <a href="<?= url('admin/index.php') ?>" style="font-size:.82rem;color:var(--clr-500);">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <?php if (!$recipes): ?>
    <div class="empty-state" style="padding:40px 0;">
      <div class="empty-state-icon" style="font-size:2.5rem;">🍽️</div>
      <h3 style="font-size:1rem;">No recipes found</h3>
    </div>
    <?php else: ?>

    <div style="overflow-x:auto;margin:-1px;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--divider);">
            <th style="padding:10px 14px;text-align:left;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--text-light);">ID</th>
            <th style="padding:10px 14px;text-align:left;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--text-light);">Title</th>
            <th style="padding:10px 14px;text-align:left;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--text-light);">Category</th>
            <th style="padding:10px 14px;text-align:left;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--text-light);">Author</th>
            <th style="padding:10px 14px;text-align:left;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--text-light);">Created</th>
            <th style="padding:10px 14px;text-align:right;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--text-light);">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recipes as $r): ?>
          <tr style="border-bottom:1px solid var(--divider);transition:background var(--t-fast);"
              onmouseover="this.style.background='var(--bg-body)'"
              onmouseout="this.style.background=''">
            <td style="padding:14px;color:var(--text-light);font-weight:600;">#<?= (int)$r['id'] ?></td>
            <td style="padding:14px;">
              <a href="<?= url('recipe.php?id=' . $r['id']) ?>" target="_blank"
                 style="font-weight:700;color:var(--clr-600);text-decoration:none;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">
                <?= e($r['title']) ?>
              </a>
            </td>
            <td style="padding:14px;">
              <?php if ($r['category']): ?>
              <span style="background:var(--clr-50);color:var(--clr-700);padding:3px 10px;border-radius:var(--r-full);font-size:.75rem;font-weight:700;">
                <?= e(ucfirst($r['category'])) ?>
              </span>
              <?php else: ?>
              <span style="color:var(--text-light);font-size:.82rem;">—</span>
              <?php endif; ?>
            </td>
            <td style="padding:14px;color:var(--text-muted);font-size:.85rem;"><?= e($r['author'] ?? '—') ?></td>
            <td style="padding:14px;color:var(--text-light);font-size:.8rem;white-space:nowrap;">
              <?= date('M j, Y', strtotime($r['created_at'])) ?>
            </td>
            <td style="padding:14px;">
              <div style="display:flex;gap:8px;justify-content:flex-end;align-items:center;flex-wrap:wrap;">
                <a href="<?= url('admin/comments.php?recipe_id=' . $r['id']) ?>"
                   style="padding:6px 12px;background:var(--bg-body);border:1px solid var(--divider);border-radius:var(--r-full);font-size:.75rem;font-weight:700;color:var(--text-muted);text-decoration:none;white-space:nowrap;transition:all var(--t-fast);">
                  <i class="fa-regular fa-comment"></i> Comments
                </a>
                <a href="<?= url('admin/edit_recipe.php?id=' . $r['id']) ?>"
                   style="padding:6px 14px;background:var(--clr-500);color:white;border-radius:var(--r-full);font-size:.75rem;font-weight:700;text-decoration:none;white-space:nowrap;transition:all var(--t-fast);">
                  <i class="fa-solid fa-pen"></i> Edit
                </a>
                <form method="post" action="<?= url('admin/delete_recipe.php') ?>" style="margin:0;"
                      onsubmit="return confirm('Delete «<?= e(addslashes($r['title'])) ?>»? This cannot be undone.')">
                  <?php csrf_field(); ?>
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button type="submit"
                          style="padding:6px 14px;background:transparent;border:1px solid #fca5a5;color:#ef4444;border-radius:var(--r-full);font-size:.75rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:all var(--t-fast);">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php endif; ?>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
