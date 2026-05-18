<?php
$base       = 'index.php';
$activeDest = 'spiti';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Spiti Valley Tour &amp; Cab Booking | Himachal Yatra Travels</title>
  <meta name="description" content="Explore Spiti Valley with Himachal Yatra Travels. Key Monastery, Chandratal Lake, Kaza, Pin Valley — 8N/9D adventure packages with expert high-altitude drivers.">
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
      <img src="https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=1920&q=90" alt="Spiti Valley high altitude Himachal Pradesh" fetchpriority="high" decoding="async" width="1920" height="1080">
    </div>
    <div class="dest-hero-overlay" style="background:linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(22,101,52,0.35) 100%)"></div>
    <div class="container mx-auto px-3 dest-hero-content">
      <nav class="dest-breadcrumb" aria-label="breadcrumb">
        <a href="index.php">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="index.php#routes">Destinations</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Spiti Valley</span>
      </nav>
      <div class="dest-hero-tag"><i class="fa-solid fa-person-hiking"></i> Adventure Destination</div>
      <h1>Spiti Valley</h1>
      <p>The Middle Land — ancient monasteries, barren moonscapes and some of the world's highest motorable roads</p>
      <div class="dest-hero-facts">
        <div class="dest-fact"><i class="fa-solid fa-mountain"></i><span>3,800 m+ Altitude</span></div>
        <div class="dest-fact"><i class="fa-regular fa-calendar"></i><span>Jun – Sep Only</span></div>
        <div class="dest-fact"><i class="fa-solid fa-route"></i><span>~700 km from Delhi</span></div>
        <div class="dest-fact"><i class="fa-solid fa-temperature-half"></i><span>−30°C to 15°C</span></div>
      </div>
      <div class="dest-hero-warning"><i class="fa-solid fa-triangle-exclamation"></i> High altitude destination — expert drivers mandatory. We specialise in Spiti routes.</div>
      <a href="index.php#contact" class="dest-hero-btn"><i class="fa-brands fa-whatsapp"></i> Plan Spiti Trip</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="dest-about section-pad">
    <div class="container mx-auto px-3">
      <div class="dest-about-grid">
        <div class="dest-about-text reveal">
          <div class="section-tag"><i class="fa-solid fa-info-circle"></i> About Spiti Valley</div>
          <h2 class="section-title">The Last <span>Forbidden Land</span></h2>
          <p>Spiti Valley is a cold desert mountain valley nestled high in the Himalayas in north-eastern Himachal Pradesh. Bordered by Tibet to the east, it is one of the most remote and sparsely populated regions of India — and one of the most spectacular.</p>
          <p>At an average altitude of 3,800 metres, Spiti's landscape is dramatic and barren — rolling barren mountains, ancient Buddhist monasteries perched on cliffsides, high-altitude lakes and river valleys that look like they belong on another planet. Getting here requires expert drivers, acclimatization and planning. Our team specialises exclusively in Spiti expeditions.</p>
          <div class="dest-about-tags">
            <span><i class="fa-solid fa-circle-check"></i> Expert Drivers Required</span>
            <span><i class="fa-solid fa-circle-check"></i> 9-Day Packages Available</span>
            <span><i class="fa-solid fa-circle-check"></i> Jun–Sep Road Access</span>
            <span><i class="fa-solid fa-circle-check"></i> 4WD Vehicles</span>
          </div>
        </div>
        <div class="dest-about-img reveal">
          <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=85" alt="Spiti Valley high altitude landscape" loading="lazy" decoding="async" width="800" height="600">
        </div>
      </div>
    </div>
  </section>

  <!-- HIGHLIGHTS -->
  <section class="dest-highlights section-pad" style="background:var(--green-light)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-star"></i> Must Visit</div>
        <h2 class="section-title">Top Highlights of <span>Spiti Valley</span></h2>
      </div>
      <div class="highlights-grid">
        <?php
        $highlights = [
          ['fa-place-of-worship','Key Monastery',   '4,166 m high monastery, one of the largest in the area. Spectacular location on a rocky hilltop. Must-visit in Spiti.'],
          ['fa-water',          'Chandratal Lake',   '"Moon Lake" at 14,100 ft. Crescent-shaped gem surrounded by snow-capped peaks. Camping at the lakeside.'],
          ['fa-city',           'Kaza',              'District headquarters of Spiti at 3,800 m. Base camp for Spiti explorations. Markets, guesthouses and warm locals.'],
          ['fa-place-of-worship','Dhankar Monastery','Built on a cliff 1,000 m above the confluence of Spiti and Pin rivers. One of the most dramatic monastery locations.'],
          ['fa-mountain',       'Pin Valley',        'National Park with some of Spiti\'s rarest wildlife — Snow Leopard, Ibex, Bharal. Mud village with oldest gompa.'],
          ['fa-house',          'Kibber Village',    'One of the highest villages in the world with a road. At 4,270 m — a true edge-of-the-world experience.'],
          ['fa-place-of-worship','Tabo Monastery',   '1,000-year-old monastery in Tabo, called the "Ajanta of the Himalayas". Ancient frescoes and clay sculptures.'],
          ['fa-road',           'Kunzum Pass',       '4,590 m high pass — dramatic gateway to Spiti from Manali via Lahaul. The highest motorable pass in Himachal.'],
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
        <h2 class="section-title">Spiti Valley in <span>Pictures</span></h2>
      </div>
      <div class="dest-gallery-grid reveal">
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=1200&q=85" alt="Spiti Valley high altitude" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=700&q=85" alt="Mountain pass Spiti" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85" alt="Snow peaks Spiti Valley" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1548013584-24e97daa89c6?auto=format&fit=crop&w=700&q=85" alt="Mountain landscape Spiti" loading="lazy"></div>
        <div class="gallery-item gi-wide"><img src="https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&q=85" alt="Himalayan peaks Spiti area" loading="lazy"></div>
        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?auto=format&fit=crop&w=700&q=85" alt="Remote valley Himachal" loading="lazy"></div>
      </div>
    </div>
  </section>

  <!-- ROUTES -->
  <section class="dest-routes-section section-pad" style="background:var(--cream)">
    <div class="container mx-auto px-3">
      <div class="section-header reveal">
        <div class="section-tag"><i class="fa-solid fa-route"></i> Plan Your Spiti Trip</div>
        <h2 class="section-title">Routes to <span>Spiti Valley</span></h2>
        <p class="section-desc">Two ways to enter Spiti — via Manali (Kunzum Pass) or via Shimla (Kinnaur). We cover both. Both require expert high-altitude drivers.</p>
      </div>
      <div class="dest-route-cards">
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Delhi via Manali</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Spiti</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> ~700 km</span>
            <span><i class="fa-regular fa-clock"></i> 2 days</span>
            <span class="drc-badge">Adventure Route</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Get Custom Quote</div>
            <a href="index.php#contact" class="drc-btn">Enquire <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> Delhi via Shimla</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Spiti</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> ~650 km</span>
            <span><i class="fa-regular fa-clock"></i> 2 days</span>
            <span class="drc-badge">Kinnaur Circuit</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Get Custom Quote</div>
            <a href="index.php#contact" class="drc-btn">Enquire <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="dest-route-card reveal">
          <div class="drc-top">
            <span class="drc-from"><i class="fa-solid fa-location-dot"></i> 8N/9D Package</span>
            <i class="fa-solid fa-arrow-right drc-arrow"></i>
            <span class="drc-to"><i class="fa-solid fa-flag-checkered"></i> Full Spiti Circuit</span>
          </div>
          <div class="drc-meta">
            <span><i class="fa-solid fa-route"></i> Full Circuit</span>
            <span><i class="fa-regular fa-clock"></i> 9 Days</span>
            <span class="drc-badge">All-Inclusive</span>
          </div>
          <div class="drc-bottom">
            <div class="drc-price">Best Value</div>
            <a href="index.php#contact" class="drc-btn">Get Package <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- IMPORTANT NOTE -->
  <section class="section-pad" style="background:var(--green-light)">
    <div class="container mx-auto px-3">
      <div class="spiti-advisory reveal">
        <div class="spiti-advisory-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
          <h3>Important: Spiti Valley Travel Advisory</h3>
          <ul>
            <li><strong>Road Open Season:</strong> June to September only. Roads close due to heavy snowfall in winter.</li>
            <li><strong>Inner Line Permit:</strong> Required for some areas near the China border. We assist with permit documentation.</li>
            <li><strong>Altitude Sickness:</strong> Acclimatize in Manali or Kaza for 1–2 days before ascending further. Do not rush.</li>
            <li><strong>4WD Vehicles:</strong> We use only suitable SUVs (Innova Crysta / Fortuner) for Spiti. No sedans.</li>
            <li><strong>Fuel:</strong> Fill up at Kaza — the last reliable petrol pump. Our drivers know every stop.</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="dest-cta-strip">
    <div class="container mx-auto px-3">
      <div class="dest-cta-inner reveal">
        <div class="dest-cta-text">
          <h2>Conquer <span>Spiti Valley?</span></h2>
          <p>This is not an ordinary trip. Our Spiti specialists will plan every detail — permits, acclimatization, the right vehicle and experienced driver.</p>
        </div>
        <div class="dest-cta-btns">
          <a href="index.php#contact" class="btn orange-btn"><i class="fa-solid fa-calendar-check"></i> Plan My Spiti Trip</a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo rawurlencode('Hi, I want to plan a Spiti Valley trip. Please share package details and availability.'); ?>" target="_blank" rel="noopener" class="btn wa-cta-btn"><i class="fa-brands fa-whatsapp"></i> WhatsApp Us</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require 'includes/foot.php'; ?>
</body>
</html>
