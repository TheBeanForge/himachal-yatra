// ── Theme switcher (runs before DOMContentLoaded to avoid flash) ──
// Only two themes are supported: 'dark' and 'light'. Anything else
// (e.g. a legacy 'blue' value) falls back to 'dark'.
const ALLOWED_THEMES = ['dark', 'light', 'pine'];
(function () {
  let saved = localStorage.getItem('site-theme');
  if (!ALLOWED_THEMES.includes(saved)) { saved = 'dark'; localStorage.setItem('site-theme', 'dark'); }
  document.documentElement.setAttribute('data-theme', saved);
})();

document.addEventListener('DOMContentLoaded', () => {

  // ── Theme switcher ──
  let currentTheme = localStorage.getItem('site-theme');
  if (!ALLOWED_THEMES.includes(currentTheme)) currentTheme = 'dark';
  const syncThemeButtons = (active) => {
    document.querySelectorAll('.theme-btn').forEach(b => {
      const on = b.dataset.theme === active;
      b.classList.toggle('active', on);
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
  };
  syncThemeButtons(currentTheme);
  document.querySelectorAll('.theme-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const theme = btn.dataset.theme;
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('site-theme', theme);
      syncThemeButtons(theme);
    });
  });

  // ── Bright-background header contrast ──
  // Flip the header to dark text + a frosted light panel whenever a bright
  // background image is active behind it. Drive it however you like:
  //   • call window.setHeaderTheme(true / false) from your bg-switch handler, or
  //   • add `data-bright-toggle` to any button to flip it on click.
  function setHeaderTheme(bright) {
    document.body.classList.toggle('bright-bg', !!bright);
  }
  window.setHeaderTheme = setHeaderTheme;
  document.querySelectorAll('[data-bright-toggle]').forEach(el => {
    el.addEventListener('click', () =>
      setHeaderTheme(!document.body.classList.contains('bright-bg')));
  });

  // ── Navbar scroll shadow ──
  const nav = document.querySelector('.site-nav');
  window.addEventListener('scroll', () => {
    nav?.classList.toggle('scrolled', window.scrollY > 60);
  }, { passive: true });

  // ── Hamburger toggle (vanilla — works even if Bootstrap's JS fails to load) ──
  const navHam  = document.getElementById('navHam');
  const mainNav = document.getElementById('mainNav');
  const setMenu = (open) => {
    if (!navHam || !mainNav) return;
    mainNav.classList.toggle('show', open);
    navHam.classList.toggle('open', open);
    navHam.setAttribute('aria-expanded', open ? 'true' : 'false');
  };
  if (navHam && mainNav) {
    navHam.addEventListener('click', () => setMenu(!mainNav.classList.contains('show')));
    // Tap outside the open menu closes it
    document.addEventListener('click', (e) => {
      if (mainNav.classList.contains('show') &&
          !mainNav.contains(e.target) && !navHam.contains(e.target)) {
        setMenu(false);
      }
    });
  }

  // ── Dropdown toggles (vanilla — no Bootstrap JS dependency) ──
  document.querySelectorAll('.js-dropdown-toggle').forEach((toggle) => {
    const parent = toggle.closest('.dropdown');
    const menu   = parent && parent.querySelector('.dropdown-menu');
    if (!parent || !menu) return;
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      const open = !parent.classList.contains('show');
      parent.classList.toggle('show', open);
      menu.classList.toggle('show', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
  // Click outside closes any open dropdown
  document.addEventListener('click', (e) => {
    document.querySelectorAll('.dropdown.show').forEach((d) => {
      if (d.contains(e.target)) return;
      d.classList.remove('show');
      d.querySelector('.dropdown-menu')?.classList.remove('show');
      d.querySelector('.js-dropdown-toggle')?.setAttribute('aria-expanded', 'false');
    });
  });

  // ── Smooth scroll for anchor links ──
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (e) => {
      const selector = link.getAttribute('href');
      if (!selector || selector === '#') return;
      const target = document.querySelector(selector);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (mainNav && mainNav.classList.contains('show')) setMenu(false);
    });
  });

  // ── Active nav link on scroll ──
  const sections = [...document.querySelectorAll('main section[id]')];
  const navLinks = [...document.querySelectorAll('.nav-link[href^="#"]')];
  const activeObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      navLinks.forEach((link) => {
        link.classList.toggle('active', link.getAttribute('href') === `#${entry.target.id}`);
      });
    });
  }, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });
  sections.forEach((s) => activeObserver.observe(s));

  // ── Reveal on scroll ──
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('visible');
      revealObserver.unobserve(entry.target);
    });
  }, { threshold: 0.10 });
  document.querySelectorAll('.reveal').forEach((el) => revealObserver.observe(el));

  // ── Animated counters ──
  const animateCounter = (counter) => {
    const target = Number(counter.dataset.count || 0);
    const start = performance.now();
    const tick = (now) => {
      const progress = Math.min((now - start) / 1300, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      counter.textContent = `${Math.floor(target * eased).toLocaleString('en-IN')}+`;
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      animateCounter(entry.target);
      counterObserver.unobserve(entry.target);
    });
  }, { threshold: 0.55 });
  document.querySelectorAll('[data-count]').forEach((el) => counterObserver.observe(el));

  // Quote calculator modal is handled by includes/calc_modal.php (window.openCalcModal)

  // Hero "Plan My Journey" button opens the quote calculator modal
  document.getElementById('openBookingModal')?.addEventListener('click', () => {
    if (typeof window.openCalcModal === 'function') window.openCalcModal('');
  });

  // ── Horizontal Review Slider ──
  const track    = document.getElementById('reviewTrack');
  const prevBtn  = document.getElementById('reviewPrev');
  const nextBtn  = document.getElementById('reviewNext');
  const dotsRow  = document.getElementById('reviewDots');

  if (track) {
    const cards = [...track.querySelectorAll('.rv-card')];
    const cardW = () => (cards[0]?.offsetWidth ?? 360) + 20; // card + gap

    // Build dots
    cards.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', `Review ${i + 1}`);
      dot.addEventListener('click', () => { scrollTo(i); stopAuto(); });
      dotsRow?.appendChild(dot);
    });

    const scrollTo = (idx) => {
      track.scrollTo({ left: idx * cardW(), behavior: 'smooth' });
    };

    prevBtn?.addEventListener('click', () => {
      const cur = Math.round(track.scrollLeft / cardW());
      scrollTo(Math.max(0, cur - 1));
      stopAuto();
    });
    nextBtn?.addEventListener('click', () => {
      const cur = Math.round(track.scrollLeft / cardW());
      scrollTo(Math.min(cards.length - 1, cur + 1));
      stopAuto();
    });

    // Touch swipe support
    let touchStartX = 0;
    track.addEventListener('touchstart', (e) => {
      touchStartX = e.touches[0].clientX;
      stopAuto();
    }, { passive: true });
    track.addEventListener('touchend', (e) => {
      const delta = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(delta) > 50) {
        const cur = Math.round(track.scrollLeft / cardW());
        scrollTo(delta > 0
          ? Math.min(cards.length - 1, cur + 1)
          : Math.max(0, cur - 1));
      }
      setTimeout(startAuto, 4000);
    }, { passive: true });

    // Update dots on scroll
    track.addEventListener('scroll', () => {
      const idx = Math.round(track.scrollLeft / cardW());
      dotsRow?.querySelectorAll('.slider-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
    }, { passive: true });

    // Auto-slide every 5.5 s, pause on hover / touch
    let autoTimer;
    const startAuto = () => {
      autoTimer = setInterval(() => {
        const cur = Math.round(track.scrollLeft / cardW());
        scrollTo(cur >= cards.length - 1 ? 0 : cur + 1);
      }, 5500);
    };
    const stopAuto = () => clearInterval(autoTimer);
    startAuto();
    track.addEventListener('mouseenter', stopAuto);
    track.addEventListener('mouseleave', startAuto);
  }

  // ── Star Rating Input ──
  const starSelector = document.getElementById('starSelector');
  const ratingInput  = document.getElementById('selectedRating');
  if (starSelector) {
    const starIcons = [...starSelector.querySelectorAll('i')];
    starIcons.forEach((star) => {
      star.addEventListener('mouseover', () => {
        const val = Number(star.dataset.val);
        starIcons.forEach((s, i) => {
          s.className = i < val ? 'fa-solid fa-star' : 'fa-regular fa-star';
        });
      });
      star.addEventListener('mouseout', () => {
        const cur = Number(ratingInput?.value || 0);
        starIcons.forEach((s, i) => {
          s.className = i < cur ? 'fa-solid fa-star' : 'fa-regular fa-star';
        });
      });
      star.addEventListener('click', () => {
        const val = Number(star.dataset.val);
        if (ratingInput) ratingInput.value = val;
        starIcons.forEach((s, i) => s.classList.toggle('active', i < val));
      });
    });
  }

  // ── Photo Upload Label ──
  document.getElementById('wrPhoto')?.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    const label = document.getElementById('photoLabel');
    if (label) label.textContent = file ? file.name : 'Upload Your Photo (optional)';
  });

  // ── Write Review Form → API → admin moderation queue ──
  const wrForm = document.getElementById('writeReviewForm');
  wrForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const rating = Number(document.getElementById('selectedRating')?.value || 0);
    const status = document.getElementById('wrStatus');
    const submit = wrForm.querySelector('.wr-submit');

    if (rating < 1) {
      if (status) { status.textContent = 'Please select a star rating.'; status.style.color = '#fca5a5'; }
      return;
    }

    const fd = new FormData(wrForm);
    fd.append('rating', String(rating));
    // Token shared with the lead form (vars.php)
    if (!fd.get('csrf_token')) fd.set('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');

    if (submit) { submit.disabled = true; submit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting…'; }
    if (status) { status.textContent = ''; }

    try {
      const res  = await fetch('api/submit_review.php', { method: 'POST', body: fd, headers: { Accept: 'application/json' } });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data.success) {
        wrForm.reset();
        if (ratingInput) ratingInput.value = '0';
        starSelector?.querySelectorAll('i').forEach((s) => { s.className = 'fa-regular fa-star'; s.classList.remove('active'); });
        const lbl = document.getElementById('photoLabel');
        if (lbl) lbl.textContent = 'Upload Photo (optional)';
        if (status) { status.style.color = '#86efac'; status.textContent = 'Thanks! Your review is in the moderation queue and will appear soon.'; }
      } else {
        if (status) { status.style.color = '#fca5a5'; status.textContent = data.error || 'Could not submit. Please try again.'; }
      }
    } catch {
      if (status) { status.style.color = '#fca5a5'; status.textContent = 'Network error. Please try again.'; }
    } finally {
      if (submit) { submit.disabled = false; submit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Review'; }
    }
  });

});
