<?php
// DEV MODE (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB
require __DIR__ . '/config/db.php';

// Header
include __DIR__ . '/includes/header.php';

// Filters
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

// Base query
$sql = "
    SELECT 
        r.id,
        r.title,
        r.category,
        r.image,
        r.steps
    FROM recipes r
    WHERE 1=1
";

$params = [];

// Search
if ($q !== '') {
    $sql .= " AND (
        r.title LIKE :q
        OR r.ingredients LIKE :q
        OR r.steps LIKE :q
    )";
    $params[':q'] = "%$q%";
}

// Category filter
if ($category !== '') {
    $sql .= " AND r.category = :c";
    $params[':c'] = $category;
}

// Sort
$sql .= " ORDER BY r.created_at DESC";

// Execute
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-4">
  <h1 class="mb-4 text-center fw-bold">Latest Recipes</h1>

  <?php if (!$recipes): ?>
    <div class="alert alert-info text-center">No recipes found.</div>
  <?php else: ?>
    <div class="row g-4">

      <?php foreach ($recipes as $r): ?>
        <div class="col-md-4 col-sm-6">
          <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">

            <!-- Image -->
            <?php if (!empty($r['image'])): ?>
              <img
                src="<?= url('uploads/' . $r['image']) ?>"
                alt="Recipe image"
                class="w-100"
                style="height:220px; object-fit:cover;">
            <?php else: ?>
              <div class="d-flex align-items-center justify-content-center bg-secondary text-white"
                   style="height:220px;">
                No image
              </div>
            <?php endif; ?>

            <!-- Body -->
            <div class="card-body">
              <h5 class="card-title"><?= e($r['title']) ?></h5>

              <?php if (!empty($r['category'])): ?>
                <span class="badge bg-primary mb-2">
                  <?= e($r['category']) ?>
                </span>
              <?php endif; ?>

              <p class="text-muted small">
                <?= e(
                    mb_strimwidth(
                        strip_tags($r['steps'] ?? ''),
                        0,
                        90,
                        '…'
                    )
                ) ?>
              </p>
            </div>

            <!-- Footer -->
            <div class="card-footer bg-white border-0">
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

<?php include __DIR__ . '/includes/footer.php'; ?>