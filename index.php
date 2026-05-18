<?php
$base       = '';
$activeDest = '';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Himachal Yatra Travels | Himachal Pradesh Cab &amp; Tour Packages</title>
  <meta name="description" content="Explore Himachal Pradesh with Himachal Yatra Travels. Affordable outstation cabs, Shimla–Manali tour packages, Spiti Valley adventures, tempo travellers. Delhi to Manali, Shimla, Dharamshala and more.">
  <meta name="keywords" content="Himachal Yatra Travels, Delhi to Manali cab, Delhi to Shimla taxi, Himachal tour package, Spiti Valley trip, tempo traveller Himachal, Dharamshala cab booking">
  <meta name="robots" content="index, follow">
  <meta name="theme-color" content="#166534">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>

  <main>

    <!-- ══ HERO ══ -->
    <section class="hero-section" id="home">
      <div class="hero-bg">
        <img
          src="https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1920&q=90"
          alt="Himachal Pradesh snow-capped mountains Manali"
          fetchpriority="high" decoding="async" width="1920" height="1080">
      </div>

      <div class="container mx-auto px-3 hero-content">
        <div class="hero-badge reveal">
          <i class="fa-solid fa-star"></i> Himachal Pradesh's Trusted Travel Partner
        </div>
        <h1 class="reveal reveal-delay-1">
          Explore the <span class="highlight">Himalayas</span><br>of Himachal Pradesh
        </h1>
        <p class="hero-subtitle reveal reveal-delay-2">
          Premium cab services, curated Himachal tour packages &amp; tempo travellers. From Shimla to Spiti — transparent fares, experienced mountain drivers.
        </p>

        <div class="booking-widget reveal reveal-delay-3">
          <div class="booking-tabs">
            <button class="booking-tab-btn active" data-tab="cab">
              <i class="fa-solid fa-car-side"></i> Cab Booking
            </button>
            <button class="booking-tab-btn" data-tab="package">
              <i class="fa-solid fa-map-marked-alt"></i> Tour Package
            </button>
            <button class="booking-tab-btn" data-tab="tempo">
              <i class="fa-solid fa-van-shuttle"></i> Tempo Traveller
            </button>
          </div>
          <div class="booking-fields">
            <div class="booking-field">
              <label><i class="fa-solid fa-location-dot"></i> From</label>
              <input type="text" id="bookFrom" placeholder="Delhi, Chandigarh, Pathankot...">
            </div>
            <div class="booking-field">
              <label><i class="fa-solid fa-flag-checkered"></i> To</label>
              <input type="text" id="bookTo" placeholder="Manali, Shimla, Dharamshala...">
            </div>
            <div class="booking-field">
              <label><i class="fa-regular fa-calendar"></i> Travel Date</label>
              <input type="date" id="bookDate">
            </div>
            <div class="booking-field">
              <label><i class="fa-regular fa-user"></i> Passengers</label>
              <select id="bookPassengers">
                <option value="classic">1–3 Passengers (Sedan)</option>
                <option value="classic">4–6 Passengers (Innova)</option>
                <option value="luxury">7–12 (Tempo 12 Seater)</option>
                <option value="luxury">13+ (Tempo 17 Seater)</option>
              </select>
            </div>
            <button class="booking-search-btn" id="heroBookBtn">
              <i class="fa-solid fa-calendar-check"></i> Book Trip
            </button>
          </div>
        </div>

        <div class="hero-trust reveal reveal-delay-3">
          <span><i class="fa-solid fa-circle-check"></i> Free Cancellation</span>
          <span><i class="fa-solid fa-circle-check"></i> No Hidden Charges</span>
          <span><i class="fa-solid fa-circle-check"></i> 24/7 Support</span>
          <span><i class="fa-solid fa-circle-check"></i> Mountain Expert Drivers</span>
        </div>
      </div>

      <div class="scroll-indicator">
        <span>SCROLL</span>
        <i class="fa-solid fa-chevron-down"></i>
      </div>
    </section>

    <!-- ══ MARQUEE ══ -->
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

    <!-- ══ POPULAR ROUTES ══ -->
    <section id="routes" class="routes-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-route"></i> Popular Routes</div>
          <h2 class="section-title">Himachal's Most Loved <span>Destinations</span></h2>
          <p class="section-desc">Book your cab for the most scenic Himachal routes at the best prices with experienced mountain drivers.</p>
        </div>
        <div class="route-grid">
          <?php
          $routes = [
            // [name, type, km, hrs, badge, price, image, dest_page]
            ['Delhi to Manali',      'One Way | Round Trip', '550 km', '12–14 hrs', 'Family Package',      '9,999', 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=900&q=90', 'manali.php'],
            ['Delhi to Shimla',      'One Way | Round Trip', '350 km', '8–9 hrs',   'Weekend Package',     '7,499', 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=900&q=90', 'shimla.php'],
            ['Delhi to Dharamshala', 'One Way | Round Trip', '480 km', '10–12 hrs', 'McLeodganj Special',  '8,999', 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=90', 'dharamshala.php'],
            ['Delhi to Dalhousie',   'One Way | Round Trip', '560 km', '11–13 hrs', 'Hill Escape Package', '9,499', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=900&q=90', 'dalhousie.php'],
            ['Chandigarh to Manali', 'One Way | Round Trip', '300 km', '8–9 hrs',   'Combo Package',       '6,999', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=900&q=90', 'manali.php'],
            ['Chandigarh to Shimla', 'One Way | Round Trip', '115 km', '3–4 hrs',   'Express Package',     '3,499', 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=900&q=90', 'shimla.php'],
          ];
          foreach ($routes as $route): ?>
          <article class="route-card reveal">
            <div class="route-card-img">
              <img src="<?php echo h($route[6]); ?>" alt="<?php echo h($route[0]); ?> cab booking Himachal" loading="lazy" decoding="async" width="900" height="540">
              <span class="route-badge"><?php echo h($route[4]); ?></span>
              <div class="route-img-info">
                <span class="route-img-name"><?php echo h($route[0]); ?></span>
                <span class="route-img-price">&#8377;<?php echo h($route[5]); ?></span>
              </div>
            </div>
            <div class="route-card-body">
              <p class="route-type"><?php echo h($route[1]); ?></p>
              <div class="route-meta">
                <span><i class="fa-solid fa-route"></i> <?php echo h($route[2]); ?></span>
                <span><i class="fa-regular fa-clock"></i> <?php echo h($route[3]); ?></span>
              </div>
              <div class="route-cta">
                <div>
                  <div class="route-price-label">Starting from</div>
                  <div class="route-price">&#8377;<?php echo h($route[5]); ?></div>
                </div>
                <div class="route-cta-btns">
                  <a href="<?php echo h($route[7]); ?>" class="route-explore-link">Explore <i class="fa-solid fa-circle-info"></i></a>
                  <a href="#contact" class="route-btn">Book Now <i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <div class="center-btn reveal">
          <a href="#contact" class="btn navy-btn"><i class="fa-solid fa-map"></i> Enquire About Any Route</a>
        </div>
      </div>
    </section>

    <!-- ══ WHY CHOOSE US ══ -->
    <section id="about" class="why-wrap section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-award"></i> Why Choose Us</div>
          <h2 class="section-title">Why <span>Himachal Yatra</span> Travels?</h2>
          <p class="section-desc">We specialise in Himachal Pradesh travel. Our drivers know every mountain road, every shortcut, every viewpoint.</p>
        </div>
        <div class="why-grid">
          <?php
          $why = [
            ['fa-mountain',        'Himachal Specialists',     'We exclusively serve Himachal Pradesh routes. Our drivers know every pass, every road condition.'],
            ['fa-car-side',        'Well Maintained Vehicles', 'Our fleet is regularly serviced for mountain terrain — safe brakes, strong engines, GPS tracking.'],
            ['fa-indian-rupee-sign','Transparent Pricing',     'No hidden charges, no surcharges. The price we quote is the price you pay. Period.'],
            ['fa-headset',         '24/7 Customer Support',    'Our team is always available before, during and after your trip — even at remote Himachal locations.'],
            ['fa-shield-halved',   'Verified Local Drivers',   'Background-checked, license-verified drivers who have years of experience on Himachal roads.'],
            ['fa-thumbs-up',       'Best Price Guarantee',     'We match or beat any competitor price. Book confidently — Himachal doesn\'t need to be expensive.'],
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

    <!-- ══ STATS ══ -->
    <section class="stats-band section-pad">
      <div class="container mx-auto px-3">
        <div class="stats-grid reveal">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-regular fa-face-smile"></i></div>
            <strong data-count="12000">0</strong>
            <span>Happy Travellers</span>
          </div>
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-car-side"></i></div>
            <strong data-count="200">0</strong>
            <span>Vehicles in Fleet</span>
          </div>
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-mountain-sun"></i></div>
            <strong data-count="40">0</strong>
            <span>Himachal Destinations</span>
          </div>
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
            <strong data-count="8">0</strong>
            <span>Years of Experience</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ══ HOW IT WORKS ══ -->
    <section class="how-it-works section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-list-check"></i> Simple Process</div>
          <h2 class="section-title">Book Your Himachal Trip in <span>4 Easy Steps</span></h2>
          <p class="section-desc">Planning your Himachal trip has never been easier. Get started in minutes.</p>
        </div>
        <div class="steps-grid">
          <div class="step-card reveal">
            <div class="step-num">
              <i class="fa-solid fa-map-pin"></i>
              <span class="step-number">1</span>
            </div>
            <h3>Choose Your Route</h3>
            <p>Tell us your pickup city, Himachal destination and travel date.</p>
          </div>
          <div class="step-card reveal reveal-delay-1">
            <div class="step-num">
              <i class="fa-brands fa-whatsapp"></i>
              <span class="step-number">2</span>
            </div>
            <h3>Get Instant Quote</h3>
            <p>We send you a transparent, all-inclusive quote on WhatsApp in minutes.</p>
          </div>
          <div class="step-card reveal reveal-delay-2">
            <div class="step-num">
              <i class="fa-solid fa-handshake"></i>
              <span class="step-number">3</span>
            </div>
            <h3>Confirm &amp; Relax</h3>
            <p>Confirm the booking. We handle the rest — vehicle assignment, driver briefing.</p>
          </div>
          <div class="step-card reveal reveal-delay-3">
            <div class="step-num">
              <i class="fa-solid fa-mountain-sun"></i>
              <span class="step-number">4</span>
            </div>
            <h3>Enjoy Himachal</h3>
            <p>Your mountain-expert driver arrives on time. Focus on the mountains, not the road.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ══ REVIEWS ══ -->
    <section id="reviews" class="reviews-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-star"></i> Customer Reviews</div>
          <h2 class="section-title">What Our <span>Travellers Say</span></h2>
          <p class="section-desc">Over 12,000 happy travellers have explored Himachal Pradesh with us. Here's what some of them shared.</p>
        </div>

        <!-- Horizontal Slider -->
        <div class="review-slider-wrapper reveal">
          <button class="slider-nav-btn slider-prev" id="reviewPrev" aria-label="Previous review">
            <i class="fa-solid fa-chevron-left"></i>
          </button>
          <div class="review-track" id="reviewTrack">
            <?php
            $reviews = [
              ['Rajesh Sharma',    52, 'New Delhi',  5, false, '#166534', 'Delhi–Manali',   'Booked Delhi to Manali for our entire family of 6. The Innova Crysta was spotless, driver Ramesh ji was very experienced with mountain roads. Reached Manali safely even through heavy snowfall near Rohtang. Will book again!'],
              ['Priya Verma',      28, 'Chandigarh', 5, false, '#b45309', 'Shimla Weekend', 'Went to Shimla for a weekend with 3 friends. Cab arrived 15 minutes early, driver was professional and gave great tips about Mall Road and Jakhu Temple. Price exactly as quoted — no extras. Perfect!'],
              ['Sukhwinder Singh', 45, 'Ludhiana',   5, false, '#1e40af', 'Dharamshala',   'Family trip to Dharamshala and McLeodganj. Driver knew all the best local spots. Very helpful with luggage on steep roads. Kids loved the journey. Highly recommend for Kangra valley visits.'],
              ['Anjali Kapoor',    34, 'Gurugram',   5, false, '#7c3aed', 'Manali Honeymoon','Honeymoon trip to Manali was made perfect by Himachal Yatra. Cab was premium, clean and comfortable. Driver gave us privacy and was always on time. Solang Valley and Rohtang visit were flawless.'],
              ['Mohit Agarwal',    62, 'Jaipur',     4, true,  '#0f766e', 'Shimla Leisure', 'Travelled to Shimla with my wife for a leisure trip. Very comfortable journey. Driver was patient with senior passengers and stopped at our request for rest breaks. Good service overall.'],
              ['Divya Sharma',     26, 'Noida',      5, false, '#be185d', 'Kasol Trip',     'Group of 8 friends to Kasol in a tempo traveller — what a ride! Driver bhaiya knew the Parvati Valley roads perfectly. Super comfortable and safe. WhatsApp booking process was quick and easy. 10/10!'],
              ['Harpreet Kaur',    39, 'Amritsar',   5, false, '#c2410c', 'Dalhousie',     'Dalhousie trip with husband and in-laws was beautifully organized. Innova was clean and fully air-conditioned. Driver showed us Khajjiar (mini Switzerland) which was not even in our plan. Excellent!'],
              ['Vikash Gupta',     31, 'Lucknow',    5, false, '#166534', 'Spiti Valley',  'Spiti Valley requires serious driving expertise and Himachal Yatra delivered. Our driver navigated narrow mountain roads, river crossings and high altitude passes with complete confidence. 9-day trip was flawless.'],
              ['Sunita Yadav',     47, 'New Delhi',  5, false, '#b45309', 'Manali 5 Days', 'Booked 5-day Manali package for me and my sister. Everything was arranged perfectly — pickup, hotel coordination, sightseeing. Driver was like a local guide, took us to Naggar Castle. Superb experience!'],
              ['Arjun Nair',       29, 'Chandigarh', 5, false, '#1e40af', 'McLeodganj',    'Road trip to McLeodganj with college group. Tempo traveller was fully loaded with gear and still very comfortable. Driver was friendly, on time and made the 5-hour drive feel short. Highly recommended!'],
            ];
            foreach ($reviews as $i => $review):
              $stars   = $review[3];
              $hasHalf = $review[4];
              $bg      = urlencode($review[5]);
              $name    = urlencode($review[0]);
              $avatarUrl = "https://ui-avatars.com/api/?name={$name}&background=" . ltrim($bg, '%23') . "&color=ffffff&bold=true&rounded=true&size=100&font-size=0.40";
            ?>
            <article class="review-card">
              <div class="stars">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <?php if ($s <= $stars): ?>
                    <i class="fa-solid fa-star"></i>
                  <?php elseif ($hasHalf && $s === $stars + 1): ?>
                    <i class="fa-solid fa-star-half-stroke"></i>
                  <?php else: ?>
                    <i class="fa-regular fa-star"></i>
                  <?php endif; ?>
                <?php endfor; ?>
              </div>
              <p>"<?php echo h($review[6]); ?>"</p>
              <div class="review-person">
                <img src="<?php echo $avatarUrl; ?>" alt="<?php echo h($review[0]); ?>" loading="lazy" width="56" height="56">
                <div class="reviewer-info">
                  <h3><?php echo h($review[0]); ?></h3>
                  <span><?php echo h($review[2]); ?> &bull; Age <?php echo (int)$review[1]; ?></span>
                  <div class="trip-tag"><i class="fa-solid fa-mountain-sun"></i> <?php echo h($review[7]); ?></div>
                </div>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
          <button class="slider-nav-btn slider-next" id="reviewNext" aria-label="Next review">
            <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
        <div class="slider-dots-row" id="reviewDots" aria-hidden="true"></div>
      </div>
    </section>

    <!-- ══ WRITE A REVIEW ══ -->
    <section class="write-review-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-pen-to-square"></i> Share Your Experience</div>
          <h2 class="section-title">Travelled with <span>Us?</span></h2>
          <p class="section-desc">Your honest review helps thousands of travellers plan better Himachal trips. Takes just 2 minutes!</p>
        </div>
        <div class="write-review-card reveal">
          <div class="write-review-left">
            <div class="wr-icon"><i class="fa-solid fa-star"></i></div>
            <h3>Share Your Story</h3>
            <p>Tell us about your Himachal journey — the route, your driver, the experience. Every review is read by our team.</p>
            <ul class="wr-perks">
              <li><i class="fa-solid fa-circle-check"></i> Published within 24 hours</li>
              <li><i class="fa-solid fa-circle-check"></i> Helps future travellers</li>
              <li><i class="fa-solid fa-circle-check"></i> Your privacy is safe</li>
            </ul>
          </div>
          <div class="write-review-right">
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
            <form class="wr-form" id="writeReviewForm">
              <div class="wr-row">
                <div class="wr-field">
                  <input type="text" id="wrName" placeholder="Your Full Name *" required>
                </div>
                <div class="wr-field">
                  <input type="text" id="wrCity" placeholder="Your City *" required>
                </div>
              </div>
              <div class="wr-row">
                <div class="wr-field">
                  <input type="text" id="wrTrip" placeholder="Route Travelled (e.g. Delhi to Manali)" required>
                </div>
                <div class="wr-field">
                  <label class="photo-upload-label" for="wrPhoto">
                    <i class="fa-solid fa-camera"></i>
                    <span id="photoLabel">Upload Your Photo (optional)</span>
                    <input type="file" id="wrPhoto" accept="image/*" class="sr-only">
                  </label>
                </div>
              </div>
              <textarea id="wrText" placeholder="Write your review here — tell us about your driver, the cab, the experience... *" required rows="4"></textarea>
              <button type="submit" class="wr-submit">
                <i class="fa-brands fa-whatsapp"></i> Submit Review via WhatsApp
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- ══ FLEET ══ -->
    <section id="fleet" class="fleet-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-car-side"></i> Our Fleet</div>
          <h2 class="section-title">Our <span>Vehicles</span></h2>
          <p class="section-desc">Every vehicle in our fleet is maintained for Himachal's tough mountain terrain — safe, clean and comfortable.</p>
        </div>
        <div class="fleet-info-note reveal">
          <i class="fa-solid fa-circle-info"></i>
          Our team will recommend the best vehicle for your route and group size. Contact us to get the right fit.
        </div>
        <div class="fleet-grid">
          <?php
          $fleet = [
            ['Swift Dzire',           '4 Seats', 'AC', 'City &amp; short hill routes', 'https://commons.wikimedia.org/wiki/Special:FilePath/Suzuki%20Dzire%201.2%20GL%202019.jpg?width=900'],
            ['Innova Hycross',        '6 Seats', 'AC', 'Premium Himachal comfort',      'https://commons.wikimedia.org/wiki/Special:FilePath/2022%20Toyota%20Kijang%20Innova%20Zenix%20V%20%28front%29.jpg?width=900'],
            ['Innova Crysta',         '6 Seats', 'AC', 'Best for mountain roads',       'https://commons.wikimedia.org/wiki/Special:FilePath/Toyota%20Innova%20Crysta.jpg?width=900'],
            ['Tempo Traveller 12',   '12 Seats', 'AC', 'Small group hill tours',        'https://commons.wikimedia.org/wiki/Special:FilePath/Force%20Traveller%20Luxury.jpg?width=900'],
            ['Tempo Traveller 17',   '17 Seats', 'AC', 'Large group Himachal trips',    'https://commons.wikimedia.org/wiki/Special:FilePath/Force%20Motors%20-%20Traveller%2026%20-%20Agra%202014-05-14%204222.JPG?width=900'],
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
        </div>
      </div>
    </section>

    <!-- ══ PACKAGES ══ -->
    <section id="packages" class="packages-area section-pad">
      <div class="container mx-auto px-3">
        <div class="section-header reveal">
          <div class="section-tag"><i class="fa-solid fa-map-marked-alt"></i> Tour Packages</div>
          <h2 class="section-title">Curated Himachal <span>Packages</span></h2>
          <p class="section-desc">Handcrafted trips for every kind of traveller — families, honeymooners, adventure seekers and pilgrims.</p>
        </div>
        <div class="package-grid">
          <article class="package-card reveal">
            <div class="package-card-bg">
              <img src="https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=900&q=90" alt="Shimla Manali Honeymoon Package" loading="lazy">
            </div>
            <div class="package-icon"><i class="fa-solid fa-heart"></i></div>
            <span class="package-duration">6 Nights / 7 Days</span>
            <div class="package-card-body">
              <h3>Shimla–Manali Honeymoon</h3>
              <p>Shimla, Kufri, Kullu, Manali, Solang Valley. Includes cab, couple hotel &amp; sightseeing.</p>
              <a href="#contact" class="package-link">Get Quote <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </article>
          <article class="package-card reveal reveal-delay-1">
            <div class="package-card-bg">
              <img src="https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=900&q=90" alt="Himachal Family Tour" loading="lazy">
            </div>
            <div class="package-icon"><i class="fa-solid fa-people-roof"></i></div>
            <span class="package-duration">5 Nights / 6 Days</span>
            <div class="package-card-body">
              <h3>Himachal Family Tour</h3>
              <p>Shimla, Manali &amp; Kullu valley with family-friendly hotels, cab &amp; child-safe itinerary.</p>
              <a href="#contact" class="package-link">Get Quote <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </article>
          <article class="package-card reveal reveal-delay-2">
            <div class="package-card-bg">
              <img src="https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=900&q=90" alt="Spiti Valley Adventure" loading="lazy">
            </div>
            <div class="package-icon"><i class="fa-solid fa-person-hiking"></i></div>
            <span class="package-duration">8 Nights / 9 Days</span>
            <div class="package-card-body">
              <h3>Spiti Valley Adventure</h3>
              <p>Kaza, Chandratal Lake, Pin Valley, Key Monastery. For the true Himalayan explorer.</p>
              <a href="#contact" class="package-link">Get Quote <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ══ CONTACT ══ -->
    <section id="contact" class="contact-strip section-pad">
      <div class="container mx-auto px-3">
        <div class="contact-card reveal">
          <div class="contact-info">
            <div>
              <h2>Plan Your <span>Himachal</span> Trip Today</h2>
              <p>Fill the form and our team will get back to you on WhatsApp within minutes with the best quote for your Himachal journey.</p>
            </div>
            <div class="contact-detail-list">
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-solid fa-phone"></i></div>
                <span><a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a></span>
              </div>
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-brands fa-whatsapp"></i></div>
                <span><a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></span>
              </div>
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-regular fa-envelope"></i></div>
                <span>info@himachalyatratravels.com</span>
              </div>
              <div class="contact-detail">
                <div class="contact-detail-icon"><i class="fa-solid fa-location-dot"></i></div>
                <span>Shimla &amp; New Delhi, India</span>
              </div>
            </div>
          </div>
          <div class="contact-form-wrap">
            <h3>Send Us Your Travel Details</h3>
            <form id="inquiryForm" action="api/submit.php" method="post">
              <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['lead_form_token']); ?>">
              <input type="hidden" name="email" value="">
              <input type="hidden" name="pax" value="1">
              <input class="hp-field" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true">
              <input name="name" type="text" placeholder="Your Full Name" required>
              <input name="phone" type="tel" placeholder="Phone Number" required>
              <input name="pickup" type="text" placeholder="Pickup City (e.g. Delhi)">
              <input name="destination" type="text" placeholder="Destination (e.g. Manali)">
              <input name="travel_date" type="date">
              <select name="package">
                <option value="classic">Cab Booking</option>
                <option value="budget">Tour Package</option>
                <option value="luxury">Tempo Traveller</option>
              </select>
              <textarea name="message" placeholder="Any additional requirements, group size, hotel preferences..."></textarea>
              <div class="form-submit-row">
                <button type="submit">
                  <i class="fa-brands fa-whatsapp"></i> Submit &amp; Chat on WhatsApp
                </button>
                <p id="formStatus" class="form-status" role="status" aria-live="polite"></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- ══ BOOKING POPUP MODAL ══ -->
  <div id="bookingModal" class="modal-overlay" role="dialog" aria-modal="true" aria-label="Quick Booking">
    <div class="modal-card">
      <button class="modal-close" aria-label="Close">&times;</button>
      <div class="modal-header">
        <i class="fa-solid fa-mountain-sun"></i>
        <h2>Plan Your Himachal Trip</h2>
        <p>Get a free quote in under 5 minutes</p>
      </div>
      <form class="modal-form" id="modalForm">
        <input type="text" name="modal_name" placeholder="Your Name" required>
        <input type="tel" name="modal_phone" placeholder="Phone Number" required>
        <input type="text" name="modal_from" placeholder="Pickup City (e.g. Delhi)">
        <input type="text" name="modal_to" placeholder="Destination (e.g. Manali)">
        <input type="date" name="modal_date">
        <button type="submit" class="modal-submit">
          <i class="fa-brands fa-whatsapp"></i> Get Free Quote on WhatsApp
        </button>
      </form>
      <p class="modal-note"><i class="fa-solid fa-shield-halved"></i> Your information is 100% safe &amp; private</p>
    </div>
  </div>

<?php require 'includes/foot.php'; ?>
</body>
</html>
