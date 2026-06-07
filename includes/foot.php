<?php
$fBase = $base ?? '';
$fAl   = fn(string $id) => $fBase ? "index.php#{$id}" : "#{$id}";
?>
  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-top">
      <div class="container mx-auto px-3">
        <div class="footer-grid">

          <!-- About -->
          <div class="footer-brand">
            <a class="footer-logo-full" href="<?php echo $fBase ?: '#home'; ?>" aria-label="Himachal Yatra Travels">
              <img src="assets/logo.svg" alt="Himachal Yatra Travels" width="200" height="46" loading="lazy">
            </a>
            <h3 class="footer-about-heading">Born in the Mountains.<br>Built for the Road.</h3>
            <p>Founded by mountain-road specialists who have driven every major Himachal route in all seasons. Verified drivers, transparent fares and a travel desk that stays reachable from departure to drop. No middlemen — just us, you and the mountains.</p>
            <div class="footer-social">
              <?php
                // Real profile URLs come from env vars; unset ones are skipped (no dead links).
                foreach ([
                  ['SOCIAL_FACEBOOK',  'facebook-f', 'Facebook'],
                  ['SOCIAL_INSTAGRAM', 'instagram',  'Instagram'],
                  ['SOCIAL_YOUTUBE',   'youtube',    'YouTube'],
                ] as [$env, $icon, $label]):
                  $url = getenv($env) ?: '';
                  if ($url === '') continue; ?>
              <a href="<?php echo h($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo h($label); ?>"><i class="fa-brands fa-<?php echo $icon; ?>"></i></a>
              <?php endforeach; ?>
              <a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
          </div>

          <!-- Quick Links -->
          <div class="footer-col">
            <h3>Quick Links</h3>
            <a href="<?php echo $fBase ?: '#home'; ?>"><i class="fa-solid fa-chevron-right"></i> Home</a>

            <a href="<?php echo $fAl('routes'); ?>"><i class="fa-solid fa-chevron-right"></i> Popular Routes</a>
            <a href="<?php echo $fAl('fleet'); ?>"><i class="fa-solid fa-chevron-right"></i> Our Fleet</a>
            <a href="<?php echo $fAl('packages'); ?>"><i class="fa-solid fa-chevron-right"></i> Tour Packages</a>
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
            <div class="footer-contact-item">
              <i class="fa-brands fa-whatsapp"></i>
              <span><a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener">WhatsApp Concierge</a></span>
            </div>
            <div class="footer-contact-item">
              <i class="fa-regular fa-envelope"></i>
              <span>info@himachalyatratravels.com</span>
            </div>
            <div class="footer-contact-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Shimla &amp; New Delhi, India</span>
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
        <span>&copy; <?php echo date('Y'); ?> Himachal Yatra Travels. All Rights Reserved.</span>
        <span><a href="privacy.php">Privacy Policy</a> &nbsp;|&nbsp; <a href="terms.php">Terms &amp; Conditions</a></span>
      </div>
    </div>
  </footer>

  <!-- WhatsApp Float -->
  <a class="wa-float" href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>

  <!-- Mobile CTA -->
  <div class="mobile-cta">
    <a href="tel:<?php echo h($phoneTel); ?>"><i class="fa-solid fa-phone"></i> Call</a>
    <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> Concierge</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="script.js" defer></script>
