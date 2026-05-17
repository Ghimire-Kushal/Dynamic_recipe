<?php
require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

// Category-to-emoji map
$catEmoji = [
  'breakfast'=>'🍳','lunch'=>'🥗','dinner'=>'🍽️','dessert'=>'🍰',
  'desserts'=>'🍰','snack'=>'🍿','snacks'=>'🍿','pizza'=>'🍕',
  'pasta'=>'🍝','soup'=>'🍲','salad'=>'🥗','burger'=>'🍔',
  'chicken'=>'🍗','seafood'=>'🦞','vegan'=>'🥦','vegetarian'=>'🥕',
  'nepali'=>'🫙','indian'=>'🍛','fast food'=>'🍔','healthy'=>'🥑',
  'drink'=>'🥤','drinks'=>'🥤','baking'=>'🧁','bread'=>'🍞',
];

function guessTime(string $cat): string {
  $map = ['breakfast'=>'15 min','snack'=>'10 min','snacks'=>'10 min','lunch'=>'30 min',
          'dinner'=>'45 min','dessert'=>'40 min','desserts'=>'40 min','baking'=>'60 min',
          'soup'=>'35 min','pasta'=>'25 min','pizza'=>'40 min','salad'=>'10 min',
          'healthy'=>'20 min','fast food'=>'15 min'];
  return $map[strtolower($cat)] ?? '30 min';
}
function guessDiff(string $cat): string {
  $hard = ['baking','dessert','desserts','pizza'];
  $easy = ['breakfast','salad','snack','snacks','healthy','fast food'];
  $c = strtolower($cat);
  if (in_array($c,$hard)) return 'Medium';
  if (in_array($c,$easy)) return 'Easy';
  return 'Medium';
}

$cats = $pdo->query("
  SELECT category, COUNT(*) AS cnt
  FROM recipes
  WHERE category IS NOT NULL AND category <> ''
  GROUP BY category
  ORDER BY cnt DESC, category ASC
")->fetchAll(PDO::FETCH_ASSOC);

$totalRecipes = (int)$pdo->query("SELECT COUNT(*) FROM recipes")->fetchColumn();
?>

<!-- Page intro -->
<section class="page-intro">
  <div class="container">
    <h1><i class="fa-solid fa-bowl-food me-2" style="opacity:.85;"></i>All Recipes</h1>
    <p><?= $totalRecipes ?> recipes across <?= count($cats) ?> categories</p>
  </div>
</section>

<div class="container" style="padding-top:60px; padding-bottom:80px;">

  <?php if (!$cats): ?>
    <div class="empty-state">
      <div class="empty-state-icon">🍽️</div>
      <h3>No recipes yet</h3>
      <p>Be the first to add a recipe!</p>
      <a href="<?= url('add_recipe.php') ?>" class="btn-primary-custom mt-4">
        <i class="fa-solid fa-plus"></i> Add Recipe
      </a>
    </div>
  <?php endif; ?>

  <?php foreach ($cats as $catRow):
    $cat   = $catRow['category'];
    $emoji = $catEmoji[strtolower($cat)] ?? '🍽️';

    $st = $pdo->prepare("
      SELECT id, title, image, steps, category, created_at
      FROM recipes
      WHERE category = ?
      ORDER BY created_at DESC
      LIMIT 12
    ");
    $st->execute([$cat]);
    $items = $st->fetchAll(PDO::FETCH_ASSOC);
    if (!$items) continue;
  ?>

  <!-- Category section -->
  <div style="margin-bottom: 60px;">

    <div class="cat-section-title">
      <span style="font-size:1.6rem;"><?= $emoji ?></span>
      <h3><?= e(ucfirst($cat)) ?></h3>
      <div class="cat-section-divider"></div>
      <a class="cat-see-all" href="<?= url('index.php?category=' . urlencode($cat)) ?>">
        See all <i class="fa-solid fa-arrow-right" style="font-size:.7rem;"></i>
      </a>
    </div>

    <div class="recipes-grid">
      <?php foreach ($items as $r):
        $desc  = mb_strimwidth(strip_tags($r['steps'] ?? ''), 0, 100, '…');
        $time  = guessTime($cat);
        $diff  = guessDiff($cat);
        $likes = ($r['id'] * 7 + 3) % 48 + 2;
      ?>
      <div class="recipe-card">

        <div class="recipe-card-img-wrap">
          <?php if (!empty($r['image'])): ?>
            <img class="recipe-card-img"
                 src="<?= url('uploads/' . e($r['image'])) ?>"
                 alt="<?= e($r['title']) ?>"
                 loading="lazy">
          <?php else: ?>
            <div class="recipe-card-img-placeholder"><?= $emoji ?></div>
          <?php endif; ?>
          <div class="recipe-card-overlay"></div>
          <span class="recipe-cat-badge"><?= e(ucfirst($cat)) ?></span>
          <button class="recipe-save-btn" title="Save recipe">
            <i class="fa-regular fa-bookmark"></i>
          </button>
        </div>

        <div class="recipe-card-body">
          <h3 class="recipe-card-title"><?= e($r['title']) ?></h3>
          <p class="recipe-card-desc"><?= e($desc) ?></p>
          <div class="recipe-meta">
            <span class="recipe-meta-item"><i class="fa-regular fa-clock"></i><?= $time ?></span>
            <span class="recipe-meta-item"><i class="fa-solid fa-signal"></i><?= $diff ?></span>
          </div>
        </div>

        <div class="recipe-card-footer">
          <button class="recipe-likes">
            <i class="fa-regular fa-heart"></i>
            <span class="like-count"><?= $likes ?></span>
          </button>
          <a class="btn-view-recipe" href="<?= url('recipe.php?id=' . $r['id']) ?>">
            View <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

      </div>
      <?php endforeach; ?>
    </div>

  </div><!-- /category section -->

  <?php endforeach; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
