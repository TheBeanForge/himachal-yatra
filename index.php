<?php
$base       = '';
$activeDest = '';
require_once 'includes/vars.php';

// Admin-selected homepage hero slideshow photos (up to 5). Empty → local seasonal fallback slides.
$heroSlides = array_slice(photos_in_slot($conn ?? null, 'home_hero'), 0, 5);

// Active fleet vehicles — only what the admin has added (Admin → Vehicles) is
// shown; there are no hardcoded defaults. No vehicles → the section is hidden.
$dbFleet = [];
if ($conn instanceof mysqli) {
    // Admin-set serial order first; older installs without sort_order fall back to price order.
    foreach (["SELECT vehicle_name, photo, seating_capacity FROM vehicles WHERE status='active' ORDER BY sort_order ASC, id ASC",
              "SELECT vehicle_name, photo, seating_capacity FROM vehicles WHERE status='active' ORDER BY daily_rate ASC"] as $fleetSql) {
        try {
            $rf = $conn->query($fleetSql);
            if ($rf) { $dbFleet = $rf->fetch_all(MYSQLI_ASSOC); break; }
        } catch (mysqli_sql_exception) {}
    }
}

// Load approved reviews (newest first). Falls back to a curated seed array when the table is empty / DB is down.
$db_reviews = [];
if ($conn instanceof mysqli) {
    try {
        $res = $conn->query("SELECT name, city, route, rating, review_text, photo FROM reviews WHERE status = 'approved' ORDER BY submitted_at DESC LIMIT 20");
        if ($res) $db_reviews = $res->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception) {
        // table may not exist yet — fall through to seed data
    }
}

// FAQ content — retained for potential reuse, but the luxury homepage does not
// render an FAQ block, so we DON'T emit FAQPage schema (no orphan rich snippet).
$faqs = [
  ['Can you plan a complete Himachal trip, not just a cab?', 'Yes. We start with private transport and can help coordinate route timing, sightseeing order, hotel movement, halt planning and multi-city itineraries.'],
  ['Which Himachal routes do you cover?', 'We regularly cover Shimla, Manali, Dharamshala, Dalhousie, Spiti Valley, Kullu, Kasol, McLeodganj, Khajjiar and custom routes from Delhi, Chandigarh and nearby cities.'],
  ['How do you suggest the right vehicle?', 'We recommend vehicles based on passenger count, luggage, route length, road conditions and season. Families and longer mountain routes usually work best with SUVs or Tempo Travellers.'],
  ['Are prices confirmed before booking?', 'Yes. We share the route, vehicle type, fare, inclusions and exclusions before confirmation so there is clarity before your journey starts.'],
  ['Do drivers know mountain and snow routes?', 'Our drivers are familiar with Himachal hill roads, long transfers, high-altitude routes and seasonal route changes. For snow or Spiti routes, we plan more carefully around access and weather.'],
];
$seoFaq = $faqs; // FAQ restored in the luxury layout — emit FAQPage rich-snippet schema.

// Honest aggregate rating for rich snippets — only from real approved reviews
// that are actually shown on the page (avoids Google's fake-review penalties).
$seoRatingValue = null; $seoRatingCount = 0; $seoReviews = [];
if (!empty($db_reviews)) {
    $sum = 0; foreach ($db_reviews as $r) $sum += (int) $r['rating'];
    $seoRatingCount = count($db_reviews);
    $seoRatingValue = round($sum / max(1, $seoRatingCount), 1);
    // Up to 5 real approved reviews for honest Review rich-snippet markup.
    foreach (array_slice($db_reviews, 0, 5) as $r) {
        $seoReviews[] = [
            'author' => $r['name'],
            'rating' => (int) $r['rating'],
            'text'   => $r['review_text'],
        ];
    }
}

