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
  const tabBtns = document.querySelectorAll('.booking-tab-btn');
  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      tabBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  // ── Hero "Book Trip" → pre-fills contact form & scrolls ──
  document.getElementById('heroBookBtn')?.addEventListener('click', () => {
    const from = document.getElementById('bookFrom')?.value.trim();
    const to   = document.getElementById('bookTo')?.value.trim();
    const date = document.getElementById('bookDate')?.value;
    const passEl = document.getElementById('bookPassengers');
    const service = passEl?.options[passEl.selectedIndex]?.value || 'classic';

    if (form) {
      const set = (name, val) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (el && val) el.value = val;
      };
      set('pickup', from);
      set('destination', to);
      set('travel_date', date);
      set('package', service);
    }
    document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // ── Booking Popup Modal ──
  const modal    = document.getElementById('bookingModal');
  const modalClose = document.querySelector('.modal-close');
  let popupShown = sessionStorage.getItem('hyt_popup');

  const showPopup = () => {
    if (popupShown || !modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    popupShown = '1';
    sessionStorage.setItem('hyt_popup', '1');
  };

  const hidePopup = () => {
    modal?.classList.remove('active');
    document.body.style.overflow = '';
  };

  // Show after 30% scroll
  const onScroll = () => {
    if (!popupShown) {
      const scrolled = window.scrollY / (document.body.scrollHeight - window.innerHeight);
      if (scrolled > 0.30) showPopup();
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });

  modalClose?.addEventListener('click', hidePopup);
  modal?.addEventListener('click', (e) => { if (e.target === modal) hidePopup(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hidePopup(); });

  // Modal form → WhatsApp
  document.getElementById('modalForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const lines = [
      'Hi Himachal Yatra Travels, I need a free quote.',
      `Name: ${(fd.get('modal_name') || '').toString().trim()}`,
      `Phone: ${(fd.get('modal_phone') || '').toString().trim()}`,
      `From: ${(fd.get('modal_from') || '').toString().trim()}`,
      `Destination: ${(fd.get('modal_to') || '').toString().trim()}`,
      `Date: ${(fd.get('modal_date') || '').toString().trim()}`,
    ].filter((l) => !l.endsWith(': '));
    window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(lines.join('\n'))}`, '_blank', 'noopener');
    hidePopup();
  });

  // ── Horizontal Review Slider ──
  const track    = document.getElementById('reviewTrack');
  const prevBtn  = document.getElementById('reviewPrev');
  const nextBtn  = document.getElementById('reviewNext');
  const dotsRow  = document.getElementById('reviewDots');

  if (track) {
    const cards = [...track.querySelectorAll('.review-card')];
    const cardW = () => cards[0]?.offsetWidth + 20 || 360; // card + gap

    // Build dots
    cards.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
      dot.addEventListener('click', () => scrollTo(i));
      dotsRow?.appendChild(dot);
    });

    const scrollTo = (idx) => {
      track.scrollTo({ left: idx * cardW(), behavior: 'smooth' });
    };

    prevBtn?.addEventListener('click', () => {
      const cur = Math.round(track.scrollLeft / cardW());
      scrollTo(Math.max(0, cur - 1));
    });
    nextBtn?.addEventListener('click', () => {
      const cur = Math.round(track.scrollLeft / cardW());
      scrollTo(Math.min(cards.length - 1, cur + 1));
    });

    // Update dots on scroll
    track.addEventListener('scroll', () => {
      const idx = Math.round(track.scrollLeft / cardW());
      dotsRow?.querySelectorAll('.slider-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
    }, { passive: true });
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

  // ── Write Review Form → WhatsApp ──
  document.getElementById('writeReviewForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const rating = document.getElementById('selectedRating')?.value || '0';
    if (rating === '0') { alert('Please select a star rating.'); return; }
    const name  = document.getElementById('wrName')?.value.trim();
    const city  = document.getElementById('wrCity')?.value.trim();
    const trip  = document.getElementById('wrTrip')?.value.trim();
    const text  = document.getElementById('wrText')?.value.trim();
    const stars = '★'.repeat(Number(rating)) + '☆'.repeat(5 - Number(rating));
    const lines = [
      `New Review from ${name}`,
      `Rating: ${stars} (${rating}/5)`,
      `City: ${city}`,
      `Trip: ${trip}`,
      `Review: ${text}`,
    ];
    window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(lines.join('\n'))}`, '_blank', 'noopener');
    e.target.reset();
    if (ratingInput) ratingInput.value = '0';
    starSelector?.querySelectorAll('i').forEach((s) => { s.className = 'fa-regular fa-star'; s.classList.remove('active'); });
    document.getElementById('photoLabel').textContent = 'Upload Your Photo (optional)';
  });

  // ── Contact form submit → WhatsApp ──
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

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!form.reportValidity()) return;

    const btn = form.querySelector('button[type="submit"]');
    const data = new FormData(form);
    const fallbackUrl = buildWaUrl(data);
    const originalHtml = btn?.innerHTML;

    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...'; }
    if (status) status.textContent = 'Sending your enquiry...';

    let nextUrl = fallbackUrl;
    try {
      const res = await fetch(form.action, { method: 'POST', body: data, headers: { Accept: 'application/json' } });
      const result = await res.json().catch(() => ({}));
      if (res.ok && result.wa) nextUrl = result.wa;
      if (status) status.textContent = res.ok ? 'Enquiry received! Opening WhatsApp...' : (result.error || 'Opening WhatsApp for your enquiry.');
    } catch {
      if (status) status.textContent = 'Opening WhatsApp for your enquiry.';
    }

    window.open(nextUrl, '_blank', 'noopener');
    form.reset();
    if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
  });
});
