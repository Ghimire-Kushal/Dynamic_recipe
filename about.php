<?php
require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
?>

<!-- Page intro -->
<section class="page-intro">
  <div class="container">
    <h1><i class="fa-solid fa-circle-info me-2" style="opacity:.85;"></i>About RecipeApp</h1>
    <p>A modern PHP & MySQL recipe platform — built for food lovers</p>
  </div>
</section>

<div class="container" style="padding-top:60px;padding-bottom:80px;">

  <!-- Hero row -->
  <div class="row align-items-start g-5 mb-5">
    <div class="col-lg-7">
      <span class="section-eyebrow" style="margin-bottom:12px;display:inline-block;">About the Project</span>
      <h2 class="section-title" style="text-align:left;margin-bottom:16px;">
        A recipe app built with<br>
        <span class="text-gradient">modern web standards</span>
      </h2>
      <p style="color:var(--text-muted);line-height:1.8;font-size:.97rem;margin-bottom:24px;">
        RecipeApp is a full-stack PHP & MySQL application for discovering, sharing, and managing
        recipes. It features full-text search, category filtering, image uploads, comments,
        a live theme customizer, and a clean admin dashboard.
      </p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a class="btn-primary-custom" href="<?= url('recipes.php') ?>">
          <i class="fa-solid fa-bowl-food"></i> Browse Recipes
        </a>
        <a class="btn-secondary-custom" href="<?= url('add_recipe.php') ?>">
          <i class="fa-solid fa-plus"></i> Add Your Recipe
        </a>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="detail-card" style="margin-bottom:0;">
        <h3><i class="fa-solid fa-bolt"></i> Tech Stack</h3>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php
          $stack = [
            ['PHP 8',        'fa-brands fa-php',      'Backend language'],
            ['MySQL / PDO',  'fa-solid fa-database',   'Database & queries'],
            ['Bootstrap 5',  'fa-brands fa-bootstrap', 'Layout & grid'],
            ['Font Awesome', 'fa-solid fa-icons',      'Icon library'],
            ['Poppins / Inter','fa-solid fa-font',     'Typography'],
            ['CSS Custom Props','fa-solid fa-palette', 'Live theme system'],
          ];
          foreach ($stack as [$name, $icon, $desc]):
          ?>
          <div style="display:flex;align-items:center;gap:14px;padding:10px 14px;background:var(--bg-body);border-radius:var(--r-lg);border:1px solid var(--divider);">
            <div style="width:36px;height:36px;background:var(--clr-50);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:var(--clr-600);font-size:.95rem;flex-shrink:0;">
              <i class="<?= $icon ?>"></i>
            </div>
            <div>
              <div style="font-size:.88rem;font-weight:700;color:var(--text-main);"><?= $name ?></div>
              <div style="font-size:.76rem;color:var(--text-light);"><?= $desc ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Feature cards -->
  <div class="row g-4 mb-5">
    <?php
    $features = [
      ['🔍','Full-text Search','Search recipes by title, ingredients, or steps with real-time filtering.'],
      ['🏷️','Categories','Browse recipes organized by meal type, cuisine, and more.'],
      ['🖼️','Image Uploads','Add beautiful photos to your recipes — JPG, PNG, WEBP supported.'],
      ['💬','Comments','Leave feedback and share tips on any recipe.'],
      ['🎨','Live Customizer','Instantly switch themes, colors, card styles, and fonts.'],
      ['🔐','Auth System','Secure login, registration, and role-based access control.'],
    ];
    foreach ($features as [$emoji, $title, $desc]):
    ?>
    <div class="col-md-6 col-lg-4">
      <div class="detail-card" style="height:100%;margin-bottom:0;">
        <div style="font-size:2rem;margin-bottom:12px;"><?= $emoji ?></div>
        <h4 style="font-size:1rem;font-weight:800;color:var(--text-main);margin-bottom:8px;"><?= $title ?></h4>
        <p style="font-size:.86rem;color:var(--text-muted);margin:0;line-height:1.6;"><?= $desc ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Security + Contact row -->
  <div class="row g-4">
    <div class="col-md-6">
      <div class="detail-card" style="height:100%;">
        <h3><i class="fa-solid fa-shield-halved"></i> Security</h3>
        <ul style="padding-left:0;margin:0;list-style:none;display:flex;flex-direction:column;gap:10px;">
          <?php foreach([
            'Password hashing with <code>password_hash()</code>',
            'Prepared statements (PDO) against SQL injection',
            'CSRF tokens on all admin actions',
            'Strict file-type validation on image uploads',
            'Session-based auth with role checks',
          ] as $item): ?>
          <li style="display:flex;align-items:flex-start;gap:10px;font-size:.87rem;color:var(--text-muted);">
            <i class="fa-solid fa-check" style="color:var(--clr-500);margin-top:3px;flex-shrink:0;"></i>
            <span><?= $item ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="col-md-6">
      <div class="detail-card" style="height:100%;">
        <h3><i class="fa-solid fa-envelope"></i> Contact</h3>
        <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:20px;line-height:1.7;">
          Have feedback, found a bug, or want to collaborate? Feel free to reach out!
        </p>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <a href="mailto:kushal.upr@gmail.com"
             style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--bg-body);border-radius:var(--r-lg);border:1px solid var(--divider);color:var(--text-main);text-decoration:none;font-size:.87rem;font-weight:600;transition:all var(--t-fast);">
            <i class="fa-solid fa-envelope" style="color:var(--clr-500);"></i>
            kushal.upr@gmail.com
          </a>
          <a href="<?= url('index.php') ?>" class="btn-secondary-custom" style="justify-content:center;">
            <i class="fa-solid fa-house"></i> Back to Home
          </a>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
