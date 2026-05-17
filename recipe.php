<?php
require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

if (!function_exists('is_logged_in')) {
  function is_logged_in(): bool { return !empty($_SESSION['user']); }
}
if (!function_exists('current_user_id')) {
  function current_user_id() { return $_SESSION['user']['id'] ?? null; }
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
  echo '<div class="container py-5"><div class="empty-state"><div class="empty-state-icon">⚠️</div><h3>Invalid recipe</h3><p>The recipe ID is missing.</p><a href="' . url('index.php') . '" class="btn-primary-custom mt-4">Go Home</a></div></div>';
  include 'includes/footer.php'; exit;
}

$st = $pdo->prepare("SELECT r.*, u.username AS author FROM recipes r LEFT JOIN users u ON u.id = r.author_id WHERE r.id = ?");
$st->execute([$id]);
$r = $st->fetch(PDO::FETCH_ASSOC);

if (!$r) {
  echo '<div class="container py-5"><div class="empty-state"><div class="empty-state-icon">🔍</div><h3>Recipe not found</h3><p>This recipe may have been deleted.</p><a href="' . url('index.php') . '" class="btn-primary-custom mt-4">Go Home</a></div></div>';
  include 'includes/footer.php'; exit;
}

// Post comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && is_logged_in()) {
  $c = trim($_POST['comment']);
  if ($c !== '') {
    $pdo->prepare("INSERT INTO comments (recipe_id, user_id, comment, created_at) VALUES (?,?,?,NOW())")
        ->execute([$id, current_user_id(), $c]);
    header('Location: ' . url('recipe.php?id=' . $id) . '#comments');
    exit;
  }
}

$cs = $pdo->prepare("SELECT c.*, u.username FROM comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.recipe_id = ? ORDER BY c.created_at DESC");
$cs->execute([$id]);
$comments = $cs->fetchAll(PDO::FETCH_ASSOC);

$cat   = $r['category'] ?? '';
$catEmoji = [
  'breakfast'=>'🍳','lunch'=>'🥗','dinner'=>'🍽️','dessert'=>'🍰',
  'desserts'=>'🍰','snack'=>'🍿','snacks'=>'🍿','pizza'=>'🍕',
  'pasta'=>'🍝','soup'=>'🍲','salad'=>'🥗','burger'=>'🍔',
  'chicken'=>'🍗','seafood'=>'🦞','vegan'=>'🥦','vegetarian'=>'🥕',
  'nepali'=>'🫙','indian'=>'🍛','fast food'=>'🍔','healthy'=>'🥑',
];
$emoji = $catEmoji[strtolower($cat)] ?? '🍽️';

// Cook time / difficulty guesses
$timeMap = ['breakfast'=>'15 min','snack'=>'10 min','snacks'=>'10 min','lunch'=>'30 min',
            'dinner'=>'45 min','dessert'=>'40 min','desserts'=>'40 min','baking'=>'60 min',
            'soup'=>'35 min','pasta'=>'25 min','pizza'=>'40 min','salad'=>'10 min',
            'healthy'=>'20 min','fast food'=>'15 min'];
$cookTime = $timeMap[strtolower($cat)] ?? '30 min';

$hardCats = ['baking','dessert','desserts','pizza'];
$easyCats = ['breakfast','salad','snack','snacks','healthy','fast food'];
if (in_array(strtolower($cat), $hardCats))      $difficulty = 'Medium';
elseif (in_array(strtolower($cat), $easyCats)) $difficulty = 'Easy';
else $difficulty = 'Medium';

// Parse ingredients into a list
$ingredientsRaw = $r['ingredients'] ?? '';
$ingredientLines = array_filter(array_map('trim', preg_split('/\r?\n/', $ingredientsRaw)));

// Parse steps into numbered lines
$stepsRaw = $r['steps'] ?? '';
$stepLines = array_filter(array_map('trim', preg_split('/\r?\n/', $stepsRaw)));

$pseudoLikes = ($r['id'] * 7 + 3) % 48 + 2;
$commentCount = count($comments);
?>

<!-- Recipe Hero -->
<?php if (!empty($r['image'])): ?>
<div class="recipe-hero">
  <img src="<?= url('uploads/' . e($r['image'])) ?>" alt="<?= e($r['title']) ?>">
  <div class="recipe-hero-overlay"></div>
  <div class="recipe-hero-content">
    <?php if ($cat): ?>
      <div class="badge-cat"><?= $emoji ?> <?= e(ucfirst($cat)) ?></div>
    <?php endif; ?>
    <h1><?= e($r['title']) ?></h1>
    <div class="recipe-hero-meta">
      <?php if ($r['author']): ?>
      <span><i class="fa-solid fa-user"></i> By <?= e($r['author']) ?></span>
      <?php endif; ?>
      <span><i class="fa-regular fa-clock"></i> <?= $cookTime ?></span>
      <span><i class="fa-solid fa-signal"></i> <?= $difficulty ?></span>
      <span><i class="fa-regular fa-comment"></i> <?= $commentCount ?> comment<?= $commentCount!==1?'s':'' ?></span>
    </div>
  </div>
