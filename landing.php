<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/config/db.php';
$currentUser = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RecipeApp — The Smart Recipe Platform</title>
  <meta name="description" content="Discover, create, and share delicious recipes. The all-in-one platform for home cooks and food enthusiasts.">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:title"       content="RecipeApp — The Smart Recipe Platform">
  <meta property="og:description" content="Discover, create, and share delicious recipes. The all-in-one platform for home cooks and food enthusiasts.">
  <meta property="og:image"       content="<?= url('public/og-image.png') ?>">
  <meta property="og:url"         content="http://<?= e($_SERVER['HTTP_HOST'] ?? 'localhost') ?><?= e($_SERVER['REQUEST_URI'] ?? '/') ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="RecipeApp — The Smart Recipe Platform">
  <meta name="twitter:description" content="Discover, create, and share delicious recipes. The all-in-one platform for home cooks and food enthusiasts.">
  <meta name="twitter:image"       content="<?= url('public/og-image.png') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= url('public/style.css') ?>" rel="stylesheet">

  <script>
    (function(){
      var s = {};
      try { s = JSON.parse(localStorage.getItem('rcpSettings') || '{}'); } catch(e){}
      var b = document.documentElement;
      if(s.theme)  b.setAttribute('data-theme',  s.theme);
      if(s.color)  b.setAttribute('data-color',  s.color);
      if(s.font)   b.setAttribute('data-font',   s.font);
      var hues={indigo:239,green:142,orange:25,red:0,purple:270,teal:175};
      var sats={indigo:'80%',green:'65%',orange:'95%',red:'78%',purple:'75%',teal:'70%'};
      var c = s.color || 'indigo';
      if(hues[c]) {
        b.style.setProperty('--hue', hues[c]);
        b.style.setProperty('--sat', sats[c]);
      }
    })();
  </script>

  <style>
    /* ── Landing-page-only styles ── */
    .lp-nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      background: var(--bg-nav);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border-clr);
      padding: .9rem 0;
      transition: background var(--t-base);
    }
    .lp-nav .navbar-brand {
      font-weight: 800;
      font-size: 1.3rem;
      color: var(--clr-600);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: .4rem;
    }
    .lp-nav-links a {
      color: var(--text-main);
      text-decoration: none;
      font-size: .9rem;
      font-weight: 500;
      padding: .35rem .7rem;
      border-radius: var(--r-full);
      transition: background var(--t-fast), color var(--t-fast);
    }
    .lp-nav-links a:hover { background: var(--clr-50); color: var(--clr-600); }

    /* Hero */
    .lp-hero {
      min-height: 100vh;
      min-height: 100svh;
      display: flex;
      align-items: center;
      padding-top: 100px;
      padding-bottom: 60px;
      background:
        radial-gradient(ellipse 80% 60% at 60% -10%, hsla(var(--hue), 80%, 70%, .22) 0%, transparent 70%),
        radial-gradient(ellipse 50% 40% at 10% 80%, hsla(calc(var(--hue)+40), 80%, 65%, .15) 0%, transparent 60%),
        var(--bg-body);
      overflow: hidden;
      position: relative;
    }
    .lp-hero::before {
      content: '';
      position: absolute;
      width: 600px; height: 600px;
      border-radius: 50%;
      background: hsla(var(--hue), var(--sat), 57%, .07);
      top: -200px; right: -100px;
      pointer-events: none;
    }
    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      background: hsl(var(--hue), 80%, 95%);
      color: var(--clr-700);
      border: 1px solid hsl(var(--hue), 80%, 85%);
      border-radius: var(--r-full);
      padding: .3rem .85rem;
      font-size: .78rem;
      font-weight: 600;
      letter-spacing: .03em;
      margin-bottom: 1.25rem;
    }
    [data-theme="dark"] .hero-badge {
      background: hsl(var(--hue), 60%, 18%);
      color: var(--clr-300);
      border-color: hsl(var(--hue), 60%, 30%);
    }
    .hero-title {
      font-size: clamp(2.4rem, 5vw, 4rem);
      font-weight: 800;
      line-height: 1.12;
      color: var(--text-main);
      margin-bottom: 1.25rem;
    }
    .hero-title .highlight {
      background: var(--hero-grad);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .hero-sub {
      font-size: 1.1rem;
      color: var(--text-muted);
      max-width: 500px;
      line-height: 1.7;
      margin-bottom: 2rem;
    }
    .hero-cta-group { display: flex; gap: .75rem; flex-wrap: wrap; align-items: center; }
    .btn-hero-primary {
      background: var(--hero-grad);
      color: #fff;
      border: none;
      border-radius: var(--r-full);
      padding: .85rem 2rem;
      font-size: 1rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      box-shadow: 0 6px 24px hsla(var(--hue), var(--sat), 57%, .38);
      transition: transform var(--t-fast), box-shadow var(--t-fast);
    }
    .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 32px hsla(var(--hue), var(--sat), 57%, .50); color: #fff; }
    .btn-hero-ghost {
      background: transparent;
      color: var(--text-main);
      border: 2px solid var(--border-clr);
      border-radius: var(--r-full);
      padding: .8rem 1.75rem;
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      transition: border-color var(--t-fast), background var(--t-fast);
    }
    .btn-hero-ghost:hover { border-color: var(--clr-400); background: var(--clr-50); color: var(--text-main); }
    .hero-stats { display: flex; gap: 2.5rem; margin-top: 2.5rem; flex-wrap: wrap; }
    .hero-stat-num {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--text-main);
      line-height: 1;
    }
    .hero-stat-lbl { font-size: .78rem; color: var(--text-muted); margin-top: .2rem; }

    /* Hero mockup */
    .hero-mockup {
      position: relative;
      background: var(--bg-card);
      border-radius: var(--r-2xl);
      box-shadow: var(--shadow-xl), 0 0 0 1px var(--border-clr);
      overflow: hidden;
      aspect-ratio: 4/3;
    }
    .mockup-bar {
      background: var(--clr-50);
      padding: .6rem 1rem;
      display: flex;
      align-items: center;
      gap: .5rem;
      border-bottom: 1px solid var(--border-clr);
    }
    [data-theme="dark"] .mockup-bar { background: rgba(255,255,255,.04); }
    .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
    .mockup-body { padding: 1.25rem; }
    .mockup-search {
      background: var(--bg-body);
      border-radius: var(--r-full);
      padding: .5rem 1rem;
      font-size: .8rem;
      color: var(--text-muted);
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }
    .mockup-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; }
    .mockup-card {
      background: var(--bg-body);
      border-radius: var(--r-md);
      overflow: hidden;
      box-shadow: var(--shadow-xs);
    }
    .mockup-thumb {
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
    .mockup-card-body { padding: .4rem .5rem; }
    .mockup-card-title { font-size: .68rem; font-weight: 600; color: var(--text-main); }
    .mockup-card-sub { font-size: .6rem; color: var(--text-muted); margin-top: .1rem; }

    /* Logos bar */
    .lp-logos {
      padding: 2.5rem 0;
      border-top: 1px solid var(--divider);
      border-bottom: 1px solid var(--divider);
      background: var(--bg-body);
    }
    .logos-label { font-size: .8rem; font-weight: 600; color: var(--text-light); letter-spacing: .08em; text-transform: uppercase; }
    .logo-item { font-size: .95rem; font-weight: 700; color: var(--text-light); opacity: .6; white-space: nowrap; }

    /* Section shared */
    .lp-section { padding: 100px 0; }
    .section-eyebrow {
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--clr-500);
      margin-bottom: .5rem;
    }
    .section-title {
      font-size: clamp(1.8rem, 3.5vw, 2.6rem);
      font-weight: 800;
      color: var(--text-main);
      line-height: 1.2;
      margin-bottom: 1rem;
    }
    .section-sub { font-size: 1.05rem; color: var(--text-muted); max-width: 520px; line-height: 1.7; }

    /* Features */
    .lp-features { background: var(--bg-body); }
    .feature-card {
      background: var(--bg-card);
      border-radius: var(--r-xl);
      padding: 2rem;
      border: 1px solid var(--border-clr);
      box-shadow: var(--shadow-sm);
      height: 100%;
      transition: transform var(--t-base), box-shadow var(--t-base);
    }
    .feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
    .feature-icon {
      width: 54px; height: 54px;
      border-radius: var(--r-lg);
      background: hsl(var(--hue), 80%, 95%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: var(--clr-600);
      margin-bottom: 1.25rem;
    }
    [data-theme="dark"] .feature-icon { background: hsl(var(--hue), 60%, 18%); color: var(--clr-400); }
    .feature-title { font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: .5rem; }
    .feature-desc { font-size: .9rem; color: var(--text-muted); line-height: 1.65; }

    /* Demo */
    .lp-demo { background: var(--bg-card); }
    .demo-tab-list { display: flex; gap: .5rem; margin-bottom: 2rem; flex-wrap: wrap; }
    .demo-tab {
      background: transparent;
      border: 2px solid var(--border-clr);
      border-radius: var(--r-full);
      padding: .45rem 1.1rem;
      font-size: .85rem;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all var(--t-fast);
    }
    .demo-tab.active, .demo-tab:hover {
      background: var(--hero-grad);
      color: #fff;
      border-color: transparent;
    }
    .demo-panel { display: none; }
    .demo-panel.active { display: block; }
    .demo-screen {
      background: var(--bg-body);
      border-radius: var(--r-xl);
      border: 1px solid var(--border-clr);
      padding: 2rem;
      min-height: 340px;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .demo-row {
      display: flex;
      align-items: center;
      gap: 1rem;
      background: var(--bg-card);
      border-radius: var(--r-md);
      padding: .8rem 1rem;
      box-shadow: var(--shadow-xs);
    }
    .demo-emoji { font-size: 1.6rem; flex-shrink: 0; }
    .demo-row-title { font-size: .9rem; font-weight: 600; color: var(--text-main); }
    .demo-row-sub { font-size: .78rem; color: var(--text-muted); margin-top: .1rem; }
    .demo-badge {
      margin-left: auto;
      font-size: .72rem;
      font-weight: 700;
      padding: .2rem .65rem;
      border-radius: var(--r-full);
    }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-orange { background: #fff7ed; color: #9a3412; }
    .badge-blue { background: var(--clr-50); color: var(--clr-700); }
    [data-theme="dark"] .badge-green  { background: rgba(22,163,74,.2);  color: #86efac; }
    [data-theme="dark"] .badge-orange { background: rgba(234,88,12,.2);   color: #fdba74; }
    [data-theme="dark"] .badge-blue   { background: hsl(var(--hue),60%,18%); color: var(--clr-300); }

    /* Pricing */
    .lp-pricing { background: var(--bg-body); }
    .pricing-card {
      background: var(--bg-card);
      border-radius: var(--r-xl);
      border: 2px solid var(--border-clr);
      padding: 2.25rem;
      height: 100%;
      transition: transform var(--t-base), box-shadow var(--t-base), border-color var(--t-base);
    }
    .pricing-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
    .pricing-card.popular {
      border-color: var(--clr-500);
      box-shadow: var(--shadow-glow);
    }
    .popular-badge {
      display: inline-block;
      background: var(--hero-grad);
      color: #fff;
      font-size: .72rem;
      font-weight: 700;
      padding: .25rem .75rem;
      border-radius: var(--r-full);
      margin-bottom: 1rem;
      letter-spacing: .04em;
    }
    .pricing-plan-name { font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: .3rem; }
    .pricing-price { font-size: 2.4rem; font-weight: 800; color: var(--text-main); line-height: 1; margin: .75rem 0 .3rem; }
    .pricing-price span { font-size: 1rem; font-weight: 500; color: var(--text-muted); }
    .pricing-desc { font-size: .85rem; color: var(--text-muted); margin-bottom: 1.5rem; }
    .pricing-features { list-style: none; padding: 0; margin: 0 0 2rem; }
    .pricing-features li {
      display: flex;
      align-items: center;
      gap: .6rem;
      font-size: .88rem;
      color: var(--text-main);
      padding: .45rem 0;
      border-bottom: 1px solid var(--divider);
    }
    .pricing-features li:last-child { border-bottom: none; }
    .pricing-features .check { color: var(--clr-500); font-size: .85rem; }
    .pricing-features .cross { color: var(--text-light); font-size: .85rem; }
    .btn-plan {
      display: block;
      text-align: center;
      border-radius: var(--r-full);
      padding: .75rem;
      font-size: .95rem;
      font-weight: 700;
      text-decoration: none;
      transition: all var(--t-fast);
    }
    .btn-plan-outline {
      border: 2px solid var(--clr-500);
      color: var(--clr-600);
      background: transparent;
    }
    .btn-plan-outline:hover { background: var(--clr-50); color: var(--clr-700); }
    .btn-plan-fill {
      background: var(--hero-grad);
      color: #fff;
      border: none;
      box-shadow: 0 4px 16px hsla(var(--hue), var(--sat), 57%, .35);
    }
    .btn-plan-fill:hover { box-shadow: 0 6px 24px hsla(var(--hue), var(--sat), 57%, .5); color: #fff; }

    /* FAQ */
    .lp-faq { background: var(--bg-card); }
    .faq-item {
      border: 1px solid var(--border-clr);
      border-radius: var(--r-lg);
      margin-bottom: .75rem;
      overflow: hidden;
    }
    .faq-q {
      width: 100%;
      background: transparent;
      border: none;
      padding: 1.1rem 1.4rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: .95rem;
      font-weight: 600;
      color: var(--text-main);
      cursor: pointer;
      text-align: left;
      gap: 1rem;
      transition: background var(--t-fast);
    }
    .faq-q:hover { background: var(--clr-50); }
    [data-theme="dark"] .faq-q:hover { background: rgba(255,255,255,.04); }
    .faq-icon { flex-shrink: 0; color: var(--clr-500); transition: transform var(--t-base); }
    .faq-item.open .faq-icon { transform: rotate(45deg); }
    .faq-a {
      max-height: 0;
      overflow: hidden;
      transition: max-height var(--t-slow), padding var(--t-slow);
      font-size: .9rem;
      color: var(--text-muted);
      line-height: 1.7;
      padding: 0 1.4rem;
    }
    .faq-item.open .faq-a { max-height: 200px; padding: 0 1.4rem 1.1rem; }

    /* CTA */
    .lp-cta {
      padding: 100px 0;
      background: var(--hero-grad);
      position: relative;
      overflow: hidden;
    }
    .lp-cta::before {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      border-radius: 50%;
      background: rgba(255,255,255,.06);
      top: -200px; right: -100px;
    }
    .lp-cta::after {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
      bottom: -100px; left: -50px;
    }
    .cta-title { font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 800; color: #fff; margin-bottom: 1rem; }
    .cta-sub { font-size: 1.05rem; color: rgba(255,255,255,.82); max-width: 480px; line-height: 1.7; margin-bottom: 2.25rem; }
    .btn-cta-white {
      background: #fff;
      color: var(--clr-700);
      border: none;
      border-radius: var(--r-full);
      padding: .9rem 2.25rem;
      font-size: 1rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      box-shadow: 0 4px 20px rgba(0,0,0,.18);
      transition: transform var(--t-fast), box-shadow var(--t-fast);
    }
    .btn-cta-white:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,.25); color: var(--clr-700); }
    .btn-cta-ghost-white {
      background: transparent;
      color: #fff;
      border: 2px solid rgba(255,255,255,.5);
      border-radius: var(--r-full);
      padding: .85rem 2rem;
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      transition: border-color var(--t-fast), background var(--t-fast);
    }
    .btn-cta-ghost-white:hover { border-color: #fff; background: rgba(255,255,255,.12); color: #fff; }

    /* Footer */
    .lp-footer {
      background: var(--bg-body);
      border-top: 1px solid var(--divider);
      padding: 2.5rem 0;
    }
    .footer-brand { font-size: 1.1rem; font-weight: 800; color: var(--clr-600); }
    .footer-links a { color: var(--text-muted); text-decoration: none; font-size: .85rem; transition: color var(--t-fast); }
    .footer-links a:hover { color: var(--clr-500); }
    .footer-copy { font-size: .8rem; color: var(--text-light); }

    /* Scroll-reveal: elements are ALWAYS visible.
       Animations are cosmetic only — translate/blur, never opacity:0.
       This means content shows instantly even without JS or in headless tools. */
    .reveal { opacity: 1; transform: none; }

    /* Cosmetic entrance animations (no opacity change — no blank flash) */
    @keyframes enterUp {
      from { transform: translateY(22px); filter: blur(3px); opacity: 0.6; }
      to   { transform: none; filter: none; opacity: 1; }
    }
    @keyframes enterLeft {
      from { transform: translateX(-20px); filter: blur(2px); opacity: 0.6; }
      to   { transform: none; filter: none; opacity: 1; }
    }
    @keyframes enterScale {
      from { transform: scale(0.93) translateY(14px); filter: blur(2px); opacity: 0.6; }
      to   { transform: none; filter: none; opacity: 1; }
    }
    .reveal.anim-up    { animation: enterUp    0.55s cubic-bezier(0.16,1,0.3,1) both; }
    .reveal.anim-left  { animation: enterLeft  0.5s  cubic-bezier(0.16,1,0.3,1) both; }
    .reveal.anim-scale { animation: enterScale 0.5s  cubic-bezier(0.34,1.56,0.64,1) both; }

    /* Child stagger inside reveal groups */
    .reveal-group .reveal:nth-child(1) { animation-delay: 0s; }
    .reveal-group .reveal:nth-child(2) { animation-delay: .09s; }
    .reveal-group .reveal:nth-child(3) { animation-delay: .18s; }
    .reveal-group .reveal:nth-child(4) { animation-delay: .27s; }
    .reveal-group .reveal:nth-child(5) { animation-delay: .36s; }
    .reveal-group .reveal:nth-child(6) { animation-delay: .45s; }
    .reveal-group .reveal:nth-child(7) { animation-delay: .54s; }
    .reveal-group .reveal:nth-child(8) { animation-delay: .63s; }
    .reveal-group .reveal:nth-child(9) { animation-delay: .72s; }

    /* Fix navbar overlap on anchor scroll */
    #features, #howitworks, #demo, #pricing, #faq { scroll-margin-top: 84px; }

    /* How it works */
    .lp-howitworks { background: var(--bg-card); }
    .hiw-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      position: relative;
    }
    .hiw-num {
      width: 56px; height: 56px;
      border-radius: 50%;
      background: var(--hero-grad);
      color: #fff;
      font-size: 1.2rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.25rem;
      box-shadow: 0 4px 16px hsla(var(--hue), var(--sat), 57%, .35);
      flex-shrink: 0;
    }
    .hiw-connector {
      position: absolute;
      top: 28px;
      left: calc(50% + 36px);
      width: calc(100% - 72px);
      height: 2px;
      background: linear-gradient(90deg, var(--clr-300), var(--clr-100));
      z-index: 0;
    }
    @media (max-width: 767px) { .hiw-connector { display: none; } }
    .hiw-icon {
      width: 64px; height: 64px;
      border-radius: var(--r-xl);
      background: hsl(var(--hue), 80%, 95%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      color: var(--clr-600);
      margin: 0 auto 1rem;
    }
    [data-theme="dark"] .hiw-icon { background: hsl(var(--hue), 60%, 18%); color: var(--clr-400); }
    .hiw-title { font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: .4rem; }
    .hiw-desc { font-size: .88rem; color: var(--text-muted); line-height: 1.65; max-width: 200px; margin: 0 auto; }

    /* Back to top */
    .back-to-top {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      width: 44px; height: 44px;
      border-radius: 50%;
      background: var(--hero-grad);
      color: #fff;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .95rem;
      box-shadow: 0 4px 16px hsla(var(--hue), var(--sat), 57%, .4);
      opacity: 0;
      transform: translateY(12px);
      transition: opacity var(--t-base), transform var(--t-base);
      z-index: 999;
      text-decoration: none;
    }
    .back-to-top.show { opacity: 1; transform: none; }
    .back-to-top:hover { color: #fff; box-shadow: 0 6px 24px hsla(var(--hue), var(--sat), 57%, .55); }

    /* Mobile nav drawer */
    .mobile-nav-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.45);
      z-index: 1100;
      backdrop-filter: blur(4px);
    }
    .mobile-nav-overlay.open { display: block; }
    .mobile-nav-drawer {
      position: fixed;
      top: 0; right: 0; bottom: 0;
      width: 260px;
      background: var(--bg-card);
      z-index: 1101;
      padding: 1.5rem;
      transform: translateX(100%);
      transition: transform var(--t-slow);
      display: flex;
      flex-direction: column;
      gap: .5rem;
      box-shadow: -8px 0 40px rgba(0,0,0,.15);
    }
    .mobile-nav-overlay.open .mobile-nav-drawer { transform: none; }
    .mobile-nav-close {
      align-self: flex-end;
      background: none;
      border: none;
      font-size: 1.3rem;
      color: var(--text-muted);
      cursor: pointer;
      padding: .25rem .5rem;
      margin-bottom: .5rem;
    }
    .mobile-nav-link {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: .75rem 1rem;
      border-radius: var(--r-md);
      color: var(--text-main);
      text-decoration: none;
      font-size: .95rem;
      font-weight: 600;
      transition: background var(--t-fast), color var(--t-fast);
    }
    .mobile-nav-link:hover { background: var(--clr-50); color: var(--clr-600); }
    .mobile-nav-divider { border: none; border-top: 1px solid var(--divider); margin: .5rem 0; }
    .mobile-hamburger {
      display: none;
      background: none;
      border: none;
      color: var(--text-main);
      font-size: 1.25rem;
      cursor: pointer;
      padding: .4rem;
    }
    @media (max-width: 767px) { .mobile-hamburger { display: flex; align-items: center; } }
  </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="lp-nav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="<?= url('landing.php') ?>" class="navbar-brand">
      <span>🍳</span> RecipeApp
    </a>
    <div class="lp-nav-links d-none d-md-flex align-items-center gap-1">
      <a href="#features">Features</a>
      <a href="#howitworks">How it works</a>
      <a href="#demo">Demo</a>
      <a href="#pricing">Pricing</a>
      <a href="#faq">FAQ</a>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if ($currentUser): ?>
        <a href="<?= url('index.php') ?>" class="btn-hero-primary d-none d-md-inline-flex" style="padding:.55rem 1.25rem;font-size:.88rem;">
          <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
      <?php else: ?>
        <a href="<?= url('auth/login.php') ?>" class="btn-hero-ghost d-none d-md-inline-flex" style="padding:.55rem 1.25rem;font-size:.88rem;">Login</a>
        <a href="<?= url('auth/register.php') ?>" class="btn-hero-primary d-none d-md-inline-flex" style="padding:.55rem 1.25rem;font-size:.88rem;">Get Started Free</a>
      <?php endif; ?>
      <button class="mobile-hamburger" id="mobileMenuBtn" aria-label="Open menu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </div>
</nav>

<!-- ── MOBILE NAV DRAWER ── -->
<div class="mobile-nav-overlay" id="mobileNavOverlay">
  <div class="mobile-nav-drawer">
    <button class="mobile-nav-close" id="mobileNavClose" aria-label="Close menu">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <a href="#features"    class="mobile-nav-link"><i class="fa-solid fa-star fa-fw"></i> Features</a>
    <a href="#howitworks"  class="mobile-nav-link"><i class="fa-solid fa-list-ol fa-fw"></i> How it works</a>
    <a href="#demo"        class="mobile-nav-link"><i class="fa-solid fa-play fa-fw"></i> Demo</a>
    <a href="#pricing"     class="mobile-nav-link"><i class="fa-solid fa-tag fa-fw"></i> Pricing</a>
    <a href="#faq"         class="mobile-nav-link"><i class="fa-solid fa-circle-question fa-fw"></i> FAQ</a>
    <hr class="mobile-nav-divider">
    <?php if ($currentUser): ?>
      <a href="<?= url('index.php') ?>" class="btn-hero-primary" style="justify-content:center;">
        <i class="fa-solid fa-gauge"></i> Dashboard
      </a>
    <?php else: ?>
      <a href="<?= url('auth/login.php') ?>" class="mobile-nav-link"><i class="fa-solid fa-right-to-bracket fa-fw"></i> Login</a>
      <a href="<?= url('auth/register.php') ?>" class="btn-hero-primary mt-1" style="justify-content:center;">
        <i class="fa-solid fa-rocket"></i> Get Started Free
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- ── HERO ── -->
<section class="lp-hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="hero-badge reveal">
          <i class="fa-solid fa-fire-flame-curved"></i> Trusted by food lovers worldwide
        </div>
        <h1 class="hero-title reveal">
          Cook Smarter.<br>
          Share <span class="highlight">Delicious</span><br>
          Recipes.
        </h1>
        <p class="hero-sub reveal">
          RecipeApp is the all-in-one platform for discovering, creating, and organizing your favorite recipes — with smart search, category filters, and a beautiful cookbook you'll actually use.
        </p>
        <div class="hero-cta-group reveal">
          <a href="<?= url('auth/register.php') ?>" class="btn-hero-primary">
            <i class="fa-solid fa-rocket"></i> Start for Free
          </a>
          <a href="#demo" class="btn-hero-ghost">
            <i class="fa-solid fa-play"></i> See how it works
          </a>
        </div>
        <div class="hero-stats reveal">
          <div class="d-flex align-items-center gap-2" style="color:var(--text-muted);font-size:.88rem;">
            <i class="fa-solid fa-circle-check" style="color:var(--clr-500);"></i> No credit card required
          </div>
          <div class="d-flex align-items-center gap-2" style="color:var(--text-muted);font-size:.88rem;">
            <i class="fa-solid fa-circle-check" style="color:var(--clr-500);"></i> Free forever plan
          </div>
          <div class="d-flex align-items-center gap-2" style="color:var(--text-muted);font-size:.88rem;">
            <i class="fa-solid fa-circle-check" style="color:var(--clr-500);"></i> Cancel anytime
          </div>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <div class="hero-mockup">
          <div class="mockup-bar">
            <div class="mockup-dot" style="background:#ff5f57;"></div>
            <div class="mockup-dot" style="background:#ffbd2e;"></div>
            <div class="mockup-dot" style="background:#28ca41;"></div>
            <span style="font-size:.75rem;color:var(--text-muted);margin-left:.5rem;">RecipeApp — Home</span>
          </div>
          <div class="mockup-body">
            <div class="mockup-search">
              <i class="fa-solid fa-magnifying-glass" style="font-size:.8rem;"></i>
              Search recipes, ingredients, cuisines…
            </div>
            <div class="mockup-grid">
              <?php
              $demo_cards = [
                ['🍝','Pasta Carbonara','Italian · 20 min'],
                ['🥗','Greek Salad','Healthy · 10 min'],
                ['🍜','Ramen Bowl','Asian · 45 min'],
                ['🍕','Margherita Pizza','Italian · 30 min'],
                ['🍛','Chicken Curry','Indian · 40 min'],
                ['🥞','Fluffy Pancakes','Breakfast · 15 min'],
              ];
              foreach ($demo_cards as $c): ?>
                <div class="mockup-card">
                  <div class="mockup-thumb" style="background:var(--clr-50);"><?= $c[0] ?></div>
                  <div class="mockup-card-body">
                    <div class="mockup-card-title"><?= $c[1] ?></div>
                    <div class="mockup-card-sub"><?= $c[2] ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── HOW IT WORKS ── -->
<section class="lp-section lp-howitworks" id="howitworks">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-eyebrow">How it works</div>
      <h2 class="section-title">Up and cooking in 3 steps</h2>
      <p class="section-sub mx-auto">No setup, no complexity. Just great food.</p>
    </div>
    <div class="row g-4 justify-content-center">

      <div class="col-sm-4 reveal" style="animation-delay:0s">
        <div class="hiw-step">
          <div class="hiw-num">1</div>
          <div class="hiw-icon"><i class="fa-solid fa-user-plus"></i></div>
          <div class="hiw-title">Create a free account</div>
          <div class="hiw-desc">Sign up in 30 seconds. No credit card, no commitment — just your name and email.</div>
        </div>
      </div>

      <div class="col-sm-4 reveal" style="animation-delay:.13s">
        <div class="hiw-step">
          <div class="hiw-num">2</div>
          <div class="hiw-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
          <div class="hiw-title">Discover or add recipes</div>
          <div class="hiw-desc">Browse hundreds of community recipes, or add your own with photos, ingredients, and steps.</div>
        </div>
      </div>

      <div class="col-sm-4 reveal" style="animation-delay:.26s">
        <div class="hiw-step">
          <div class="hiw-num">3</div>
          <div class="hiw-icon"><i class="fa-solid fa-utensils"></i></div>
          <div class="hiw-title">Cook, share & repeat</div>
          <div class="hiw-desc">Follow the step-by-step instructions, leave comments, save your favorites, and share with friends.</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── FEATURES ── -->
<section class="lp-section lp-features" id="features">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-eyebrow">Features</div>
      <h2 class="section-title">Everything you need to cook better</h2>
      <p class="section-sub mx-auto">From discovery to the dinner table, RecipeApp has every tool a modern home cook needs.</p>
    </div>
    <div class="row g-4 reveal-group">
      <?php
      $features = [
        ['fa-magnifying-glass-plus','Smart Recipe Search','Instantly find recipes by name, ingredient, or cuisine. Filter by category and discover something new every day.'],
        ['fa-pen-to-square','Create & Share','Add your own recipes with rich details — ingredients, steps, cook time, and photos. Share with the community.'],
        ['fa-layer-group','Category Explorer','Browse 20+ cuisine categories from Italian to Indian. Every recipe is neatly organized so you never feel lost.'],
        ['fa-star','Save Favorites','Bookmark the recipes you love. Build your personal cookbook that\'s always a click away.'],
        ['fa-comments','Community Comments','Leave feedback, ask questions, and share tips on any recipe. Learn from a community of passionate cooks.'],
        ['fa-moon','Dark Mode & Themes','Switch between light and dark mode, and customize accent colors to match your style. Your eyes will thank you.'],
        ['fa-gauge','Admin Dashboard','Full admin panel with user management, recipe moderation, and analytics. Stay in control of your platform.'],
        ['fa-mobile-screen-button','Fully Responsive','Seamlessly beautiful on desktop, tablet, and mobile. Cook from any device, anywhere.'],
        ['fa-lock','Secure Accounts','Signed-in accounts with hashed passwords, session management, and protected routes from day one.'],
      ];
      foreach ($features as $i => $f): ?>
        <div class="col-sm-6 col-lg-4 reveal" style="transition-delay:<?= $i * 0.07 ?>s">
          <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid <?= $f[0] ?>"></i></div>
            <div class="feature-title"><?= $f[1] ?></div>
            <div class="feature-desc"><?= $f[2] ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── DEMO ── -->
<section class="lp-section lp-demo" id="demo">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-md-5 reveal">
        <div class="section-eyebrow">Product Demo</div>
        <h2 class="section-title">See it in action</h2>
        <p class="section-sub">Click through the tabs to explore the core flows that make RecipeApp the go-to platform for home chefs.</p>
        <div class="demo-tab-list mt-4" id="demoTabs">
          <button class="demo-tab active" data-panel="discover">Discover</button>
          <button class="demo-tab" data-panel="create">Create</button>
          <button class="demo-tab" data-panel="manage">Manage</button>
        </div>
      </div>
      <div class="col-md-7 reveal">
        <div id="panel-discover" class="demo-panel active">
          <div class="demo-screen">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);letter-spacing:.06em;text-transform:uppercase;">Trending Today</div>
            <?php
            $discover = [
              ['🍝','Pasta Carbonara','Italian · 4.9★ · 324 saves','badge-green','Popular'],
              ['🍜','Spicy Ramen','Asian · 4.8★ · 210 saves','badge-orange','Trending'],
              ['🥗','Mediterranean Bowl','Healthy · 4.7★ · 189 saves','badge-blue','New'],
              ['🍛','Butter Chicken','Indian · 4.9★ · 402 saves','badge-green','Top Rated'],
            ];
            foreach ($discover as $r): ?>
              <div class="demo-row">
                <div class="demo-emoji"><?= $r[0] ?></div>
                <div>
                  <div class="demo-row-title"><?= $r[1] ?></div>
                  <div class="demo-row-sub"><?= $r[2] ?></div>
                </div>
                <span class="demo-badge <?= $r[3] ?>"><?= $r[4] ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div id="panel-create" class="demo-panel">
          <div class="demo-screen">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);letter-spacing:.06em;text-transform:uppercase;">Add a Recipe</div>
            <?php
            $create_steps = [
              ['fa-heading','Recipe Title','Name your dish and choose a category','badge-green','Done'],
              ['fa-list-ul','Ingredients','Add all ingredients with quantities','badge-green','Done'],
              ['fa-list-ol','Instructions','Write step-by-step cooking instructions','badge-orange','In Progress'],
              ['fa-image','Upload Photo','Add a photo to make it mouth-watering','badge-blue','Next'],
            ];
            foreach ($create_steps as $s): ?>
              <div class="demo-row">
                <div style="width:36px;height:36px;border-radius:var(--r-md);background:var(--clr-50);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <i class="fa-solid <?= $s[0] ?>" style="color:var(--clr-500);font-size:.85rem;"></i>
                </div>
                <div>
                  <div class="demo-row-title"><?= $s[1] ?></div>
                  <div class="demo-row-sub"><?= $s[2] ?></div>
                </div>
                <span class="demo-badge <?= $s[3] ?>"><?= $s[4] ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div id="panel-manage" class="demo-panel">
          <div class="demo-screen">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);letter-spacing:.06em;text-transform:uppercase;">Admin Dashboard</div>
            <?php
            $manage = [
              ['fa-users','Total Users','1,240 registered cooks','badge-blue','Active'],
              ['fa-bowl-food','Total Recipes','528 published recipes','badge-green','Published'],
              ['fa-comments','Comments','3,471 community comments','badge-green','Live'],
              ['fa-flag','Flagged Content','2 pending reviews','badge-orange','Action Needed'],
            ];
            foreach ($manage as $m): ?>
              <div class="demo-row">
                <div style="width:36px;height:36px;border-radius:var(--r-md);background:var(--clr-50);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <i class="fa-solid <?= $m[0] ?>" style="color:var(--clr-500);font-size:.85rem;"></i>
                </div>
                <div>
                  <div class="demo-row-title"><?= $m[1] ?></div>
                  <div class="demo-row-sub"><?= $m[2] ?></div>
                </div>
                <span class="demo-badge <?= $m[3] ?>"><?= $m[4] ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── PRICING ── -->
<section class="lp-section lp-pricing" id="pricing">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-eyebrow">Pricing</div>
      <h2 class="section-title">Simple, honest pricing</h2>
      <p class="section-sub mx-auto">Start free and upgrade as you grow. No hidden fees, no credit card required.</p>
    </div>
    <div class="row g-4 justify-content-center reveal-group">

      <!-- Free -->
      <div class="col-sm-6 col-lg-4 reveal">
        <div class="pricing-card">
          <div class="pricing-plan-name">Free</div>
          <div class="pricing-price">$0 <span>/ forever</span></div>
          <div class="pricing-desc">Perfect for curious home cooks getting started.</div>
          <ul class="pricing-features">
            <li><i class="fa-solid fa-check check"></i> Browse all recipes</li>
            <li><i class="fa-solid fa-check check"></i> Create up to 5 recipes</li>
            <li><i class="fa-solid fa-check check"></i> Comment on recipes</li>
            <li><i class="fa-solid fa-xmark cross"></i> <span style="color:var(--text-light);">Save favorites</span></li>
            <li><i class="fa-solid fa-xmark cross"></i> <span style="color:var(--text-light);">Photo uploads</span></li>
            <li><i class="fa-solid fa-xmark cross"></i> <span style="color:var(--text-light);">Priority support</span></li>
          </ul>
          <a href="<?= url('auth/register.php') ?>" class="btn-plan btn-plan-outline">Get Started Free</a>
        </div>
      </div>

      <!-- Pro -->
      <div class="col-md-6 col-lg-4 reveal" style="transition-delay:.08s">
        <div class="pricing-card popular">
          <div class="popular-badge"><i class="fa-solid fa-star me-1"></i>Most Popular</div>
          <div class="pricing-plan-name">Pro</div>
          <div class="pricing-price">$9 <span>/ month</span></div>
          <div class="pricing-desc">For passionate cooks who want the full experience.</div>
          <ul class="pricing-features">
            <li><i class="fa-solid fa-check check"></i> Everything in Free</li>
            <li><i class="fa-solid fa-check check"></i> Unlimited recipes</li>
            <li><i class="fa-solid fa-check check"></i> Photo uploads</li>
            <li><i class="fa-solid fa-check check"></i> Save favorites</li>
            <li><i class="fa-solid fa-check check"></i> Custom profile</li>
            <li><i class="fa-solid fa-check check"></i> Priority support</li>
          </ul>
          <a href="<?= url('auth/register.php') ?>" class="btn-plan btn-plan-fill">Start Pro Free Trial</a>
        </div>
      </div>

      <!-- Chef -->
      <div class="col-md-6 col-lg-4 reveal" style="transition-delay:.16s">
        <div class="pricing-card">
          <div class="pricing-plan-name">Chef</div>
          <div class="pricing-price">$29 <span>/ month</span></div>
          <div class="pricing-desc">For food bloggers, culinary schools, and teams.</div>
          <ul class="pricing-features">
            <li><i class="fa-solid fa-check check"></i> Everything in Pro</li>
            <li><i class="fa-solid fa-check check"></i> Team collaboration</li>
            <li><i class="fa-solid fa-check check"></i> Analytics dashboard</li>
            <li><i class="fa-solid fa-check check"></i> API access</li>
            <li><i class="fa-solid fa-check check"></i> White-label option</li>
            <li><i class="fa-solid fa-check check"></i> Dedicated support</li>
          </ul>
          <a href="<?= url('auth/register.php') ?>" class="btn-plan btn-plan-outline">Get Chef Plan</a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── FAQ ── -->
<section class="lp-section lp-faq" id="faq">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="text-center mb-5 reveal">
          <div class="section-eyebrow">FAQ</div>
          <h2 class="section-title">Frequently asked questions</h2>
        </div>
        <?php
        $faqs = [
          ['Do I need a credit card to sign up?', 'No. The Free plan requires no credit card. You can create an account and start exploring recipes immediately.'],
          ['Can I cancel my subscription anytime?', 'Absolutely. You can cancel your Pro or Chef plan at any time from your account settings. You keep access until the end of your billing period.'],
          ['Is my recipe data safe?', 'Yes. All data is stored in a secure MySQL database with PDO-prepared statements to prevent SQL injection. Passwords are hashed and never stored in plain text.'],
          ['Can I import recipes from other sites?', 'RecipeApp currently supports manual recipe entry. An import tool for popular formats is on our roadmap for Q3 2026.'],
          ['Is there a mobile app?', 'RecipeApp is fully responsive and works beautifully in any mobile browser. Native iOS and Android apps are on the roadmap.'],
        ];
        foreach ($faqs as $i => $faq): ?>
          <div class="faq-item reveal" style="transition-delay:<?= $i * 0.06 ?>s">
            <button class="faq-q" onclick="toggleFaq(this)">
              <?= e($faq[0]) ?>
              <i class="fa-solid fa-plus faq-icon"></i>
            </button>
            <div class="faq-a"><?= e($faq[1]) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section class="lp-cta">
  <div class="container position-relative" style="z-index:1;">
    <div class="row justify-content-center text-center">
      <div class="col-lg-7 reveal">
        <h2 class="cta-title">Ready to cook something amazing?</h2>
        <p class="cta-sub mx-auto">Join thousands of home cooks. Create your free account in 30 seconds and start exploring recipes today.</p>
        <div class="hero-cta-group justify-content-center">
          <a href="<?= url('auth/register.php') ?>" class="btn-cta-white">
            <i class="fa-solid fa-rocket"></i> Create Free Account
          </a>
          <a href="<?= url('index.php') ?>" class="btn-cta-ghost-white">
            <i class="fa-solid fa-eye"></i> Browse Recipes
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer class="lp-footer">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div class="footer-brand">🍳 RecipeApp</div>
      <div class="footer-links d-flex flex-wrap gap-3">
        <a href="<?= url('index.php') ?>">Home</a>
        <a href="<?= url('recipes.php') ?>">Recipes</a>
        <a href="<?= url('about.php') ?>">About</a>
        <a href="<?= url('auth/login.php') ?>">Login</a>
        <a href="<?= url('auth/register.php') ?>">Register</a>
      </div>
      <div class="footer-copy">&copy; <?= date('Y') ?> RecipeApp. All rights reserved.</div>
    </div>
  </div>
</footer>

<!-- Back to top -->
<a href="#" class="back-to-top" id="backToTop" aria-label="Back to top">
  <i class="fa-solid fa-chevron-up"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Demo tabs
  document.getElementById('demoTabs').addEventListener('click', function(e) {
    var btn = e.target.closest('.demo-tab');
    if (!btn) return;
    document.querySelectorAll('.demo-tab').forEach(function(t){ t.classList.remove('active'); });
    document.querySelectorAll('.demo-panel').forEach(function(p){ p.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('panel-' + btn.dataset.panel).classList.add('active');
  });

  // FAQ accordion
  function toggleFaq(btn) {
    var item = btn.closest('.faq-item');
    var wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(function(i){ i.classList.remove('open'); });
    if (!wasOpen) item.classList.add('open');
  }

  // Scroll reveal handled by Motion.js (see module script below)

  // Smooth scroll for nav anchor links (closes mobile drawer too)
  document.querySelectorAll('a[href^="#"]').forEach(function(a) {
    a.addEventListener('click', function(e) {
      var href = this.getAttribute('href');
      if (href === '#') {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
      }
      var target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        closeMobileNav();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // Back to top
  var btt = document.getElementById('backToTop');
  window.addEventListener('scroll', function() {
    btt.classList.toggle('show', window.scrollY > 400);
  }, { passive: true });

  // Mobile nav drawer
  function openMobileNav()  { document.getElementById('mobileNavOverlay').classList.add('open'); document.body.style.overflow = 'hidden'; }
  function closeMobileNav() { document.getElementById('mobileNavOverlay').classList.remove('open'); document.body.style.overflow = ''; }
  document.getElementById('mobileMenuBtn').addEventListener('click', openMobileNav);
  document.getElementById('mobileNavClose').addEventListener('click', closeMobileNav);
  document.getElementById('mobileNavOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeMobileNav();
  });
</script>

<!-- Scroll-reveal: cosmetic animations only, elements always visible -->
<script>
(function() {
  // Assign animation type per element
  var typeMap = [
    ['.faq-item',                         'anim-left'],
    ['.feature-card,.pricing-card,.hiw-step,.hiw-num,.hiw-icon', 'anim-scale'],
    ['.reveal',                           'anim-up'],   // default fallback
  ];
  typeMap.forEach(function(pair) {
    document.querySelectorAll(pair[0]).forEach(function(el) {
      if (!el.dataset.animType) el.dataset.animType = pair[1];
    });
  });

  var io = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (!entry.isIntersecting) return;
      var el = entry.target;
      var cls = el.dataset.animType || 'anim-up';
      // Restart animation cleanly
      el.classList.remove('anim-up', 'anim-left', 'anim-scale');
      void el.offsetWidth; // reflow
      el.classList.add(cls);
      io.unobserve(el);
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal, .feature-card, .pricing-card, .hiw-step, .faq-item')
    .forEach(function(el) { io.observe(el); });
})();
</script>

<!-- Motion: hero entrance + interactive effects only -->
<script src="<?= url('public/motion.js') ?>"></script>
<script>
(function() {
  var animate = Motion.animate, stagger = Motion.stagger;
  var SMOOTH = [0.16, 1, 0.3, 1];
  var SNAPPY = [0.34, 1.56, 0.64, 1];

  // ── Hero layered entrance (no opacity FROM so elements stay visible in print/PDF) ──
  animate('.hero-badge',   { y:[14,0], scale:[0.92,1] }, { duration:0.5,  delay:0.1,  easing:SNAPPY });
  animate('.hero-title',   { y:[28,0] },                  { duration:0.65, delay:0.22, easing:SMOOTH });
  animate('.hero-sub',     { y:[20,0] },                  { duration:0.55, delay:0.38, easing:SMOOTH });
  animate('.hero-cta-group .btn-hero-primary', { y:[14,0], scale:[0.96,1] }, { duration:0.45, delay:0.5, easing:SNAPPY });
  animate('.hero-cta-group .btn-hero-ghost',   { y:[14,0] },                 { duration:0.45, delay:0.6, easing:SMOOTH });
  animate('.hero-stats',   { y:[10,0] },                  { duration:0.45, delay:0.7,  easing:SMOOTH });

  // ── Hero mockup: slide in then idle float ──
  animate('.hero-mockup', { x:[56,0], scale:[0.94,1] }, { duration:0.85, delay:0.3, easing:SMOOTH })
    .then(function() {
      animate('.hero-mockup', { y:[0,-10,0] }, { duration:3.8, repeat:Infinity, easing:'ease-in-out' });
    });
  animate('.mockup-card', { scale:[0.88,1], y:[10,0] }, { duration:0.38, delay:stagger(0.055,{start:0.85}), easing:SNAPPY });

  // ── Demo tab switch ──
  document.getElementById('demoTabs').addEventListener('click', function(e) {
    var btn = e.target.closest('.demo-tab');
    if (!btn) return;
    var panel = document.getElementById('panel-' + btn.dataset.panel);
    animate(panel, { opacity:[0,1], y:[10,0] }, { duration:0.28, easing:SMOOTH });
    animate(panel.querySelectorAll('.demo-row'), { opacity:[0,1], x:[-14,0] }, { duration:0.28, delay:stagger(0.055), easing:SMOOTH });
  });

  // ── Navbar compact on scroll ──
  var nav = document.querySelector('.lp-nav');
  window.addEventListener('scroll', function() {
    var s = window.scrollY > 60;
    nav.style.padding   = s ? '.45rem 0' : '.9rem 0';
    nav.style.boxShadow = s ? '0 2px 20px rgba(0,0,0,.08)' : 'none';
  }, { passive:true });

  // ── Hover effects (pointer only) ──
  if (window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll('.feature-card').forEach(function(card) {
      var icon = card.querySelector('.feature-icon i');
      card.addEventListener('mouseenter', function() {
        animate(card, { y:-7, scale:1.022 }, { duration:0.28, easing:SMOOTH });
        if (icon) animate(icon, { rotate:[0,-10,7,0], scale:[1,1.18,1] }, { duration:0.45, easing:SMOOTH });
      });
      card.addEventListener('mouseleave', function() { animate(card, { y:0, scale:1 }, { duration:0.32, easing:SMOOTH }); });
    });

    document.querySelectorAll('.pricing-card').forEach(function(card) {
      card.addEventListener('mouseenter', function() { animate(card, { y:-6, scale:1.012 }, { duration:0.25, easing:SMOOTH }); });
      card.addEventListener('mouseleave', function() { animate(card, { y:0, scale:1 },     { duration:0.3,  easing:SMOOTH }); });
    });

    document.querySelectorAll('.btn-hero-primary, .btn-cta-white, .btn-plan-fill').forEach(function(btn) {
      btn.addEventListener('mousedown',  function() { animate(btn, { scale:0.96 }, { duration:0.1 }); });
      btn.addEventListener('mouseup',    function() { animate(btn, { scale:1 }, { duration:0.2, easing:SNAPPY }); });
      btn.addEventListener('mouseleave', function() { animate(btn, { scale:1 }, { duration:0.2, easing:SMOOTH }); });
    });

    document.querySelectorAll('.hiw-icon').forEach(function(icon) {
      icon.addEventListener('mouseenter', function() {
        animate(icon, { rotate:[0,-8,8,-3,0], scale:[1,1.1,1] }, { duration:0.45, easing:SMOOTH });
      });
    });

    document.querySelectorAll('.lp-nav-links a').forEach(function(a) {
      a.addEventListener('mouseenter', function() { animate(a, { scale:1.05 }, { duration:0.16, easing:SNAPPY }); });
      a.addEventListener('mouseleave', function() { animate(a, { scale:1 },   { duration:0.2,  easing:SMOOTH }); });
    });

    var bttEl = document.getElementById('backToTop');
    bttEl.addEventListener('mouseenter', function() { animate(bttEl, { scale:1.1, y:-2 }, { duration:0.2,  easing:SNAPPY }); });
    bttEl.addEventListener('mouseleave', function() { animate(bttEl, { scale:1,   y:0  }, { duration:0.25, easing:SMOOTH }); });
  }
})();
</script>
</body>
</html>
