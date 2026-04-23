<?php
// SHOW ERRORS FIRST (always at the top)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load the database connection file
require __DIR__ . '/config/db.php';

// Include the page header (HTML layout, navigation, etc.)
include __DIR__ . '/includes/header.php';

// Retrieve search inputs from URL (?q=search&category=xyz)
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

// Base SQL query to fetch recipes + join authors table
$sql = "
    SELECT r.*, u.username AS author
    FROM recipes r
    LEFT JOIN users u ON r.author_id = u.id
    WHERE 1=1
";

$params = [];

// If the user typed a search term
if ($q !== '') {
    $sql .= " AND (r.title LIKE :q OR r.ingredients LIKE :q OR r.steps LIKE :q)";
    $params[':q'] = "%$q%";
}

// If the user selected a category
if ($category !== '') {
    $sql .= " AND r.category = :c";
    $params[':c'] = $category;
}

// Sort newest first
$sql .= " ORDER BY r.created_at DESC";

// Prepare and execute
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Fetch all results
$recipes = $stmt->fetchAll();
?>

<!-- Main content container -->
<div class="container py-4">
  <h1 class="mb-4 text-center fw-bold">Latest Recipes</h1>

  <?php if (!$recipes): ?>
    <div class="alert alert-info text-center">No recipes found.</div>
  <?php else: ?>
    <div class="row g-4 recipes-row justify-content-start">

      <?php foreach ($recipes as $r): ?>
        <div class="col-md-4 col-sm-6">
          <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">

            <?php if (!empty($r['image'])): ?>
              <img src="<?= url('uploads/' . $r['image']) ?>"
                   class="card-img-top"
                   alt="Recipe image"
                   style="height:220px;object-fit:cover;">
            <?php else: ?>
              <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="height:220px;">
                <span>No image</span>
              </div>
            <?php endif; ?>

            <div class="card-body">
              <h5 class="card-title mb-2">
                <?= htmlspecialchars($r['title']) ?>
              </h5>

              <?php if (!empty($r['category'])): ?>
                <span class="badge bg-primary mb-2">
                  <?= htmlspecialchars(ucfirst($r['category'])) ?>
                </span>
              <?php endif; ?>

              <p class="card-text text-muted small">
                <?= htmlspecialchars(mb_strimwidth(strip_tags($r['steps']), 0, 100, '...')) ?>
              </p>
            </div>

            <div class="card-footer bg-white border-0 pb-3">
              <a href="<?= url('recipe.php?id=' . $r['id']) ?>"
                 class="btn btn-outline-primary w-100">
                 View Recipe
              </a>
            </div>

          </div>
        </div>
      <?php endforeach; ?>

    </div>
  <?php endif; ?>
</div>

<!-- Hero section -->
<section class="hero-section mb-4">
  <div class="hero-text">
    <h1 class="hero-title">Cook, Save & Share Your Favorite Recipes</h1>

    <p class="hero-subtitle">
      Keep all your dishes in one place. Search by name, category and more —
      from quick snacks to full-course meals.
    </p>

    <div class="hero-actions">
      <a href="<?= url('add_recipe.php') ?>" class="btn btn-primary btn-lg">
        + Add New Recipe
      </a>

      <a href="<?= url('recipes.php') ?>" class="btn btn-outline-primary btn-lg">
        Browse All Recipes
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