</div>
<?php else: ?>
<div class="recipe-no-img-header">
  <div class="container">
    <?php if ($cat): ?>
      <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);padding:5px 16px;border-radius:999px;font-size:.8rem;font-weight:700;text-transform:uppercase;margin-bottom:14px;">
        <?= $emoji ?> <?= e(ucfirst($cat)) ?>
      </div>
    <?php endif; ?>
    <h1 style="font-size:clamp(1.6rem,4vw,2.6rem);font-weight:800;margin:0 0 12px;"><?= e($r['title']) ?></h1>
    <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:.88rem;opacity:.9;">
      <?php if ($r['author']): ?><span><i class="fa-solid fa-user me-1"></i><?= e($r['author']) ?></span><?php endif; ?>
      <span><i class="fa-regular fa-clock me-1"></i><?= $cookTime ?></span>
      <span><i class="fa-solid fa-signal me-1"></i><?= $difficulty ?></span>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Detail body -->
<div class="container">
  <div class="recipe-detail-grid">

    <!-- ── Main column ── -->
    <div>

      <!-- Quick stats row -->
      <div class="detail-card" style="padding:20px 24px;">
        <div class="recipe-stats-grid">
          <div class="recipe-stat-item">
            <div class="recipe-stat-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="recipe-stat-value"><?= $cookTime ?></div>
            <div class="recipe-stat-label">Cook Time</div>
          </div>
          <div class="recipe-stat-item">
            <div class="recipe-stat-icon"><i class="fa-solid fa-signal"></i></div>
            <div class="recipe-stat-value"><?= $difficulty ?></div>
            <div class="recipe-stat-label">Difficulty</div>
          </div>
          <div class="recipe-stat-item">
            <div class="recipe-stat-icon"><i class="fa-regular fa-heart"></i></div>
            <div class="recipe-stat-value"><?= $pseudoLikes ?></div>
            <div class="recipe-stat-label">Likes</div>
          </div>
          <div class="recipe-stat-item">
            <div class="recipe-stat-icon"><i class="fa-regular fa-comment"></i></div>
            <div class="recipe-stat-value"><?= $commentCount ?></div>
            <div class="recipe-stat-label">Comments</div>
          </div>
        </div>
      </div>

      <!-- Ingredients -->
      <div class="detail-card">
        <h3><i class="fa-solid fa-list-check"></i> Ingredients</h3>
        <?php if ($ingredientLines): ?>
          <ul class="ingredients-list">
            <?php foreach ($ingredientLines as $ing): ?>
              <li><?= e($ing) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <pre style="white-space:pre-wrap;font-family:var(--font);font-size:.9rem;color:var(--text-main);margin:0;"><?= e($ingredientsRaw) ?></pre>
        <?php endif; ?>
      </div>

      <!-- Steps -->
      <div class="detail-card">
        <h3><i class="fa-solid fa-shoe-prints"></i> Instructions</h3>
        <?php if ($stepLines): ?>
          <ol class="steps-list" style="padding:0;margin:0;">
            <?php $stepNum = 0; foreach ($stepLines as $step): $stepNum++; ?>
              <li>
                <span class="step-num"><?= $stepNum ?></span>
                <span class="step-text"><?= e($step) ?></span>
              </li>
            <?php endforeach; ?>
          </ol>
        <?php else: ?>
          <div style="font-size:.92rem;color:var(--text-main);line-height:1.8;"><?= nl2br(e($stepsRaw)) ?></div>
        <?php endif; ?>
      </div>

      <!-- Action row -->
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:32px;">
        <button class="recipe-likes btn-secondary-custom" style="gap:8px;">
          <i class="fa-regular fa-heart"></i>
          <span>Like this recipe (<span class="like-count"><?= $pseudoLikes ?></span>)</span>
        </button>
        <button class="recipe-save-btn" style="position:static;width:auto;height:auto;border-radius:var(--r-full);padding:11px 20px;background:var(--bg-card);border:1.5px solid var(--border-clr);color:var(--text-muted);font-size:.88rem;font-weight:600;display:flex;align-items:center;gap:8px;cursor:pointer;transition:all var(--t-fast);">
          <i class="fa-regular fa-bookmark"></i> Save
        </button>
        <a href="<?= url('recipes.php') ?>" class="btn-secondary-custom">
          <i class="fa-solid fa-arrow-left"></i> All Recipes
        </a>
      </div>

      <!-- Comments -->
      <div class="detail-card" id="comments">
        <h3><i class="fa-regular fa-comments"></i> Comments (<?= $commentCount ?>)</h3>

        <?php if (is_logged_in()): ?>
        <form method="post" style="margin-bottom:24px;">
          <textarea name="comment" class="comment-textarea"
                    placeholder="Share your thoughts about this recipe…" rows="3"></textarea>
          <div style="margin-top:12px;">
            <button type="submit" class="btn-primary-custom" style="padding:10px 24px;font-size:.88rem;">
              <i class="fa-solid fa-paper-plane"></i> Post Comment
            </button>
          </div>
        </form>
        <?php else: ?>
        <div style="background:var(--bg-body);border:1.5px dashed var(--border-clr);border-radius:var(--r-lg);padding:20px;text-align:center;margin-bottom:24px;">
          <p style="margin:0;color:var(--text-muted);font-size:.9rem;">
            <i class="fa-solid fa-lock me-1"></i>
            Please <a href="<?= url('auth/login.php') ?>" style="color:var(--clr-600);font-weight:700;">log in</a> to leave a comment.
          </p>
        </div>
        <?php endif; ?>

        <?php if ($comments): ?>
          <div>
            <?php foreach ($comments as $c): ?>
            <div class="comment-item">
              <div class="comment-avatar">
                <?= strtoupper(substr($c['username'] ?? 'U', 0, 1)) ?>
              </div>
              <div class="comment-body">
                <div class="comment-header">
                  <span class="comment-author"><?= e($c['username'] ?? 'User') ?></span>
                  <span class="comment-date">
                    <?= date('M j, Y', strtotime($c['created_at'])) ?>
                  </span>
                </div>
                <div class="comment-text"><?= nl2br(e($c['comment'])) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state" style="padding:40px 0;">
            <div class="empty-state-icon" style="font-size:2.5rem;">💬</div>
            <h3 style="font-size:1rem;">No comments yet</h3>
            <p style="font-size:.85rem;">Be the first to share your thoughts!</p>
          </div>
        <?php endif; ?>
      </div>

    </div><!-- /main col -->

    <!-- ── Sidebar ── -->
    <aside>

      <!-- About this recipe -->
      <div class="detail-card">
        <h3><i class="fa-solid fa-circle-info"></i> About</h3>
        <div style="display:flex;flex-direction:column;gap:14px;">

          <?php if ($cat): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Category</span>
            <span style="background:var(--clr-50);color:var(--clr-700);padding:4px 12px;border-radius:var(--r-full);font-size:.78rem;font-weight:700;"><?= e(ucfirst($cat)) ?></span>
          </div>
          <?php endif; ?>

          <?php if ($r['author']): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Added by</span>
            <span style="font-size:.85rem;font-weight:700;color:var(--text-main);"><?= e($r['author']) ?></span>
          </div>
          <?php endif; ?>

          <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Posted</span>
            <span style="font-size:.85rem;color:var(--text-main);"><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
          </div>

          <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Cook Time</span>
            <span style="font-size:.85rem;font-weight:700;color:var(--text-main);"><?= $cookTime ?></span>
          </div>

          <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Difficulty</span>
            <span style="font-size:.85rem;font-weight:700;color:var(--text-main);"><?= $difficulty ?></span>
          </div>

        </div>
      </div>

      <!-- Quick ingredients summary -->
      <div class="detail-card">
        <h3><i class="fa-solid fa-cart-shopping"></i> Ingredients (<?= count($ingredientLines) ?: 'List' ?>)</h3>
        <p style="font-size:.83rem;color:var(--text-muted);margin:0 0 14px;line-height:1.5;">
          <?= count($ingredientLines) ?> ingredients needed for this recipe.
        </p>
        <a href="<?= url('add_recipe.php') ?>" class="btn-primary-custom" style="width:100%;justify-content:center;font-size:.85rem;padding:10px;">
          <i class="fa-solid fa-plus"></i> Add Your Recipe
        </a>
      </div>

      <!-- Share -->
      <div class="detail-card">
        <h3><i class="fa-solid fa-share-nodes"></i> Share</h3>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button onclick="navigator.clipboard&&navigator.clipboard.writeText(window.location.href).then(function(){alert('Link copied!')})"
                  class="btn-secondary-custom" style="font-size:.82rem;padding:9px 16px;">
            <i class="fa-solid fa-link"></i> Copy Link
          </button>
          <button onclick="window.open('https://twitter.com/intent/tweet?text='+encodeURIComponent('Check out this recipe: <?= e($r['title']) ?>')+'&url='+encodeURIComponent(window.location.href),'_blank')"
                  class="btn-secondary-custom" style="font-size:.82rem;padding:9px 16px;">
            <i class="fab fa-twitter"></i> Tweet
          </button>
        </div>
      </div>

    </aside>

  </div><!-- /recipe-detail-grid -->
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
