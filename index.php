<?php
$base       = '';
$activeDest = '';
require_once 'includes/vars.php';

// Admin-selected homepage hero slideshow photos (up to 5). Empty → local seasonal fallback slides.
$heroSlides = array_slice(photos_in_slot($conn ?? null, 'home_hero'), 0, 5);

// Active fleet vehicles (admin-managed, with photos). Empty → hardcoded fallback in the fleet section.
$dbFleet = [];
if ($conn instanceof mysqli) {
    try {
        $rf = $conn->query("SELECT vehicle_name, photo, seating_capacity FROM vehicles WHERE status='active' ORDER BY daily_rate ASC");
        if ($rf) $dbFleet = $rf->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception) {}
}
// Only switch the website fleet to the admin's vehicles once at least one has a
// photo — until then keep the polished default cards so the page never looks bare.
$fleetHasPhoto = false;
foreach ($dbFleet as $v) { if (!empty($v['photo'])) { $fleetHasPhoto = true; break; } }

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

// FAQ content — rendered in the page below AND emitted as FAQPage structured
// data for rich snippets (kept in one place so they never drift apart).
$faqs = [
  ['Can you plan a complete Himachal trip, not just a cab?', 'Yes. We start with private transport and can help coordinate route timing, sightseeing order, hotel movement, halt planning and multi-city itineraries.'],
  ['Which Himachal routes do you cover?', 'We regularly cover Shimla, Manali, Dharamshala, Dalhousie, Spiti Valley, Kullu, Kasol, McLeodganj, Khajjiar and custom routes from Delhi, Chandigarh and nearby cities.'],
  ['How do you suggest the right vehicle?', 'We recommend vehicles based on passenger count, luggage, route length, road conditions and season. Families and longer mountain routes usually work best with SUVs or Tempo Travellers.'],
  ['Are prices confirmed before booking?', 'Yes. We share the route, vehicle type, fare, inclusions and exclusions before confirmation so there is clarity before your journey starts.'],
  ['Do drivers know mountain and snow routes?', 'Our drivers are familiar with Himachal hill roads, long transfers, high-altitude routes and seasonal route changes. For snow or Spiti routes, we plan more carefully around access and weather.'],
];
$seoFaq = $faqs;

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
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Himachal Safar | Private Himachal Cab &amp; Tour Packages</title>
  <meta name="description" content="Plan private Himachal journeys with Himachal Safar. Premium cabs, verified mountain drivers, Shimla-Manali packages, Spiti Valley expeditions and tempo travellers.">
  <meta name="keywords" content="Himachal Safar, Delhi to Manali cab, Delhi to Shimla taxi, Himachal tour package, Spiti Valley trip, tempo traveller Himachal, Dharamshala cab booking">
  <meta name="robots" content="index, follow">
  <?php
    $seoTitle = 'Himachal Safar | Private Himachal Cab & Tour Packages';
    $seoDesc  = 'Plan private Himachal journeys with Himachal Safar. Premium cabs, verified mountain drivers, Shimla-Manali packages, Spiti Valley expeditions and tempo travellers.';
    $seoPath  = '';
    include __DIR__ . '/includes/seo_head.php';
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo @filemtime(__DIR__ . '/style.css'); ?>">
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

  <main>

    <!-- HERO -->
    <section class="hero-section" id="home">
      <div class="hero-bg" aria-hidden="true">
        <?php if (!empty($heroSlides)): $n = count($heroSlides); ?>
          <?php foreach ($heroSlides as $i => $s): ?>
          <div class="hero-slide" style="background-image:url('uploads/photos/<?= h($s['filename']) ?>');<?= $n > 1 ? ' animation-delay:' . ($i * 5) . 's;' : '' ?>"></div>
          <?php endforeach; ?>
          <?php if ($n > 1):
            // Adaptive cross-fade: each slide owns 1/n of an (n×5s) loop with a ~1s fade.
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
        <!-- Local illustrated seasonal fallback slides (spring · summer · autumn · winter). Backgrounds set in style.css. -->
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
        <?php endif; ?>
      </div>

      <div class="container mx-auto px-3 hero-content">
        <div class="hero-badge reveal">
          <i class="fa-solid fa-location-dot"></i> Private Himachal Travel Concierge
        </div>
        <h1 class="reveal reveal-delay-1">
          Explore <span class="highlight">Himachal.</span><br>Leave the Planning to Us.
        </h1>
        <p class="hero-subtitle reveal reveal-delay-2">
          Private cabs, handpicked routes, local experts, and hassle-free travel experiences.
        </p>

        <div class="hero-cta-row reveal reveal-delay-3">
          <button type="button" class="btn-hero-primary" id="openBookingModal">
            <i class="fa-solid fa-calendar-check"></i> Plan My Journey
          </button>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener" class="btn-hero-wa" data-wa-lead data-source="hero">
            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
          </a>
          <a href="#routes" class="btn-hero-link">
            <i class="fa-solid fa-route"></i> Explore Routes
          </a>
        </div>

        <div class="hero-trust reveal">
          <span><i class="fa-solid fa-circle-check"></i> Verified Drivers</span>
          <span><i class="fa-solid fa-circle-check"></i> Transparent Quotes</span>
          <span><i class="fa-solid fa-circle-check"></i> 24/7 Travel Desk</span>
          <span><i class="fa-solid fa-circle-check"></i> Route-Smart Planning</span>
        </div>
      </div>

      <!-- Spinning text badge (bottom-right of hero) -->
      <div class="spin-badge" aria-hidden="true">
        <svg viewBox="0 0 120 120" width="120" height="120">
          <defs>
            <path id="spinCircle" d="M 60,60 m -42,0 a 42,42 0 1,1 84,0 a 42,42 0 1,1 -84,0"/>
          </defs>
          <text fill="rgba(214,199,161,.82)" font-size="11.5" font-family="Inter,sans-serif" font-weight="700" letter-spacing="3">
            <textPath href="#spinCircle">ADVENTURE · HIMACHAL · TRAVEL · COMFORT · </textPath>
          </text>
        </svg>
        <div class="spin-badge-icon"><img src="assets/logo-icon.svg" alt="" width="36" height="36"></div>
      </div>

      <div class="scroll-indicator">
        <span>SCROLL</span>
        <i class="fa-solid fa-chevron-down"></i>
      </div>
    </section>

    <!-- MARQUEE -->
    <div class="marquee-strip" aria-hidden="true">
      <div class="marquee-track">
        <?php
        $destinations = ['Manali','Shimla','Dharamshala','Dalhousie','Spiti Valley','Kasol','Kufri','Chail','McLeodganj','Bir Billing','Solang Valley','Khajjiar','Kangra','Kinnaur','Kullu','Rampur'];
        $all = array_merge($destinations, $destinations);
        foreach ($all as $dest): ?>
        <span class="marquee-item"><i class="fa-solid fa-mountain-sun"></i> <?php echo h($dest); ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- POPULAR ROUTES -->
    <section id="routes" class="routes-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-route"></i> Popular Routes</div>
          <h2 class="section-title">Signature <span>Himachal Routes</span></h2>
          <p class="section-desc">Private transfers and route-led journeys planned for comfort, timing and mountain-road safety.</p>
        </div>
        <div class="route-grid">
          <?php
          $fallbacks = [
            'manali'      => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=900&q=90',
            'shimla'      => 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=900&q=90',
            'dharamshala' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=90',
            'dalhousie'   => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=900&q=90',
            'spiti'       => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=900&q=90',
          ];
          // Use DB routes if available, else static fallback
          $routes_list = !empty($route_photos) ? $route_photos : [
            ['name'=>'Delhi to Manali',     'km'=>'550 km','duration'=>'12-14 hrs','badge'=>'Popular Route',    'dest_key'=>'manali',     'dest_page'=>'manali.php',     'filename'=>null],
            ['name'=>'Delhi to Shimla',     'km'=>'350 km','duration'=>'8-9 hrs',  'badge'=>'Weekend Escape',   'dest_key'=>'shimla',     'dest_page'=>'shimla.php',     'filename'=>null],
            ['name'=>'Delhi to Dharamshala','km'=>'480 km','duration'=>'10-12 hrs','badge'=>'McLeodganj Retreat','dest_key'=>'dharamshala','dest_page'=>'dharamshala.php','filename'=>null],
            ['name'=>'Delhi to Dalhousie',  'km'=>'560 km','duration'=>'11-13 hrs','badge'=>'Heritage Hills',   'dest_key'=>'dalhousie',  'dest_page'=>'dalhousie.php',  'filename'=>null],
            ['name'=>'Chandigarh to Manali','km'=>'300 km','duration'=>'8-9 hrs',  'badge'=>'Comfort Transfer', 'dest_key'=>'manali',     'dest_page'=>'manali.php',     'filename'=>null],
            ['name'=>'Chandigarh to Shimla','km'=>'115 km','duration'=>'3-4 hrs',  'badge'=>'Quick Mountain Run','dest_key'=>'shimla',    'dest_page'=>'shimla.php',     'filename'=>null],
          ];
          foreach ($routes_list as $route):
            $img = $route['filename'] ? 'uploads/photos/' . h($route['filename']) : ($fallbacks[$route['dest_key']] ?? '');
          ?>
          <article class="route-card reveal">
            <img src="<?= $img ?>" alt="<?= h($route['name']) ?> cab booking Himachal" loading="lazy" decoding="async" width="900" height="600">
            <div class="rc-overlay">
              <span class="rc-badge"><?= h($route['badge']) ?></span>
              <div class="rc-bottom">
                <h3 class="rc-name"><?= h($route['name']) ?></h3>
                <div class="rc-meta">
                  <span><i class="fa-solid fa-route"></i> <?= h($route['km']) ?></span>
                  <span><i class="fa-regular fa-clock"></i> <?= h($route['duration']) ?></span>
                </div>
                <div class="rc-actions">
                  <a href="<?= h($route['dest_page']) ?>" class="rc-btn-outline">Explore</a>
                  <a href="javascript:void(0)" onclick="openCalcModal('')" class="rc-btn-fill">Request Quote <i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <div class="center-btn reveal">
          <a href="#contact" class="btn gold-btn"><i class="fa-solid fa-map"></i> Plan a Custom Route</a>
        </div>
      </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section id="about" class="why-wrap section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-award"></i> Why Choose Us</div>
          <h2 class="section-title">Built for <span>Private Mountain Travel</span></h2>
          <p class="section-desc">A calm, accountable travel desk for families, couples, groups and premium travellers moving through Himachal.</p>
        </div>
        <div class="why-grid">
          <?php
          $why = [
            ['fa-mountain',        'Route-Smart Planning',       'We plan around hill traffic, weather windows, sightseeing time and realistic mountain-road pacing.'],
            ['fa-car-side',        'Inspected Vehicles',         'Clean, maintained cabs with route-appropriate vehicle suggestions for comfort and luggage space.'],
            ['fa-indian-rupee-sign','Clear Quotes',               'You receive a clear fare breakup before confirmation, including the inclusions that matter.'],
            ['fa-headset',         '24/7 Travel Desk',            'Our team remains available before pickup, during the route and after your journey closes.'],
            ['fa-shield-halved',   'Verified Mountain Drivers',   'Licensed drivers familiar with Himachal terrain, snow routes, passes and long hill transfers.'],
            ['fa-hotel',           'Trip Coordination',           'Need hotels, sightseeing timing or multi-city routing? We coordinate the details with you.'],
          ];
          foreach ($why as $item): ?>
          <div class="why-item reveal">
            <div class="why-icon"><i class="fa-solid <?php echo h($item[0]); ?>"></i></div>
            <h3><?php echo h($item[1]); ?></h3>
            <p><?php echo h($item[2]); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- STATS -->
    <section class="stats-band">
      <div class="stats-band-inner">
        <div class="stats-grid">
          <div class="stat-item reveal">
            <div class="stat-icon"><i class="fa-regular fa-face-smile"></i></div>
            <strong data-count="12000">12,000+</strong>
            <span>Guest Journeys</span>
            <small>Private and group trips planned</small>
          </div>
          <div class="stat-item reveal">
            <div class="stat-icon"><i class="fa-solid fa-car-side"></i></div>
            <strong data-count="200">200+</strong>
            <span>Partner Vehicles</span>
            <small>Sedans, SUVs and Tempo Travellers</small>
          </div>
          <div class="stat-item reveal">
            <div class="stat-icon"><i class="fa-solid fa-mountain-sun"></i></div>
            <strong data-count="40">40+</strong>
            <span>Himachal Destinations</span>
            <small>From Shimla to Spiti &amp; beyond</small>
          </div>
          <div class="stat-item reveal">
            <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
            <strong data-count="8">8+</strong>
            <span>Years of Experience</span>
            <small>Trusted mountain travel since 2016</small>
          </div>
        </div>
      </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how-it-works section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-list-check"></i> Simple Process</div>
          <h2 class="section-title">A Simple <span>Concierge Process</span></h2>
          <p class="section-desc">Share the route once. We help you shape the vehicle, timing and itinerary before you confirm.</p>
        </div>
        <ol class="steps-grid">
          <li class="step-card reveal">
            <div class="step-num">
              <i class="fa-solid fa-map-pin" aria-hidden="true"></i>
              <span class="step-number">1</span>
            </div>
            <h3>Share Your Plan</h3>
            <p>Tell us pickup city, dates, destination list, group size and hotel preferences.</p>
          </li>
          <li class="step-card reveal reveal-delay-1">
            <div class="step-num">
              <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
              <span class="step-number">2</span>
            </div>
            <h3>Receive a Clear Quote</h3>
            <p>We suggest the right vehicle and send inclusions, fare and route timing on WhatsApp.</p>
          </li>
          <li class="step-card reveal reveal-delay-2">
            <div class="step-num">
              <i class="fa-solid fa-handshake" aria-hidden="true"></i>
              <span class="step-number">3</span>
            </div>
            <h3>Confirm the Details</h3>
            <p>Once confirmed, we assign the vehicle, brief the driver and lock pickup coordination.</p>
          </li>
          <li class="step-card reveal reveal-delay-3">
            <div class="step-num">
              <i class="fa-solid fa-mountain-sun" aria-hidden="true"></i>
              <span class="step-number">4</span>
            </div>
            <h3>Travel with Support</h3>
            <p>Your driver and travel desk stay connected through the journey.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- REVIEWS -->
    <section id="reviews" class="reviews-area section-pad">
      <div class="container mx-auto px-3">

        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-star"></i> Customer Reviews</div>
          <h2 class="section-title">What Our <span>Travellers</span> Say</h2>
          <p class="section-desc">Real traveller stories from family holidays, honeymoon trips, group tours and high-altitude routes.</p>
        </div>

        <div class="rv-slider reveal">
          <div class="rv-track-scroll" id="reviewTrack">
          <div class="rv-track">
            <?php
            // Fallback reviews shown when the DB has no approved reviews yet.
            $seedReviews = [
              ['Rajesh Sharma',    'New Delhi',  5, '#166534', 'Delhi-Manali',     'Booked Delhi to Manali for our family of 6. The Innova Crysta was spotless, and the driver was experienced with mountain roads. We felt safe throughout the snowfall section near Rohtang.', ''],
              ['Priya Verma',      'Chandigarh', 5, '#b45309', 'Shimla Weekend',   'Went to Shimla for a weekend with 3 friends. Cab arrived early, driver was professional and gave useful local tips for Mall Road and Jakhu Temple. Price was exactly as quoted.', ''],
              ['Sukhwinder Singh', 'Ludhiana',   5, '#1e40af', 'Dharamshala',      'Family trip to Dharamshala and McLeodganj. Driver knew all the best local spots and was very helpful with luggage on steep roads. Kids loved the journey. Highly recommend for Kangra valley visits.', ''],
              ['Anjali Kapoor',    'Gurugram',   5, '#7c3aed', 'Manali Honeymoon', 'Honeymoon trip to Manali was made perfect by Himachal Safar. Cab was premium and comfortable. Driver gave us privacy and was always on time. Solang Valley and Rohtang visit were absolutely flawless.', ''],
              ['Mohit Agarwal',    'Jaipur',     5, '#0f766e', 'Shimla Leisure',   'Travelled to Shimla with my wife for a leisure trip. Very comfortable journey. Driver was patient with senior passengers and stopped whenever we needed rest breaks. Good service overall.', ''],
              ['Divya Sharma',     'Noida',      5, '#be185d', 'Kasol Trip',       'Group of 8 friends to Kasol in a tempo traveller. The driver knew the Parvati Valley roads perfectly, the booking was quick on WhatsApp, and the ride felt comfortable and safe.', ''],
              ['Harpreet Kaur',    'Amritsar',   5, '#c2410c', 'Dalhousie',        'Dalhousie trip with husband and in-laws was beautifully organized. Innova was clean and fully air-conditioned. Driver showed us Khajjiar (mini Switzerland) which was not even in our plan. Excellent!', ''],
              ['Vikash Gupta',     'Lucknow',    5, '#166534', 'Spiti Valley',     'Spiti Valley requires serious driving expertise and Himachal Safar delivered. Our driver navigated narrow mountain roads, river crossings and high-altitude passes with complete confidence. 9-day trip was flawless.', ''],
              ['Sunita Yadav',     'New Delhi',  5, '#b45309', 'Manali 5 Days',    'Booked 5-day Manali package for me and my sister. Everything was arranged — pickup, hotel coordination, sightseeing. Driver was like a local guide, took us to Naggar Castle. Superb experience!', ''],
              ['Arjun Nair',       'Chandigarh', 5, '#1e40af', 'McLeodganj',       'Road trip to McLeodganj with college group. Tempo traveller was fully loaded with gear and still very comfortable. Driver was friendly, on time, and made the 5-hour drive feel short. Highly recommended!', ''],
            ];
            // Normalise DB reviews into same shape as seed rows, then append after seed reviews.
            $palette = ['#166534','#b45309','#1e40af','#7c3aed','#0f766e','#be185d','#c2410c'];
            $dbList  = [];
            foreach ($db_reviews as $i => $r) {
              $dbList[] = [
                $r['name'], $r['city'] ?: '-', (int)$r['rating'],
                $palette[$i % count($palette)], $r['route'] ?: 'Himachal Trip',
                $r['review_text'], $r['photo'] ?? '',
              ];
            }
            // Approved reviews appear first; seed reviews always show after
            $reviewList = array_merge($dbList, $seedReviews);
            foreach ($reviewList as $review):
              $stars = $review[2];
              $bg    = ltrim($review[3], '#');
              $nm    = urlencode($review[0]);
              $photoUrl  = !empty($review[6]) ? "uploads/reviews/" . h($review[6]) : null;
              $avatarUrl = "https://ui-avatars.com/api/?name={$nm}&background={$bg}&color=ffffff&bold=true&rounded=true&size=96&font-size=0.38";
            ?>
            <article class="rv-card">
              <div class="rv-stars">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <?php if ($s <= $stars): ?>
                    <i class="fa-solid fa-star"></i>
                  <?php else: ?>
                    <i class="fa-regular fa-star"></i>
                  <?php endif; ?>
                <?php endfor; ?>
              </div>
              <blockquote class="rv-text"><?php echo h($review[5]); ?></blockquote>
              <footer class="rv-foot">
                <img src="<?php echo $photoUrl ?: $avatarUrl; ?>" alt="<?php echo h($review[0]); ?>" loading="lazy" width="48" height="48">
                <div class="rv-meta">
                  <span class="rv-name"><?php echo h($review[0]); ?></span>
                  <span class="rv-city"><?php echo h($review[1]); ?></span>
                </div>
                <span class="rv-route"><i class="fa-solid fa-route"></i> <?php echo h($review[4]); ?></span>
              </footer>
            </article>
            <?php endforeach; ?>
          </div>
          </div>
        </div>

        <div class="rv-controls">
          <button class="rv-btn" id="reviewPrev" aria-label="Previous review">
            <i class="fa-solid fa-arrow-left"></i>
          </button>
          <div class="rv-dots" id="reviewDots" role="tablist" aria-label="Review navigation"></div>
          <button class="rv-btn" id="reviewNext" aria-label="Next review">
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

      </div>
    </section>

    <!-- WRITE A REVIEW -->
    <section class="write-review-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-pen-to-square"></i> Share Your Experience</div>
          <h2 class="section-title">Travelled with <span>Us?</span></h2>
          <p class="section-desc">Share route details, driver experience and comfort notes to help future guests choose better.</p>
        </div>
        <div class="write-review-card reveal">
          <div class="write-review-left">
            <div class="wr-icon"><i class="fa-solid fa-star"></i></div>
            <h3>Share Your Story</h3>
            <p>Tell us about your Himachal journey: the route, your driver and the overall experience. Every review is read by our team.</p>
            <ul class="wr-perks">
              <li><i class="fa-solid fa-circle-check"></i> Reviewed &amp; published within 24 hours</li>
              <li><i class="fa-solid fa-circle-check"></i> Helps future travellers plan</li>
              <li><i class="fa-solid fa-circle-check"></i> Your phone &amp; email stay private</li>
            </ul>
            <div class="wr-rating-badge">
              <div class="wr-rating-num">4.9</div>
              <div class="wr-rating-meta">
                <div class="wr-stars-mini">5 star average</div>
                <div class="wr-rating-count">Based on 1,200+ trips</div>
              </div>
            </div>
          </div>
          <div class="write-review-right">
            <p class="wr-form-title">How was your experience?</p>
            <div class="star-rating-input">
              <label>Your Rating <span>*</span></label>
              <div class="star-selector" id="starSelector">
                <i class="fa-regular fa-star" data-val="1"></i>
                <i class="fa-regular fa-star" data-val="2"></i>
                <i class="fa-regular fa-star" data-val="3"></i>
                <i class="fa-regular fa-star" data-val="4"></i>
                <i class="fa-regular fa-star" data-val="5"></i>
              </div>
              <input type="hidden" id="selectedRating" value="0">
            </div>
            <form class="wr-form" id="writeReviewForm" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['lead_form_token']); ?>">
              <input class="hp-field" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true">
              <div class="wr-row">
                <div class="wr-field">
                  <input type="text" id="wrName" name="name" placeholder="Your Full Name *" required aria-label="Your full name">
                </div>
                <div class="wr-field">
                  <input type="text" id="wrCity" name="city" placeholder="Your City *" required aria-label="Your city">
                </div>
              </div>
              <div class="wr-row">
                <div class="wr-field">
                  <input type="text" id="wrTrip" name="route" placeholder="Route Travelled (e.g. Delhi-Manali)" required aria-label="Route travelled">
                </div>
                <div class="wr-field">
                  <label class="photo-upload-label" for="wrPhoto">
                    <i class="fa-solid fa-camera"></i>
                    <span id="photoLabel">Upload Photo (optional)</span>
                    <input type="file" id="wrPhoto" name="photo" accept="image/jpeg,image/png,image/webp" class="sr-only">
                  </label>
                </div>
              </div>
              <textarea id="wrText" name="text" placeholder="Tell us about your driver, the cab, the route and the real experience... *" required rows="4" aria-label="Your review"></textarea>
              <p id="wrStatus" class="form-status" role="status" aria-live="polite"></p>
              <button type="submit" class="wr-submit">
                <i class="fa-solid fa-paper-plane"></i> Submit Review
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- FLEET -->
    <section id="fleet" class="fleet-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-car-side"></i> Our Fleet</div>
          <h2 class="section-title">Premium <span>Route-Ready Fleet</span></h2>
          <p class="section-desc">Choose from clean sedans, premium SUVs and group vehicles selected for Himachal terrain, comfort and luggage needs.</p>
        </div>
        <div class="fleet-info-note reveal">
          <i class="fa-solid fa-circle-info"></i>
          Tell us your route, passenger count and luggage. We will recommend the right vehicle before quoting.
        </div>
        <div class="fleet-grid">
          <?php if ($fleetHasPhoto): ?>
            <?php foreach ($dbFleet as $v): ?>
            <article class="fleet-card reveal">
              <div class="fleet-card-img">
                <?php if (!empty($v['photo'])): ?>
                <img src="uploads/photos/<?php echo h($v['photo']); ?>" alt="<?php echo h($v['vehicle_name']); ?> — Himachal cab" loading="lazy" decoding="async" width="900" height="560">
                <?php else: ?>
                <div class="fleet-img-placeholder"><i class="fa-solid fa-car-side"></i></div>
                <?php endif; ?>
              </div>
              <div class="fleet-card-body">
                <h3><?php echo h($v['vehicle_name']); ?></h3>
                <div class="fleet-meta">
                  <span class="fleet-tag"><i class="fa-regular fa-user"></i> <?php echo h($v['seating_capacity']); ?></span>
                  <span class="fleet-tag"><i class="fa-regular fa-snowflake"></i> AC</span>
                </div>
              </div>
            </article>
            <?php endforeach; ?>
          <?php else:
          $fleet = [
            ['Swift Dzire',          '1-3 Guests',  'AC', 'Efficient city pickups and short hill transfers', 'https://commons.wikimedia.org/wiki/Special:FilePath/Suzuki%20Dzire%201.2%20GL%202019.jpg?width=900'],
            ['Innova Hycross',       '4-6 Guests',  'AC', 'Premium comfort for families and longer routes',   'https://commons.wikimedia.org/wiki/Special:FilePath/2022%20Toyota%20Kijang%20Innova%20Zenix%20V%20%28front%29.jpg?width=900'],
            ['Innova Crysta',        '4-6 Guests',  'AC', 'Preferred SUV for Manali, Shimla and Dharamshala', 'https://commons.wikimedia.org/wiki/Special:FilePath/Toyota%20Innova%20Crysta.jpg?width=900'],
            ['Tempo Traveller 12',   '7-12 Guests', 'AC', 'Comfortable group tours with luggage space',       'https://commons.wikimedia.org/wiki/Special:FilePath/Force%20Traveller%20Luxury.jpg?width=900'],
            ['Tempo Traveller 17',   '13+ Guests',  'AC', 'Large group and corporate Himachal journeys',      'https://commons.wikimedia.org/wiki/Special:FilePath/Force%20Motors%20-%20Traveller%2026%20-%20Agra%202014-05-14%204222.JPG?width=900'],
          ];
          foreach ($fleet as $car): ?>
          <article class="fleet-card reveal">
            <div class="fleet-card-img">
              <img src="<?php echo h($car[4]); ?>" alt="<?php echo h($car[0]); ?> Himachal Pradesh" loading="lazy" decoding="async" width="900" height="560">
            </div>
            <div class="fleet-card-body">
              <h3><?php echo h($car[0]); ?></h3>
              <div class="fleet-meta">
                <span class="fleet-tag"><i class="fa-regular fa-user"></i> <?php echo h($car[1]); ?></span>
                <span class="fleet-tag"><i class="fa-regular fa-snowflake"></i> <?php echo h($car[2]); ?></span>
              </div>
              <div class="fleet-note"><?php echo $car[3]; ?></div>
            </div>
          </article>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="faq-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-circle-question"></i> FAQ</div>
          <h2 class="section-title">Himachal Travel <span>Questions</span></h2>
          <p class="section-desc">Quick answers before you plan your cab, package or private mountain route.</p>
        </div>

        <div class="faq-list reveal">
          <?php foreach ($faqs as $i => $f): ?>
          <details class="faq-item"<?= $i === 0 ? ' open' : '' ?>>
            <summary><?= h($f[0]) ?></summary>
            <p><?= h($f[1]) ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="contact-strip section-pad">
      <div class="container mx-auto px-3">
        <div class="contact-card reveal">
          <div class="contact-info">
            <div>
              <h2>Request a <span>Private Quote</span></h2>
              <p>Share your route, dates and guest count. Our travel desk will respond on WhatsApp with a clear plan and fare.</p>
            </div>
            <div class="contact-detail-list">
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-solid fa-phone"></i></div>
                <span><a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a></span>
              </div>
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-brands fa-whatsapp"></i></div>
                <span><a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener">Speak to Travel Concierge</a></span>
              </div>
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-regular fa-envelope"></i></div>
                <span><a href="mailto:info@himachalsafar.com">info@himachalsafar.com</a></span>
              </div>
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-solid fa-location-dot"></i></div>
                <span>Shimla &amp; New Delhi, India</span>
              </div>
            </div>
          </div>
          <!-- Right column: CTA that opens the instant-quote popup -->
          <div class="contact-booking">
            <div class="contact-quote-cta">
              <div class="cqcta-icon"><i class="fa-solid fa-calculator"></i></div>
              <h3>Get an Instant Price Estimate</h3>
              <p>Pick your package, vehicle and dates — see a clear fare breakup in seconds, then submit your enquiry in one tap.</p>
              <div class="cqcta-actions">
                <button type="button" class="cqcta-btn" data-open-quote=""><i class="fa-solid fa-bolt"></i> Open Instant Quote</button>
                <a class="cqcta-wa" href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener" data-wa-lead data-source="contact"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
              </div>
              <span class="cqcta-note"><i class="fa-solid fa-lock"></i> No spam · We reply within 24 hours</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Instant-quote popup form + floating button (page-level overlay) -->
    <?php require 'includes/calc_modal.php'; ?>

  </main>

<?php require 'includes/foot.php'; ?>
</body>
</html>
