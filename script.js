// ── Theme switcher (runs before DOMContentLoaded to avoid flash) ──
// Themes: 'light' (default — luxury paper editorial), 'dark', 'pine', 'sky'.
// Anything else (e.g. a legacy value) falls back to the default.
const ALLOWED_THEMES = ['dark', 'light', 'pine', 'sky'];
const DEFAULT_THEME  = 'dark';
(function () {
  let saved = localStorage.getItem('site-theme');
  if (!ALLOWED_THEMES.includes(saved)) { saved = DEFAULT_THEME; localStorage.setItem('site-theme', DEFAULT_THEME); }
  document.documentElement.setAttribute('data-theme', saved);
})();

document.addEventListener('DOMContentLoaded', () => {

  // ── Theme dropdown ──
  const THEME_META = {
    light: { icon: 'fa-sun',   label: 'Ivory White' },
    dark:  { icon: 'fa-moon',  label: 'Midnight Blue' },
    pine:  { icon: 'fa-tree',  label: 'Pine Green' },
    sky:   { icon: 'fa-cloud', label: 'Ocean Blue' },
  };
  let currentTheme = localStorage.getItem('site-theme');
  if (!ALLOWED_THEMES.includes(currentTheme)) currentTheme = DEFAULT_THEME;

  const themeDd = document.querySelector('.theme-dd');
  const themeDdBtn = document.getElementById('themeDdBtn');
  const syncThemeDd = (active) => {
    const meta = THEME_META[active] || THEME_META[DEFAULT_THEME];
    const ic = themeDd?.querySelector('.theme-dd-ic');
    const tx = themeDd?.querySelector('.theme-dd-txt');
    if (ic) ic.className = `fa-solid ${meta.icon} theme-dd-ic`;
    if (tx) tx.textContent = meta.label;
    themeDd?.querySelectorAll('.theme-opt').forEach((o) => {
      o.classList.toggle('active', o.dataset.setTheme === active);
      o.setAttribute('aria-selected', o.dataset.setTheme === active ? 'true' : 'false');
    });
  };
  const setThemeDdOpen = (open) => {
    themeDd?.classList.toggle('open', open);
    themeDdBtn?.setAttribute('aria-expanded', open ? 'true' : 'false');
  };
  syncThemeDd(currentTheme);

  themeDdBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    setThemeDdOpen(!themeDd.classList.contains('open'));
  });
  themeDd?.querySelectorAll('.theme-opt').forEach((opt) => {
    opt.addEventListener('click', () => {
      const theme = opt.dataset.setTheme;
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('site-theme', theme);
      syncThemeDd(theme);
      setThemeDdOpen(false);
    });
  });
  document.addEventListener('click', (e) => {
    if (themeDd?.classList.contains('open') && !themeDd.contains(e.target)) setThemeDdOpen(false);
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && themeDd?.classList.contains('open')) { setThemeDdOpen(false); themeDdBtn?.focus(); }
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

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ── Back to top ──
  const backTop = document.getElementById('backTop');
  if (backTop) {
    window.addEventListener('scroll', () => {
      backTop.classList.toggle('show', window.scrollY > 640);
    }, { passive: true });
    backTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }

  // ── Stagger grid reveals ──
  // Cards inside a grid rise one after another instead of all at once.
  document.querySelectorAll(
    '.lux-dest-grid, .lux-routes-grid, .lux-why-grid, .lux-fleet-grid, .lux-steps,' +
    '.lux-quotes-grid, .lux-stats-grid, .highlights-grid, .season-grid, .dest-route-cards, .pkg-grid'
  ).forEach((grid) => {
    [...grid.children].forEach((el, i) => {
      if (el.className.includes('reveal')) el.style.transitionDelay = `${Math.min(i * 90, 450)}ms`;
    });
  });

  // ── Hero parallax (scroll) ──
  const heroBg = document.querySelector('.lux-hero .hero-bg, .dest-hero-bg');
  if (heroBg && !reduceMotion) {
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => {
        const y = window.scrollY;
        if (y < window.innerHeight * 1.2) heroBg.style.setProperty('translate', `0 ${y * 0.18}px`);
        ticking = false;
      });
    }, { passive: true });
  }

  // ── Card tilt (pointer devices only, subtle) ──
  if (!reduceMotion && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    document.querySelectorAll('.lux-card, .pkg-card, .lux-fleet-card').forEach((card) => {
      let raf = 0;
      card.addEventListener('pointermove', (e) => {
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          const r = card.getBoundingClientRect();
          const rx = ((e.clientY - r.top) / r.height - 0.5) * -3.5;
          const ry = ((e.clientX - r.left) / r.width - 0.5) * 3.5;
          card.style.transform = `translateY(-8px) perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg)`;
        });
      });
      card.addEventListener('pointerleave', () => {
        cancelAnimationFrame(raf);
        card.style.transform = '';
      });
    });
  }

  // ── Newsletter (footer, site-wide) ──
  const nlForm = document.getElementById('nlForm');
  nlForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const status = document.getElementById('nlStatus');
    const btn = document.getElementById('nlBtn');
    const email = document.getElementById('nlEmail')?.value.trim() || '';
    const say = (msg, cls) => { if (status) { status.textContent = msg; status.className = `footer-news-status ${cls}`; } };

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { say('Please enter a valid email address.', 'err'); return; }
    if (btn) btn.disabled = true;
    try {
      const res = await fetch('api/subscribe.php', { method: 'POST', body: new FormData(nlForm), headers: { Accept: 'application/json' } });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data.ok) {
        nlForm.reset();
        say('You’re on the list — see you in the mountains.', 'ok');
      } else {
        say(data.error || 'Could not subscribe. Please try again.', 'err');
      }
    } catch {
      say('Network error. Please try again.', 'err');
    } finally {
      if (btn) btn.disabled = false;
    }
  });

  // ── Testimonials carousel (homepage) ──
  const lqTrack = document.getElementById('lqTrack');
  if (lqTrack) {
    const lqCards = [...lqTrack.querySelectorAll('.lux-quote')];
    const lqDots = document.getElementById('lqDots');
    const step = () => (lqCards[0]?.offsetWidth ?? 320) + 28; // card + gap
    const maxLeft = () => lqTrack.scrollWidth - lqTrack.clientWidth - 4;

    lqCards.forEach((_, i) => {
      const d = document.createElement('button');
      d.type = 'button';
      d.className = 'lq-dot' + (i === 0 ? ' active' : '');
      d.addEventListener('click', () => { stopLqAuto(); lqTrack.scrollTo({ left: i * step() }); });
      lqDots?.appendChild(d);
    });

    const syncLq = () => {
      const idx = Math.min(lqCards.length - 1, Math.round(lqTrack.scrollLeft / step()));
      lqDots?.querySelectorAll('.lq-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
      const prev = document.getElementById('lqPrev');
      const next = document.getElementById('lqNext');
      if (prev) prev.toggleAttribute('disabled', lqTrack.scrollLeft <= 2);
      if (next) next.toggleAttribute('disabled', lqTrack.scrollLeft >= maxLeft());
    };
    lqTrack.addEventListener('scroll', syncLq, { passive: true });
    syncLq();

    document.getElementById('lqPrev')?.addEventListener('click', () => { stopLqAuto(); lqTrack.scrollBy({ left: -step() }); });
    document.getElementById('lqNext')?.addEventListener('click', () => { stopLqAuto(); lqTrack.scrollBy({ left: step() }); });

    // Gentle auto-advance; any interaction hands control back to the reader.
    let lqTimer = null;
    const startLqAuto = () => {
      if (reduceMotion || lqCards.length < 2) return;
      lqTimer = setInterval(() => {
        lqTrack.scrollTo({ left: lqTrack.scrollLeft >= maxLeft() ? 0 : lqTrack.scrollLeft + step() });
      }, 6500);
    };
    const stopLqAuto = () => { clearInterval(lqTimer); lqTimer = null; };
    startLqAuto();
    ['mouseenter', 'pointerdown', 'focusin'].forEach((ev) => lqTrack.addEventListener(ev, stopLqAuto));
    lqTrack.addEventListener('mouseleave', () => { if (!lqTimer) startLqAuto(); });
  }

  // ── Gallery lightbox (destination pages) ──
  const galleryImgs = [...document.querySelectorAll('.gallery-item img')];
  if (galleryImgs.length) {
    const overlay = document.createElement('div');
    overlay.className = 'lb-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-label', 'Photo viewer');
    overlay.innerHTML =
      '<button class="lb-close" aria-label="Close photo viewer"><i class="fa-solid fa-xmark"></i></button>' +
      '<button class="lb-prev" aria-label="Previous photo"><i class="fa-solid fa-chevron-left"></i></button>' +
      '<img alt="">' +
      '<button class="lb-next" aria-label="Next photo"><i class="fa-solid fa-chevron-right"></i></button>' +
      '<span class="lb-count"></span>';
    document.body.appendChild(overlay);
    const lbImg = overlay.querySelector('img');
    const lbCount = overlay.querySelector('.lb-count');
    let idx = 0;

    const showAt = (i) => {
      idx = (i + galleryImgs.length) % galleryImgs.length;
      lbImg.src = galleryImgs[idx].src;
      lbImg.alt = galleryImgs[idx].alt || '';
      lbCount.textContent = `${idx + 1} / ${galleryImgs.length}`;
    };
    const openLb = (i) => { showAt(i); overlay.classList.add('open'); document.body.style.overflow = 'hidden'; };
    const closeLb = () => { overlay.classList.remove('open'); document.body.style.overflow = ''; };

    galleryImgs.forEach((img, i) => img.addEventListener('click', () => openLb(i)));
    overlay.querySelector('.lb-close').addEventListener('click', closeLb);
    overlay.querySelector('.lb-prev').addEventListener('click', () => showAt(idx - 1));
    overlay.querySelector('.lb-next').addEventListener('click', () => showAt(idx + 1));
    overlay.addEventListener('click', (e) => { if (e.target === overlay) closeLb(); });
    document.addEventListener('keydown', (e) => {
      if (!overlay.classList.contains('open')) return;
      if (e.key === 'Escape') closeLb();
      if (e.key === 'ArrowLeft') showAt(idx - 1);
      if (e.key === 'ArrowRight') showAt(idx + 1);
    });
  }

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
