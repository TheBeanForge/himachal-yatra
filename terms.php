<?php
$base       = 'index.php';
$activeDest = '';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en" data-season="<?php echo h(current_season()); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Terms &amp; Conditions | Himachal Safar</title>
  <meta name="description" content="The terms that apply when you request a quote, book a cab, or travel with Himachal Safar.">
  <?php
    $seoTitle = 'Terms & Conditions | Himachal Safar';
    $seoDesc  = 'The terms that apply when you book a cab or tour with Himachal Safar.';
    $seoPath  = 'terms.php';
    $seoCrumb = 'Terms & Conditions';
    include __DIR__ . '/includes/seo_head.php';
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/base.css?v=<?php echo @filemtime(__DIR__ . "/assets/base.css"); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo @filemtime(__DIR__ . '/style.css'); ?>">
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-4GZDXE68YZ"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-4GZDXE68YZ');
  </script>
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>
<main>
  <section class="section-pad legal-page" id="home">
    <div class="container mx-auto px-3">
      <div class="legal-wrap">
        <h1>Terms &amp; Conditions</h1>
        <p class="legal-updated">Last updated: <?php echo date('F Y'); ?></p>

        <p>These terms apply to quotes, bookings and travel arranged through Himachal Safar.
        By using our website or services you agree to them.</p>

        <h2>Quotes &amp; Bookings</h2>
        <p>Prices shown by the instant-quote tool are indicative estimates. The final fare is confirmed
        after we review your route and requirements. A booking is confirmed only once we acknowledge it
        directly.</p>

        <h2>Payments</h2>
        <?php $advPct = max(5, min(100, (int)app_setting('advance_percent', '25'))); ?>
        <p>A booking is confirmed on payment of an advance of <strong><?= $advPct ?>% of the estimated
        total</strong>; the balance is payable during the trip as agreed at confirmation. Applicable
        taxes and toll/parking charges are as stated in your quote.</p>

        <h2>Cancellations &amp; Changes</h2>
        <p>Refunds of the advance are tiered by notice period: <strong>100% with 20+ days' notice</strong>,
        75% at 10–19 days, 50% at 5–9 days, and <strong>no refund with less than 5 days' notice</strong> or
        for no-shows. One free reschedule is included when requested at least 72 hours before pickup. If
        <em>we</em> cancel due to weather, road closures or vehicle issues, you receive a full refund or a
        free reschedule — your choice. Full details, including how refunds are paid, are in our
        <a href="cancellation.php">Cancellation &amp; Refund Policy</a>.</p>

        <h2>Liability</h2>
        <p>We arrange experienced drivers and well-maintained vehicles, but we are not liable for delays or
        losses caused by weather, road closures, or other circumstances outside our reasonable control.</p>

        <h2>Governing Law</h2>
        <p>These terms are governed by the laws of India, with jurisdiction in Himachal Pradesh.</p>

        <h2>Contact</h2>
        <p>Questions? Email <a href="mailto:<?php echo h($bizEmail); ?>"><?php echo h($bizEmail); ?></a>
        or call <a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a>.</p>

        <p class="legal-note"><i class="fa-solid fa-circle-info"></i> This is a starting template — please review it with a legal professional before launch.</p>
      </div>
    </div>
  </section>
</main>
<?php require 'includes/foot.php'; ?>
</body>
</html>
