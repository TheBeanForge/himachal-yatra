<?php
$base       = 'index.php';
$activeDest = 'dalhousie';
require_once 'includes/vars.php';
require_once 'api/config.php';
$db_photos = [];
if ($conn instanceof mysqli) {
    $stmt = $conn->prepare('SELECT filename, caption FROM photos WHERE destination = ? ORDER BY sort_order ASC, uploaded_at ASC');
    $stmt->bind_param('s', $activeDest);
    $stmt->execute();
    $db_photos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dalhousie Tour Packages &amp; Cab Booking | Himachal Yatra Travels</title>
  <meta name="description" content="Book Delhi to Dalhousie cab from ₹9,499. Khajjiar (mini Switzerland), Dainkund Peak, Chamera Lake — peaceful hill station with Himachal Yatra Travels.">
  <meta name="theme-color" content="#0d0d14">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

<main>

  <!-- DEST HERO -->
  <section class="dest-hero">
    <div class="dest-hero-bg">
      <?php $heroImg = !empty($db_photos[0]) ? 'uploads/photos/' . h($db_photos[0]['filename']) : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=90'; ?>
      <img src="<?= $heroImg ?>" alt="Dalhousie Khajjiar Himachal Pradesh" fetchpriority="high" decoding="async" width="1920" height="1080">
    </div>
    <div class="dest-hero-overlay"></div>
    <div class="container mx-auto px-3 dest-hero-content">
      <nav class="dest-breadcrumb" aria-label="breadcrumb">
        <a href="index.php">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="index.php#routes">Destinations</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Dalhousie</span>
      </nav>
      <div class="dest-hero-tag"><i class="fa-solid fa-mountain-sun"></i> Himachal Pradesh</div>
      <h1>Dalhousie</h1>
      <p>Scotland of India — colonial bungalows, misty meadows and the magical Khajjiar</p>
      <div class="dest-hero-facts">
        <div class="dest-fact"><i class="fa-solid fa-mountain"></i><span>2,036 m Altitude</span></div>
        <div class="dest-fact"><i class="fa-regular fa-calendar"></i><span>Mar – Jun, Oct – Nov</span></div>
        <div class="dest-fact"><i class="fa-solid fa-route"></i><span>560 km from Delhi</span></div>
        <div class="dest-fact"><i class="fa-solid fa-temperature-half"></i><span>−1°C to 24°C</span></div>
      </div>
      <a href="index.php#contact" class="dest-hero-btn"><i class="fa-brands fa-whatsapp"></i> Plan Dalhousie Trip</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="dest-about section-pad">
    <div class="container mx-auto px-3">
      <div class="dest-about-grid">
        <div class="dest-about-text reveal">
          <div class="section-tag"><i class="fa-solid fa-info-circle"></i> About Dalhousie</div>
          <h2 class="section-title">Himachal's <span>Serene</span> Retreat</h2>
          <p>Dalhousie is a quaint colonial hill station spread over five hills — Kathalagh, Potreyn, Terah, Bakrota and Bhangora — in the Chamba district of Himachal Pradesh. Named after Lord Dalhousie, who established it as a British summer retreat in 1854, it retains a quiet, unhurried charm unlike the more touristy Shimla or Manali.</p>
          <p>Just 22 km from Dalhousie lies Khajjiar — famously called the "Mini Switzerland of India" — a saucer-shaped meadow with a small lake at its center, ringed by dense Deodar forests and backed by snow-capped Dhauladhar peaks. It is one of the most photographed spots in Himachal Pradesh.</p>
          <div class="dest-about-tags">
            <span><i class="fa-solid fa-circle-check"></i> Khajjiar Meadows</span>
            <span><i class="fa-solid fa-circle-check"></i> Colonial Heritage</span>
            <span><i class="fa-solid fa-circle-check"></i> Dense Deodar Forests</span>
            <span><i class="fa-solid fa-circle-check"></i> Chamera Lake</span>
          </div>
        </div>
        <div class="dest-about-img reveal">
          <?php $aboutImg = !empty($db_photos[1]) ? 'uploads/photos/' . h($db_photos[1]['filename']) : (!empty($db_photos[0]) ? 'uploads/photos/' . h($db_photos[0]['filename']) : 'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=800&q=85'); ?>
          <img src="<?= $aboutImg ?>" alt="Dalhousie green hills Himachal" loading="lazy" decoding="async" width="800" height="600">
        </div>
      </div>
    </div>
  </section>

  <!-- HIGHLIGHTS -->
  <section class="dest-highlights section-pad" style="background:var(--green-light)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-star"></i> Must Visit</div>
        <h2 class="section-title">Top Highlights of <span>Dalhousie</span></h2>
      </div>
      <div class="highlights-grid">
        <?php
        $highlights = [
          ['fa-leaf',          'Khajjiar',          '"Mini Switzerland of India" — a magical circular meadow with a lake, Deodar forest and Dhauladhar backdrop.'],
          ['fa-mountain',      'Dainkund Peak',     'Highest point near Dalhousie at 2,755 m. A 2-km trek with panoramic 360° views of the Chamba Valley.'],
          ['fa-water',         'Chamera Lake',      'A beautiful reservoir lake on the Ravi River. Boating, picnicking and scenic views — perfect family outing.'],
          ['fa-person-hiking', 'Bakrota Hills Walk','A 5-km circular walk through oak and rhododendron forests. Best walk in Dalhousie for sunrise views.'],
          ['fa-temple',        'Kalatop Wildlife',  'Kalatop-Khajjiar Wildlife Sanctuary — Himalayan black bear, barking deer and incredible birdlife in Deodar forest.'],
          ['fa-church',        'St. John\'s Church', 'Beautiful 1863 Gothic church in the heart of Dalhousie. A peaceful colonial-era landmark.'],
          ['fa-person-walking','Gandhi Chowk',      'Lively central square with shops, restaurants and Subhash Baoli — the historic spring visited by Subhas Chandra Bose.'],
          ['fa-monument',      'Chamba Town',       '56 km from Dalhousie. Ancient Chamba Kingdom palaces, Lakshmi Narayan temples and the famous Manimahesh Lake.'],
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
        <h2 class="section-title">Dalhousie in <span>Pictures</span></h2>
      </div>
      <div class="dest-gallery-grid reveal">
        <?php if (!empty($db_photos)): ?>
          <?php foreach ($db_photos as $i => $p): ?>
          <div class="gallery-item<?= $i === 0 || $i === 4 ? ' gi-wide' : '' ?>">
            <img src="uploads/photos/<?= h($p['filename']) ?>" alt="<?= h($p['caption'] ?: 'Dalhousie') ?>" loading="lazy">
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=85" alt="Dalhousie mountain meadow" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=700&q=85" alt="Green hills Dalhousie" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=700&q=85" alt="Forest trail Khajjiar" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=700&q=85" alt="Deodar forest Dalhousie" loading="lazy"></div>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=85" alt="Mountain road Dalhousie" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85" alt="Snow peaks near Dalhousie" loading="lazy"></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ROUTES -->
  <section class="dest-routes-section section-pad" style="background:var(--cream)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-route"></i> Book a Cab</div>
        <h2 class="section-title">Routes to <span>Dalhousie</span></h2>
        <p class="section-desc">We cover all major routes to Dalhousie — comfortable cabs, experienced drivers, transparent fares.</p>
      </div>
      <div class="dest-route-cards">
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Delhi</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Dalhousie</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 560 km</span>
            <span><i class="fa-regular fa-clock"></i> 11–13 hrs</span>
            <span class="drc-badge">Hill Escape Package</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Starting ₹9,499</div>
            <a href="index.php#contact" class="drc-btn">Request Quote <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Pathankot</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Dalhousie</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 80 km</span>
            <span><i class="fa-regular fa-clock"></i> 2–3 hrs</span>
            <span class="drc-badge">Airport Transfer</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Starting ₹2,200</div>
            <a href="index.php#contact" class="drc-btn">Request Quote <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Amritsar</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Dalhousie</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 200 km</span>
            <span><i class="fa-regular fa-clock"></i> 4–5 hrs</span>
            <span class="drc-badge">Punjab Combo</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Starting ₹4,999</div>
            <a href="index.php#contact" class="drc-btn">Request Quote <i class="fa-solid fa-arrow-right"></i></a>
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
          <h2>Escape to <span>Dalhousie?</span></h2>
          <p>Share your dates and route preferences. We will shape a relaxed Dalhousie and Khajjiar plan with a clear quote.</p>
        </div>
        <div class="dest-cta-btns">
          <a href="index.php#contact" class="btn orange-btn"><i class="fa-solid fa-calendar-check"></i> Request Quote</a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo rawurlencode('Hi, I want a private Dalhousie/Khajjiar trip quote. Please help me plan the route.'); ?>" target="_blank" rel="noopener" class="btn wa-cta-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge</a>
        </div>
      </div>
    </div>
  </section>

  <!-- BEST TIME -->
  <section class="dest-besttime section-pad">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-regular fa-calendar"></i> Travel Tips</div>
        <h2 class="section-title">Best Time to Visit <span>Dalhousie</span></h2>
      </div>
      <div class="season-grid">
        <div class="season-card reveal">
          <div class="season-icon season-summer"><i class="fa-solid fa-sun"></i></div>
          <h3>Summer (Mar–Jun)</h3>
          <p>Perfect escape from North Indian heat. 10°C–24°C. Khajjiar at its greenest. Most popular time to visit.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-1">
          <div class="season-icon season-monsoon"><i class="fa-solid fa-cloud-rain"></i></div>
          <h3>Monsoon (Jul–Sep)</h3>
          <p>Lush green Khajjiar meadows at their most beautiful. Rainfall can be heavy. Roads generally safe.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-2">
          <div class="season-icon season-autumn"><i class="fa-solid fa-leaf"></i></div>
          <h3>Autumn (Oct–Nov)</h3>
          <p>Clear skies, golden forests. First snow on Dhauladhar peaks. Excellent for photography and nature walks.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-3">
          <div class="season-icon season-winter"><i class="fa-solid fa-snowflake"></i></div>
          <h3>Winter (Dec–Feb)</h3>
          <p>Heavy snowfall, −1°C to 10°C. Khajjiar under snow is breathtaking. Ideal for snow lovers and honeymooners.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require 'includes/foot.php'; ?>
</body>
</html>


