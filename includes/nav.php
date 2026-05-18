<?php
// $base     = ''            on index.php,  e.g. 'index.php' on sub-pages
// $activeDest = ''          on index.php,  e.g. 'manali' on manali.php
$base      = $base ?? '';
$activeDest = $activeDest ?? '';
$home      = $base ?: '#home';
$al        = fn(string $id) => $base ? "index.php#{$id}" : "#{$id}";
?>
<div class="top-strip">
  <div class="container mx-auto px-3">
    <div class="top-left">
      <span><i class="fa-solid fa-headset"></i> 24/7 Support</span>
      <span><i class="fa-solid fa-shield-halved"></i> 100% Safe &amp; Verified</span>
      <span><i class="fa-solid fa-indian-rupee-sign"></i> Best Price Guarantee</span>
    </div>
    <div class="top-social">
      <span>Follow:</span>
      <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
      <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg site-nav sticky-top">
  <div class="container mx-auto px-3">
    <a class="navbar-brand logo" href="<?php echo $home; ?>" aria-label="Himachal Yatra Travels">
      <span class="logo-icon"><i class="fa-solid fa-mountain-sun"></i></span>
      <span class="logo-text">
        <span class="brand-main">HIMACHAL <span>YATRA</span></span>
        <span class="brand-sub">TRAVELS</span>
      </span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item"><a class="nav-link <?php echo !$activeDest && !$base ? 'active' : ''; ?>" href="<?php echo $home; ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('about'); ?>">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('routes'); ?>">Routes</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('packages'); ?>">Packages</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('fleet'); ?>">Fleet</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo $activeDest ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Destinations
          </a>
          <ul class="dropdown-menu dest-dropdown">
            <li><a class="dropdown-item <?php echo $activeDest==='manali'?'active':''; ?>" href="manali.php"><i class="fa-solid fa-mountain-sun"></i> Manali</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='shimla'?'active':''; ?>" href="shimla.php"><i class="fa-solid fa-tree"></i> Shimla</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='dharamshala'?'active':''; ?>" href="dharamshala.php"><i class="fa-solid fa-om"></i> Dharamshala</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='dalhousie'?'active':''; ?>" href="dalhousie.php"><i class="fa-solid fa-cloud-sun"></i> Dalhousie</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='spiti'?'active':''; ?>" href="spiti.php"><i class="fa-solid fa-person-hiking"></i> Spiti Valley</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('reviews'); ?>">Reviews</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('contact'); ?>">Contact</a></li>
        <li class="nav-item ms-lg-2">
          <a class="call-pill" href="tel:<?php echo h($phoneTel); ?>">
            <i class="fa-solid fa-phone"></i> <?php echo h($phoneDisplay); ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
