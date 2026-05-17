</main><!-- /main.main-content -->

<!-- ================================================================
     FOOTER
     ================================================================ -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-5">

      <!-- Brand col -->
      <div class="col-lg-4">
        <div class="footer-brand">
          <span style="width:36px;height:36px;background:var(--hero-grad);border-radius:var(--r-md);display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;margin-right:4px;">🍳</span>
          RecipeApp
        </div>
        <p class="footer-desc">
          Discover, create and share delicious recipes with a community that loves food as much as you do.
        </p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" style="width:34px;height:34px;background:var(--clr-50);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--clr-600);font-size:.85rem;transition:all var(--t-fast);" class="social-icon">
            <i class="fab fa-github"></i>
          </a>
          <a href="#" style="width:34px;height:34px;background:var(--clr-50);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--clr-600);font-size:.85rem;transition:all var(--t-fast);" class="social-icon">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" style="width:34px;height:34px;background:var(--clr-50);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--clr-600);font-size:.85rem;transition:all var(--t-fast);" class="social-icon">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
      </div>

      <!-- Explore -->
      <div class="col-6 col-lg-2">
        <div class="footer-heading">Explore</div>
        <ul class="footer-links">
          <li><a href="<?= url('index.php') ?>">Home</a></li>
          <li><a href="<?= url('recipes.php') ?>">All Recipes</a></li>
          <li><a href="<?= url('add_recipe.php') ?>">Add Recipe</a></li>
          <li><a href="<?= url('about.php') ?>">About</a></li>
        </ul>
      </div>

      <!-- Categories -->
      <div class="col-6 col-lg-2">
        <div class="footer-heading">Categories</div>
        <ul class="footer-links">
          <li><a href="<?= url('index.php?category=breakfast') ?>">Breakfast</a></li>
          <li><a href="<?= url('index.php?category=lunch') ?>">Lunch</a></li>
          <li><a href="<?= url('index.php?category=dinner') ?>">Dinner</a></li>
          <li><a href="<?= url('index.php?category=dessert') ?>">Desserts</a></li>
        </ul>
      </div>

      <!-- Built with -->
      <div class="col-lg-4">
        <div class="footer-heading">Built With</div>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach(['PHP','MySQL','Bootstrap 5','Font Awesome','Google Fonts'] as $tech): ?>
          <span style="background:var(--clr-50);color:var(--clr-700);padding:4px 12px;border-radius:var(--r-full);font-size:.75rem;font-weight:700;border:1px solid var(--clr-100);">
            <?= $tech ?>
          </span>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <div>© <?= date('Y') ?> RecipeApp. Built with <span style="color:#ef4444;">❤</span> by Kushal.</div>
      <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;">
        <i class="fa-solid fa-palette" style="color:var(--clr-500);"></i>
        <span>Use the customizer <i class="fa-solid fa-arrow-right" style="font-size:.7rem;"></i> to change the theme</span>
      </div>
    </div>
  </div>
</footer>

<!-- ================================================================
     BACK TO TOP
     ================================================================ -->
<button id="backToTop" class="back-to-top" aria-label="Back to top">
  <i class="fa-solid fa-arrow-up"></i>
</button>

<!-- ================================================================
     LIVE TEMPLATE CUSTOMIZER
     ================================================================ -->

<!-- Backdrop -->
<div id="customizerOverlay" class="customizer-overlay"></div>

<!-- Trigger button (right edge) -->
<button id="customizerToggle" class="customizer-toggle" title="Customize Theme">
  <i class="fa-solid fa-palette"></i>
  <span class="customizer-toggle-label">Style</span>
</button>

