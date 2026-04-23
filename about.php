<?php
// Load database connection (even though this page doesn’t query the DB,
// it ensures consistency with other pages and allows future expansion)
require __DIR__ . '/config/db.php';

// Include the header file (navigation bar, metadata, global layout)
include __DIR__ . '/includes/header.php';
?>

<!-- Main page container -->
<div class="container py-5">

  <!-- ABOUT SECTION -->
  <div class="row align-items-center mb-5">
    <div class="col-lg-7">

      <!-- Page title -->
      <h1 class="display-6 fw-bold mb-3">About Recipe App</h1>

      <!-- Short description of what the Recipe App is -->
      <p class="lead text-muted">
        Recipe App is a simple, modern PHP & MySQL application for discovering, sharing, and managing recipes.
        It features full-text search, categories, image uploads, comments, and a clean admin dashboard.
      </p>

      <!-- Buttons to navigate to main features -->
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-primary" href="<?= url('recipes.php') ?>">Browse Recipes</a>
        <a class="btn btn-outline-secondary" href="<?= url('add_recipe.php') ?>">Add Your Recipe</a>
      </div>
    </div>

    <!-- Quick Facts Card -->
    <div class="col-lg-5 mt-4 mt-lg-0">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <h5 class="mb-3">Quick facts</h5>

          <!-- General information list about the application -->
          <ul class="list-unstyled small mb-0">
            <li><strong>Stack:</strong> PHP 8, MySQL (PDO), Bootstrap 5</li>
            <li><strong>Features:</strong> Auth, search, categories, uploads, comments</li>
            <li><strong>Admin:</strong> Manage recipes (edit/delete) with CSRF protection</li>
            <li><strong>Version:</strong> 1.0.0</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- HOW IT WORKS + SECURITY SECTION -->
  <div class="row g-4">

    <!-- How the application works -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-4">
        <div class="card-body">
          <h4 class="mb-3">How it works</h4>

          <!-- Steps explaining how users interact with the app -->
          <ol class="mb-0 text-muted">
            <li>Search or filter by category to find a recipe.</li>
            <li>Open a recipe to view ingredients, steps, and comments.</li>
            <li>Create an account and add your own recipes with images.</li>
            <li>Admins can edit or remove any recipe in the dashboard.</li>
          </ol>
        </div>
      </div>
    </div>

    <!-- Security Best Practices -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-4">
        <div class="card-body">
          <h4 class="mb-3">Security & Best Practices</h4>

          <!-- Highlights of security implementations used in the app -->
          <ul class="mb-0 text-muted">
            <li>Password hashing with <code>password_hash()</code>.</li>
            <li>Prepared statements (PDO) to prevent SQL injection.</li>
            <li>CSRF tokens for destructive/admin actions.</li>
            <li>File type checks on image uploads.</li>
          </ul>
        </div>
      </div>
    </div>

  </div>

  <!-- CREDITS + CONTACT SECTION -->
  <div class="row g-4 mt-1">

    <!-- Credits card -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-4">
        <div class="card-body">
          <h4 class="mb-3">Credits</h4>
          <p class="text-muted mb-0">
            Built with ❤️ by the Recipe App team. UI powered by Bootstrap 5.
            Data stored in MySQL and accessed via PDO.
          </p>
        </div>
      </div>
    </div>

    <!-- Contact information -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 rounded-4">
        <div class="card-body">
          <h4 class="mb-3">Contact</h4>

          <!-- Short message and email -->
          <p class="text-muted">Have feedback or found a bug? We’d love to hear from you.</p>

          <ul class="list-unstyled small mb-3 text-muted">
            <li><strong>Email:</strong> 
              <a href="mailto:kushal.upr@gmail.com">kushal.upr@gmail.com</a>
            </li>
          </ul>

          <!-- Button to return to home -->
          <a class="btn btn-outline-primary" href="<?= url('index.php') ?>">Back to Home</a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php 
// Include the footer (closing tags, scripts, layout end)
include __DIR__ . '/includes/footer.php'; 
?>