// Load per-route photos from routes table
$route_photos = [];
if ($conn instanceof mysqli) {
    try {
        $res = $conn->query("SELECT r.id, r.name, r.km, r.duration, r.badge, r.dest_key, r.dest_page, p.filename
                             FROM routes r LEFT JOIN photos p ON r.photo_id = p.id
                             ORDER BY r.sort_order ASC");
        if ($res) $route_photos = $res->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception) {}
}

// Load pickup locations for booking modal dropdown
$pickup_locations = [];
if ($conn instanceof mysqli) {
    try {
        $res = $conn->query("SELECT city FROM pickup_locations WHERE active = 1 ORDER BY sort_order ASC");
        if ($res) $pickup_locations = $res->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception) {
        // table may not exist yet
    }
}

// ── Testimonials: real approved reviews (editorial), else a curated fallback. ──
$lux_reviews = [];
foreach ($db_reviews as $r) {
    $lux_reviews[] = ['name' => $r['name'], 'city' => $r['city'], 'text' => $r['review_text'], 'rating' => (int)($r['rating'] ?? 5), 'photo' => $r['photo'] ?? ''];
}
if (!$lux_reviews) {
    $lux_reviews = [
        ['name' => 'Anjali Kapoor', 'city' => 'Gurugram',   'text' => 'From the moment we landed, every detail was handled — a calm driver who knew the mountains, a stay with a view we still talk about, and an itinerary that never once felt rushed.', 'rating' => 5, 'photo' => ''],
        ['name' => 'Vikash Gupta',  'city' => 'Lucknow',    'text' => 'Spiti demands real expertise. Our guide read the roads, the weather and the altitude perfectly. Nine days, and not a single anxious moment — only the landscape.', 'rating' => 5, 'photo' => ''],
        ['name' => 'Priya Verma',   'city' => 'Chandigarh', 'text' => 'It felt less like booking a cab and more like having a friend in the hills plan everything for us. Quietly luxurious, exactly as promised.', 'rating' => 5, 'photo' => ''],
    ];
}
$lux_reviews = array_slice($lux_reviews, 0, 3);

// Homepage decorative images — admin-replaceable via Photo Gallery (general bucket),
// with mood fallbacks so the page is never bare.
$homeStoryImg   = slot_img_src($conn ?? null, 'home_story',   'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1100&q=80');
$homeBannerImg  = slot_img_src($conn ?? null, 'home_banner',  'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1920&q=80');
$homeContactImg = slot_img_src($conn ?? null, 'home_contact', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1920&q=80');

// Editorial featured destinations. Shimla & Spiti reuse their admin-managed cover photos
// (and link to their pages); Tirthan opens the planner.
$lux_dest = [
    ['title' => 'Shimla',        'href' => 'shimla.php', 'img' => slot_img_src($conn ?? null, 'shimla_hero', 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=900&q=80'), 'desc' => 'The colonial Queen of Hills — Mall Road evenings, cedar ridges and toy-train mornings.'],
    ['title' => 'Spiti Valley',  'href' => 'spiti.php',  'img' => slot_img_src($conn ?? null, 'spiti_hero',  'https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=900&q=80'), 'desc' => 'A high-desert wilderness of monasteries, cobalt skies and roads few ever travel.'],
    ['title' => 'Tirthan Valley', 'href' => '',          'img' => 'https://images.unsplash.com/photo-1571536802807-30451e3955d8?auto=format&fit=crop&w=900&q=80', 'desc' => 'Trout streams, slow mornings and forest stays on the quiet edge of the Great Himalayan National Park.'],
];

$lux_why = [
    ['icon' => 'fa-id-card-clip', 'title' => 'Private Chauffeurs',      'desc' => 'Vetted mountain drivers who know every bend, season and viewpoint — discreet, punctual and calm.'],
    ['icon' => 'fa-bell-concierge', 'title' => 'Handpicked Stays',      'desc' => 'Boutique lodges and heritage retreats, personally selected for character, comfort and the view.'],
    ['icon' => 'fa-compass',      'title' => 'Local Experts',           'desc' => 'On-the-ground specialists who plan around weather, access and the rhythm of the hills.'],
    ['icon' => 'fa-feather',      'title' => 'Personalized Itineraries', 'desc' => 'Every journey designed around your pace, your interests and the kind of trip you actually want.'],
];

$waLink = 'https://wa.me/' . h($whatsappNumber) . '?text=' . h($defaultMessage);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Himachal Safar | Custom Himachal Trips, Cabs &amp; Travel Planning</title>
  <meta name="description" content="Plan a personalised Himachal trip with Himachal Safar. Tailored itineraries, premium cabs, verified mountain drivers and curated stays across Shimla, Manali, Spiti and beyond — built around you.">
  <meta name="keywords" content="Himachal Safar, custom Himachal trip, Himachal travel planner, Delhi to Manali cab, Delhi to Shimla taxi, Spiti Valley trip, tempo traveller Himachal, Dharamshala cab booking">
  <meta name="robots" content="index, follow">
  <?php
    $seoTitle = 'Himachal Safar | Custom Himachal Trips, Cabs & Travel Planning';
    $seoDesc  = 'Plan a personalised Himachal trip with Himachal Safar. Tailored itineraries, premium cabs, verified mountain drivers and curated stays across Shimla, Manali, Spiti and beyond — built around you.';
    $seoPath  = '';
    include __DIR__ . '/includes/seo_head.php';
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo @filemtime(__DIR__ . '/style.css'); ?>">
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

  <main class="lux">

    <!-- ═══ 1 · HERO ═══ -->
    <section class="lux-hero" id="home">
      <div class="hero-bg" aria-hidden="true">
        <?php if (!empty($heroSlides)): $n = count($heroSlides); ?>
          <?php foreach ($heroSlides as $i => $s): ?>
          <div class="hero-slide" style="background-image:url('uploads/photos/<?= h($s['filename']) ?>');<?= $n > 1 ? ' animation-delay:' . ($i * 5) . 's;' : '' ?>"></div>
          <?php endforeach; ?>
          <?php if ($n > 1):
            $T = $n * 5; $slot = 100 / $n; $fade = 20 / $n; ?>
          <style>
          .hero-bg .hero-slide { animation-name: heroFadeDyn; animation-duration: <?= $T ?>s; }
          @keyframes heroFadeDyn {
            0% { opacity: 1; }
            <?= round($slot, 3) ?>% { opacity: 1; }
            <?= round($slot + $fade, 3) ?>% { opacity: 0; }
            <?= round(100 - $fade, 3) ?>% { opacity: 0; }
            100% { opacity: 1; }
          }
          </style>
          <?php else: ?>
          <style>.hero-bg .hero-slide { animation: none; opacity: 1; }</style>
          <?php endif; ?>
        <?php else: ?>
        <!-- Local illustrated seasonal fallback slides (spring · summer · autumn · winter). -->
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
        <?php endif; ?>
      </div>
      <div class="lux-hero-scrim" aria-hidden="true"></div>

      <div class="lux-hero-inner">
        <span class="lux-eyebrow light reveal">Private Himachal Concierge</span>
        <h1 class="lux-hero-title reveal reveal-delay-1">Explore Himachal Through<br>Handpicked Local Experiences</h1>
        <p class="lux-hero-sub reveal reveal-delay-2">Private cabs, curated stays, local guides, and personalized itineraries across Himachal Pradesh.</p>
        <div class="lux-cta-row reveal reveal-delay-3">
          <button type="button" class="lux-btn lux-btn-gold" data-open-quote="">Plan My Journey</button>
          <a class="lux-btn lux-btn-ghost" href="<?= $waLink ?>" target="_blank" rel="noopener" data-wa-lead data-source="hero"><i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge</a>
        </div>
      </div>

      <a href="#about" class="lux-scroll-cue" aria-label="Scroll to explore"><span></span></a>
    </section>

    <!-- ═══ 2 · STORY — local experts ═══ -->
    <section class="lux-story" id="about">
      <div class="lux-container lux-story-grid">
        <div class="lux-story-media reveal">
          <img src="<?= h($homeStoryImg) ?>" alt="A quiet valley in the Himachal Himalaya" loading="lazy" width="1100" height="1320">
          <span class="lux-story-tag">Himachal specialists since 2016</span>
        </div>
        <div class="lux-story-text reveal reveal-delay-1">
          <span class="lux-eyebrow">Who We Are</span>
          <h2 class="lux-h2">Your Local Experts<br>in Himachal</h2>
          <p class="lux-lead">We create journeys beyond standard tourist routes — combining local knowledge, private transport, carefully selected stays, and authentic experiences.</p>
          <p class="lux-body">From the first conversation to the final drop-off, a single travel desk stays with you — reading the weather, the roads and the season so that every day unfolds quietly in your favour.</p>
          <button type="button" class="lux-textlink" data-open-quote="">Begin your itinerary <span aria-hidden="true">→</span></button>
        </div>
      </div>
    </section>

    <!-- ═══ 3 · FULL-WIDTH SCENIC BANNER ═══ -->
    <section class="lux-banner reveal" style="background-image:url('<?= h($homeBannerImg) ?>')">
      <div class="lux-banner-scrim" aria-hidden="true"></div>
      <div class="lux-banner-inner">
        <h2 class="lux-h2 light">The Mountains Are Waiting</h2>
        <p class="lux-banner-sub">Travel stress-free with private transport, verified accommodations, and local expertise.</p>
        <button type="button" class="lux-btn lux-btn-gold" data-open-quote="">Start Planning</button>
      </div>
    </section>

    <!-- ═══ 4 · FEATURED DESTINATIONS ═══ -->
    <section class="lux-section lux-dest" id="destinations">
      <div class="lux-container">
        <div class="lux-head reveal">
          <span class="lux-eyebrow">Featured Destinations</span>
          <h2 class="lux-h2">Places We Know Intimately</h2>
        </div>
        <div class="lux-dest-grid">
          <?php foreach ($lux_dest as $d): ?>
          <article class="lux-card reveal">
            <div class="lux-card-media">
              <img src="<?= h($d['img']) ?>" alt="<?= h($d['title']) ?>, Himachal Pradesh" loading="lazy" width="900" height="1100">
            </div>
            <div class="lux-card-body">
              <h3 class="lux-card-title"><?= h($d['title']) ?></h3>
              <p class="lux-card-desc"><?= h($d['desc']) ?></p>
              <?php if ($d['href']): ?>
              <a class="lux-textlink" href="<?= h($d['href']) ?>">Explore <span aria-hidden="true">→</span></a>
              <?php else: ?>
              <button type="button" class="lux-textlink" data-open-quote="">Explore <span aria-hidden="true">→</span></button>
              <?php endif; ?>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══ 4b · SIGNATURE ROUTES ═══ -->
    <section class="lux-section lux-routes" id="routes">
      <div class="lux-container">
        <div class="lux-head reveal">
          <span class="lux-eyebrow">Signature Routes</span>
          <h2 class="lux-h2">Journeys We Plan Often</h2>
          <p class="lux-fleet-note">Private, route-led transfers planned for comfort, timing and mountain-road safety &mdash; or tell us your own.</p>
        </div>
        <div class="lux-dest-grid lux-routes-grid">
          <?php
          $fallbacks = [
            'manali'      => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=900&q=90',
            'shimla'      => 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=900&q=90',
            'dharamshala' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=90',
            'dalhousie'   => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=900&q=90',
            'spiti'       => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=900&q=90',
          ];
          $routes_list = !empty($route_photos) ? $route_photos : [
            ['name'=>'Delhi to Manali',      'km'=>'550 km','duration'=>'12-14 hrs','badge'=>'Popular Route',     'dest_key'=>'manali',     'dest_page'=>'manali.php',     'filename'=>null],
            ['name'=>'Delhi to Shimla',      'km'=>'350 km','duration'=>'8-9 hrs',  'badge'=>'Weekend Escape',    'dest_key'=>'shimla',     'dest_page'=>'shimla.php',     'filename'=>null],
            ['name'=>'Delhi to Dharamshala', 'km'=>'480 km','duration'=>'10-12 hrs','badge'=>'McLeodganj Retreat','dest_key'=>'dharamshala','dest_page'=>'dharamshala.php','filename'=>null],
            ['name'=>'Delhi to Dalhousie',   'km'=>'560 km','duration'=>'11-13 hrs','badge'=>'Heritage Hills',    'dest_key'=>'dalhousie',  'dest_page'=>'dalhousie.php',  'filename'=>null],
            ['name'=>'Chandigarh to Manali', 'km'=>'300 km','duration'=>'8-9 hrs',  'badge'=>'Comfort Transfer',  'dest_key'=>'manali',     'dest_page'=>'manali.php',     'filename'=>null],
            ['name'=>'Chandigarh to Shimla', 'km'=>'115 km','duration'=>'3-4 hrs',  'badge'=>'Quick Mountain Run','dest_key'=>'shimla',     'dest_page'=>'shimla.php',     'filename'=>null],
          ];
          foreach ($routes_list as $route):
            $img = $route['filename'] ? 'uploads/photos/' . h($route['filename']) : ($fallbacks[$route['dest_key']] ?? '');
          ?>
          <article class="lux-card reveal">
            <div class="lux-card-media"><img src="<?= $img ?>" alt="<?= h($route['name']) ?> cab booking Himachal" loading="lazy" decoding="async" width="900" height="600"></div>
            <div class="lux-card-body">
              <span class="lux-route-badge"><?= h($route['badge']) ?></span>
              <h3 class="lux-card-title"><?= h($route['name']) ?></h3>
              <div class="lux-route-meta">
                <span><i class="fa-solid fa-route"></i> <?= h($route['km']) ?></span>
                <span><i class="fa-regular fa-clock"></i> <?= h($route['duration']) ?></span>
              </div>
              <div class="lux-route-actions">
                <?php if (!empty($route['dest_page'])): ?><a class="lux-textlink" href="<?= h($route['dest_page']) ?>">Explore <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
                <button type="button" class="lux-textlink" data-open-quote="">Request Quote <span aria-hidden="true">&rarr;</span></button>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══ 5 · WHY TRAVEL WITH US ═══ -->
    <section class="lux-section lux-why" id="why">
      <div class="lux-container">
        <div class="lux-head reveal">
          <span class="lux-eyebrow light">The Concierge Difference</span>
          <h2 class="lux-h2 light">Why Travel With Us</h2>
        </div>
        <div class="lux-why-grid">
          <?php foreach ($lux_why as $w): ?>
          <div class="lux-why-card reveal">
            <span class="lux-why-icon"><i class="fa-solid <?= h($w['icon']) ?>"></i></span>
            <h3 class="lux-why-title"><?= h($w['title']) ?></h3>
            <p class="lux-why-desc"><?= h($w['desc']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══ 5a · STATS ═══ -->
    <section class="lux-stats">
      <div class="lux-container lux-stats-grid">
        <?php foreach ([
          ['12,000+', 'Guest Journeys',        '12000'],
          ['200+',    'Partner Vehicles',      '200'],
          ['40+',     'Himachal Destinations', '40'],
          ['8+',      'Years of Experience',   '8'],
        ] as $st): ?>
        <div class="lux-stat reveal">
          <strong data-count="<?= $st[2] ?>"><?= $st[0] ?></strong>
          <span><?= $st[1] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ═══ 5b · FLEET — admin-managed vehicles only; hidden when none exist ═══ -->
    <?php if ($dbFleet): ?>
    <section class="lux-section lux-fleet" id="fleet">
      <div class="lux-container">
        <div class="lux-head reveal">
          <span class="lux-eyebrow">The Fleet</span>
          <h2 class="lux-h2">Travel in Quiet Comfort</h2>
          <p class="lux-fleet-note">Clean sedans, premium SUVs and group vehicles, chosen for Himachal terrain. Tell us your route, guests and luggage &mdash; we&rsquo;ll recommend the right one before quoting.</p>
        </div>
        <div class="lux-fleet-grid">
          <?php foreach ($dbFleet as $v): ?>
          <article class="lux-fleet-card reveal">
            <div class="lux-fleet-media">
              <?php if (!empty($v['photo'])): ?>
              <img src="uploads/photos/<?= h($v['photo']) ?>" alt="<?= h($v['vehicle_name']) ?> &mdash; Himachal cab" loading="lazy" decoding="async" width="900" height="560">
              <?php else: ?>
              <div class="lux-fleet-ph"><i class="fa-solid fa-car-side"></i></div>
              <?php endif; ?>
            </div>
            <div class="lux-fleet-body">
              <h3 class="lux-fleet-name"><?= h($v['vehicle_name']) ?></h3>
              <div class="lux-fleet-meta">
                <span><i class="fa-regular fa-user"></i> <?= h($v['seating_capacity']) ?></span>
                <span><i class="fa-regular fa-snowflake"></i> AC</span>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <div class="lux-fleet-cta reveal">
          <button type="button" class="lux-btn lux-btn-gold" data-open-quote="">Get a Vehicle Recommendation</button>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ═══ 5c · HOW IT WORKS ═══ -->
    <section class="lux-section lux-steps-sec" id="how">
      <div class="lux-container">
        <div class="lux-head reveal">
          <span class="lux-eyebrow">The Process</span>
          <h2 class="lux-h2">A Simple Concierge Process</h2>
        </div>
        <ol class="lux-steps">
          <?php foreach ([
            ['fa-map-pin',        'Share Your Plan',       'Tell us pickup city, dates, destinations, group size and stay preferences.'],
            ['fa-whatsapp',       'Receive a Clear Quote', 'We suggest the right vehicle and send inclusions, fare and timing on WhatsApp.', true],
            ['fa-handshake',      'Confirm the Details',   'Once confirmed, we assign the vehicle, brief the driver and lock pickup coordination.'],
            ['fa-mountain-sun',   'Travel with Support',   'Your driver and travel desk stay connected through the entire journey.'],
          ] as $i => $sp): ?>
          <li class="lux-step reveal">
            <span class="lux-step-num"><?= $i + 1 ?></span>
            <span class="lux-step-icon"><i class="<?= !empty($sp[3]) ? 'fa-brands' : 'fa-solid' ?> <?= h($sp[0]) ?>"></i></span>
            <h3 class="lux-step-title"><?= h($sp[1]) ?></h3>
            <p class="lux-step-desc"><?= h($sp[2]) ?></p>
          </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </section>

    <!-- ═══ 6 · TESTIMONIALS ═══ -->
    <section class="lux-section lux-quotes" id="reviews">
      <div class="lux-container">
        <div class="lux-head reveal">
          <span class="lux-eyebrow">Guest Stories</span>
          <h2 class="lux-h2">In Their Words</h2>
        </div>
        <div class="lux-quotes-wrap">
          <button type="button" class="lq-nav lq-prev" id="lqPrev" aria-label="Previous reviews"><i class="fa-solid fa-arrow-left"></i></button>
          <div class="lux-quotes-grid" id="lqTrack" tabindex="0" aria-label="Guest reviews">
          <?php foreach ($lux_reviews as $rv):
            $rvRating = max(0, min(5, (int)($rv['rating'] ?? 0))); ?>
          <figure class="lux-quote reveal">
            <span class="lux-quote-mark" aria-hidden="true">&ldquo;</span>
            <blockquote><?= h($rv['text']) ?></blockquote>
            <?php if (!empty($rv['photo'])): ?>
            <div class="lux-quote-photo">
              <img src="uploads/reviews/<?= h($rv['photo']) ?>" alt="Photo shared by <?= h($rv['name']) ?>" loading="lazy" decoding="async">
            </div>
            <?php endif; ?>
            <?php if ($rvRating): ?>
            <div class="lux-quote-stars" aria-label="<?= $rvRating ?> out of 5 stars">
              <?php for ($s = 1; $s <= 5; $s++): ?>
              <i class="fa-<?= $s <= $rvRating ? 'solid' : 'regular' ?> fa-star"></i>
              <?php endfor; ?>
            </div>
            <?php endif; ?>
            <figcaption><span><?= h($rv['name']) ?></span><?= $rv['city'] ? ' · ' . h($rv['city']) : '' ?></figcaption>
          </figure>
          <?php endforeach; ?>
          </div>
          <button type="button" class="lq-nav lq-next" id="lqNext" aria-label="Next reviews"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <div class="lq-dots" id="lqDots" aria-hidden="true"></div>
      </div>
    </section>

    <!-- ═══ 6b · WRITE A REVIEW ═══ -->
    <section class="lux-section lux-review" id="write-review">
      <div class="lux-container lux-review-grid">
        <div class="lux-review-intro reveal">
          <span class="lux-eyebrow">Share Your Story</span>
          <h2 class="lux-h2">Travelled With Us?</h2>
          <p class="lux-body">Tell us about your route, your driver and the experience. Every review is read by our team and helps future travellers plan with confidence.</p>
          <ul class="lux-review-perks">
            <li><i class="fa-solid fa-circle-check"></i> Reviewed &amp; published within 24 hours</li>
            <li><i class="fa-solid fa-circle-check"></i> Helps future travellers plan</li>
            <li><i class="fa-solid fa-circle-check"></i> Your phone &amp; email stay private</li>
          </ul>
        </div>
        <div class="lux-review-form reveal reveal-delay-1">
          <p class="lux-review-q">How was your experience?</p>
          <div class="star-rating-input">
            <label>Your Rating <span>*</span></label>
            <div class="star-selector" id="starSelector">
              <i class="fa-regular fa-star" data-val="1"></i>
              <i class="fa-regular fa-star" data-val="2"></i>
              <i class="fa-regular fa-star" data-val="3"></i>
              <i class="fa-regular fa-star" data-val="4"></i>
              <i class="fa-regular fa-star" data-val="5"></i>
            </div>
          </div>
          <form class="wr-form" id="writeReviewForm" action="api/submit_review.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="selectedRating" name="rating" value="0">
            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['lead_form_token']); ?>">
            <input class="hp-field" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true" style="display:none">
            <div class="lux-form-row">
              <input type="text" id="wrName" name="name" placeholder="Your Full Name *" required aria-label="Your full name">
              <input type="text" id="wrCity" name="city" placeholder="Your City *" required aria-label="Your city">
            </div>
            <div class="lux-form-row">
              <input type="text" id="wrTrip" name="route" placeholder="Route Travelled (e.g. Delhi-Manali) *" required aria-label="Route travelled">
              <label class="photo-upload-label" for="wrPhoto">
                <i class="fa-solid fa-camera"></i> <span id="photoLabel">Upload Photo (optional)</span>
                <input type="file" id="wrPhoto" name="photo" accept="image/jpeg,image/png,image/webp" class="sr-only">
              </label>
            </div>
            <textarea id="wrText" name="text" placeholder="Tell us about your driver, the cab, the route and the real experience... *" required rows="4" aria-label="Your review"></textarea>
            <p id="wrStatus" class="form-status" role="status" aria-live="polite"></p>
            <button type="submit" class="wr-submit lux-btn lux-btn-gold"><i class="fa-solid fa-paper-plane"></i> Submit Review</button>
          </form>
        </div>
      </div>
    </section>

    <!-- ═══ 6c · FAQ ═══ -->
    <section class="lux-section lux-faq" id="faq">
      <div class="lux-container lux-faq-wrap">
        <div class="lux-head reveal">
          <span class="lux-eyebrow">Good to Know</span>
          <h2 class="lux-h2">Himachal Travel Questions</h2>
        </div>
        <div class="lux-faq-list reveal">
          <?php foreach ($faqs as $i => $f): ?>
          <details class="lux-faq-item"<?= $i === 0 ? ' open' : '' ?>>
            <summary><?= h($f[0]) ?><i class="fa-solid fa-plus" aria-hidden="true"></i></summary>
            <p><?= h($f[1]) ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══ 7 · FINAL CTA ═══ -->
    <section class="lux-final reveal" id="contact" style="background-image:url('<?= h($homeContactImg) ?>')">
      <div class="lux-final-scrim" aria-hidden="true"></div>
      <div class="lux-final-inner">
        <span class="lux-eyebrow light">Begin the Journey</span>
        <h2 class="lux-h2 light">Let&rsquo;s Plan Your Himalayan Journey</h2>
        <p class="lux-banner-sub">Share your dates and the kind of trip you dream of — our travel desk will craft the rest.</p>
        <div class="lux-cta-row center">
          <button type="button" class="lux-btn lux-btn-gold" data-open-quote="">Plan My Journey</button>
          <a class="lux-btn lux-btn-ghost" href="<?= $waLink ?>" target="_blank" rel="noopener" data-wa-lead data-source="contact"><i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge</a>
        </div>
      </div>
    </section>

    <!-- Instant-quote popup form + floating button + WhatsApp pre-chat (page-level overlays) -->
    <?php require 'includes/calc_modal.php'; ?>

  </main>

<?php require 'includes/foot.php'; ?>
</body>
</html>
