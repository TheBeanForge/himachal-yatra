document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const form = document.querySelector('#inquiryForm');
  const status = document.querySelector('#formStatus');
  const waNumber = (body.dataset.wa || '919876543210').replace(/\D/g, '');

  // ── Navbar scroll shadow ──
  const nav = document.querySelector('.site-nav');
  window.addEventListener('scroll', () => {
    nav?.classList.toggle('scrolled', window.scrollY > 60);
  }, { passive: true });

  // ── Custom hamburger animation ──
  const navHam  = document.getElementById('navHam');
  const mainNav = document.getElementById('mainNav');
  if (navHam && mainNav) {
    mainNav.addEventListener('show.bs.collapse', () => navHam.classList.add('open'));
    mainNav.addEventListener('hide.bs.collapse', () => navHam.classList.remove('open'));
  }

  // ── Smooth scroll for anchor links ──
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (e) => {
      const selector = link.getAttribute('href');
      if (!selector || selector === '#') return;
      const target = document.querySelector(selector);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      const openMenu = document.querySelector('.navbar-collapse.show');
      if (openMenu && window.bootstrap) {
        window.bootstrap.Collapse.getOrCreateInstance(openMenu).hide();
      }
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

  // ── Booking widget tabs ──
  // Track which service the hero tabs have selected; passed into the modal on open.
  const tabBtns = document.querySelectorAll('.booking-tab-btn');
  let heroService = document.querySelector('.booking-tab-btn.active')?.dataset.tab || 'classic';
  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      tabBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      heroService = btn.dataset.tab || 'classic';
    });
  });

  // heroBookBtn (booking widget quote button) is handled inside the modal block above

  // ── Booking Popup Modal ──
  const modal = document.getElementById('bookingModal');

  const showPopup = () => {
    if (!modal) return;
    modal.classList.add('active');
    const scrollY = window.scrollY;
    document.body.style.overflow = 'hidden';
    document.body.style.top = `-${scrollY}px`;
    document.body.dataset.scrollY = scrollY;
    // focus first input for accessibility
    setTimeout(() => modal.querySelector('.mf-input')?.focus({ preventScroll: true }), 350);
  };
  const hidePopup = () => {
    modal?.classList.remove('active');
    const scrollY = parseInt(document.body.dataset.scrollY || '0');
    document.body.style.overflow = '';
    document.body.style.top = '';
    window.scrollTo({ top: scrollY, behavior: 'instant' });
    // reset form and success state after animation finishes
    setTimeout(() => {
      const mForm = document.getElementById('modalForm');
      const mSuccess = document.getElementById('modalSuccess');
      const mTabs = modal?.querySelector('.modal-service-tabs');
      const mNote = modal?.querySelector('.modal-note');
      if (mForm)    { mForm.reset(); mForm.hidden = false; }
      if (mSuccess) mSuccess.hidden = true;
      if (mTabs)    mTabs.hidden = false;
      if (mNote)    mNote.style.opacity = '';
      document.getElementById('modalError')?.setAttribute('hidden', '');
      const submitBtn = document.getElementById('modalSubmitBtn');
      if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Quote'; }
    }, 350);
  };

  // Open triggers
  document.getElementById('openBookingModal')?.addEventListener('click', showPopup);
  // Sync the modal's service tabs + hidden package input to the chosen service.
  const setModalService = (service) => {
    const input = document.getElementById('modalPackageInput');
    if (input) input.value = service;
    document.querySelectorAll('.mst-btn').forEach((b) => {
      b.classList.toggle('active', b.dataset.service === service);
    });
  };

  document.getElementById('heroBookBtn')?.addEventListener('click', () => {
    // pre-fill from booking widget — assign to .value so it overwrites any prior input
    const from = document.getElementById('bookFrom')?.value.trim() || '';
    const to   = document.getElementById('bookTo')?.value.trim() || '';
    const date = document.getElementById('bookDate')?.value || '';
    const pickupEl = modal?.querySelector('[name="pickup"]');
    const destEl   = modal?.querySelector('[name="destination"]');
    const dateEl   = modal?.querySelector('[name="travel_date"]');
    if (pickupEl) pickupEl.value = from;
    if (destEl)   destEl.value   = to;
    if (dateEl)   dateEl.value   = date;
    setModalService(heroService);
    showPopup();
  });

  // Close triggers
  document.getElementById('modalCloseBtn')?.addEventListener('click', hidePopup);
  document.getElementById('modalSuccessClose')?.addEventListener('click', hidePopup);
  modal?.addEventListener('click', (e) => { if (e.target === modal) hidePopup(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hidePopup(); });

  // Service tabs
  document.querySelectorAll('.mst-btn').forEach((btn) => {
    btn.addEventListener('click', () => setModalService(btn.dataset.service));
  });

  // Build WA message from modal form data
  const buildModalWaUrl = (fd) => {
    const get = (k) => (fd.get(k) || '').toString().trim();
    const lines = [
      'Hi Himachal Yatra Travels, I would like a private Himachal trip quote.',
      `Name: ${get('name')}`,
      `Phone: ${get('phone')}`,
      `Pickup: ${get('pickup')}`,
      `Destination: ${get('destination')}`,
      `Date: ${get('travel_date')}`,
      `Passengers: ${get('pax')}`,
    ].filter((l) => !l.endsWith(': '));
    return `https://wa.me/${waNumber}?text=${encodeURIComponent(lines.join('\n'))}`;
  };

  // WhatsApp direct button
  document.getElementById('modalWaBtn')?.addEventListener('click', () => {
    const fd = new FormData(document.getElementById('modalForm'));
    window.open(buildModalWaUrl(fd), '_blank', 'noopener');
  });

  // Modal form submit → POST to API → show success
  document.getElementById('modalForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = document.getElementById('modalSubmitBtn');
    const errorBox  = document.getElementById('modalError');
    const fd = new FormData(e.target);
    const name = (fd.get('name') || '').toString().trim();
    const waUrl = buildModalWaUrl(fd);

    errorBox.hidden = true;
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…'; }

    try {
      const res    = await fetch('api/submit.php', { method: 'POST', body: fd, headers: { Accept: 'application/json' } });
      const result = await res.json().catch(() => ({}));

      if (res.ok && result.success) {
        // show success state
        const nameEl = document.getElementById('modalSuccessName');
        const waLink = document.getElementById('modalSuccessWa');
        if (nameEl) nameEl.textContent = name || 'there';
        if (waLink) waLink.href = waUrl;
        e.target.hidden = true;
        modal.querySelector('.modal-service-tabs').hidden = true;
        document.getElementById('modalSuccess').hidden = false;
        // hide note
        modal.querySelector('.modal-note').style.opacity = '0';
      } else {
        errorBox.textContent = result.error || 'Something went wrong. Please try again.';
        errorBox.hidden = false;
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Quote'; }
      }
    } catch {
      errorBox.textContent = 'Network error. Please use the WhatsApp button instead.';
      errorBox.hidden = false;
      if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Quote'; }
    }
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

    if (submit) { submit.disabled = true; submit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…'; }
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

  // ── Contact form: two-action buttons ──
  const getValue = (data, key) => String(data.get(key) || '').trim();
  const buildWaUrl = (data) => {
    const lines = [
      'Hi Himachal Yatra Travels, I need a travel quote.',
      `Name: ${getValue(data, 'name')}`,
      `Phone: ${getValue(data, 'phone')}`,
      `Pickup: ${getValue(data, 'pickup')}`,
      `Destination: ${getValue(data, 'destination')}`,
      `Journey Date: ${getValue(data, 'travel_date')}`,
      `Service: ${getValue(data, 'package')}`,
      `Message: ${getValue(data, 'message')}`,
    ].filter((line) => !line.endsWith(': '));
    return `https://wa.me/${waNumber}?text=${encodeURIComponent(lines.join('\n'))}`;
  };

  // WhatsApp concierge button: open WA immediately with current form data, no API call
  document.getElementById('waDirectBtn')?.addEventListener('click', () => {
    window.open(buildWaUrl(new FormData(form)), '_blank', 'noopener');
  });

  // Request quote: POST to API, save lead to DB, show thank-you state
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!form.reportValidity()) return;

    const submitBtn = form.querySelector('.btn-enquiry-submit');
    const data      = new FormData(form);
    const name      = getValue(data, 'name');
    const waUrl     = buildWaUrl(data);

    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…'; }
    if (status)    { status.textContent = ''; }

    try {
      const res    = await fetch(form.action, { method: 'POST', body: data, headers: { Accept: 'application/json' } });
      const result = await res.json().catch(() => ({}));

      if (res.ok && result.success) {
        // Populate and show success state
        const fsName  = document.getElementById('fsName');
        const fsWaBtn = document.getElementById('fsWaBtn');
        if (fsName)  fsName.textContent = name || 'there';
        if (fsWaBtn) fsWaBtn.href = waUrl;

        form.hidden = true;
        document.getElementById('formHeading')?.style.setProperty('display', 'none');
        document.getElementById('formSuccess').hidden = false;
      } else {
        if (status)    status.textContent = result.error || 'Something went wrong. Please try again.';
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Private Quote'; }
      }
    } catch {
      if (status)    status.textContent = 'Network error. Please use the WhatsApp button instead.';
      if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Private Quote'; }
    }
  });

  // New enquiry button inside success state: reset everything
  document.getElementById('fsResetBtn')?.addEventListener('click', () => {
    form.reset();
    form.hidden = false;
    document.getElementById('formHeading')?.style.removeProperty('display');
    document.getElementById('formSuccess').hidden = true;
    if (status) status.textContent = '';
    const submitBtn = form.querySelector('.btn-enquiry-submit');
    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Private Quote'; }
  });
});
