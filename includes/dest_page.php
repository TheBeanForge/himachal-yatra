<?php
/*
 * Shared layout for the five destination pages.
 *
 * Each <destination>.php is now a stub that sets $activeDest and includes this
 * file; all content comes from includes/dest_data.php. The markup below is a
 * faithful reproduction of the original hand-written pages — rendered output
 * was diffed byte-for-byte against pre-refactor snapshots of all five.
 *
 * Fields that legitimately carry HTML (heading <span> emphasis, season star
 * markup) are echoed raw; everything else goes through h(). Both cases are
 * marked inline so the distinction stays deliberate.
 *
 * Expects: $activeDest (string key), $base (string)
 */
declare(strict_types=1);

$base       = $base ?? 'index.php';
$DEST_ALL   = require __DIR__ . '/dest_data.php';
$D          = $DEST_ALL[$activeDest] ?? null;
if (!$D) { http_response_code(404); exit('Unknown destination.'); }

require_once __DIR__ . '/vars.php';

$db_photos = []; $hero_photo = null; $about_photo = null;
if ($conn instanceof mysqli) {
    [$hero_photo, $about_photo, $db_photos] = load_dest_photos($conn, $activeDest);
    $conn->close();
}
?><!doctype html>
<html lang="en" data-season="<?php echo h(current_season()); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $D['docTitle'] ?></title>
  <meta name="description" content="<?= $D['metaDesc'] ?>">
  <?php
    $seoTitle = $D['title'];
    $seoDesc  = $D['desc'];
    $seoPath  = $activeDest . '.php';
    // Breadcrumb label. seo_head defaults to ucwords(basename), which is right
    // for most pages but would turn "Spiti Valley" into "Spiti" in the
    // BreadcrumbList schema — so pages that need a fuller name carry one.
    if (!empty($D['crumb'])) $seoCrumb = $D['crumb'];
    // Per-page social share image = this destination's own hero (falls back to generic default).
    $SITE_URL = rtrim(getenv('SITE_URL') ?: 'https://himachalsafar.com', '/');
    if ($hero_photo) $seoImage = $SITE_URL . '/uploads/photos/' . $hero_photo['filename'];
    include __DIR__ . '/seo_head.php';
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/base.css?v=<?php echo @filemtime(__DIR__ . '/../assets/base.css'); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo @filemtime(__DIR__ . '/../style.css'); ?>">
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-4GZDXE68YZ"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-4GZDXE68YZ');
  </script>
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require __DIR__ . '/nav.php'; ?>

