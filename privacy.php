<?php
$base       = 'index.php';
$activeDest = '';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en" data-season="<?php echo h(current_season()); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Privacy Policy | Himachal Safar</title>
  <meta name="description" content="How Himachal Safar collects, uses, and protects the personal information you share when requesting a quote or booking a trip.">
  <?php
    $seoTitle = 'Privacy Policy | Himachal Safar';
    $seoDesc  = 'How Himachal Safar collects, uses, and protects your personal information.';
    $seoPath  = 'privacy.php';
    $seoCrumb = 'Privacy Policy';
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

    gtag('config', 'G-4GZDXE68YZ');   // GA4 — analytics
    gtag('config', 'AW-18335368848');  // Google Ads — conversion tracking
  </script>
</head>
<body data-wa="<?php echo h($whatsappNumber); ?>">
<?php require 'includes/nav.php'; ?>
<main>
  <section class="section-pad legal-page" id="home">
    <div class="container mx-auto px-3">
      <div class="legal-wrap">
        <h1>Privacy Policy</h1>
        <p class="legal-updated">Last updated: <?php echo date('F Y'); ?></p>

        <p>Himachal Safar ("we", "us") respects your privacy. This policy explains what
        information we collect when you use our website or request a travel quote, and how we use it.</p>

        <h2>Information We Collect</h2>
        <ul>
          <li><strong>Contact details</strong> you provide — name, phone number and email — when you submit a quote or enquiry.</li>
          <li><strong>Trip details</strong> such as pickup city, destination, travel dates and group size.</li>
          <li><strong>Basic technical data</strong> (e.g. IP address) used for security and abuse prevention.</li>
        </ul>

        <h2>How We Use Your Information</h2>
        <ul>
          <li>To prepare your quote and contact you about your trip via phone, WhatsApp or email.</li>
          <li>To improve our services and respond to support requests.</li>
          <li>To meet legal and accounting obligations.</li>
        </ul>

        <h2>Sharing</h2>
        <p>We do not sell your data. We share details only with the drivers and partners assigned to your
        trip, and where required by law.</p>

        <h2>Data Security &amp; Retention</h2>
        <p>We use reasonable safeguards to protect your information and retain enquiry records only as long
        as needed to serve you and meet legal requirements.</p>

        <h2>Your Rights</h2>
        <p>You may request access to, correction of, or deletion of your personal data by contacting us.</p>

        <h2>Contact</h2>
        <p>Questions about this policy? Email <a href="mailto:<?php echo h($bizEmail); ?>"><?php echo h($bizEmail); ?></a>
        or call <a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a>.</p>

        <p class="legal-note"><i class="fa-solid fa-circle-info"></i> This is a starting template — please review it with a legal professional before launch.</p>
      </div>
    </div>
  </section>
</main>
<?php require 'includes/foot.php'; ?>
</body>
</html>
