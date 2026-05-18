<?php
$base       = 'index.php';
$activeDest = 'shimla';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shimla Tour Packages &amp; Cab Booking | Himachal Yatra Travels</title>
  <meta name="description" content="Book Shimla cab from Delhi from ₹7,499. Mall Road, Jakhu Temple, Kufri, Chail — experience the Queen of Hills with Himachal Yatra Travels.">
  <meta name="theme-color" content="#166534">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
      <img src="https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=1920&q=90" alt="Shimla Queen of Hills Himachal Pradesh" fetchpriority="high" decoding="async" width="1920" height="1080">
    </div>
    <div class="dest-hero-overlay"></div>
    <div class="container mx-auto px-3 dest-hero-content">
      <nav class="dest-breadcrumb" aria-label="breadcrumb">
        <a href="index.php">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="index.php#routes">Destinations</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Shimla</span>
      </nav>
      <div class="dest-hero-tag"><i class="fa-solid fa-mountain-sun"></i> Himachal Pradesh</div>
      <h1>Shimla</h1>
      <p>The Queen of Hills — colonial charm, pine forests and the iconic Mall Road</p>
      <div class="dest-hero-facts">
        <div class="dest-fact"><i class="fa-solid fa-mountain"></i><span>2,205 m Altitude</span></div>
        <div class="dest-fact"><i class="fa-regular fa-calendar"></i><span>Year-round Destination</span></div>
        <div class="dest-fact"><i class="fa-solid fa-route"></i><span>350 km from Delhi</span></div>
        <div class="dest-fact"><i class="fa-solid fa-temperature-half"></i><span>2°C to 30°C</span></div>
      </div>
      <a href="index.php#contact" class="dest-hero-btn"><i class="fa-brands fa-whatsapp"></i> Book Shimla Trip</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="dest-about section-pad">
    <div class="container mx-auto px-3">
      <div class="dest-about-grid">
        <div class="dest-about-text reveal">
          <div class="section-tag"><i class="fa-solid fa-info-circle"></i> About Shimla</div>
          <h2 class="section-title">The <span>Queen of Hills</span></h2>
          <p>Shimla, the capital of Himachal Pradesh, is one of India's most beloved hill stations. Perched at 2,205 metres in the Shivalik range, it was once the summer capital of British India — and its Victorian-era architecture, wide promenades and cedar forests still carry that old-world elegance.</p>
          <p>From the buzzing Mall Road and Ridge to the quiet apple orchards of Kufri, from the adventure at Narkanda to the royal heritage of Chail — Shimla offers something for every traveller. It is the perfect year-round destination, with snow in winters and cool summer escapes.</p>
          <div class="dest-about-tags">
            <span><i class="fa-solid fa-circle-check"></i> Heritage Architecture</span>
            <span><i class="fa-solid fa-circle-check"></i> Apple Orchards</span>
            <span><i class="fa-solid fa-circle-check"></i> Toy Train</span>
            <span><i class="fa-solid fa-circle-check"></i> Skiing at Kufri</span>
          </div>
        </div>
        <div class="dest-about-img reveal">
          <img src="https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=800&q=85" alt="Shimla hills Himachal Pradesh" loading="lazy" decoding="async" width="800" height="600">
        </div>
      </div>
    </div>
  </section>

  <!-- HIGHLIGHTS -->
  <section class="dest-highlights section-pad" style="background:var(--green-light)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-star"></i> Must Visit</div>
        <h2 class="section-title">Top Highlights of <span>Shimla</span></h2>
      </div>
      <div class="highlights-grid">
        <?php
        $highlights = [
          ['fa-person-walking', 'Mall Road',        'The heartbeat of Shimla. Shops, restaurants, colonial buildings and mountain views all in one iconic promenade.'],
          ['fa-place-of-worship','Jakhu Temple',    '2,455 m high temple dedicated to Lord Hanuman. Magnificent views of Shimla — and mischievous monkeys!'],
          ['fa-skiing',         'Kufri',             '16 km from Shimla. Skiing in winter, horse-riding and Himalayan Nature Park in summer.'],
          ['fa-chess-rook',     'Chail',             'Highest cricket ground in the world at 2,250 m. Royal Chail Palace and dense forest — a serene retreat.'],
          ['fa-train',          'Kalka–Shimla Railway','UNESCO heritage toy train through 102 tunnels and 18 viaducts. One of the most scenic rail journeys in India.'],
          ['fa-hot-tub',        'Tattapani',         'Hot sulphur springs on the banks of Sutlej River, 51 km from Shimla. Perfect for relaxation.'],
          ['fa-apple-whole',    'Narkanda',          'Apple orchard country at 2,700 m. Skiing in winter, Hatu Peak and breathtaking Himalayan panoramas.'],
          ['fa-temple',         'The Ridge',         'Open space in heart of Shimla. Gaiety Cultural Complex, Christ Church and stunning views of snow-capped mountains.'],
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
        <h2 class="section-title">Shimla in <span>Pictures</span></h2>
      </div>
      <div class="dest-gallery-grid reveal">
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=1200&q=85" alt="Shimla hill station" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85" alt="Snow covered Shimla" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=700&q=85" alt="Pine forests Shimla" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=700&q=85" alt="Shimla hills landscape" loading="lazy"></div>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85" alt="Shimla mountains green" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=700&q=85" alt="Forest trail Shimla" loading="lazy"></div>
      </div>
    </div>
  </section>

  <!-- ROUTES TO SHIMLA -->
  <section class="dest-routes-section section-pad" style="background:var(--cream)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-route"></i> Book a Cab</div>
        <h2 class="section-title">Routes to <span>Shimla</span></h2>
        <p class="section-desc">Easy road access from Delhi, Chandigarh and Pathankot with our expert mountain drivers.</p>
      </div>
      <div class="dest-route-cards">
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Delhi</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Shimla</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 350 km</span>
            <span><i class="fa-regular fa-clock"></i> 8–9 hrs</span>
            <span class="drc-badge">Weekend Package</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Starting ₹7,499</div>
            <a href="index.php#contact" class="drc-btn">Book Now <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Chandigarh</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Shimla</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 115 km</span>
            <span><i class="fa-regular fa-clock"></i> 3–4 hrs</span>
            <span class="drc-badge">Express Package</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Starting ₹3,499</div>
            <a href="index.php#contact" class="drc-btn">Book Now <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Ambala</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Shimla</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> 200 km</span>
            <span><i class="fa-regular fa-clock"></i> 5–6 hrs</span>
            <span class="drc-badge">Custom Package</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Get Quote</div>
            <a href="index.php#contact" class="drc-btn">Enquire <i class="fa-solid fa-arrow-right"></i></a>
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
          <h2>Ready to Visit <span>Shimla?</span></h2>
          <p>Get a free quote on WhatsApp in minutes. Expert drivers, transparent pricing, no hidden charges.</p>
        </div>
        <div class="dest-cta-btns">
          <a href="index.php#contact" class="btn orange-btn"><i class="fa-solid fa-calendar-check"></i> Book Now</a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo rawurlencode('Hi, I want to book a trip to Shimla. Please share a quote.'); ?>" target="_blank" rel="noopener" class="btn wa-cta-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Us</a>
        </div>
      </div>
    </div>
  </section>

  <!-- BEST TIME -->
  <section class="dest-besttime section-pad">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-regular fa-calendar"></i> Travel Tips</div>
        <h2 class="section-title">Best Time to Visit <span>Shimla</span></h2>
      </div>
      <div class="season-grid">
        <div class="season-card reveal">
          <div class="season-icon season-summer"><i class="fa-solid fa-sun"></i></div>
          <h3>Summer (Mar–Jun)</h3>
          <p>Most popular — 15°C to 30°C. Escape the Delhi heat. Apple blossoms in spring. Book months in advance.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-1">
          <div class="season-icon season-monsoon"><i class="fa-solid fa-cloud-rain"></i></div>
          <h3>Monsoon (Jul–Sep)</h3>
          <p>Green, lush landscapes but slippery roads. Landslide risk on some routes. Fewer crowds, lower prices.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-2">
          <div class="season-icon season-autumn"><i class="fa-solid fa-leaf"></i></div>
          <h3>Autumn (Oct–Nov)</h3>
          <p>Apple harvest season. Clear skies and crisp air. Best for scenic drives — Kufri, Narkanda and Chail look stunning.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="season-card reveal reveal-delay-3">
          <div class="season-icon season-winter"><i class="fa-solid fa-snowflake"></i></div>
          <h3>Winter (Dec–Feb)</h3>
          <p>Heavy snowfall, 2°C to 10°C. Kufri skiing season. Shimla under snow is magical. Perfect for snow holiday.</p>
          <div class="season-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require 'includes/foot.php'; ?>
</body>
</html>
