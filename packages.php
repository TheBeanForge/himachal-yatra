<?php
$base       = 'index.php';
$activeDest = '';
require_once 'includes/vars.php';

// Active tour packages (admin-managed in admin/packages.php). Resilient to older schemas.
$packages = [];
if ($conn instanceof mysqli) {
    foreach ([
        "SELECT id, package_name, description, category, photo, duration_days, duration_nights, is_bestseller FROM tour_packages WHERE status='active' ORDER BY sort_order ASC, id ASC",
        "SELECT id, package_name, description, photo, duration_days, duration_nights, is_bestseller FROM tour_packages WHERE status='active' ORDER BY sort_order ASC, id ASC",
        "SELECT id, package_name, description, photo FROM tour_packages WHERE status='active' ORDER BY sort_order ASC, id ASC",
        "SELECT id, package_name FROM tour_packages WHERE status='active' ORDER BY id ASC",
    ] as $sql) {
        try { $packages = $conn->query($sql)->fetch_all(MYSQLI_ASSOC); break; }
        catch (mysqli_sql_exception) { continue; }
    }
    $conn->close();
}

if (!function_exists('package_dummy_image')) {
function package_dummy_image(array $package, int $index): string {
    $seed = (int)($package['id'] ?? 0);
    if ($seed <= 0) $seed = $index + 1;
    $slot = (($seed - 1) % 6) + 1;
    return 'assets/photos/package-placeholder-' . $slot . '.svg';
}
}
?><!doctype html>
<html lang="en" data-season="<?php echo h(current_season()); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tour Packages | Himachal Safar</title>
  <meta name="description" content="Curated Himachal & Ladakh taxi tour packages — Shimla, Manali, Spiti, Kinnaur, Dharamshala, Dalhousie and Leh. Private cabs with expert mountain drivers.">
  <?php
    $seoTitle = 'Tour Packages | Himachal Safar';
    $seoDesc  = 'Curated Himachal & Ladakh taxi tour packages with private cabs and expert mountain drivers — Shimla, Manali, Spiti, Dharamshala, Dalhousie and Leh.';
    $seoPath  = 'packages.php';
    include __DIR__ . '/includes/seo_head.php';
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/base.css?v=<?php echo @filemtime(__DIR__ . "/assets/base.css"); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo @filemtime(__DIR__ . '/style.css'); ?>">
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-4GZDXE68YZ"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-4GZDXE68YZ');   // GA4 — analytics
    gtag('config', 'AW-18335368848');  // Google Ads — conversion tracking
  </script>
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