<!-- Slide-in panel -->
<div id="customizerPanel" class="customizer-panel">

  <div class="customizer-panel-header">
    <h6><i class="fa-solid fa-sliders"></i> Customize</h6>
    <button id="customizerClose" class="customizer-close" aria-label="Close">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div class="customizer-body" id="customizerBody">

    <!-- ─── Theme Mode ─── -->
    <div>
      <div class="cust-section-label">Theme Mode</div>
      <div class="cust-chips" data-group="theme">
        <button class="cust-chip" data-val="light">
          <i class="fa-regular fa-sun me-1"></i>Light
        </button>
        <button class="cust-chip" data-val="dark">
          <i class="fa-regular fa-moon me-1"></i>Dark
        </button>
        <button class="cust-chip" data-val="amoled">
          <i class="fa-solid fa-circle me-1"></i>AMOLED
        </button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- ─── Color ─── -->
    <div>
      <div class="cust-section-label">Accent Color</div>
      <div class="cust-colors" data-group="color">
        <button class="cust-color-swatch" data-val="indigo"
                style="background:hsl(239,80%,57%);" title="Indigo"></button>
        <button class="cust-color-swatch" data-val="purple"
                style="background:hsl(270,75%,65%);" title="Purple"></button>
        <button class="cust-color-swatch" data-val="teal"
                style="background:hsl(175,70%,41%);" title="Teal"></button>
        <button class="cust-color-swatch" data-val="green"
                style="background:hsl(142,65%,39%);" title="Green"></button>
        <button class="cust-color-swatch" data-val="orange"
                style="background:hsl(25,95%,53%);" title="Orange"></button>
        <button class="cust-color-swatch" data-val="red"
                style="background:hsl(0,78%,60%);" title="Red"></button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- ─── Card Style ─── -->
    <div>
      <div class="cust-section-label">Card Style</div>
      <div class="cust-chips" data-group="card">
        <button class="cust-chip" data-val="default">Default</button>
        <button class="cust-chip" data-val="glass">
          <i class="fa-solid fa-droplet me-1"></i>Glass
        </button>
        <button class="cust-chip" data-val="flat">Flat</button>
        <button class="cust-chip" data-val="elevated">Elevated</button>
        <button class="cust-chip" data-val="bordered">Bordered</button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- ─── Navbar ─── -->
    <div>
      <div class="cust-section-label">Navbar Style</div>
      <div class="cust-chips" data-group="navbar">
        <button class="cust-chip" data-val="solid">Solid</button>
        <button class="cust-chip" data-val="glass">
          <i class="fa-solid fa-droplet me-1"></i>Glass
        </button>
        <button class="cust-chip" data-val="floating">Floating</button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- ─── Layout ─── -->
    <div>
      <div class="cust-section-label">Content Width</div>
      <div class="cust-chips" data-group="layout">
        <button class="cust-chip" data-val="compact">Compact</button>
        <button class="cust-chip" data-val="comfortable">Comfortable</button>
        <button class="cust-chip" data-val="wide">Wide</button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- ─── Font ─── -->
    <div>
      <div class="cust-section-label">Typography</div>
      <div class="cust-chips" data-group="font">
        <button class="cust-chip" data-val="poppins" style="font-family:'Poppins',sans-serif;">Poppins</button>
        <button class="cust-chip" data-val="inter"   style="font-family:'Inter',sans-serif;">Inter</button>
        <button class="cust-chip" data-val="roboto"  style="font-family:'Roboto',sans-serif;">Roboto</button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- ─── Animations ─── -->
    <div>
      <div class="cust-section-label">Animations</div>
      <div class="cust-chips" data-group="animations">
        <button class="cust-chip" data-val="on">
          <i class="fa-solid fa-bolt me-1"></i>Enabled
        </button>
        <button class="cust-chip" data-val="off">Disabled</button>
      </div>
    </div>

    <hr style="border-color:var(--divider);margin:0;">

    <!-- Reset -->
    <button id="customizerReset" class="cust-reset">
      <i class="fa-solid fa-rotate-left me-2"></i>Reset to Default
    </button>

    <!-- Live preview note -->
    <p style="font-size:.75rem;color:var(--text-light);text-align:center;margin:0;line-height:1.5;">
      Changes apply instantly &amp; are saved to your browser.
    </p>

  </div>
</div>

