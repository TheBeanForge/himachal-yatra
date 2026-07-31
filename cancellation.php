<?php
$base       = 'index.php';
$activeDest = '';
require_once 'includes/vars.php';
?><!doctype html>
<html lang="en" data-season="<?php echo h(current_season()); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cancellation &amp; Refund Policy | Himachal Safar</title>
  <meta name="description" content="Himachal Safar's cancellation and refund policy — refund tiers by notice period, free rescheduling, and how weather or road closures are handled.">
  <?php
    $seoTitle = 'Cancellation & Refund Policy | Himachal Safar';
    $seoDesc  = 'Refund tiers by notice period, free rescheduling, and how weather or road closures are handled at Himachal Safar.';
    $seoPath  = 'cancellation.php';
    $seoCrumb = 'Cancellation & Refund Policy';
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
        <h1>Cancellation &amp; Refund Policy</h1>
        <p class="legal-updated">Last updated: <?php echo date('F Y'); ?></p>

        <?php $advPct = max(5, min(100, (int)app_setting('advance_percent', '25'))); ?>
        <p>Plans change — especially in the mountains. This policy explains exactly what happens
        if you cancel or reschedule a confirmed booking, so there are no surprises. It applies to
        the <strong>advance amount</strong> (currently <?= $advPct ?>% of the estimated total) paid
        at confirmation; the balance is only ever collected for trips that run.</p>

        <h2>Refunds by notice period</h2>
        <p>Notice is counted from the moment you inform us to your scheduled pickup time.</p>
        <div class="legal-table-wrap">
          <table class="legal-table">
            <thead>
              <tr><th>Notice before pickup</th><th>Refund of advance</th></tr>
            </thead>
            <tbody>
              <tr><td>20 days or more</td><td><strong>100%</strong> — full refund</td></tr>
              <tr><td>10 – 19 days</td><td><strong>75%</strong></td></tr>
              <tr><td>5 – 9 days</td><td><strong>50%</strong></td></tr>
              <tr><td>Less than 5 days / no-show</td><td>No refund</td></tr>
            </tbody>
          </table>
        </div>

        <h2>Free rescheduling</h2>
        <p>Rather move your dates than cancel? One <strong>free reschedule</strong> is included with
        every booking when requested at least <strong>72 hours</strong> before pickup, subject to
        vehicle availability on the new dates. Any fare difference for the new dates (for example,
        peak-season rates) applies. Further reschedules are treated as a cancellation and rebooking.</p>

        <h2>If weather or roads stop the trip</h2>
        <p>Himalayan travel depends on conditions. If <em>we</em> cancel your trip — because of
        heavy snowfall, landslides, official road closures, or a vehicle problem we cannot resolve —
        you choose between a <strong>100% refund of the advance</strong> or a
        <strong>free reschedule</strong> to any available date. This applies regardless of notice period.</p>

        <h2>Trips shortened mid-journey</h2>
        <p>If a trip already underway must be cut short due to conditions beyond anyone's control,
        unused full days of the vehicle hire are refunded on a pro-rata basis. Days already travelled,
        and third-party costs already incurred on your behalf (hotels, permits), are not refundable
        by us — though we will help you pursue any refunds those providers offer.</p>

        <h2>How to cancel or reschedule</h2>
        <p>Message us on <a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" rel="noopener">WhatsApp</a>,
        call <a href="tel:<?php echo h($phoneTel); ?>"><?php echo h($phoneDisplay); ?></a>, or email
        <a href="mailto:<?php echo h($bizEmail); ?>"><?php echo h($bizEmail); ?></a> with your booking
        name and pickup date. We confirm every cancellation in writing — your notice period is locked
        from the moment your message reaches us, not from our reply.</p>

        <h2>Refund timeline</h2>
        <p>Approved refunds are issued to the original payment method within
        <strong>5–7 business days</strong> of confirmation. UPI and bank-transfer refunds usually
        arrive faster.</p>

        <h2>Related policies</h2>
        <p>See our <a href="terms.php">Terms &amp; Conditions</a> for booking and payment terms, and our
        <a href="privacy.php">Privacy Policy</a> for how your information is handled.</p>

        <p class="legal-note"><i class="fa-solid fa-circle-info"></i> This is a starting template — please review it with a legal professional before relying on it.</p>
      </div>
    </div>
  </section>
</main>
<?php require 'includes/foot.php'; ?>
</body>
</html>
