<?php
/**
 * Shared SEO/social head tags. Set $seoTitle, $seoDesc, $seoPath before including.
 * Domain comes from the SITE_URL env var (set it in production), with a sensible
 * fallback for local dev.
 */
$SITE_URL  = rtrim(getenv('SITE_URL') ?: 'https://himachalyatratravels.com', '/');
$seoTitle  = $seoTitle ?? 'Himachal Yatra Travels | Private Himachal Cab & Tour Packages';
$seoDesc   = $seoDesc  ?? 'Private Himachal journeys — premium cabs, verified mountain drivers, Shimla-Manali packages and Spiti expeditions.';
$seoPath   = $seoPath  ?? '';
$seoImage  = $seoImage ?? 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&h=630&q=80';
$canonical = $SITE_URL . '/' . ltrim($seoPath, '/');
?>
<link rel="canonical" href="<?php echo h($canonical); ?>">
<link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
<meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#f5f5f0" media="(prefers-color-scheme: light)">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Himachal Yatra Travels">
<meta property="og:title" content="<?php echo h($seoTitle); ?>">
<meta property="og:description" content="<?php echo h($seoDesc); ?>">
<meta property="og:url" content="<?php echo h($canonical); ?>">
<meta property="og:image" content="<?php echo h($seoImage); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo h($seoTitle); ?>">
<meta name="twitter:description" content="<?php echo h($seoDesc); ?>">
<meta name="twitter:image" content="<?php echo h($seoImage); ?>">
<script type="application/ld+json">
<?php echo json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'TravelAgency',
  'name'     => 'Himachal Yatra Travels',
  'url'      => $SITE_URL . '/',
  'logo'     => $SITE_URL . '/assets/logo.svg',
  'image'    => $seoImage,
  'telephone'=> $phoneTel ?? '',
  'email'    => 'info@himachalyatratravels.com',
  'priceRange' => '₹₹',
  'areaServed' => 'Himachal Pradesh, India',
  'address'  => [
    '@type' => 'PostalAddress',
    'addressRegion'  => 'Himachal Pradesh',
    'addressCountry' => 'IN',
  ],
  // Configure real profiles via env vars; empty ones are dropped.
  'sameAs' => array_values(array_filter([
    getenv('SOCIAL_FACEBOOK')  ?: '',
    getenv('SOCIAL_INSTAGRAM') ?: '',
    getenv('SOCIAL_YOUTUBE')   ?: '',
  ])),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php if ($seoPath !== ''):
  $seoCrumb = $seoCrumb ?? ucwords(str_replace('-', ' ', basename($seoPath, '.php')));
?>
<script type="application/ld+json">
<?php echo json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',      'item' => $SITE_URL . '/'],
    ['@type' => 'ListItem', 'position' => 2, 'name' => $seoCrumb,   'item' => $canonical],
  ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php endif; ?>