<main id="home">
  <section class="lux-section pkgpage" id="packages" style="padding-top:clamp(96px,12vw,150px)">
    <div class="lux-container">
      <div class="lux-head reveal">
        <span class="lux-eyebrow">Curated Itineraries</span>
        <h1 class="lux-h2">Tour Packages</h1>
        <p class="lux-body" style="max-width:640px;margin:14px auto 0">Handpicked Himachal &amp; Ladakh journeys — private cabs, expert mountain drivers and routes planned around the season. Tap a package to plan it with our travel desk.</p>
      </div>

      <?php
        // One shared card template keeps the bestseller and full lists identical & maintainable.
        $renderCard = function (array $p, int $i = 0) use ($whatsappNumber) {
          $waMsg = 'Hi Himachal Safar, I am interested in the "' . $p['package_name'] . '" package. Please share the itinerary and a quote.';
          $waUrl = 'https://wa.me/' . h($whatsappNumber) . '?text=' . rawurlencode($waMsg);
          $nn = (int)($p['duration_nights'] ?? 0); $dd = (int)($p['duration_days'] ?? 0);
          if ($dd > 0 && $nn === 0) $nn = $dd - 1;   // nights default to days−1 (standard tour convention)
          $badge = ($nn > 0 && $dd > 0) ? "{$dd}D/{$nn}N" : ($dd > 0 ? "{$dd}D" : ($nn > 0 ? "{$nn}N" : 'Taxi Tour'));
          $best = !empty($p['is_bestseller']);
          $cat  = trim($p['category'] ?? '');
          $durLabel = ($nn > 0 && $dd > 0) ? "{$nn} Nights / {$dd} Days" : ($dd > 0 ? "{$dd} Days" : ($nn > 0 ? "{$nn} Nights" : ''));
          ?>
          <article class="pkg-card reveal<?= $best ? ' is-bestseller' : '' ?>">
            <div class="pkg-card-media">
              <?php if (!empty($p['photo'])): ?>
                <img src="uploads/photos/<?= h($p['photo']) ?>" alt="<?= h($p['package_name']) ?>" loading="lazy" decoding="async">
              <?php else: ?>
                <img class="pkg-card-dummy" src="<?= h(package_dummy_image($p, $i)) ?>" alt="<?= h($p['package_name']) ?>" loading="lazy" decoding="async">
              <?php endif; ?>
              <?php if ($best): ?><span class="pkg-card-star"><i class="fa-solid fa-star"></i> Bestseller</span><?php endif; ?>
            </div>
            <div class="pkg-card-body">
              <?php if ($cat !== ''): ?><span class="pkg-card-cat"><?= h($cat) ?></span><?php endif; ?>
              <h3 class="pkg-card-title"><?= h($p['package_name']) ?></h3>
              <?php if (!empty($p['description'])): ?>
              <p class="pkg-card-desc"><?= h($p['description']) ?></p>
              <?php endif; ?>
              <span class="pkg-card-badge"><?= h($badge) ?></span>
              <div class="pkg-card-actions">
                <button type="button" class="pkg-card-cta pkg-quote-open"
                  data-id="<?= (int)$p['id'] ?>" data-name="<?= h($p['package_name']) ?>"
                  data-nights="<?= $nn ?>" data-days="<?= $dd ?>" data-dur="<?= h($durLabel) ?>">
                  <i class="fa-solid fa-calculator"></i> Get Quote
                </button>
                <a class="pkg-card-cta pkg-card-cta-wa" href="<?= $waUrl ?>" target="_blank" rel="noopener" data-wa-lead data-source="packages">
                  <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>
              </div>
            </div>
          </article>
          <?php
        };

        $bestsellers = array_filter($packages, fn($p) => !empty($p['is_bestseller']));
        $others      = array_filter($packages, fn($p) => empty($p['is_bestseller']));
      ?>

      <?php if (empty($packages)): ?>
      <p style="text-align:center;color:var(--ink-2)">Our packages are being updated — please check back soon, or reach us on WhatsApp for a custom itinerary.</p>
      <?php else: ?>

        <?php if ($bestsellers): ?>
        <div class="pkg-secthead reveal">
          <span class="pkg-secthead-ey"><i class="fa-solid fa-star"></i> Most Loved</span>
          <h3 class="pkg-secthead-h">Bestsellers</h3>
        </div>
        <div class="pkg-grid">
          <?php foreach (array_values($bestsellers) as $i => $p) $renderCard($p, $i); ?>
        </div>
        <?php endif; ?>

        <?php if ($others): ?>
        <div class="pkg-secthead reveal"<?= $bestsellers ? ' style="margin-top:clamp(48px,6vw,76px)"' : '' ?>>
          <span class="pkg-secthead-ey"><i class="fa-solid fa-route"></i> Explore</span>
          <h3 class="pkg-secthead-h"><?= $bestsellers ? 'More Tour Packages' : 'All Tour Packages' ?></h3>
        </div>
        <div class="pkg-grid">
          <?php foreach (array_values($others) as $i => $p) $renderCard($p, $i); ?>
        </div>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </section>
</main>

<?php if (!empty($packages)): ?>
<!-- Tour package "Get Quote" opens the main calculator (Plan Your Himachal Journey)
     with the trip length auto-locked to the package's days. -->
<script>window.CQ_NO_AUTOPOPUP = true;</script>
<?php require 'includes/calc_modal.php'; ?>
<script>
document.querySelectorAll('.pkg-quote-open').forEach(function(b){
  b.addEventListener('click', function(){
    if (typeof window.openCalcModalForPackage === 'function') {
      window.openCalcModalForPackage({
        id:   b.dataset.id,
        name: b.dataset.name,
        days: parseInt(b.dataset.days || '0', 10) || 0
      });
    }
  });
});
</script>
<?php endif; ?>

<?php require 'includes/foot.php'; ?>
</body>
</html>
