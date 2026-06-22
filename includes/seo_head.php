<?php
/**
 * Shared SEO / social head tags. Set $seoTitle, $seoDesc, $seoPath before including.
 * Optional: $seoImage, $seoCrumb, $seoFaq (array of [question, answer]),
 *           $seoRatingValue + $seoRatingCount (real review aggregate).
 * Domain comes from the SITE_URL env var in production, with a sensible local fallback.
 */
$SITE_URL  = rtrim(getenv('SITE_URL') ?: 'https://himachalsafar.com', '/');
$seoTitle  = $seoTitle ?? 'Himachal Safar | Private Himachal Cab & Tour Packages';
$seoDesc   = $seoDesc  ?? 'Private Himachal journeys — premium cabs, verified mountain drivers, Shimla–Manali tour packages and Spiti Valley expeditions across Himachal Pradesh.';
$seoPath   = $seoPath  ?? '';
$seoImage  = $seoImage ?? 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&h=630&q=80';
$canonical = $SITE_URL . '/' . ltrim($seoPath, '/');
$bizEmail  = getenv('AGENCY_EMAIL') ?: 'info@himachalsafar.com';
?>
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="geo.region" content="IN-HP">
<meta name="geo.placename" content="Himachal Pradesh, India">
<link rel="canonical" href="<?php echo h($canonical); ?>">
<link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
<meta name="theme-color" content="#0B0B0D" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#f5f5f0" media="(prefers-color-scheme: light)">
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
  'address' => ['@type' => 'PostalAddress', 'addressRegion' => 'Himachal Pradesh', 'addressCountry' => 'IN'],
  'contactPoint' => [
    '@type'           => 'ContactPoint',
    'contactType'     => 'customer service',
    'telephone'       => $phoneTel ?? '',
    'areaServed'      => 'IN',
    'availableLanguage' => ['English', 'Hindi'],
  ],
  // Add real profiles via SOCIAL_* env vars; empty ones are dropped.
  'sameAs' => array_values(array_filter([
    getenv('SOCIAL_FACEBOOK')  ?: '',
    getenv('SOCIAL_INSTAGRAM') ?: '',
    getenv('SOCIAL_YOUTUBE')   ?: '',
  ])),
];
// Only emit an aggregate rating when it reflects real reviews shown on the page.
if (!empty($seoRatingValue) && !empty($seoRatingCount)) {
  $agency['aggregateRating'] = [
    '@type'       => 'AggregateRating',
    'ratingValue' => (string) $seoRatingValue,
    'reviewCount' => (string) $seoRatingCount,
    'bestRating'  => '5',
    'worstRating' => '1',
  ];
}
// Individual Review markup — only real approved reviews shown on the page.
if (!empty($seoReviews)) {
  $agency['review'] = array_map(fn($r) => [
    '@type'        => 'Review',
    'author'       => ['@type' => 'Person', 'name' => $r['author']],
    'reviewRating' => [
      '@type'       => 'Rating',
      'ratingValue' => (string) $r['rating'],
      'bestRating'  => '5',
      'worstRating' => '1',
    ],
    'reviewBody'   => $r['text'],
  ], $seoReviews);
}
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
