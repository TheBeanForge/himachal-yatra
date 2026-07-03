<?php
// $base      = '' on index.php, e.g. 'index.php' on sub-pages
// $activeDest = '' on index.php, e.g. 'manali' on manali.php
$base       = $base ?? '';
$activeDest = $activeDest ?? '';
$home       = $base ?: '#home';
$al         = fn(string $id) => $base ? "index.php#{$id}" : "#{$id}";
?>
<a class="skip-link" href="#home">Skip to content</a>
<nav class="navbar navbar-expand-lg site-nav sticky-top">
  <div class="container mx-auto px-3">

    <a class="navbar-brand logo" href="<?php echo $home; ?>" aria-label="Himachal Safar">
      <span class="logo-icon">
        <img src="<?php echo $base ? '' : ''; ?>assets/logo-icon.svg" alt="" width="34" height="34" aria-hidden="true" style="display:block">
      </span>
      <span class="logo-text">
        <span class="brand-main">Himachal <span>Safar</span></span>
        <span class="brand-sub">Travels</span>
      </span>
    </a>

    <button class="nav-ham" id="navHam" type="button"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto align-items-lg-center">
        <!-- theme toggle injected by JS -->
        <li class="nav-item">
          <a class="nav-link <?php echo !$activeDest && !$base ? 'active' : ''; ?>" href="<?php echo $home; ?>">Home</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('about'); ?>">Experiences</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle js-dropdown-toggle <?php echo $activeDest ? 'active' : ''; ?>"
             href="#" role="button" aria-haspopup="true" aria-expanded="false">
            Destinations <i class="fa-solid fa-chevron-down nav-chevron"></i>
          </a>
          <ul class="dropdown-menu dest-dropdown">
            <li><a class="dropdown-item <?php echo $activeDest==='manali'?'active':''; ?>" href="manali.php"><i class="fa-solid fa-mountain-sun"></i> Manali</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='shimla'?'active':''; ?>" href="shimla.php"><i class="fa-solid fa-tree"></i> Shimla</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='dharamshala'?'active':''; ?>" href="dharamshala.php"><i class="fa-solid fa-om"></i> Dharamshala</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='dalhousie'?'active':''; ?>" href="dalhousie.php"><i class="fa-solid fa-cloud-sun"></i> Dalhousie</a></li>
            <li><a class="dropdown-item <?php echo $activeDest==='spiti'?'active':''; ?>" href="spiti.php"><i class="fa-solid fa-person-hiking"></i> Spiti Valley</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF'])==='packages.php'?'active':''; ?>" href="packages.php">Packages</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('why'); ?>">Why Us</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('fleet'); ?>">Fleet</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('reviews'); ?>">Reviews</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $al('contact'); ?>">Contact</a></li>
      </ul>

      <!-- Theme Toggle -->
      <div class="theme-switcher" id="themeSwitcher" role="group" aria-label="Colour theme">
        <button class="theme-btn" data-theme="dark" title="Dark" aria-label="Dark theme" aria-pressed="false">
          <i class="fa-solid fa-moon" aria-hidden="true"></i>
        </button>
        <button class="theme-btn active" data-theme="light" title="Light" aria-label="Light theme" aria-pressed="true">
          <i class="fa-solid fa-sun" aria-hidden="true"></i>
        </button>
        <button class="theme-btn" data-theme="pine" title="Pine (forest)" aria-label="Pine forest theme" aria-pressed="false">
          <i class="fa-solid fa-tree" aria-hidden="true"></i>
        </button>
        <button class="theme-btn" data-theme="sky" title="Sky (blue &amp; white)" aria-label="Sky blue and white theme" aria-pressed="false">
          <i class="fa-solid fa-cloud" aria-hidden="true"></i>
        </button>
      </div>

    </div>

  </div>
</nav>
