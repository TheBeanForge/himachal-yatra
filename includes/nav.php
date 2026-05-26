<?php
// $base      = '' on index.php, e.g. 'index.php' on sub-pages
// $activeDest = '' on index.php, e.g. 'manali' on manali.php
$base       = $base ?? '';
$activeDest = $activeDest ?? '';
$home       = $base ?: '#home';
$al         = fn(string $id) => $base ? "index.php#{$id}" : "#{$id}";
?>
<div class="top-strip">
  <div class="container mx-auto px-3 ts-inner">
    <div class="ts-trust">
      <span><i class="fa-solid fa-shield-halved"></i> Verified Drivers</span>
      <span class="ts-dot" aria-hidden="true"></span>
      <span><i class="fa-solid fa-headset"></i> 24/7 Support</span>
    </div>
    <div class="ts-contact">
      <a href="tel:<?php echo h($phoneTel); ?>" class="ts-phone">
        <i class="fa-solid fa-phone"></i> <?php echo h($phoneDisplay); ?>
      </a>
      <a href="https://wa.me/<?php echo h($whatsappNumber); ?>?text=<?php echo $defaultMessage; ?>"
         class="ts-wa" target="_blank" rel="noopener" aria-label="WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
      </a>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg site-nav sticky-top">
  <div class="container mx-auto px-3">

    <a class="navbar-brand logo" href="<?php echo $home; ?>" aria-label="Himachal Yatra Travels">
      <span class="logo-icon">
        <img src="<?php echo $base ? '' : ''; ?>assets/logo-icon.svg" alt="" width="34" height="34" aria-hidden="true" style="display:block">
      </span>
      <span class="logo-text">
        <span class="brand-main">Himachal <span>Yatra</span></span>
        <span class="brand-sub">Travels</span>
      </span>
    </a>

    <button class="nav-ham" id="navHam" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link <?php echo !$activeDest && !$base ? 'active' : ''; ?>" href="<?php echo $home; ?>">Home</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('about'); ?>">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('routes'); ?>">Routes</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('packages'); ?>">Packages</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('fleet'); ?>">Fleet</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo $activeDest ? 'active' : ''; ?>"
             href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Destinations <i class="fa-solid fa-chevron-down nav-chevron"></i>
          </a>
          <ul class="dropdown-menu dest-dropdown">
            <li><a class="dropdown-item <?php echo $activeDest==='manali'?'active':''; ?>" href="<?php echo $base; ?>manali.php"><i class="fa-solid fa-mountain-sun"></i> Manali</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='shimla'?'active':''; ?>" href="<?php echo $base; ?>shimla.php"><i class="fa-solid fa-tree"></i> Shimla</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='dharamshala'?'active':''; ?>" href="<?php echo $base; ?>dharamshala.php"><i class="fa-solid fa-om"></i> Dharamshala</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='dalhousie'?'active':''; ?>" href="<?php echo $base; ?>dalhousie.php"><i class="fa-solid fa-cloud-sun"></i> Dalhousie</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='spiti'?'active':''; ?>" href="<?php echo $base; ?>spiti.php"><i class="fa-solid fa-person-hiking"></i> Spiti Valley</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('reviews'); ?>">Reviews</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('contact'); ?>">Contact</a></li>
      </ul>
    </div>

  </div>
</nav>
