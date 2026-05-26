<?php
$fBase = $base ?? '';
$fAl   = fn(string $id) => $fBase ? "index.php#{$id}" : "#{$id}";
?>
  <!-- FOOTER -->
  <footer class="footer">
    <div class="container mx-auto px-3">
      <div class="footer-row">

        <!-- Logo -->
        <div class="footer-logo-section">
          <a href="<?php echo $fBase ?: '#home'; ?>" aria-label="Himachal Yatra Travels">
            <img src="assets/logo.svg" alt="Himachal Yatra Travels" width="160" height="37" loading="lazy">
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="footer-nav-section">
          <a href="<?php echo $fBase ?: '#home'; ?>">Home</a>
          <a href="<?php echo $fAl('routes'); ?>">Routes</a>
          <a href="<?php echo $fAl('fleet'); ?>">Fleet</a>
          <a href="<?php echo $fAl('packages'); ?>">Packages</a>
          <a href="manali.php">Manali</a>
          <a href="shimla.php">Shimla</a>
          <a href="dharamshala.php">Dharamshala</a>
          <a href="<?php echo $fAl('reviews'); ?>">Reviews</a>
          <a href="<?php echo $fAl('contact'); ?>">Contact</a>
        </div>

        <!-- Contact & Social -->
        <div class="footer-contact-section">
          <a href="tel:<?php echo h($phoneTel); ?>" class="contact-link">
            <i class="fa-solid fa-phone"></i> <?php echo h($phoneDisplay); ?>
          </a>
          <a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener" class="contact-link">
            <i class="fa-brands fa-whatsapp"></i> Concierge
          </a>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          </div>
        </div>

      </div>

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
    <a href="tel:<?php echo h($phoneTel); ?>"><i class="fa-solid fa-phone"></i> Call</a>
    <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo h($defaultMessage); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> Concierge</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="script.js" defer></script>
