<?php
$base       = 'index.php';
$activeDest = '';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Terms &amp; Conditions | Himachal Yatra Travels</title>
  <meta name="description" content="The terms that apply when you request a quote, book a cab, or travel with Himachal Yatra Travels.">
  <?php
    $seoTitle = 'Terms & Conditions | Himachal Yatra Travels';
    $seoDesc  = 'The terms that apply when you book a cab or tour with Himachal Yatra Travels.';
    $seoPath  = 'terms.php';
    $seoCrumb = 'Terms & Conditions';
    include __DIR__ . '/includes/seo_head.php';
  ?>
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
  <section class="section-pad legal-page" id="home">
    <div class="container mx-auto px-3">
      <div class="legal-wrap">
        <h1>Terms &amp; Conditions</h1>
        <p class="legal-updated">Last updated: <?php echo date('F Y'); ?></p>

        <p>These terms apply to quotes, bookings and travel arranged through Himachal Yatra Travels.
        By using our website or services you agree to them.</p>

        <h2>Quotes &amp; Bookings</h2>
        <p>Prices shown by the instant-quote tool are indicative estimates. The final fare is confirmed
        after we review your route and requirements. A booking is confirmed only once we acknowledge it
        directly.</p>

        <h2>Payments</h2>
        <p>Payment terms (advance and balance) are communicated at the time of confirmation. Applicable
        taxes and toll/parking charges are as stated in your quote.</p>

        <h2>Cancellations &amp; Changes</h2>
        <p>Cancellation and rescheduling terms depend on the package and notice period and will be shared
        at confirmation. Mountain travel may be affected by weather and road conditions beyond our control.</p>

        <h2>Liability</h2>
        <p>We arrange experienced drivers and well-maintained vehicles, but we are not liable for delays or
        losses caused by weather, road closures, or other circumstances outside our reasonable control.</p>

        <h2>Governing Law</h2>
        <p>These terms are governed by the laws of India, with jurisdiction in Himachal Pradesh.</p>

        <h2>Contact</h2>
        <p>Questions? Email <a href="mailto:info@himachalyatratravels.com">info@himachalyatratravels.com</a>
        or call <a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a>.</p>

        <p class="legal-note"><i class="fa-solid fa-circle-info"></i> This is a starting template — please review it with a legal professional before launch.</p>
      </div>
    </div>
  </section>
</main>
<?php require 'includes/foot.php'; ?>
</body>
</html>
