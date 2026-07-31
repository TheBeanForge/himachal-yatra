<?php
$base       = 'index.php';
$activeDest = 'manali';
require_once 'includes/vars.php';
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
  <title>Manali Trips &amp; Cab Booking | Himachal Safar</title>
  <meta name="description" content="Plan your Manali trip with Himachal Safar. Delhi to Manali. Rohtang Pass, Solang Valley, Hadimba Temple — expert mountain drivers.">
  <?php
    $seoTitle = 'Manali Trips & Cab Booking | Himachal Safar';
    $seoDesc  = 'Plan your Manali trip with Himachal Safar. Delhi to Manali. Rohtang Pass, Solang Valley, Hadimba Temple — expert mountain drivers.';
    $seoPath  = 'manali.php';
    // Per-page social share image = this destination's own hero (falls back to generic default).
    $SITE_URL = rtrim(getenv('SITE_URL') ?: 'https://himachalsafar.com', '/');
    if ($hero_photo) $seoImage = $SITE_URL . '/uploads/photos/' . $hero_photo['filename'];
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

    gtag('config', 'G-4GZDXE68YZ');
  </script>
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

<main>

  <!-- DEST HERO -->
  <section class="dest-hero" id="home">
    <div class="dest-hero-bg">
      <img src="<?= h(dest_img_src($hero_photo, $activeDest)) ?>" alt="Manali snow mountains Himachal Pradesh" fetchpriority="high" decoding="async" width="1920" height="1080">
    </div>
    <div class="dest-hero-overlay"></div>
    <div class="container mx-auto px-3 dest-hero-content">
      <nav class="dest-breadcrumb" aria-label="breadcrumb">
        <a href="index.php">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="index.php#routes">Destinations</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Manali</span>
      </nav>
      <div class="dest-hero-tag"><i class="fa-solid fa-mountain-sun"></i> Himachal Pradesh</div>
      <h1>Manali</h1>
      <p>The Valley of the Gods — Himalayan peaks, alpine meadows and the spirit of adventure</p>
      <div class="dest-hero-facts">
        <div class="dest-fact"><i class="fa-solid fa-mountain"></i><span>2,050 m Altitude</span></div>
        <div class="dest-fact"><i class="fa-regular fa-calendar"></i><span>Oct – Jun Best Time</span></div>
        <div class="dest-fact"><i class="fa-solid fa-route"></i><span>550 km from Delhi</span></div>
        <div class="dest-fact"><i class="fa-solid fa-temperature-half"></i><span>−2°C to 25°C</span></div>
      </div>
      <a href="index.php?calc=manali" class="dest-hero-btn"><i class="fa-solid fa-calculator"></i> Plan Manali Trip</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="dest-about section-pad">
    <div class="container mx-auto px-3">
      <div class="dest-about-grid">
        <div class="dest-about-text reveal">
          <div class="section-tag"><i class="fa-solid fa-info-circle"></i> About Manali</div>
          <h2 class="section-title">The <span>Crown Jewel</span> of Himachal Pradesh</h2>
          <p>Manali is a high-altitude Himalayan resort town nestled in the Beas River Valley, at the northern end of the Kullu Valley. Sitting at an elevation of 2,050 metres, it is one of the most visited hill stations in India — and for good reason.</p>
          <p>From the thundering Rohtang Pass at 3,978 metres to the serene apple orchards of Old Manali, from the glaciers of Solang Valley to the sacred Hadimba Devi Temple — Manali offers every kind of Himalayan experience. Whether you are a honeymooner, adventurer, family or solo traveller, Manali delivers.</p>
          <div class="dest-about-tags">
            <span><i class="fa-solid fa-circle-check"></i> Honeymoon Destination</span>
            <span><i class="fa-solid fa-circle-check"></i> Adventure Sports</span>
            <span><i class="fa-solid fa-circle-check"></i> Trekking &amp; Camping</span>
            <span><i class="fa-solid fa-circle-check"></i> Snow Activities</span>
          </div>
        </div>
        <div class="dest-about-img reveal">
          <img src="<?= h(dest_img_src($about_photo, $activeDest)) ?>" alt="Manali valley Himalaya" loading="lazy" decoding="async" width="800" height="600">
        </div>
      </div>
    </div>
  </section>

  <!-- HIGHLIGHTS -->
  <section class="dest-highlights section-pad" style="background:var(--green-light)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-star"></i> Must Visit</div>
        <h2 class="section-title">Top Highlights of <span>Manali</span></h2>
      </div>
      <div class="highlights-grid">
        <?php
        $highlights = [
          ['fa-road',          'Rohtang Pass',      '3,978 m high mountain pass with snow year-round. Gateway to Lahaul–Spiti. Open June to November.'],
          ['fa-skiing',        'Solang Valley',     'Adventure hub — skiing, zorbing, paragliding, rope crossing. 14 km from Manali town.'],
          ['fa-place-of-worship','Hadimba Temple',  '16th-century wooden temple in a cedar forest. Dedicated to Goddess Hadimba — serene and sacred.'],
          ['fa-city',          'Old Manali',        'Hippie cafés, budget stays, art shops and the charming Manu Temple above the Beas River.'],
          ['fa-landmark',        'Naggar Castle',     '500-year-old stone castle, now a heritage hotel. Stunning views over the Kullu Valley.'],
          ['fa-water',         'Beas River',        'White-water rafting stretches from Pirdi to Jhiri. River walks and riverside camping available.'],
          ['fa-moon',          'Chandratal Lake',   '14,100 ft crescent-shaped lake. One of the most beautiful high-altitude lakes in the world.'],
          ['fa-motorcycle',    'Leh via Manali',    'The legendary Manali–Leh highway — one of the highest motorable roads on earth at 5,328 m.'],
        ];
        foreach ($highlights as $h): ?>
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
        <div class="section-tag"><i class="fa-solid fa-images"></i> Photo Gallery</div>
        <h2 class="section-title">Manali in <span>Pictures</span></h2>
      </div>
      <div class="dest-gallery-grid reveal">
        <?php if (!empty($db_photos)): ?>
          <?php foreach ($db_photos as $i => $p): ?>
          <div class="gallery-item<?= $i === 0 || $i === 4 ? ' gi-wide' : '' ?>">
            <img src="uploads/photos/<?= h($p['filename']) ?>" alt="<?= h($p['caption'] ?: 'Manali') ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&q=85" alt="Manali snow mountains" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=700&q=85" alt="Rohtang mountain road" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85" alt="Snow peaks Manali" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1457530378978-8bac673b8062?auto=format&fit=crop&w=700&q=85" alt="Solang Valley Manali" loading="lazy"></div>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?auto=format&fit=crop&w=1200&q=85" alt="Himalayan valley Manali" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=700&q=85" alt="Mountain lake Manali" loading="lazy"></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ROUTES TO MANALI -->
  <section class="dest-routes-section section-pad" style="background:var(--cream)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-route"></i> Book a Cab</div>
        <h2 class="section-title">Routes to <span>Manali</span></h2>
        <p class="section-desc">Choose your departure city — we cover all major routes to Manali with experienced mountain drivers.</p>
      </div>
      <div class="dest-route-cards">
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Delhi</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Manali</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 550 km</span>
            <span><i class="fa-regular fa-clock"></i> 12–14 hrs</span>
            <span class="drc-badge">Family Itinerary</span>
          </div>
          <div class="drc-bottom">
            
            <a href="index.php?calc=manali" class="drc-btn">Plan My Trip <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Chandigarh</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Manali</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 300 km</span>
            <span><i class="fa-regular fa-clock"></i> 8–9 hrs</span>
            <span class="drc-badge">Combo Itinerary</span>
          </div>
          <div class="drc-bottom">
            
            <a href="index.php?calc=manali" class="drc-btn">Plan My Trip <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Pathankot</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Manali</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 380 km</span>
            <span><i class="fa-regular fa-clock"></i> 9–11 hrs</span>
            <span class="drc-badge">Custom Itinerary</span>
          </div>
          <div class="drc-bottom">
            
            <a href="index.php?calc=manali" class="drc-btn">Plan My Trip <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BOOKING CTA -->
  <section class="dest-cta-strip">
    <div class="container mx-auto px-3">
      <div class="dest-cta-inner reveal">
        <div class="dest-cta-text">
          <h2>Plan a Private <span>Manali</span> Journey</h2>
          <p>Share your dates, guest count and hotel preferences. Our travel desk will respond with a clear route plan and quote.</p>
        </div>
        <div class="dest-cta-btns">
          <a href="index.php?calc=manali" class="btn orange-btn"><i class="fa-solid fa-calculator"></i> Plan My Trip</a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo rawurlencode('Hi, I want a private Manali trip quote. Please help me plan the route.'); ?>" target="_blank" rel="noopener" class="btn wa-cta-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge</a>
        </div>
      </div>
    </div>
  </section>

  <!-- BEST TIME -->
  <section class="dest-besttime section-pad">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-regular fa-calendar"></i> Travel Tips</div>
        <h2 class="section-title">Best Time to Visit <span>Manali</span></h2>
      </div>
      <div class="season-grid">
        <div class="season-card reveal">
          <div class="season-icon season-summer"><i class="fa-solid fa-sun"></i></div>
          <h3>Summer (Mar–Jun)</h3>
          <p>Perfect weather, 10°C–25°C. Roads to Rohtang and Solang open. Most popular season — book early.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-1">
          <div class="season-icon season-monsoon"><i class="fa-solid fa-cloud-rain"></i></div>
          <h3>Monsoon (Jul–Sep)</h3>
          <p>Heavy rains, landslide risk on highway. Some parts of Rohtang closed. Not recommended for first-time visitors.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-2">
          <div class="season-icon season-autumn"><i class="fa-solid fa-leaf"></i></div>
          <h3>Autumn (Oct–Nov)</h3>
          <p>Clear skies, golden foliage, fewer crowds. Snow starts from late October. Ideal for scenic drives.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-3">
          <div class="season-icon season-winter"><i class="fa-solid fa-snowflake"></i></div>
          <h3>Winter (Dec–Feb)</h3>
          <p>Heavy snowfall, −2°C to 10°C. Rohtang closed but Manali town looks magical. Perfect for snow lovers.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require 'includes/foot.php'; ?>
</body>
</html>





