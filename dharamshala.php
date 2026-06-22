<?php
$base       = 'index.php';
$activeDest = 'dharamshala';
require_once 'includes/vars.php';
$db_photos = []; $hero_photo = null; $about_photo = null;
if ($conn instanceof mysqli) {
    [$hero_photo, $about_photo, $db_photos] = load_dest_photos($conn, $activeDest);
    $conn->close();
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dharamshala &amp; McLeodganj Tour | Himachal Safar</title>
  <meta name="description" content="Book Delhi to Dharamshala. McLeodganj, Triund Trek, Bhagsu Waterfall, Dalai Lama Temple — expert Kangra Valley travel with Himachal Safar.">
  <?php
    $seoTitle = 'Dharamshala & McLeodganj Tour | Himachal Safar';
    $seoDesc  = 'Book Delhi to Dharamshala. McLeodganj, Triund Trek, Bhagsu Waterfall, Dalai Lama Temple — expert Kangra Valley travel with Himachal Safar.';
    $seoPath  = 'dharamshala.php';
    // Per-page social share image = this destination's own hero (falls back to generic default).
    $SITE_URL = rtrim(getenv('SITE_URL') ?: 'https://himachalsafar.com', '/');
    if ($hero_photo) $seoImage = $SITE_URL . '/uploads/photos/' . $hero_photo['filename'];
    include __DIR__ . '/includes/seo_head.php';
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo @filemtime(__DIR__ . '/style.css'); ?>">
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

<main>

  <!-- DEST HERO -->
  <section class="dest-hero">
    <div class="dest-hero-bg">
      <img src="<?= $hero_photo ? 'uploads/photos/' . h($hero_photo['filename']) : '' ?>" alt="Dharamshala McLeodganj Himachal Pradesh" fetchpriority="high" decoding="async" width="1920" height="1080">
    </div>
    <div class="dest-hero-overlay"></div>
    <div class="container mx-auto px-3 dest-hero-content">
      <nav class="dest-breadcrumb" aria-label="breadcrumb">
        <a href="index.php">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="index.php#routes">Destinations</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Dharamshala</span>
      </nav>
      <div class="dest-hero-tag"><i class="fa-solid fa-mountain-sun"></i> Himachal Pradesh</div>
      <h1>Dharamshala</h1>
      <p>Little Lhasa of India — Tibetan culture, misty mountains and the Triund trail</p>
      <div class="dest-hero-facts">
        <div class="dest-fact"><i class="fa-solid fa-mountain"></i><span>1,457 m Altitude</span></div>
        <div class="dest-fact"><i class="fa-regular fa-calendar"></i><span>Mar – Nov Best Time</span></div>
        <div class="dest-fact"><i class="fa-solid fa-route"></i><span>480 km from Delhi</span></div>
        <div class="dest-fact"><i class="fa-solid fa-temperature-half"></i><span>5°C to 28°C</span></div>
      </div>
      <a href="index.php?calc=dharamshala" class="dest-hero-btn"><i class="fa-solid fa-calculator"></i> Plan Dharamshala Trip</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="dest-about section-pad">
    <div class="container mx-auto px-3">
      <div class="dest-about-grid">
        <div class="dest-about-text reveal">
          <div class="section-tag"><i class="fa-solid fa-info-circle"></i> About Dharamshala</div>
          <h2 class="section-title">Home of the <span>Dalai Lama</span></h2>
          <p>Dharamshala is a unique blend of Indian and Tibetan culture, nestled in the Kangra Valley with the Dhauladhar range as a dramatic backdrop. The upper part, McLeodganj, is known as "Little Lhasa" — the seat of the Tibetan government-in-exile and residence of His Holiness the 14th Dalai Lama.</p>
          <p>McLeodganj's cobblestone streets are lined with Tibetan cafés, monasteries, handcraft shops and trekking agencies. The Triund trek above McLeodganj is one of the most accessible and stunning high-altitude treks in Himachal Pradesh — a must-do for any visitor.</p>
          <div class="dest-about-tags">
            <span><i class="fa-solid fa-circle-check"></i> Tibetan Culture</span>
            <span><i class="fa-solid fa-circle-check"></i> Triund Trek</span>
            <span><i class="fa-solid fa-circle-check"></i> Cricket Stadium</span>
            <span><i class="fa-solid fa-circle-check"></i> Waterfall Walks</span>
          </div>
        </div>
        <div class="dest-about-img reveal">
          <img src="<?= $about_photo ? 'uploads/photos/' . h($about_photo['filename']) : '' ?>" alt="Dharamshala Kangra valley" loading="lazy" decoding="async" width="800" height="600">
        </div>
      </div>
    </div>
  </section>

  <!-- HIGHLIGHTS -->
  <section class="dest-highlights section-pad" style="background:var(--green-light)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-star"></i> Must Visit</div>
        <h2 class="section-title">Top Highlights of <span>Dharamshala</span></h2>
      </div>
      <div class="highlights-grid">
        <?php
        $highlights = [
          ['fa-om',            'Namgyal Monastery', 'Residence monastery of the Dalai Lama. Visitors can attend prayer sessions and learn about Tibetan Buddhism.'],
          ['fa-person-hiking', 'Triund Trek',       '9 km trek from McLeodganj to 2,875 m. Stunning 360° Dhauladhar views. Camping available at the top.'],
          ['fa-water',         'Bhagsu Waterfall',  '20-minute walk from McLeodganj. Beautiful two-tiered waterfall in a rocky gorge — perfect day walk.'],
          ['fa-baseball',      'HPCA Cricket Stadium','One of the most scenic cricket grounds in the world, surrounded by snow-capped mountains. Iconic venue.'],
          ['fa-place-of-worship','Dalai Lama Temple','The Tsuglagkhang complex — monks debating, prayer wheels and the magnificent central temple.'],
          ['fa-water',         'Dal Lake Dharamshala','5 km from McLeodganj. Small mountain lake surrounded by cedar trees — peaceful picnic spot.'],
          ['fa-mountain',      'Dharamkot',         'Quiet village above McLeodganj. Yoga retreats, rock climbing and trekking base for Triund and Indrahar Pass.'],
          ['fa-church',        'Church of St. John', 'Gothic church built in 1852, in loving memory of Lord Elgin. Set in a quiet forest — serene heritage site.'],
        ];
        foreach ($highlights as $hl): ?>
        <div class="highlight-card reveal">
          <div class="highlight-icon"><i class="fa-solid <?php echo $hl[0]; ?>"></i></div>
          <h3><?php echo $hl[1]; ?></h3>
          <p><?php echo $hl[2]; ?></p>
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
        <h2 class="section-title">Dharamshala in <span>Pictures</span></h2>
      </div>
      <div class="dest-gallery-grid reveal">
        <?php if (!empty($db_photos)): ?>
          <?php foreach ($db_photos as $i => $p): ?>
          <div class="gallery-item<?= $i === 0 || $i === 4 ? ' gi-wide' : '' ?>">
            <img src="uploads/photos/<?= h($p['filename']) ?>" alt="<?= h($p['caption'] ?: 'Dharamshala') ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1200&q=85" alt="Dharamshala mountain view" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=700&q=85" alt="Mountain trek Triund" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=700&q=85" alt="Kangra valley landscape" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=700&q=85" alt="Forest hills Dharamshala" loading="lazy"></div>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1200&q=85" alt="Pine forest McLeodganj" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=700&q=85" alt="Nature landscape Himachal" loading="lazy"></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ROUTES -->
  <section class="dest-routes-section section-pad" style="background:var(--cream)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-route"></i> Book a Cab</div>
        <h2 class="section-title">Routes to <span>Dharamshala</span></h2>
        <p class="section-desc">Book your cab from Delhi, Chandigarh or Pathankot — our drivers know the Kangra Valley roads perfectly.</p>
      </div>
      <div class="dest-route-cards">
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Delhi</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Dharamshala</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 480 km</span>
            <span><i class="fa-regular fa-clock"></i> 10–12 hrs</span>
            <span class="drc-badge">McLeodganj Special</span>
          </div>
          <div class="drc-bottom">
            
            <a href="index.php?calc=dharamshala" class="drc-btn">Plan My Trip <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Chandigarh</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Dharamshala</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 245 km</span>
            <span><i class="fa-regular fa-clock"></i> 6–7 hrs</span>
            <span class="drc-badge">Kangra Package</span>
          </div>
          <div class="drc-bottom">
            
            <a href="index.php?calc=dharamshala" class="drc-btn">Plan My Trip <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Pathankot</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Dharamshala</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 100 km</span>
            <span><i class="fa-regular fa-clock"></i> 2–3 hrs</span>
            <span class="drc-badge">Airport Transfer</span>
          </div>
          <div class="drc-bottom">
            
            <a href="index.php?calc=dharamshala" class="drc-btn">Plan My Trip <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="dest-cta-strip">
    <div class="container mx-auto px-3">
      <div class="dest-cta-inner reveal">
        <div class="dest-cta-text">
          <h2>Plan Your <span>Dharamshala Trip?</span></h2>
          <p>Share your travel dates and pickup city. We will plan a clear Kangra Valley route with the right vehicle and driver.</p>
        </div>
        <div class="dest-cta-btns">
          <a href="index.php?calc=dharamshala" class="btn orange-btn"><i class="fa-solid fa-calculator"></i> Plan My Trip</a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo rawurlencode('Hi, I want a private Dharamshala/McLeodganj trip quote. Please help me plan the route.'); ?>" target="_blank" rel="noopener" class="btn wa-cta-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge</a>
        </div>
      </div>
    </div>
  </section>

  <!-- BEST TIME -->
  <section class="dest-besttime section-pad">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-regular fa-calendar"></i> Travel Tips</div>
        <h2 class="section-title">Best Time to Visit <span>Dharamshala</span></h2>
      </div>
      <div class="season-grid">
        <div class="season-card reveal">
          <div class="season-icon season-summer"><i class="fa-solid fa-sun"></i></div>
          <h3>Spring/Summer (Mar–Jun)</h3>
          <p>Best weather for Triund trek, 10°C–25°C. Rhododendrons in bloom. Perfect for outdoor activities.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-1">
          <div class="season-icon season-monsoon"><i class="fa-solid fa-cloud-rain"></i></div>
          <h3>Monsoon (Jul–Sep)</h3>
          <p>Heavy rainfall. Landslide risk. Triund trek not advisable. However, valleys are lush and beautiful.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-2">
          <div class="season-icon season-autumn"><i class="fa-solid fa-leaf"></i></div>
          <h3>Autumn (Oct–Nov)</h3>
          <p>Clear mountain views, crisp air. Triund covered in snow by November. Best for photography.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-3">
          <div class="season-icon season-winter"><i class="fa-solid fa-snowflake"></i></div>
          <h3>Winter (Dec–Feb)</h3>
          <p>Heavy snow above McLeodganj. Town itself is pleasant. Snowfall on Dhauladhar range is spectacular.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require 'includes/foot.php'; ?>
</body>
</html>







