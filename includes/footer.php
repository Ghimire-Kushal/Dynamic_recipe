    </main>
    <footer class="border-top bg-white">
      <div class="container py-4 small text-muted d-flex justify-content-between align-items-center">
        <div>© <?= date('Y') ?> Recipe App</div>
        <div>Built with PHP & MySQL</div>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <button type="button" class="btn btn-primary rounded-circle shadow-lg back-to-top" id="backToTop" aria-label="Back to top">
      ↑
    </button>
    <script>
      (function () {
        const body = document.body;
        const toggle = document.getElementById('themeToggle');
        const backToTop = document.getElementById('backToTop');

        const applyTheme = function (theme) {
          if (!body) return;
          body.setAttribute('data-theme', theme);
          const prefersDark = theme === 'light';

          body.classList.toggle('bg-light', prefersDark);
          body.classList.toggle('bg-light', !prefersDark);

          if (toggle) {
            const lightIcon = toggle.querySelector('.theme-icon-light');
            const darkIcon = toggle.querySelector('.theme-icon-dark');
            if (lightIcon && darkIcon) {
              if (prefersDark) {
                lightIcon.classList.add('d-none');
                darkIcon.classList.remove('d-none');
              } else {
                lightIcon.classList.remove('d-none');
                darkIcon.classList.add('d-none');
              }
            }
          }
        };

        try {
          let initial = localStorage.getItem('theme');
          if (!initial) {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
              initial = 'dark';
            } else {
              initial = 'light';
            }
          }
          applyTheme(initial);

          if (toggle) {
            toggle.addEventListener('click', function () {
              const current = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
              const next = current === 'dark' ? 'light' : 'dark';
              localStorage.setItem('theme', next);
              applyTheme(next);
            });
          }
        } catch (e) {
          // Fallback without localStorage
          applyTheme('light');
        }

        if (backToTop) {
          window.addEventListener('scroll', function () {
            if (window.scrollY > 250) {
              backToTop.classList.add('back-to-top--visible');
            } else {
              backToTop.classList.remove('back-to-top--visible');
            }
          });
          backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
          });
        }
      })();
    </script>
  </body>
</html>
