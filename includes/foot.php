<?php
$fBase = $base ?? '';
$fAl   = fn(string $id) => $fBase ? "index.php#{$id}" : "#{$id}";
?>
  <!-- ══ FOOTER ══ -->
  <footer class="footer">
    <div class="footer-top">
      <div class="container mx-auto px-3">
        <div class="footer-grid">
          <div class="footer-brand">
            <a class="logo" href="<?php echo $fBase ?: '#home'; ?>" aria-label="Himachal Yatra Travels">
              <span class="logo-icon"><i class="fa-solid fa-mountain-sun"></i></span>
              <span class="logo-text">
                <span class="brand-main">HIMACHAL <span>YATRA</span></span>
                <span class="brand-sub">TRAVELS</span>
              </span>
            </a>
            <p>Himachal Pradesh's trusted travel partner. Reliable cabs, curated tour packages and group tempo travellers across all of Himachal — from Shimla to Spiti.</p>
            <div class="footer-social">
              <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
              <a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
          </div>
          <div class="footer-col">
            <h3>Quick Links</h3>
            <a href="<?php echo $fBase ?: '#home'; ?>"><i class="fa-solid fa-chevron-right"></i> Home</a>
            <a href="<?php echo $fAl('about'); ?>"><i class="fa-solid fa-chevron-right"></i> About Us</a>
            <a href="<?php echo $fAl('fleet'); ?>"><i class="fa-solid fa-chevron-right"></i> Our Fleet</a>
            <a href="<?php echo $fAl('routes'); ?>"><i class="fa-solid fa-chevron-right"></i> Popular Routes</a>
            <a href="<?php echo $fAl('contact'); ?>"><i class="fa-solid fa-chevron-right"></i> Contact Us</a>
          </div>
          <div class="footer-col">
            <h3>Destinations</h3>
            <a href="manali.php"><i class="fa-solid fa-chevron-right"></i> Manali</a>
            <a href="shimla.php"><i class="fa-solid fa-chevron-right"></i> Shimla</a>
            <a href="dharamshala.php"><i class="fa-solid fa-chevron-right"></i> Dharamshala</a>
            <a href="dalhousie.php"><i class="fa-solid fa-chevron-right"></i> Dalhousie</a>
            <a href="spiti.php"><i class="fa-solid fa-chevron-right"></i> Spiti Valley</a>
          </div>
          <div class="footer-col">
            <h3>Contact Us</h3>
            <div class="footer-contact-item">
              <i class="fa-solid fa-phone"></i>
              <span><a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a></span>
            </div>
            <div class="footer-contact-item">
              <i class="fa-regular fa-envelope"></i>
              <span>info@himachalyatratravels.com</span>
            </div>
            <div class="footer-contact-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Shimla &amp; New Delhi, India</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto px-3">
      <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> Himachal Yatra Travels. All Rights Reserved.</span>
        <span><a href="#">Privacy Policy</a> &nbsp;|&nbsp; <a href="#">Terms &amp; Conditions</a></span>
      </div>
    </div>
  </footer>

  <!-- WhatsApp Float -->
  <a class="wa-float" href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>

  <!-- Mobile CTA -->
  <div class="mobile-cta">
    <a href="tel:<?php echo h($phoneTel); ?>"><i class="fa-solid fa-phone"></i> Call Now</a>
    <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="script.js" defer></script>