<!-- ================================================================
     SCRIPTS
     ================================================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
  'use strict';

  /* ── Color preset map ── */
  var COLOR_MAP = {
    indigo: { hue: 239, sat: '80%' },
    purple: { hue: 270, sat: '75%' },
    teal:   { hue: 175, sat: '70%' },
    green:  { hue: 142, sat: '65%' },
    orange: { hue: 25,  sat: '95%' },
    red:    { hue: 0,   sat: '78%' }
  };

  /* ── Default settings ── */
  var DEFAULTS = {
    theme:      'light',
    color:      'indigo',
    card:       'default',
    navbar:     'solid',
    layout:     'comfortable',
    font:       'poppins',
    animations: 'on'
  };

  /* ── Load / Save ── */
  function loadSettings() {
    try {
      return Object.assign({}, DEFAULTS, JSON.parse(localStorage.getItem('rcpSettings') || '{}'));
    } catch(e) { return Object.assign({}, DEFAULTS); }
  }

  function saveSettings(s) {
    try { localStorage.setItem('rcpSettings', JSON.stringify(s)); } catch(e) {}
  }

  /* ── Apply a single setting to <html> ── */
  function applySetting(key, val) {
    var root = document.documentElement;
    root.setAttribute('data-' + key, val);

    if (key === 'color') {
      var c = COLOR_MAP[val] || COLOR_MAP.indigo;
      root.style.setProperty('--hue', c.hue);
      root.style.setProperty('--sat', c.sat);
    }
  }

  /* ── Apply full settings object ── */
  function applyAll(s) {
    Object.keys(s).forEach(function(k) { applySetting(k, s[k]); });
  }

  /* ── Sync UI chips/swatches to current settings ── */
  function syncUI(s) {
    document.querySelectorAll('[data-group]').forEach(function(group) {
      var key = group.getAttribute('data-group');
      var val = s[key];
      group.querySelectorAll('[data-val]').forEach(function(btn) {
        if (btn.dataset.val === val) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });
    });
  }

  /* ── Customizer open/close ── */
  var panel   = document.getElementById('customizerPanel');
  var overlay = document.getElementById('customizerOverlay');
  var toggle  = document.getElementById('customizerToggle');
  var close   = document.getElementById('customizerClose');

  function openCustomizer() {
    panel.classList.add('open');
    overlay.classList.add('visible');
    document.body.style.overflow = 'hidden';
  }

  function closeCustomizer() {
    panel.classList.remove('open');
    overlay.classList.remove('visible');
    document.body.style.overflow = '';
  }

  if (toggle)  toggle.addEventListener('click', openCustomizer);
  if (close)   close.addEventListener('click',  closeCustomizer);
  if (overlay) overlay.addEventListener('click', closeCustomizer);

  /* ── Chip / swatch click handler ── */
  document.querySelectorAll('[data-group]').forEach(function(group) {
    group.addEventListener('click', function(e) {
      var btn = e.target.closest('[data-val]');
      if (!btn) return;

      var key = group.getAttribute('data-group');
      var val = btn.getAttribute('data-val');

      var s = loadSettings();
      s[key] = val;
      saveSettings(s);
      applySetting(key, val);
      syncUI(s);
    });
  });

  /* ── Reset ── */
  var resetBtn = document.getElementById('customizerReset');
  if (resetBtn) {
    resetBtn.addEventListener('click', function() {
      saveSettings(DEFAULTS);
      applyAll(DEFAULTS);
      syncUI(DEFAULTS);
    });
  }

  /* ── Init on load ── */
  var initial = loadSettings();
  applyAll(initial);
  syncUI(initial);

  /* ── Back to top ── */
  var btt = document.getElementById('backToTop');
  if (btt) {
    window.addEventListener('scroll', function() {
      btt.classList.toggle('visible', window.scrollY > 300);
    });
    btt.addEventListener('click', function() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── Recipe like toggle (client-side only — no backend required) ── */
  document.addEventListener('click', function(e) {
    var likeBtn = e.target.closest('.recipe-likes');
    if (!likeBtn) return;
    likeBtn.classList.toggle('liked');
    var icon = likeBtn.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-regular');
      icon.classList.toggle('fa-solid');
    }
    var count = likeBtn.querySelector('.like-count');
    if (count) {
      var n = parseInt(count.textContent) || 0;
      count.textContent = likeBtn.classList.contains('liked') ? n+1 : Math.max(0, n-1);
    }
  });

  /* ── Recipe save toggle ── */
  document.addEventListener('click', function(e) {
    var saveBtn = e.target.closest('.recipe-save-btn');
    if (!saveBtn) return;
    saveBtn.classList.toggle('saved');
    var icon = saveBtn.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-regular');
      icon.classList.toggle('fa-solid');
    }
  });

  /* ── Navbar scroll effect ── */
  var nav = document.querySelector('.site-nav');
  if (nav) {
    window.addEventListener('scroll', function() {
      nav.style.boxShadow = window.scrollY > 10
        ? '0 2px 20px rgba(0,0,0,0.1)'
        : '0 1px 12px rgba(0,0,0,0.06)';
    });
  }

})();
</script>
</body>
</html>