<main>

  <!-- DEST HERO -->
  <section class="dest-hero" id="home">
    <div class="dest-hero-bg">
      <img src="<?= h(dest_img_src($hero_photo, $activeDest)) ?>" alt="<?= h($D['heroAlt']) ?>" fetchpriority="high" decoding="async" width="1920" height="1080">
    </div>
    <div class="dest-hero-overlay"></div>
    <div class="container mx-auto px-3 dest-hero-content">
      <nav class="dest-breadcrumb" aria-label="breadcrumb">
        <a href="index.php">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="index.php#routes">Destinations</a> <i class="fa-solid fa-chevron-right"></i>
        <span><?= h($D['name']) ?></span>
      </nav>
      <div class="dest-hero-tag"><i class="<?= h($D['heroTag'][0]) ?>"></i> <?= h($D['heroTag'][1]) ?></div>
      <h1><?= h($D['name']) ?></h1>
      <p><?= h($D['tagline']) ?></p>
      <div class="dest-hero-facts">
        <?php foreach ($D['facts'] as $f): ?>
        <div class="dest-fact"><i class="<?= h($f[0]) ?>"></i><span><?= h($f[1]) ?></span></div>
        <?php endforeach; ?>
      </div>
      <?php if (!empty($D['heroWarning'])): ?>
      <div class="dest-hero-warning"><i class="<?= h($D['heroWarning']['icon']) ?>"></i> <?= h($D['heroWarning']['text']) ?></div>
      <?php endif; ?>
      <a href="index.php?calc=<?= h($activeDest) ?>" class="dest-hero-btn"><i class="fa-solid fa-calculator"></i> <?= h($D['heroBtn']) ?></a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="dest-about section-pad">
    <div class="container mx-auto px-3">
      <div class="dest-about-grid">
        <div class="dest-about-text reveal">
          <div class="section-tag"><i class="<?= h($D['aboutTag'][0]) ?>"></i> <?= h($D['aboutTag'][1]) ?></div>
          <?php /* headings carry <span> emphasis — raw by design */ ?>
          <h2 class="section-title"><?= $D['aboutTitle'] ?></h2>
          <?php foreach ($D['aboutParas'] as $p): ?>
          <p><?= $p ?></p>
          <?php endforeach; ?>
          <div class="dest-about-tags">
            <?php foreach ($D['aboutTags'] as $t): ?>
            <span><i class="fa-solid fa-circle-check"></i> <?= $t ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="dest-about-img reveal">
          <img src="<?= h(dest_img_src($about_photo, $activeDest)) ?>" alt="<?= h($D['aboutAlt']) ?>" loading="lazy" decoding="async" width="800" height="600">
        </div>
      </div>
    </div>
  </section>

  <!-- HIGHLIGHTS -->
  <section class="dest-highlights section-pad" style="background:<?= $D['hlBg'] ?>">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="<?= h($D['hlTag'][0]) ?>"></i> <?= h($D['hlTag'][1]) ?></div>
        <h2 class="section-title"><?= $D['hlTitle'] ?></h2>
      </div>
      <div class="highlights-grid">
        <?php foreach ($D['highlights'] as $h): ?>
        <div class="highlight-card reveal">
          <div class="highlight-icon"><i class="fa-solid <?php echo $h[0]; ?>"></i></div>
          <h3><?php echo $h[1]; ?></h3>
          <p><?php echo $h[2]; ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- GALLERY -->
  <section class="dest-gallery section-pad">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="<?= h($D['galTag'][0]) ?>"></i> <?= h($D['galTag'][1]) ?></div>
        <h2 class="section-title"><?= $D['galTitle'] ?></h2>
      </div>
      <div class="dest-gallery-grid reveal">
        <?php if (!empty($db_photos)): ?>
          <?php foreach ($db_photos as $i => $p): ?>
          <div class="gallery-item<?= $i === 0 || $i === 4 ? ' gi-wide' : '' ?>">
            <img src="uploads/photos/<?= h($p['filename']) ?>" alt="<?= h($p['caption'] ?: $D['name']) ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <?php foreach ($D['galFallback'] as $g): /* $g[0] is a trimmed modifier class, e.g. gi-wide */ ?>
        <div class="gallery-item<?= $g[0] !== '' ? ' ' . h($g[0]) : '' ?>"><img src="<?= $g[1] ?>" alt="<?= h($g[2]) ?>" loading="lazy"></div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ROUTES -->
  <section class="dest-routes-section section-pad" style="background:<?= $D['rtBg'] ?>">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="<?= h($D['rtTag'][0]) ?>"></i> <?= h($D['rtTag'][1]) ?></div>
        <h2 class="section-title"><?= $D['rtTitle'] ?></h2>
        <p class="section-desc"><?= h($D['rtDesc']) ?></p>
      </div>
      <div class="dest-route-cards">
        <?php foreach ($D['routes'] as $r):
          /* [from, to, km, duration, badge, href, buttonLabel] — to/href/label
             are per-route because spiti's circuit card differs from its others. */ ?>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> <?= h($r[0]) ?></span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> <?= h($r[1]) ?></span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> <?= h($r[2]) ?></span>
            <span><i class="fa-regular fa-clock"></i> <?= h($r[3]) ?></span>
            <span class="drc-badge"><?= h($r[4]) ?></span>
          </div>
          <div class="drc-bottom">
            <a href="<?= h($r[5]) ?>" class="drc-btn"><?= h($r[6]) ?> <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if (!empty($D['advisory'])): $A = $D['advisory']; ?>
  <!-- IMPORTANT NOTE -->
  <section class="section-pad" style="background:<?= $A['bg'] ?>">
    <div class="container mx-auto px-3">
      <div class="spiti-advisory reveal">
        <div class="spiti-advisory-icon"><i class="<?= h($A['icon']) ?>"></i></div>
        <div>
          <h3><?= h($A['title']) ?></h3>
          <ul>
            <?php foreach ($A['items'] as $it): ?>
            <li><strong><?= h($it[0]) ?></strong> <?= h($it[1]) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- BOOKING CTA -->
  <section class="dest-cta-strip">
    <div class="container mx-auto px-3">
      <div class="dest-cta-inner reveal">
        <div class="dest-cta-text">
          <?php /* carries <span> emphasis — raw by design */ ?>
          <h2><?= $D['ctaH'] ?></h2>
          <p><?= h($D['ctaP']) ?></p>
        </div>
        <div class="dest-cta-btns">
          <?php /* href/icon/label are data: spiti routes to #contact with its own wording. */ ?>
          <a href="<?= h($D['ctaHref']) ?>" class="btn orange-btn"><i class="<?= h($D['ctaIcon']) ?>"></i> <?= h($D['ctaBtn']) ?></a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo rawurlencode($D['ctaWa']); ?>" target="_blank" rel="noopener" class="btn wa-cta-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge</a>
        </div>
      </div>
    </div>
  </section>

  <?php if (!empty($D['seasons'])): ?>
  <!-- BEST TIME -->
  <section class="dest-besttime section-pad">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="<?= h($D['btTag'][0]) ?>"></i> <?= h($D['btTag'][1]) ?></div>
        <h2 class="section-title"><?= $D['btTitle'] ?></h2>
      </div>
      <div class="season-grid">
        <?php foreach ($D['seasons'] as $i => $s): ?>
        <div class="season-card reveal<?= $i ? ' reveal-delay-' . $i : '' ?>">
          <div class="season-icon <?= h($s[0]) ?>"><i class="<?= h($s[1]) ?>"></i></div>
          <h3><?= h($s[2]) ?></h3>
          <p><?= h($s[3]) ?></p>
          <?php /* star markup, raw by design */ ?>
          <div class="season-rating"><?= $s[4] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php require __DIR__ . '/foot.php'; ?>
</body>
</html>
