<?php
/**
 * Shared SEO / social head tags. Set $seoTitle, $seoDesc, $seoPath before including.
 * Optional: $seoImage, $seoCrumb, $seoFaq (array of [question, answer]).
 * Domain comes from the SITE_URL env var in production, with a sensible local fallback.
 */
$SITE_URL  = rtrim(getenv('SITE_URL') ?: 'https://himachalsafar.com', '/');
$seoTitle  = $seoTitle ?? 'Himachal Safar | Custom Himachal Trips, Cabs & Travel Planning';
$seoDesc   = $seoDesc  ?? 'Personalised Himachal journeys — tailored itineraries, premium cabs, verified mountain drivers and curated stays across Shimla, Manali, Spiti and beyond.';
$seoPath   = $seoPath  ?? '';
$seoImage  = $seoImage ?? 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&h=630&q=80';
$canonical = $SITE_URL . '/' . ltrim($seoPath, '/');
// $bizEmail comes from vars.php (admin-editable); keep an env/default fallback if not set.
$bizEmail  = $bizEmail ?? (getenv('AGENCY_EMAIL') ?: 'info@himachalsafar.com');
?>
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="geo.region" content="IN-HP">
<meta name="geo.placename" content="Himachal Pradesh, India">
<link rel="canonical" href="<?php echo h($canonical); ?>">
<link rel="icon" href="favicon.ico" sizes="32x32">
<link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
<link rel="icon" href="assets/brand/mark-96.png" type="image/png" sizes="96x96">
<link rel="apple-touch-icon" href="assets/brand/mark-192.png">
<meta name="theme-color" content="#070F1E">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Himachal Safar">
<meta property="og:locale" content="en_IN">
<meta property="og:title" content="<?php echo h($seoTitle); ?>">
<meta property="og:description" content="<?php echo h($seoDesc); ?>">
<meta property="og:url" content="<?php echo h($canonical); ?>">
<meta property="og:image" content="<?php echo h($seoImage); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo h($seoTitle); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo h($seoTitle); ?>">
<meta name="twitter:description" content="<?php echo h($seoDesc); ?>">
<meta name="twitter:image" content="<?php echo h($seoImage); ?>">
<script type="application/ld+json">
<?php
$agency = [
  '@context'   => 'https://schema.org',
  '@type'      => 'TravelAgency',
  '@id'        => $SITE_URL . '/#travelagency',
  'name'       => 'Himachal Safar',
  'url'        => $SITE_URL . '/',
  'logo'       => $SITE_URL . '/assets/logo.svg',
  'image'      => $seoImage,
  'description'=> $seoDesc,
  'telephone'  => $phoneTel ?? '',
  'email'      => $bizEmail,
  'priceRange' => '₹₹',
  'currenciesAccepted' => 'INR',
  'paymentAccepted'    => 'Cash, UPI, Bank Transfer',
  'knowsLanguage'      => ['en', 'hi'],
  // Service-area business (no storefront): the regions/cities we serve.
  'areaServed' => array_map(
    fn($n) => ['@type' => 'City', 'name' => $n],
    ['Manali','Shimla','Dharamshala','Dalhousie','Spiti Valley','Kullu','Kasol','McLeodganj','Khajjiar','Chandigarh','Delhi']
  ),
  'address' => array_filter([
    '@type'           => 'PostalAddress',
    'addressLocality' => trim(explode(',', $agencyLocation ?? '')[0] ?? ''),
    'addressRegion'   => 'Himachal Pradesh',
    'addressCountry'  => 'IN',
  ]),
  'contactPoint' => [
    '@type'           => 'ContactPoint',
    'contactType'     => 'customer service',
    'telephone'       => $phoneTel ?? '',
    'areaServed'      => 'IN',
    'availableLanguage' => ['English', 'Hindi'],
  ],
  // Social profiles are admin-editable in Settings (see vars.php); empty ones are dropped.
  'sameAs' => array_values($socialLinks ?? []),
];
/* AggregateRating and Review markup were emitted here from the reviews table.
   The review feature has been removed, so they are gone too — deliberately.
   Google treats rating markup with no visible reviews on the page as a
   structured-data violation, so leaving it behind would risk a manual action
   rather than just showing nothing. */
echo json_encode($agency, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
</script>
<?php if (!empty($seoFaq)): ?>
<script type="application/ld+json">
<?php echo json_encode([
  '@context'   => 'https://schema.org',
  '@type'      => 'FAQPage',
  'mainEntity' => array_map(fn($f) => [
    '@type'          => 'Question',
    'name'           => $f[0],
    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
  ], $seoFaq),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
</script>
<?php endif; ?>
<?php if ($seoPath !== ''):
  $seoCrumb = $seoCrumb ?? ucwords(str_replace('-', ' ', basename($seoPath, '.php')));
?>
<script type="application/ld+json">
<?php echo json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',    'item' => $SITE_URL . '/'],
    ['@type' => 'ListItem', 'position' => 2, 'name' => $seoCrumb, 'item' => $canonical],
  ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
</script>
<?php endif; ?>
