<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

$q        = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

// Category-to-emoji map
$catEmoji = [
  'breakfast' => '🍳', 'lunch'   => '🥗', 'dinner'  => '🍽️',
  'dessert'   => '🍰', 'desserts'=> '🍰', 'snack'   => '🍿',
  'snacks'    => '🍿', 'pizza'   => '🍕', 'pasta'   => '🍝',
  'soup'      => '🍲', 'salad'   => '🥗', 'burger'  => '🍔',
  'chicken'   => '🍗', 'seafood' => '🦞', 'vegan'   => '🥦',
  'vegetarian'=> '🥕', 'nepali'  => '🫙', 'indian'  => '🍛',
  'fast food' => '🍔', 'healthy' => '🥑', 'drink'   => '🥤',
  'drinks'    => '🥤', 'baking'  => '🧁', 'bread'   => '🍞',
];

// Get category counts for the category grid
$catRows = [];
try {
  $catRows = $pdo->query("
    SELECT category, COUNT(*) AS cnt
    FROM recipes
    WHERE category IS NOT NULL AND category <> ''
    GROUP BY category
    ORDER BY cnt DESC
    LIMIT 12
  ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Throwable $e){}

// Total stats
$totalRecipes = 0; $totalCats = 0;
try {
  $totalRecipes = (int)$pdo->query("SELECT COUNT(*) FROM recipes")->fetchColumn();
  $totalCats    = (int)$pdo->query("SELECT COUNT(DISTINCT category) FROM recipes WHERE category IS NOT NULL AND category<>''")->fetchColumn();
} catch(Throwable $e){}

// Recipe query
$sql = "SELECT r.id, r.title, r.category, r.image, r.steps, r.created_at FROM recipes r WHERE 1=1";
$params = [];

if ($q !== '') {
  $sql .= " AND (r.title LIKE :q OR r.ingredients LIKE :q OR r.steps LIKE :q)";
  $params[':q'] = "%$q%";
}
if ($category !== '') {
  $sql .= " AND r.category = :c";
  $params[':c'] = $category;
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estimated cook times and difficulty by category
function guessTime(string $cat): string {
  $map = [
    'breakfast'=>'15 min','snack'=>'10 min','snacks'=>'10 min',
    'lunch'=>'30 min','dinner'=>'45 min','dessert'=>'40 min',
    'desserts'=>'40 min','baking'=>'60 min','soup'=>'35 min',
    'pasta'=>'25 min','pizza'=>'40 min','salad'=>'10 min',
    'healthy'=>'20 min','fast food'=>'15 min',
  ];
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
?>

<?php if ($q === '' && $category === ''): ?>
<!-- ============================================================
     HERO SECTION (only on unfiltered homepage)
     ============================================================ -->
<section class="hero">
  <!-- Animated background blobs -->
  <div class="hero-blob hero-blob-1"></div>
  <div class="hero-blob hero-blob-2"></div>
  <div class="hero-blob hero-blob-3"></div>
  <div class="hero-blob hero-blob-4"></div>

  <div class="hero-content container">

    <div class="hero-eyebrow">
      <i class="fa-solid fa-fire"></i>
      <span>Discover · Cook · Share</span>
    </div>

    <h1>
      Discover Delicious<br>
      <span style="opacity:.9;">Recipes Every Day</span>
    </h1>

    <p class="hero-subtitle">
      Find inspiration for every meal — from quick weekday dinners to
      weekend cooking adventures.
    </p>

    <!-- Hero search -->
    <form method="get" action="<?= url('index.php') ?>">
      <div class="hero-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="q" placeholder="Search recipes, ingredients…">
        <select name="category">
          <option value="">All Categories</option>
          <?php foreach ($catRows as $row): ?>
            <option value="<?= e($row['category']) ?>">
              <?= e(ucfirst($row['category'])) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="hero-search-btn">
          <i class="fa-solid fa-magnifying-glass me-1"></i>Search
        </button>
      </div>
    </form>

    <!-- Stats pills -->
    <div class="hero-stats">
      <div class="hero-stat">
        <i class="fa-solid fa-book-open"></i>
        <strong><?= $totalRecipes ?>+</strong> Recipes
      </div>
      <div class="hero-stat">
        <i class="fa-solid fa-tag"></i>
        <strong><?= $totalCats ?>+</strong> Categories
      </div>
      <div class="hero-stat">
        <i class="fa-solid fa-users"></i>
        Community Recipes
      </div>
    </div>

  </div>
</section>

<!-- ============================================================
     CATEGORY GRID
     ============================================================ -->
<?php if ($catRows): ?>
<section class="py-section" style="padding-bottom:40px;">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Browse by Category</span>
      <h2 class="section-title">What are you craving?</h2>
    </div>

    <div class="cat-grid">
      <?php foreach ($catRows as $row):
        $emoji = $catEmoji[strtolower($row['category'])] ?? '🍽️';
      ?>
      <a class="cat-card" href="<?= url('index.php?category=' . urlencode($row['category'])) ?>">
        <div class="cat-icon"><?= $emoji ?></div>
        <span class="cat-label"><?= e(ucfirst($row['category'])) ?></span>
        <span class="cat-count"><?= $row['cnt'] ?> recipe<?= $row['cnt']!='1'?'s':'' ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="divider-fade"></div>
<?php endif; ?>

<!-- ============================================================
     RECIPE GRID
     ============================================================ -->
<section class="py-section">
  <div class="container">

    <!-- Section header -->
    <div class="section-header">
      <?php if ($q !== '' || $category !== ''): ?>
        <span class="section-eyebrow">Search Results</span>
        <h2 class="section-title">
          <?= $recipes ? count($recipes) . ' recipe' . (count($recipes)!==1?'s':'') . ' found' : 'No recipes found' ?>
        </h2>
        <?php if ($q): ?>
          <p class="section-subtitle">for "<?= e($q) ?>"<?= $category ? ' in ' . e(ucfirst($category)) : '' ?></p>
        <?php elseif($category): ?>
          <p class="section-subtitle">in <?= e(ucfirst($category)) ?></p>
        <?php endif; ?>
        <a href="<?= url('index.php') ?>" class="btn-secondary-custom mt-3" style="margin: 12px auto 0; display:inline-flex;">
          <i class="fa-solid fa-xmark"></i> Clear filters
        </a>
      <?php else: ?>
        <span class="section-eyebrow">Fresh & Delicious</span>
        <h2 class="section-title">Latest Recipes</h2>
        <p class="section-subtitle">Recently added by our community</p>
      <?php endif; ?>
    </div>

    <?php if (!$recipes): ?>
      <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <h3>No recipes found</h3>
        <p>Try a different search term or browse all categories above.</p>
        <a href="<?= url('index.php') ?>" class="btn-primary-custom mt-4">
          <i class="fa-solid fa-house"></i> Back to Home
        </a>
      </div>
    <?php else: ?>

      <div class="recipes-grid">
        <?php foreach ($recipes as $r):
          $desc = mb_strimwidth(strip_tags($r['steps'] ?? ''), 0, 100, '…');
          $cat  = $r['category'] ?? '';
          $emoji= $catEmoji[strtolower($cat)] ?? '🍽️';
          $time = guessTime($cat);
          $diff = guessDiff($cat);
          // Pseudo-like count from recipe id for visual variety
          $likes = ($r['id'] * 7 + 3) % 48 + 2;
        ?>
        <div class="recipe-card">

          <!-- Image -->
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

            <?php if ($cat): ?>
            <span class="recipe-cat-badge"><?= e(ucfirst($cat)) ?></span>
            <?php endif; ?>

            <button class="recipe-save-btn" title="Save recipe">
              <i class="fa-regular fa-bookmark"></i>
            </button>
          </div>

          <!-- Body -->
          <div class="recipe-card-body">
            <h3 class="recipe-card-title"><?= e($r['title']) ?></h3>
            <p class="recipe-card-desc"><?= e($desc) ?></p>

            <div class="recipe-meta">
              <span class="recipe-meta-item">
                <i class="fa-regular fa-clock"></i><?= $time ?>
              </span>
              <span class="recipe-meta-item">
                <i class="fa-solid fa-signal"></i><?= $diff ?>
              </span>
              <span class="recipe-meta-item">
                <i class="fa-solid fa-fire"></i>Cal
              </span>
            </div>
          </div>

          <!-- Footer -->
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

    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
