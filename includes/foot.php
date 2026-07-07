<?php
$fBase = $base ?? '';
$fAl   = fn(string $id) => $fBase ? "index.php#{$id}" : "#{$id}";
?>
  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-top">
      <div class="container mx-auto px-3">

        <!-- Newsletter — journey notes -->
        <div class="footer-news">
          <div class="footer-news-text">
            <h3>Journey Notes</h3>
            <p>Seasonal routes, travel windows and quiet corners of Himachal — a short note, only when it matters.</p>
          </div>
          <form class="footer-news-form" id="nlForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['lead_form_token'] ?? ''); ?>">
            <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="display:none">
            <div class="footer-news-row">
              <input type="email" name="email" id="nlEmail" placeholder="Your email address" autocomplete="email" aria-label="Email address" required>
              <button type="submit" id="nlBtn"><span>Subscribe</span><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
            </div>
            <p class="footer-news-status" id="nlStatus" role="status" aria-live="polite"></p>
          </form>
        </div>

        <div class="footer-grid">

          <!-- About -->
          <div class="footer-brand">
            <a class="footer-logo-full" href="<?php echo $fBase ?: '#home'; ?>" aria-label="Himachal Safar">
              <img src="assets/logo.svg" alt="Himachal Safar" width="200" height="46" loading="lazy">
            </a>
            <h3 class="footer-about-heading">Born in the Mountains.<br>Built for the Road.</h3>
            <p>Founded by mountain-road specialists who have driven every major Himachal route in all seasons. Verified drivers, transparent fares and a travel desk that stays reachable from departure to drop. No middlemen — just us, you and the mountains.</p>
            <div class="footer-social">
              <?php
                // Profile URLs are admin-editable in Settings; unset ones are skipped (no dead links).
                foreach ([
                  ['facebook',  'facebook-f', 'Facebook'],
                  ['instagram', 'instagram',  'Instagram'],
                  ['youtube',   'youtube',    'YouTube'],
                ] as [$key, $icon, $label]):
                  $url = $socialLinks[$key] ?? '';
                  if ($url === '') continue; ?>
              <a href="<?php echo h($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo h($label); ?>"><i class="fa-brands fa-<?php echo $icon; ?>"></i></a>
              <?php endforeach; ?>
            </div>
            <p class="footer-founders">Founded by Vishal Thakur &amp; Abhishek Thakur</p>
          </div>

          <!-- Quick Links -->
          <div class="footer-col">
            <h3>Quick Links</h3>
            <a href="<?php echo $fBase ?: '#home'; ?>"><i class="fa-solid fa-chevron-right"></i> Home</a>

            <a href="<?php echo $fAl('routes'); ?>"><i class="fa-solid fa-chevron-right"></i> Popular Routes</a>
            <a href="packages.php"><i class="fa-solid fa-chevron-right"></i> Tour Packages</a>
            <a href="<?php echo $fAl('fleet'); ?>"><i class="fa-solid fa-chevron-right"></i> Our Fleet</a>
            <?php /* href is the no-modal fallback (deep-links to the homepage calculator);
                     on pages that include calc_modal.php, JS intercepts and opens the popup. */ ?>
            <a href="index.php?calc=" data-open-quote=""><i class="fa-solid fa-chevron-right"></i> Plan a Custom Trip</a>
            <a href="<?php echo $fAl('reviews'); ?>"><i class="fa-solid fa-chevron-right"></i> Reviews</a>
            <a href="<?php echo $fAl('contact'); ?>"><i class="fa-solid fa-chevron-right"></i> Contact Us</a>
          </div>

          <!-- Destinations -->
          <div class="footer-col">
            <h3>Destinations</h3>
            <a href="manali.php"><i class="fa-solid fa-chevron-right"></i> Manali</a>
            <a href="shimla.php"><i class="fa-solid fa-chevron-right"></i> Shimla</a>
            <a href="dharamshala.php"><i class="fa-solid fa-chevron-right"></i> Dharamshala</a>
            <a href="dalhousie.php"><i class="fa-solid fa-chevron-right"></i> Dalhousie</a>
            <a href="spiti.php"><i class="fa-solid fa-chevron-right"></i> Spiti Valley</a>
          </div>

          <!-- Contact -->
          <div class="footer-col">
            <h3>Get In Touch</h3>
            <div class="footer-contact-item">
              <i class="fa-solid fa-phone"></i>
              <span><a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a></span>
            </div>
            <?php if (!empty($phone2Tel)): ?>
            <div class="footer-contact-item">
              <i class="fa-solid fa-phone"></i>
              <span><a href="tel:<?php echo h($phone2Tel); ?>"><?php echo h($phone2Display); ?></a></span>
            </div>
            <?php endif; ?>
            <div class="footer-contact-item">
              <i class="fa-brands fa-whatsapp"></i>
              <span><a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener">WhatsApp Concierge</a></span>
            </div>
            <div class="footer-contact-item">
              <i class="fa-regular fa-envelope"></i>
              <span><a href="mailto:<?php echo h($bizEmail); ?>"><?php echo h($bizEmail); ?></a></span>
            </div>
            <div class="footer-contact-item">
              <i class="fa-solid fa-location-dot"></i>
              <span><?php echo h($agencyLocation); ?></span>
            </div>
            <div class="footer-trust">
              <span><i class="fa-solid fa-shield-halved"></i> Verified Drivers</span>
              <span><i class="fa-solid fa-headset"></i> 24/7 Support</span>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="container mx-auto px-3">
      <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> Himachal Safar. All Rights Reserved.</span>
        <span><a href="privacy.php">Privacy Policy</a> &nbsp;|&nbsp; <a href="terms.php">Terms &amp; Conditions</a></span>
      </div>
    </div>
  </footer>

  <!-- WhatsApp Float (pre-chat capture; href is a no-JS fallback) -->
  <a class="wa-float" href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" data-wa-lead data-source="float">
    <i class="fa-brands fa-whatsapp"></i>
  </a>

  <!-- Back to top -->
  <button type="button" class="back-top" id="backTop" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
  </button>

  <!-- Mobile CTA: Call · WhatsApp · Get Quote. The Quote link deep-links to the homepage
       calculator as a fallback; on pages that include calc_modal.php the [data-open-quote]
       handler intercepts it and opens the popup in place. -->
  <div class="mobile-cta">
    <a href="tel:<?php echo h($phoneTel); ?>" aria-label="Call us now"><i class="fa-solid fa-phone"></i> Call</a>
    <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp" data-wa-lead data-source="mobilebar"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
    <a href="index.php?calc=" data-open-quote="" aria-label="Get an instant quote"><i class="fa-solid fa-calculator"></i> Get Quote</a>
  </div>

  <!-- Pre-chat WhatsApp lead popup (site-wide) -->
  <?php require __DIR__ . '/wa_lead_modal.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="script.js?v=<?php echo @filemtime(__DIR__ . '/../script.js'); ?>" defer></script>
