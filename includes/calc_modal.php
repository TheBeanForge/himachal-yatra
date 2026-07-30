<?php $__csrf = $_SESSION['lead_form_token'] ?? ''; ?>

<!-- ═══ QUOTE POPUP MODAL ═══
     The booking form lives here once. Every "Get Quote / Plan Trip" trigger
     (hero CTA, route cards, contact button, floating button, ?calc= deep-link)
     opens it via window.openCalcModal(destKey). -->
<div class="cq-modal" id="cqModal" aria-hidden="true">
  <div class="cq-modal-backdrop" data-cq-close></div>
  <div class="cq-modal-dialog" role="dialog" aria-modal="true" aria-label="Instant quote booking form">
    <button type="button" class="cq-modal-x" data-cq-close aria-label="Close quote form"><i class="fa-solid fa-xmark"></i></button>

    <!-- Brand rail (desktop) — sets the scene, states the promise -->
    <aside class="cq-rail" aria-hidden="true">
      <div class="cq-rail-scrim"></div>
      <div class="cq-rail-body">
        <img class="cq-rail-logo" src="assets/logo-icon.svg" width="44" height="44" alt="">
        <p class="cq-rail-ey">Private Himachal Concierge</p>
        <h3 class="cq-rail-title">One form.<br>Your whole journey, planned.</h3>
        <ul class="cq-rail-list">
          <li><i class="fa-solid fa-bolt"></i> Instant estimate — no waiting</li>
          <li><i class="fa-brands fa-whatsapp"></i> Travel desk replies in ~2 hours</li>
          <li><i class="fa-solid fa-lock"></i> No advance to get a quote</li>
          <li><i class="fa-solid fa-shield-halved"></i> Verified mountain drivers</li>
        </ul>
        <div class="cq-rail-trust">
          <span><strong>5,000+</strong> journeys</span>
          <span class="cq-rail-dot"></span>
          <span><strong>4.9★</strong> guest rating</span>
        </div>
      </div>
    </aside>

    <div class="cq-inline" id="cqInline">
      <div class="cq-card" id="cqCard">

    <!-- Journey progress: Details → Estimate → Confirmed -->
    <div class="cq-progress" id="cqProgress" data-step="1" aria-hidden="true">
      <div class="cq-prog-step" data-s="1"><span class="cq-prog-num">1</span><span class="cq-prog-lbl">Trip details</span></div>
      <div class="cq-prog-line"></div>
      <div class="cq-prog-step" data-s="2"><span class="cq-prog-num">2</span><span class="cq-prog-lbl">Estimate</span></div>
      <div class="cq-prog-line"></div>
      <div class="cq-prog-step" data-s="3"><span class="cq-prog-num">3</span><span class="cq-prog-lbl">Confirmed</span></div>
    </div>

    <!-- Header -->
    <div class="cq-header">
      <div>
        <h2 class="cq-title" id="cqTitle">Plan Your Himachal Journey</h2>
        <p class="cq-subtitle" id="cqSubtitle">Tell us a little about your trip — we&rsquo;ll tailor an instant estimate. Free, no obligation.</p>
      </div>
    </div>

    <!-- ═══ STATE 1: FORM ═══ -->
    <div id="cqStateForm">

      <form id="cqForm" novalidate autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= h($__csrf) ?>">
        <input type="hidden" name="package_id" id="cqPkgId" value="">
        <input type="text" name="website" tabindex="-1" style="display:none" autocomplete="off">

        <!-- TRIP DETAILS -->
        <div class="cq-section">
          <div class="cq-section-label"><i class="fa-solid fa-map-location-dot"></i> Trip Details</div>

          <div class="cq-field cq-msel-field" id="cqF_dest">
            <label class="cq-label">Where would you like to explore in Himachal? <span class="cq-req">*</span></label>
            <div class="cq-msel" id="cqMsel_dest">
              <div class="cq-chips" id="cqDestChips"></div>
              <div class="cq-msel-control">
                <i class="fa-solid fa-magnifying-glass cq-msel-ic"></i>
                <input type="text" id="cqDestSearch" class="cq-msel-input" autocomplete="off"
                       placeholder="Search destinations, villages, treks, lakes or enter your own place">
              </div>
              <div class="cq-sel-drop" id="cqDestDrop">
                <ul class="cq-sel-ul" id="cqUl_dest"></ul>
                <div class="cq-msel-empty" id="cqDestEmpty" hidden>
                  <span>Can&rsquo;t find your destination?</span>
                  <button type="button" class="cq-add-custom" id="cqAddCustom"><i class="fa-solid fa-plus"></i> Add Custom Destination</button>
                </div>
              </div>
            </div>
            <input type="hidden" name="trip_destination" id="cqHid_dest">
            <input type="hidden" name="custom_destinations" id="cqHid_dest_custom">
            <span class="cq-err">Please add at least one destination</span>
          </div>

          <div class="cq-field" id="cqF_loc">
            <label class="cq-label">Pickup City <span class="cq-req">*</span></label>
            <div class="cq-sel" id="cqSel_loc" data-key="loc" data-req="1" data-custom="1">
              <button type="button" class="cq-sel-btn">
                <span class="cq-sel-txt">Select or type your city…</span>
                <i class="fa-solid fa-chevron-down cq-sel-arr"></i>
              </button>
              <div class="cq-sel-drop">
                <div class="cq-sel-sr"><i class="fa-solid fa-magnifying-glass"></i><input class="cq-sel-si" type="search" placeholder="Search, or type any city…" autocomplete="off"></div>
                <ul class="cq-sel-ul" id="cqUl_loc"></ul>
              </div>
            </div>
            <input type="hidden" name="pickup_location_id" id="cqHid_loc">
            <input type="hidden" name="pickup_custom" id="cqHid_loc_custom">
            <span class="cq-err">Please select or type a pickup city</span>
          </div>

          <div class="cq-field" id="cqF_veh">
            <label class="cq-label">Vehicle / Cab Type <span class="cq-req">*</span></label>
            <div class="cq-sel" id="cqSel_veh" data-key="veh" data-req="1">
              <button type="button" class="cq-sel-btn">
                <span class="cq-sel-txt">Choose your vehicle…</span>
                <i class="fa-solid fa-chevron-down cq-sel-arr"></i>
              </button>
              <div class="cq-sel-drop">
                <div class="cq-sel-sr"><i class="fa-solid fa-magnifying-glass"></i><input class="cq-sel-si" type="search" placeholder="Search vehicles…" autocomplete="off"></div>
                <ul class="cq-sel-ul" id="cqUl_veh"></ul>
              </div>
            </div>
            <input type="hidden" name="vehicle_id" id="cqHid_veh">
            <span class="cq-err">Please select a vehicle</span>
            <span class="cq-cap-msg" id="cqVehCap" role="alert" hidden></span>
          </div>

          <div class="cq-row cq-row-3">
            <div class="cq-field" id="cqF_pd">
              <label class="cq-label" for="cqPickup">Pickup Date <span class="cq-req">*</span></label>
              <input type="date" id="cqPickup" name="pickup_date" class="cq-input">
              <span class="cq-err">Select a future pickup date</span>
            </div>
            <div class="cq-field" id="cqF_dd">
              <label class="cq-label" for="cqDrop">Drop Date <span class="cq-req">*</span></label>
              <input type="date" id="cqDrop" name="drop_date" class="cq-input">
              <span class="cq-err">Must be after pickup date</span>
              <span class="cq-pkg-hint" id="cqPkgHint" hidden></span>
            </div>
            <div class="cq-field">
              <label class="cq-label">Travelers</label>
              <div class="cq-stepper">
                <button type="button" class="cq-step-btn" data-op="-"><i class="fa-solid fa-minus"></i></button>
                <span class="cq-step-num" id="cqTravDisp">2</span>
                <input type="hidden" name="travelers" id="cqTravHid" value="2">
                <button type="button" class="cq-step-btn" data-op="+"><i class="fa-solid fa-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- YOUR DETAILS -->
        <div class="cq-section">
          <div class="cq-section-label"><i class="fa-solid fa-user"></i> Your Details</div>
          <div class="cq-row">
            <div class="cq-field" id="cqF_name">
              <label class="cq-label" for="cqName">Full Name <span class="cq-req">*</span></label>
              <input type="text" id="cqName" name="customer_name" class="cq-input" placeholder="Your full name" maxlength="100" autocomplete="name">
              <span class="cq-err">Name must be at least 3 characters</span>
            </div>
            <div class="cq-field" id="cqF_mob">
              <label class="cq-label" for="cqPhone">Phone Number <span class="cq-req">*</span></label>
              <div class="cq-tel">
                <span class="cq-tel-pre"><img src="https://flagcdn.com/w20/in.png" width="18" height="13" alt="IN" loading="lazy"> +91</span>
                <input type="tel" id="cqPhone" name="mobile" class="cq-input cq-tel-inp" placeholder="98765 43210" maxlength="15" inputmode="numeric" autocomplete="tel">
              </div>
              <span class="cq-err">Enter a valid 10-digit mobile number</span>
            </div>
          </div>
        </div>

        <!-- LIVE PRICE BREAKDOWN -->
        <div class="cq-section cq-price-section">
          <div class="cq-section-label"><i class="fa-solid fa-receipt"></i> Price Estimate</div>
          <div class="cq-bd-card">
            <div class="cq-bd-placeholder" id="cqBdPlaceholder">
              <i class="fa-solid fa-calculator"></i>
              Select your vehicle &amp; dates to see your estimate
            </div>
            <div id="cqBdRows" hidden></div>
            <div class="cq-bd-total" id="cqBdTotal" hidden>
              <div>
                <div class="cq-bd-total-label">Total Estimated Price</div>
                <div class="cq-bd-total-meta" id="cqBdMeta"></div>
              </div>
              <div class="cq-bd-total-amt" id="cqBdAmt">₹0</div>
            </div>
          </div>
        </div>

        <!-- ACTIONS -->
        <div class="cq-section cq-actions">
          <div class="cq-global-err" id="cqGlobalErr" hidden>
            <i class="fa-solid fa-circle-exclamation"></i><span id="cqGlobalErrMsg"></span>
          </div>
          <button type="button" class="cq-btn-primary" id="cqEstBtn">
            <span class="cq-btn-label"><i class="fa-solid fa-tags"></i> Get Estimate</span>
            <span class="cq-btn-spin" hidden><i class="fa-solid fa-spinner fa-spin"></i> Calculating…</span>
          </button>
          <p class="cq-footnote"><i class="fa-solid fa-lock"></i> Your details are secure and never shared.</p>
        </div>

      </form>
    </div><!-- /cqStateForm -->

    <!-- ═══ STATE 2: RESULT ═══ -->
    <div id="cqStateResult" hidden>
      <div class="cq-section">
        <div class="cq-result-head">
          <div class="cq-result-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div>
            <div class="cq-result-title">Your Price Estimate</div>
            <div class="cq-result-sub">Review your quote before submitting.</div>
          </div>
          <button type="button" class="cq-back-btn" id="cqBackBtn">
            <i class="fa-solid fa-arrow-left"></i> Edit
          </button>
        </div>

        <div class="cq-res-pills" id="cqResPills"></div>

        <div class="cq-bd-card">
          <div id="cqResRows"></div>
          <div class="cq-bd-total">
            <div>
              <div class="cq-bd-total-label">Total Estimated Price</div>
              <div class="cq-bd-total-meta" id="cqResMeta"></div>
            </div>
            <div class="cq-bd-total-amt" id="cqResAmt">₹0</div>
          </div>
        </div>

        <div class="cq-global-err cq-mt" id="cqSubmitErr" hidden>
          <i class="fa-solid fa-circle-exclamation"></i><span id="cqSubmitErrMsg"></span>
        </div>

        <button type="button" class="cq-btn-primary cq-mt" id="cqSubmitBtn">
          <span class="cq-btn-label"><i class="fa-solid fa-paper-plane"></i> Submit Enquiry</span>
          <span class="cq-btn-spin" hidden><i class="fa-solid fa-spinner fa-spin"></i> Submitting…</span>
        </button>

        <p class="cq-disclaimer">
          <i class="fa-solid fa-circle-info"></i>
          Indicative estimate. Final price confirmed after reviewing your route &amp; requirements.
          Includes accommodation &amp; transport. Excludes meals, entry tickets &amp; personal expenses.
        </p>
        <p class="cq-disclaimer" style="margin-top:.4rem">
          <i class="fa-solid fa-rotate-left"></i>
          Flexible booking — full refund with 20+ days' notice and one free reschedule up to 72 hours
          before pickup. <a href="cancellation.php" target="_blank" rel="noopener" style="color:var(--lime)">Cancellation policy</a>
        </p>
      </div>
    </div><!-- /cqStateResult -->

    <!-- ═══ STATE 3: SUCCESS ═══ -->
    <div id="cqStateSuccess" hidden>
      <div class="cq-section cq-success">
        <div class="cq-success-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h3 class="cq-success-title">Enquiry Submitted!</h3>
        <p class="cq-success-msg">Your enquiry has been submitted successfully. Our team will contact you within 24 hours.</p>
        <div class="cq-success-summary" id="cqSuccessSummary"></div>

        <!-- Hot-lead handoff: send the enquiry straight into a WhatsApp chat -->
        <a class="cq-btn-wa cq-mt" id="cqWaBtn" href="#" target="_blank" rel="noopener">
          <i class="fa-brands fa-whatsapp"></i> Continue on WhatsApp
        </a>
        <a class="cq-btn-outline cq-mt-sm" id="cqCallBtn" href="tel:<?= h($phoneTel) ?>">
          <i class="fa-solid fa-phone"></i> Call now
        </a>
        <p class="cq-footnote cq-mt-sm"><i class="fa-solid fa-clock"></i> Our travel desk usually replies within ~2 hours (9am–9pm).</p>

        <button type="button" class="cq-btn-text cq-mt-sm" id="cqNewBtn">
          <i class="fa-solid fa-rotate-left"></i> Calculate New Estimate
        </button>
      </div>
    </div><!-- /cqStateSuccess -->

      </div><!-- /cqCard -->
    </div><!-- /cqInline -->
  </div><!-- /cqModal-dialog -->
</div><!-- /cqModal -->

<!-- Floating "Get Quote" button — lets users open the quote popup from anywhere -->
<button type="button" class="cq-fab" id="cqFab" aria-label="Get an instant quote">
  <i class="fa-solid fa-calculator"></i><span>Get Quote</span>
</button>

<!-- ═══ STYLES ═══ -->
<style>
/* ── Quote popup modal ── */
.cq-modal {
  position: fixed; inset: 0; z-index: 1000;
  display: none; align-items: flex-start; justify-content: center;
  padding: 4vh 1rem;
}
.cq-modal.open { display: flex; }
.cq-modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(8,10,14,.72); backdrop-filter: blur(5px);
  animation: cqFade .25s ease;
}
.cq-modal-dialog {
  position: relative; z-index: 1; margin: auto;
  width: 100%; max-width: 960px; max-height: 92vh;
  display: flex; flex-direction: row; align-items: stretch;
  background: var(--surf-1); border: var(--glass-brd, 1px solid var(--line));
  border-radius: 24px; overflow: hidden;
  box-shadow: 0 40px 110px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.12);
  animation: cqPop .32s cubic-bezier(.22,1,.36,1);
}
[data-theme="light"] .cq-modal-dialog { background: #fdfdfb; }

/* ── Brand rail (left panel, desktop) ── */
.cq-rail {
  position: relative; flex: 0 0 320px; overflow: hidden;
  background:
    linear-gradient(200deg, rgba(216,179,106,.16), transparent 45%),
    /* 900w WebP, not the 2.7 MB PNG — this rail is only 320px wide. */
    url('assets/photos/hero-winter-900.webp') center/cover no-repeat,
    #0A1426;
  display: flex; align-items: flex-end;
}
.cq-rail-scrim { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7,15,30,.42) 0%, rgba(7,15,30,.86) 78%, rgba(7,15,30,.94) 100%); }
.cq-rail-body { position: relative; z-index: 1; padding: 32px 30px 30px; }
.cq-rail-logo { display: block; margin-bottom: 18px; filter: drop-shadow(0 4px 14px rgba(0,0,0,.4)); }
.cq-rail-ey {
  font: 700 10px var(--font-body); letter-spacing: .3em; text-transform: uppercase;
  color: var(--gold-3, #EED9A6); margin: 0 0 10px;
}
.cq-rail-title {
  font-family: var(--font-display); font-weight: 400; font-size: 1.5rem; line-height: 1.3;
  color: #fff; margin: 0 0 20px;
}
.cq-rail-list { list-style: none; margin: 0 0 22px; padding: 0; display: grid; gap: 11px; }
.cq-rail-list li { display: flex; align-items: center; gap: 11px; font: 500 .84rem var(--font-body); color: rgba(255,255,255,.85); }
.cq-rail-list i {
  width: 28px; height: 28px; flex-shrink: 0; display: grid; place-items: center;
  border-radius: 9px; font-size: .74rem;
  background: rgba(216,179,106,.14); border: 1px solid rgba(216,179,106,.3);
  color: var(--gold-3, #EED9A6);
}
.cq-rail-list li i.fa-whatsapp { background: rgba(37,211,102,.14); border-color: rgba(37,211,102,.32); color: #7DF0AC; }
.cq-rail-trust {
  display: flex; align-items: center; gap: 12px;
  padding-top: 18px; border-top: 1px solid rgba(255,255,255,.14);
  font: 500 .78rem var(--font-body); color: rgba(255,255,255,.62);
}
.cq-rail-trust strong { color: #fff; font-weight: 800; }
.cq-rail-dot { width: 4px; height: 4px; border-radius: 50%; background: var(--gold-3, #EED9A6); }
@media (max-width: 899px) { .cq-rail { display: none; } .cq-modal-dialog { max-width: 640px; } }

/* ── Journey progress (Details → Estimate → Confirmed) ── */
.cq-progress {
  display: flex; align-items: center; gap: 10px;
  padding: 1.15rem 3.6rem .35rem 1.75rem;   /* right inset clears the ✕ button */
}
.cq-prog-step { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.cq-prog-num {
  width: 24px; height: 24px; border-radius: 50%;
  display: grid; place-items: center;
  font: 700 11px var(--font-body);
  color: var(--muted); background: var(--surf-3); border: 1.5px solid var(--line);
  transition: background .35s, color .35s, border-color .35s, box-shadow .35s;
}
.cq-prog-lbl {
  font: 600 10.5px var(--font-body); letter-spacing: .1em; text-transform: uppercase;
  color: var(--muted); transition: color .35s;
}
.cq-prog-line { flex: 1; height: 1.5px; border-radius: 2px; background: var(--line); position: relative; overflow: hidden; }
.cq-prog-line::after {
  content: ""; position: absolute; inset: 0;
  background: var(--grad-gold, var(--lime));
  transform: scaleX(0); transform-origin: left;
  transition: transform .5s cubic-bezier(.22,1,.36,1);
}
/* active + completed states, driven by data-step on the wrapper */
.cq-progress[data-step="1"] .cq-prog-step[data-s="1"] .cq-prog-num,
.cq-progress[data-step="2"] .cq-prog-step[data-s="2"] .cq-prog-num,
.cq-progress[data-step="3"] .cq-prog-step[data-s="3"] .cq-prog-num {
  background: var(--grad-gold, var(--lime)); color: #10151d; border-color: transparent;
  box-shadow: 0 0 0 4px color-mix(in srgb, var(--lime) 18%, transparent);
}
.cq-progress[data-step="1"] .cq-prog-step[data-s="1"] .cq-prog-lbl,
.cq-progress[data-step="2"] .cq-prog-step[data-s="2"] .cq-prog-lbl,
.cq-progress[data-step="3"] .cq-prog-step[data-s="3"] .cq-prog-lbl { color: var(--charcoal); }
.cq-progress[data-step="2"] .cq-prog-step[data-s="1"] .cq-prog-num,
.cq-progress[data-step="3"] .cq-prog-step[data-s="1"] .cq-prog-num,
.cq-progress[data-step="3"] .cq-prog-step[data-s="2"] .cq-prog-num {
  background: var(--gold-light); color: var(--lime); border-color: color-mix(in srgb, var(--lime) 45%, transparent);
}
.cq-progress[data-step="2"] .cq-prog-line:first-of-type::after,
.cq-progress[data-step="3"] .cq-prog-line::after { transform: scaleX(1); }
@media (max-width: 580px) {
  .cq-progress { padding: 1rem 3.2rem .2rem 1.1rem; }
  .cq-prog-lbl { display: none; }
}
@keyframes cqFade { from { opacity: 0; } to { opacity: 1; } }
@keyframes cqPop  { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: none; } }
.cq-modal-x {
  position: absolute; top: .85rem; right: .85rem; z-index: 5;
  width: 36px; height: 36px; border-radius: 50%;
  border: 1px solid var(--line); background: var(--surf-3);
  color: var(--text-2); cursor: pointer; font-size: 1rem;
  display: flex; align-items: center; justify-content: center;
  transition: background .15s, color .15s, transform .2s;
}
.cq-modal-x:hover { background: var(--lime); color: #000; transform: rotate(90deg); }

/* Inline card — fills the dialog; the dialog supplies the panel surface. */
.cq-inline { flex: 1; min-width: 0; overflow-y: auto; }
.cq-card {
  width: 100%; padding: 0; display: flex; flex-direction: column;
  background: transparent;
}

/* ── Floating "Get Quote" button ── */
.cq-fab {
  position: fixed; right: 24px; bottom: 100px; z-index: 99;
  display: inline-flex; align-items: center; gap: .5rem;
  height: 54px; padding: 0 1.5rem; border: none; border-radius: 999px;
  background: var(--grad-gold, linear-gradient(135deg, var(--lime) 0%, var(--lime-2) 100%));
  color: #10151d; font: 700 .78rem var(--font-body);
  letter-spacing: .12em; text-transform: uppercase;
  cursor: pointer;
  box-shadow: 0 12px 32px rgba(200,167,93,.42), inset 0 1px 0 rgba(255,255,255,.35);
  transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s;
}
.cq-fab:hover { transform: translateY(-3px); box-shadow: 0 18px 44px rgba(200,167,93,.52); }
.cq-fab i { font-size: 1rem; }
/* The FAB shares the bottom-right rail with .back-top (style.css), which sits
   at bottom:96px — inside the FAB's 100–154px band, and beneath it (z-index
   90 vs 99), leaving back-to-top unclickable. Lift it clear of the FAB. This
   rule lives here so it only applies on pages that actually render the FAB. */
@media (min-width: 768px) {
  .back-top { bottom: 166px; }
}

@media (max-width: 767px) {
  /* On mobile the WhatsApp float is hidden and the 3-up bottom CTA bar
     (Call · WhatsApp · Get Quote) takes over — so hide the FAB entirely. */
  .cq-fab { display: none; }
}

/* Header */
.cq-header {
  display: flex; align-items: center; gap: 1rem;
  padding: .8rem 1.75rem 1.1rem; border-bottom: 1px solid var(--line);
  flex-shrink: 0;
}
.cq-header-icon {
  width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
  background: var(--gold-light); border: 1px solid rgba(200,167,93,.2);
  display: flex; align-items: center; justify-content: center;
}
.cq-title { font-family: var(--font-display); font-size: 1.35rem; font-weight: 400; letter-spacing: .01em; color: var(--charcoal); margin: 0 0 .15rem; }
.cq-subtitle { font-size: .79rem; color: var(--muted); margin: 0; }

/* Sections */
.cq-section { padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--line); }
.cq-section:last-child { border-bottom: none; }
.cq-section-label {
  font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em;
  color: var(--lime); display: flex; align-items: center; gap: .3rem; margin-bottom: .9rem;
}
.cq-price-section, .cq-actions { background: var(--surf-1); }
[data-theme="light"] .cq-price-section, [data-theme="light"] .cq-actions { background: #f0f0ea; }

/* Grid */
.cq-row { display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
.cq-row-3 { grid-template-columns: 1fr 1fr 1fr; margin-top: .85rem; }

/* Fields */
.cq-field { display: flex; flex-direction: column; gap: .3rem; }
.cq-label { font-size: .74rem; font-weight: 600; color: var(--text-2); }
.cq-req { color: var(--lime); }
.cq-err { font-size: .7rem; color: #f87171; display: none; }
.cq-err::before { content: '⚠ '; }
.cq-field.has-err .cq-err { display: block; }
/* Package mode: drop date auto-locked to the package length */
.cq-pkg-hint { font-size: .7rem; color: var(--lime-2, #a0863f); font-weight: 700; }
.cq-pkg-hint[hidden] { display: none; }
.cq-pkg-hint::before { content: '🔒 '; }
.cq-locked .cq-input { background: var(--gold-light, #f6efdd); cursor: not-allowed; opacity: .92; pointer-events: none; }
.cq-field.has-err .cq-input,
.cq-field.has-err .cq-sel-btn { border-color: #f87171 !important; }
.cq-field.has-err .cq-tel { border-color: #f87171 !important; }

/* Inputs */
.cq-input {
  height: 44px; padding: 0 .9rem;
  background: var(--surf-3); border: 1.5px solid var(--line); border-radius: 10px;
  color: var(--charcoal); font-size: .875rem; font-family: var(--font-body);
  width: 100%; box-sizing: border-box; transition: border-color .2s, box-shadow .2s;
}
.cq-input:focus { outline: none; border-color: var(--lime); box-shadow: 0 0 0 3px rgba(200,167,93,.13); }
.cq-input::placeholder { color: var(--muted); }

/* Tel */
.cq-tel {
  display: flex; align-items: stretch;
  border: 1.5px solid var(--line); border-radius: 10px; overflow: hidden;
  background: var(--surf-3); transition: border-color .2s, box-shadow .2s;
}
.cq-tel:focus-within { border-color: var(--lime); box-shadow: 0 0 0 3px rgba(200,167,93,.13); }
.cq-tel-pre {
  display: flex; align-items: center; gap: .35rem;
  padding: 0 .75rem; border-right: 1px solid var(--line);
  font-size: .75rem; font-weight: 600; color: var(--text-2);
  background: var(--surf-4); white-space: nowrap; flex-shrink: 0;
}
.cq-tel-inp {
  flex: 1; height: 44px; border: none !important; border-radius: 0 !important;
  box-shadow: none !important; background: transparent !important; padding-left: .65rem;
}

/* Stepper */
.cq-stepper {
  display: flex; align-items: center; height: 44px;
  border: 1.5px solid var(--line); border-radius: 10px; overflow: hidden;
  background: var(--surf-3);
}
.cq-step-btn {
  width: 40px; height: 100%; background: var(--surf-4); border: none;
  color: var(--text-2); cursor: pointer; font-size: .76rem;
  display: flex; align-items: center; justify-content: center;
  transition: background .15s, color .15s; flex-shrink: 0;
}
.cq-step-btn:hover { background: var(--lime); color: #000; }
.cq-step-num {
  flex: 1; text-align: center; font-weight: 800; font-size: .95rem;
  color: var(--charcoal); font-family: var(--font-body);
}

/* Searchable select */
.cq-sel { position: relative; }
.cq-sel-btn {
  width: 100%; height: 44px; display: flex; align-items: center;
  justify-content: space-between; gap: .4rem; padding: 0 .9rem;
  background: var(--surf-3); border: 1.5px solid var(--line); border-radius: 10px;
  color: var(--muted); cursor: pointer; font-family: var(--font-body); font-size: .875rem;
  text-align: left; overflow: hidden; transition: border-color .2s, box-shadow .2s;
}
.cq-sel-btn.filled { color: var(--charcoal); }
.cq-sel-btn:hover  { border-color: var(--lime-2); }
.cq-sel-btn.open   {
  border-color: var(--lime); border-bottom-left-radius: 0; border-bottom-right-radius: 0;
  box-shadow: 0 0 0 3px rgba(200,167,93,.13);
}
.cq-sel-txt  { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cq-sel-arr  { font-size: .62rem; color: var(--muted); flex-shrink: 0; transition: transform .22s; }
.cq-sel-btn.open .cq-sel-arr { transform: rotate(180deg); }
.cq-sel-drop {
  position: absolute; top: 100%; left: 0; right: 0; z-index: 500;
  background: var(--surf-2); border: 1.5px solid var(--lime); border-top: none;
  border-radius: 0 0 12px 12px; overflow: hidden;
  opacity: 0; transform: translateY(-4px); pointer-events: none;
  transition: opacity .2s, transform .2s; box-shadow: 0 16px 40px rgba(0,0,0,.3);
}
.cq-sel-drop.open { opacity: 1; transform: none; pointer-events: auto; }
.cq-sel-sr {
  position: relative; display: flex; align-items: center;
  border-bottom: 1px solid var(--line);
}
.cq-sel-sr > i { position: absolute; left: .75rem; font-size: .68rem; color: var(--muted); pointer-events: none; }
.cq-sel-si {
  width: 100%; height: 36px; padding: 0 .9rem 0 2.1rem;
  border: none; background: var(--surf-3); color: var(--charcoal);
  font-size: .82rem; font-family: var(--font-body); box-sizing: border-box; outline: none;
}
.cq-sel-si::placeholder { color: var(--muted); }
.cq-sel-ul { list-style: none; margin: 0; padding: .25rem 0; overflow-y: auto; max-height: 200px; }
.cq-sel-ul::-webkit-scrollbar { width: 4px; }
.cq-sel-ul::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }
.cq-sel-li { padding: .52rem .9rem; cursor: pointer; font-size: .85rem; color: var(--text-2); transition: background .1s, color .1s; }
.cq-sel-li:hover, .cq-sel-li.focused { background: var(--gold-light); color: var(--charcoal); }
.cq-sel-li.chosen { color: var(--lime); font-weight: 600; }
.cq-sel-li.chosen::before { content: '✓  '; font-size: .72rem; }
/* Capacity-blocked vehicle options: visibly disabled, not selectable. */
.cq-sel-li.cq-li-disabled { opacity: .4; cursor: not-allowed; }
.cq-sel-li.cq-li-disabled:hover, .cq-sel-li.cq-li-disabled.focused { background: transparent; color: var(--text-2); }
.cq-cap-msg { display: block; margin-top: .4rem; font-size: .78rem; font-weight: 600; color: #d9534f; line-height: 1.45; }
.cq-sel-empty { padding: .75rem .9rem; font-size: .78rem; color: var(--muted); }
/* Free-text "Use '<typed city>'" option for the pickup field */
.cq-sel-customli { color: var(--lime); font-weight: 600; border-top: 1px solid var(--line); }
.cq-sel-customli i { font-size: .72rem; margin-right: .15rem; }
.cq-sel-customli .cq-cust-q { font-style: italic; }

/* ── Multi-select destination picker (chips + searchable dropdown) ── */
.cq-msel { position: relative; }
.cq-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
.cq-chips:not(:empty) { margin-bottom: .5rem; }
.cq-chip {
  display: inline-flex; align-items: center; gap: .4rem;
  padding: .32rem .35rem .32rem .65rem; border-radius: 8px;
  background: var(--gold-light); border: 1px solid rgba(200,167,93,.32);
  color: var(--charcoal); font-size: .8rem; font-weight: 600; line-height: 1;
}
.cq-chip.is-custom { border-style: dashed; }
.cq-chip-x {
  display: inline-flex; align-items: center; justify-content: center;
  width: 18px; height: 18px; border: none; border-radius: 5px; cursor: pointer;
  background: transparent; color: var(--muted); font-size: .72rem;
  transition: background .15s, color .15s;
}
.cq-chip-x:hover { background: var(--lime); color: #000; }
.cq-msel-control {
  display: flex; align-items: center; gap: .55rem;
  height: 44px; padding: 0 .9rem;
  background: var(--surf-3); border: 1.5px solid var(--line); border-radius: 10px;
  transition: border-color .2s, box-shadow .2s;
}
.cq-msel-control:focus-within { border-color: var(--lime); box-shadow: 0 0 0 3px rgba(200,167,93,.13); }
.cq-msel-ic { color: var(--muted); font-size: .8rem; flex-shrink: 0; }
.cq-msel-input {
  flex: 1; min-width: 0; height: 100%; border: none; background: transparent; outline: none;
  color: var(--charcoal); font-size: .875rem; font-family: var(--font-body);
}
.cq-msel-input::placeholder { color: var(--muted); }
.cq-field.has-err .cq-msel-control { border-color: #f87171 !important; }
/* The dropdown reuses .cq-sel-drop visuals but anchors to the multiselect control */
.cq-msel .cq-sel-drop { top: 100%; margin-top: 4px; border-top: 1.5px solid var(--lime); border-radius: 12px; }
.cq-msel-empty {
  display: flex; flex-wrap: wrap; align-items: center; gap: .5rem;
  padding: .7rem .9rem; border-top: 1px solid var(--line);
  font-size: .8rem; color: var(--muted);
}
.cq-add-custom {
  display: inline-flex; align-items: center; gap: .35rem;
  padding: .4rem .7rem; border: 1px dashed var(--lime); border-radius: 8px;
  background: var(--gold-light); color: var(--lime); cursor: pointer;
  font-size: .78rem; font-weight: 700; font-family: var(--font-body);
  transition: background .15s, color .15s;
}
.cq-add-custom:hover { background: var(--lime); color: #000; }

/* Breakdown card */
.cq-bd-card { background: var(--surf-2); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
.cq-bd-placeholder {
  display: flex; align-items: center; justify-content: center; gap: .6rem;
  padding: 1.25rem; font-size: .82rem; color: var(--muted);
}
.cq-bd-placeholder i { color: var(--lime); }
.cq-bd-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: .5rem 1.1rem; font-size: .82rem; color: var(--text-2);
  border-bottom: 1px solid var(--line);
}
.cq-bd-row:last-of-type { border-bottom: none; }
.cq-bd-row-lbl { display: flex; align-items: center; gap: .4rem; }
.cq-bd-row-lbl i { color: var(--muted); font-size: .72rem; width: 14px; text-align: center; }
.cq-bd-row-amt { font-weight: 700; color: var(--charcoal); white-space: nowrap; }
.cq-bd-row.sub-row      { font-weight: 700; color: var(--charcoal); }
.cq-bd-row.tax-row      { color: var(--muted); font-size: .79rem; }
.cq-bd-row.season-row   { color: #f59e0b; }
.cq-bd-total {
  display: flex; align-items: center; justify-content: space-between; gap: 1rem;
  padding: 1rem 1.1rem; background: var(--surf-3); border-top: 2px solid var(--line);
}
.cq-bd-total-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
.cq-bd-total-meta  { font-size: .71rem; color: var(--muted); margin-top: .15rem; }
.cq-bd-total-amt   {
  font-family: var(--font-display); font-size: 1.9rem; font-weight: 400;
  color: var(--lime); letter-spacing: -.025em; white-space: nowrap;
}

/* Actions */
.cq-actions { display: flex; flex-direction: column; gap: .75rem; }
.cq-global-err {
  display: flex; align-items: flex-start; gap: .5rem;
  padding: .65rem .9rem; background: rgba(248,113,113,.08);
  border: 1px solid rgba(248,113,113,.2); border-radius: 10px;
  font-size: .78rem; color: #f87171; line-height: 1.45;
}
.cq-btn-primary {
  width: 100%; height: 54px; border: none; border-radius: 999px;
  background: var(--grad-gold, linear-gradient(135deg, var(--lime) 0%, var(--lime-2) 100%));
  color: #10151d; font: 700 .8rem var(--font-body);
  letter-spacing: .14em; text-transform: uppercase; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: .45rem;
  transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s, opacity .2s;
  box-shadow: 0 10px 30px rgba(200,167,93,.3), inset 0 1px 0 rgba(255,255,255,.35);
}
.cq-btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(200,167,93,.42); }
.cq-btn-primary:disabled { opacity: .65; cursor: not-allowed; transform: none; }
.cq-btn-outline {
  width: 100%; height: 46px; border: 1.5px solid var(--line); border-radius: 11px;
  background: transparent; color: var(--text-2); font-size: .88rem; font-weight: 600;
  font-family: var(--font-body); cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: .45rem;
  transition: border-color .2s, color .2s;
}
.cq-btn-outline:hover { border-color: var(--lime); color: var(--lime); }
/* "Call now" reads phone-green, like a dialer icon */
#cqCallBtn i { color: #22C55E; }
#cqCallBtn:hover { border-color: #22C55E; color: #22C55E; }
/* WhatsApp handoff button (success screen) */
.cq-btn-wa {
  width: 100%; height: 52px; border: none; border-radius: 999px;
  background: linear-gradient(135deg, #2EE577, #1DA851); color: #06231a;
  font: 800 .9rem var(--font-body); cursor: pointer; text-decoration: none;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  box-shadow: 0 10px 28px rgba(37,211,102,.34); transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s;
}
.cq-btn-wa:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,211,102,.45); color: #06231a; }
.cq-btn-wa i { font-size: 1.15rem; }
/* Low-emphasis text button (e.g. "Calculate New Estimate") */
.cq-btn-text {
  width: 100%; height: 40px; border: none; background: transparent;
  color: var(--muted); font-size: .82rem; font-weight: 600; font-family: var(--font-body);
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .4rem;
  transition: color .2s;
}
.cq-btn-text:hover { color: var(--lime); }
.cq-mt-sm { margin-top: .55rem; }
.cq-btn-spin { display: none; align-items: center; gap: .4rem; }
.cq-footnote { font-size: .7rem; color: var(--muted); text-align: center; margin: 0; display: flex; align-items: center; justify-content: center; gap: .3rem; }
.cq-footnote i { color: var(--lime); }

/* Result */
.cq-result-head { display: flex; align-items: flex-start; gap: .9rem; margin-bottom: 1rem; }
.cq-result-icon { font-size: 1.9rem; color: var(--lime); flex-shrink: 0; line-height: 1; }
.cq-result-title { font-family: var(--font-display); font-size: 1.15rem; font-weight: 400; color: var(--charcoal); margin: 0 0 .15rem; }
.cq-result-sub   { font-size: .78rem; color: var(--muted); }
.cq-back-btn {
  margin-left: auto; background: var(--surf-3); border: 1px solid var(--line);
  border-radius: 8px; padding: .38rem .8rem; color: var(--text-2); font-size: .75rem;
  font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: .3rem;
  transition: border-color .2s, color .2s; flex-shrink: 0;
}
.cq-back-btn:hover { border-color: var(--lime); color: var(--lime); }
.cq-res-pills { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: 1rem; }
.cq-pill {
  display: inline-flex; align-items: center; gap: .28rem;
  padding: .22rem .65rem; border-radius: 20px;
  background: var(--gold-light); border: 1px solid rgba(200,167,93,.2);
  font-size: .72rem; font-weight: 600; color: var(--lime);
}
.cq-pill i { font-size: .62rem; }
.cq-disclaimer { font-size: .7rem; color: var(--muted); display: flex; align-items: flex-start; gap: .35rem; line-height: 1.55; margin-top: 1rem; }
.cq-disclaimer i { color: var(--lime); margin-top: .1rem; flex-shrink: 0; }

/* Success */
.cq-success { text-align: center; padding: 2.5rem 1.75rem; }
.cq-success-icon { font-size: 3.5rem; color: var(--lime); margin-bottom: 1rem; line-height: 1; }
.cq-success-title { font-family: var(--font-display); font-size: 1.45rem; font-weight: 400; color: var(--charcoal); margin: 0 0 .6rem; }
.cq-success-msg { font-size: .88rem; color: var(--text-2); margin: 0 0 1.25rem; line-height: 1.6; }
.cq-success-summary {
  background: var(--surf-3); border: 1px solid var(--line); border-radius: 12px;
  padding: 1rem 1.25rem; text-align: left; font-size: .82rem; color: var(--text-2);
  display: flex; flex-direction: column; gap: .4rem; margin-bottom: .5rem;
}
.cq-success-row { display: flex; justify-content: space-between; gap: .5rem; }
.cq-success-row strong { color: var(--charcoal); white-space: nowrap; }
.cq-success-total { font-family: var(--font-display); font-size: 1.5rem; font-weight: 400; color: var(--lime); }
.cq-mt { margin-top: 1rem; }

/* Light */
[data-theme="light"] .cq-input  { background: #f5f5f0; border-color: rgba(0,0,0,.12); color: #1a1a2e; }
[data-theme="light"] .cq-tel    { background: #f5f5f0; border-color: rgba(0,0,0,.12); }
[data-theme="light"] .cq-tel-pre{ background: #eeeee8; color: #3a3a5c; border-color: rgba(0,0,0,.09); }
[data-theme="light"] .cq-stepper{ background: #f5f5f0; border-color: rgba(0,0,0,.12); }
[data-theme="light"] .cq-step-btn{ background: #eeeee8; color: #3a3a5c; }
[data-theme="light"] .cq-sel-btn { background: #f5f5f0; border-color: rgba(0,0,0,.12); }
[data-theme="light"] .cq-sel-btn.filled { color: #1a1a2e; }
[data-theme="light"] .cq-sel-drop{ background: #fff; }
[data-theme="light"] .cq-sel-si  { background: #f5f5f0; color: #1a1a2e; }
[data-theme="light"] .cq-msel-control { background: #f5f5f0; border-color: rgba(0,0,0,.12); }
[data-theme="light"] .cq-chip { color: #1a1a2e; }
[data-theme="light"] .cq-bd-card { background: #fff; }
[data-theme="light"] .cq-bd-total{ background: #f5f5f0; }

/* Responsive */
@media (max-width: 580px) {
  .cq-modal { padding: 10px 8px; }
  .cq-modal-dialog { max-height: calc(100vh - 20px); max-height: calc(100dvh - 20px); border-radius: 20px; }
  .cq-header  { padding: 1.1rem 3.2rem 1rem 1.1rem; }
  .cq-section { padding: 1.1rem; }
  .cq-row, .cq-row-3 { grid-template-columns: 1fr; }
  .cq-row-3  { margin-top: .65rem; }
  .cq-success { padding: 2rem 1.1rem; }
}
</style>

<!-- ═══ SCRIPT ═══ -->
<script>
(function(){
'use strict';

/* ══ safe number — the single fix for all NaN ══
   Handles: null, undefined, "", "1,200", "12.5", numbers, floats */
function sn(v){
  if(v===null||v===undefined||v==='') return 0;
  if(typeof v==='number') return isNaN(v)||!isFinite(v) ? 0 : v;
  const s=String(v).replace(/,/g,'').trim();
  if(s==='') return 0;
  const n=parseFloat(s);
  return isNaN(n)||!isFinite(n) ? 0 : n;
}

function fmtINR(v){ return '₹'+Math.round(sn(v)).toLocaleString('en-IN'); }
function daysDiff(a,b){ return Math.max(0,Math.round((new Date(b)-new Date(a))/86400000)); }

/* ── State ── */
let DATA=null;
let S={dest:[],loc:null,locCustom:'',veh:null,pickup:'',drop:'',travelers:2,name:'',phone:'',packageId:'',fixedDays:0};  // dest = [{name, custom}]
let lastCalc=null; // result of calcPrice(), shared between states

/* ── DOM ── */
const cqCard     = document.getElementById('cqCard');
const cqInline   = document.getElementById('cqInline');
const stateForm  = document.getElementById('cqStateForm');
const stateRes   = document.getElementById('cqStateResult');
const stateSuc   = document.getElementById('cqStateSuccess');
const todayStr   = new Date().toISOString().split('T')[0];

/* ── Package mode ──
   When opened from a Tour Package, the trip length is fixed by the package:
   the visitor just picks a start date, vehicle and guests; the drop date and
   number of days are derived automatically from the package. ── */
const cqPkgIdInp   = document.getElementById('cqPkgId');
const cqSubtitleEl = document.getElementById('cqSubtitle');
const cqPkgHintEl  = document.getElementById('cqPkgHint');
const CQ_SUBTITLE  = cqSubtitleEl ? cqSubtitleEl.textContent : '';

function addDaysISO(iso, n){
  const [y,m,d] = iso.split('-').map(Number);
  const dt = new Date(Date.UTC(y, m-1, d));
  dt.setUTCDate(dt.getUTCDate() + n);
  const mm = String(dt.getUTCMonth()+1).padStart(2,'0'), dd = String(dt.getUTCDate()).padStart(2,'0');
  return dt.getUTCFullYear() + '-' + mm + '-' + dd;
}
function applyFixedDrop(){
  if(S.fixedDays>0 && S.pickup){
    const iso = addDaysISO(S.pickup, S.fixedDays);
    S.drop = iso;
    const de = document.getElementById('cqDrop');
    if(de){ de.value = iso; de.min = iso; }
    document.getElementById('cqF_dd')?.classList.remove('has-err');
  }
}
function setPackageMode(pkg){
  const de = document.getElementById('cqDrop');
  if(pkg && pkg.days>0){
    S.packageId = String(pkg.id||''); S.fixedDays = pkg.days;
    if(cqPkgIdInp) cqPkgIdInp.value = S.packageId;
    if(de) de.readOnly = true;
    document.getElementById('cqF_dd')?.classList.add('cq-locked');
    if(cqPkgHintEl){ cqPkgHintEl.hidden=false; cqPkgHintEl.textContent = `Auto-set from your ${pkg.days}-day package`; }
    if(cqSubtitleEl) cqSubtitleEl.textContent = `${pkg.name ? pkg.name + ' — ' : ''}choose your start date, vehicle and guests for an instant estimate.`;
    applyFixedDrop();
  } else {
    S.packageId=''; S.fixedDays=0;
    if(cqPkgIdInp) cqPkgIdInp.value='';
    if(de) de.readOnly=false;
    document.getElementById('cqF_dd')?.classList.remove('cq-locked');
    if(cqPkgHintEl){ cqPkgHintEl.hidden=true; cqPkgHintEl.textContent=''; }
    if(cqSubtitleEl) cqSubtitleEl.textContent = CQ_SUBTITLE;
  }
}
window.openCalcModalForPackage = function(pkg){ if(window.openCalcModal) window.openCalcModal('', pkg); };

/* ── Conversion config (from PHP) — used to build the post-submit WhatsApp handoff ── */
const CQ_WA  = "<?= h($whatsappNumber) ?>";
const CQ_TEL = "<?= h($phoneTel) ?>";

/* ── The booking form lives in a popup modal. openCalcModal(destKey) opens it
     from any trigger (hero CTA, route-card "Request Quote", contact button,
     floating button, ?calc= deep-link) and optionally preselects a destination. ── */
const cqModal = document.getElementById('cqModal');

/* localStorage helpers — popup frequency state survives across visits */
function lsGet(k){ try{ return localStorage.getItem(k); }catch(e){ return null; } }
function lsSet(k,v){ try{ localStorage.setItem(k,v); }catch(e){} }

let cqAutoOpened=false;   // this open was triggered by the engagement engine
let cqSubmitted=false;    // an enquiry was submitted in this open

/* ── Focus management ──
   The dialog declares aria-modal="true", which promises assistive tech that the
   rest of the page is inert. Without a trap that promise is broken: Tab walks
   straight out into the page behind the backdrop. We keep focus inside while
   open and hand it back to whatever opened the modal on close. */
let cqLastFocus=null;
const CQ_FOCUSABLE='a[href],button:not([disabled]),input:not([disabled]):not([type=hidden]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';
function cqFocusable(){
  return Array.from(cqModal.querySelectorAll(CQ_FOCUSABLE))
    .filter(el=>el.offsetWidth||el.offsetHeight||el.getClientRects().length);
}
function cqTrap(e){
  if(e.key!=='Tab') return;
  const f=cqFocusable();
  if(!f.length) return;
  const first=f[0], last=f[f.length-1];
  if(e.shiftKey && document.activeElement===first){ e.preventDefault(); last.focus(); }
  else if(!e.shiftKey && document.activeElement===last){ e.preventDefault(); first.focus(); }
}

function openModal(){
  if(!cqModal) return;
  cqLastFocus = document.activeElement;
  cqModal.classList.add('open');
  cqModal.setAttribute('aria-hidden','false');
  document.body.style.overflow='hidden';   // lock background scroll
  document.addEventListener('keydown', cqTrap, true);
  // Land on the first real control rather than leaving focus behind the backdrop.
  requestAnimationFrame(()=>{ cqFocusable()[0]?.focus(); });
  try{ sessionStorage.setItem('cqShown','1'); }catch(e){}  // once per session
}
function closeModal(){
  if(!cqModal) return;
  cqModal.classList.remove('open');
  cqModal.setAttribute('aria-hidden','true');
  document.body.style.overflow='';
  document.removeEventListener('keydown', cqTrap, true);
  // Hand focus back to the trigger so keyboard users keep their place.
  if(cqLastFocus && document.contains(cqLastFocus)) cqLastFocus.focus();
  cqLastFocus=null;
  // Dismissing an AUTO-opened popup without submitting → stay quiet for 3 days.
  if(cqAutoOpened && !cqSubmitted){
    lsSet('cqSnoozeUntil', String(Date.now() + 3*24*60*60*1000));
  }
  cqAutoOpened=false;
}

window.openCalcModal = function(destKey, pkg){
  show('form');
  setTitle('');                              // default friendly heading; preselectDest may personalise it
  setPackageMode(pkg || null);               // lock the days to the package, or clear package mode
  loadData().then(()=>{ if(destKey) preselectDest(destKey); recalc(); });
  openModal();
  cqInline?.scrollTo({top:0});             // reset scroll to the top of the form
};
window.closeCalcModal = closeModal;

function show(state){
  stateForm.hidden = state!=='form';
  stateRes.hidden  = state!=='result';
  stateSuc.hidden  = state!=='success';
  // Journey progress: Details(1) → Estimate(2) → Confirmed(3)
  const prog=document.getElementById('cqProgress');
  if(prog) prog.dataset.step = state==='success' ? '3' : (state==='result' ? '2' : '1');
}

/* ── Data loading ── */
async function loadData(){
  if(DATA) return;
  try{
    const r=await fetch('api/get_quote_data.php');
    DATA=await r.json();
    buildSelectLists();
  }catch(e){
    console.warn('Quote data load failed',e);
    DATA={packages:[],locations:[],vehicles:[],taxes:[],dests:[],seasonal:[],advance_pct:25};
  }
}

function buildSelectLists(){
  // Destinations are rendered by the multi-select (renderDestList); loc & veh stay single-select.
  fillList('cqUl_loc',  DATA.locations, l=>l.name||'?',                            l=>String(l.id));
  fillList('cqUl_veh',  DATA.vehicles,  v=>`${v.name||'?'} — ${v.seats||'?'} Seater`, v=>String(v.id), v=>vehCapacity(v),
           'No cab types are listed right now — tap "WhatsApp" below and our travel desk will suggest the right vehicle.');
  applyVehicleCapacity();
  renderDestList('');
}

function fillList(ulId, items, labelFn, valFn, capFn, emptyMsg){
  const ul=document.getElementById(ulId); if(!ul) return;
  ul.innerHTML=items.length
    ? items.map(it=>`<li class="cq-sel-li" data-val="${valFn(it)}"${capFn?` data-cap="${capFn(it)}"`:''}>${labelFn(it)}</li>`).join('')
    : `<li class="cq-sel-empty">${emptyMsg||'No options available'}</li>`;
  ul.querySelectorAll('.cq-sel-li').forEach(li=>
    li.addEventListener('click',()=>{
      if(li.classList.contains('cq-li-disabled')) return;   // capacity-blocked: not selectable
      const wrap=li.closest('.cq-sel');
      if(wrap) pickOpt(wrap,li.dataset.val,li.textContent.trim());
    })
  );
}

// Passenger capacity of a vehicle = the leading number of its seating string ("7+1" → 7).
function vehCapacity(v){ return parseInt(String(v && v.seats!=null ? v.seats : ''),10)||0; }

// Keep the vehicle list and the passenger count in sync: disable every vehicle that
// seats fewer than the chosen passenger count, and drop an already-selected one that
// no longer fits. Called on load and whenever the traveler stepper changes.
function applyVehicleCapacity(){
  const need=S.travelers;
  document.querySelectorAll('#cqUl_veh .cq-sel-li').forEach(li=>{
    const cap=parseInt(li.dataset.cap||'0',10)||0;
    const bad=cap>0 && cap<need;
    li.classList.toggle('cq-li-disabled',bad);
    li.setAttribute('aria-disabled',bad?'true':'false');
  });
  const capMsg=document.getElementById('cqVehCap');
  if(S.veh){
    const sel=(DATA&&DATA.vehicles||[]).find(v=>String(v.id)===String(S.veh));
    const cap=vehCapacity(sel);
    if(cap>0 && cap<need){
      // Auto-deselect the now-too-small vehicle and explain why.
      S.veh=null;
      const hid=document.getElementById('cqHid_veh'); if(hid) hid.value='';
      const wrap=document.getElementById('cqSel_veh');
      const btn=wrap&&wrap.querySelector('.cq-sel-btn');
      if(btn){ btn.querySelector('.cq-sel-txt').textContent='Choose your vehicle…'; btn.classList.remove('filled'); }
      wrap&&wrap.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.remove('chosen'));
      if(capMsg){ capMsg.hidden=false; capMsg.textContent=`The selected vehicle cannot accommodate ${need} passengers. Please choose a vehicle with at least ${need} seats.`; }
      recalc();
      return;
    }
  }
  if(capMsg) capMsg.hidden=true;
}

const DEFAULT_TITLE='Plan Your Himachal Journey';
const cqTitleEl=document.getElementById('cqTitle');
function setTitle(destName){
  if(!cqTitleEl) return;
  cqTitleEl.textContent = destName ? `Plan Your ${destName} Journey` : DEFAULT_TITLE;
}

function preselectDest(key){
  if(!DATA||!DATA.dests) return;
  const d=DATA.dests.find(x=>x.dest_key===key);
  if(d){
    addDest(d.name, false);                    // seed the multi-select with this destination
    // Friendly, non-static heading keyed to the destination the visitor came from.
    if(d.name) setTitle(d.name);
  }
}

/* ── Multi-select destination picker ──
   S.dest is an array of {name, custom}. Users pick any number of DB destinations
   and/or type their own — a traveller is never blocked by a missing place. */
const destSearch = document.getElementById('cqDestSearch');
const destChips  = document.getElementById('cqDestChips');
const destDrop   = document.getElementById('cqDestDrop');
const destUl     = document.getElementById('cqUl_dest');
const destEmpty  = document.getElementById('cqDestEmpty');
const destHid    = document.getElementById('cqHid_dest');
const destHidCus = document.getElementById('cqHid_dest_custom');

function destHas(name){ return S.dest.some(d=>d.name.toLowerCase()===name.toLowerCase()); }

function addDest(name, custom){
  name=(name||'').trim();
  if(!name || destHas(name)) return;
  S.dest.push({name, custom:!!custom});
  renderChips();
  renderDestList(destSearch?destSearch.value.trim():'');
}
function removeDest(name){
  S.dest=S.dest.filter(d=>d.name.toLowerCase()!==name.toLowerCase());
  renderChips();
  renderDestList(destSearch?destSearch.value.trim():'');
}

function renderChips(){
  if(destChips){
    destChips.innerHTML=S.dest.map(d=>
      `<span class="cq-chip${d.custom?' is-custom':''}">${escHtml(d.name)}`+
      `<button type="button" class="cq-chip-x" data-name="${escAttr(d.name)}" aria-label="Remove ${escAttr(d.name)}"><i class="fa-solid fa-xmark"></i></button></span>`
    ).join('');
    destChips.querySelectorAll('.cq-chip-x').forEach(b=>b.addEventListener('click',()=>removeDest(b.dataset.name)));
  }
  if(destHid)    destHid.value    = S.dest.map(d=>d.name).join(', ');
  if(destHidCus) destHidCus.value = S.dest.filter(d=>d.custom).map(d=>d.name).join(', ');
  if(S.dest.length) document.getElementById('cqF_dest')?.classList.remove('has-err');
}

function renderDestList(q){
  if(!destUl) return;
  const ql=(q||'').toLowerCase();
  const items=(DATA?.dests||[]).filter(d=>!ql || (d.name||'').toLowerCase().includes(ql));
  destUl.innerHTML=items.map(d=>{
    const chosen=destHas(d.name);
    return `<li class="cq-sel-li${chosen?' chosen':''}" data-name="${escAttr(d.name)}">${escHtml(d.name)}</li>`;
  }).join('');
  destUl.querySelectorAll('.cq-sel-li').forEach(li=>li.addEventListener('click',()=>{
    destHas(li.dataset.name) ? removeDest(li.dataset.name) : addDest(li.dataset.name,false);
  }));
  // Offer "Add Custom" whenever the typed value isn't already an exact DB option / chosen chip.
  const exact=(DATA?.dests||[]).some(d=>(d.name||'').toLowerCase()===ql);
  const showAdd = !!q && !exact;
  if(destEmpty) destEmpty.hidden = !showAdd;
}

function escHtml(s){ return String(s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
function escAttr(s){ return escHtml(s).replace(/'/g,'&#39;'); }

function initMultiSelect(){
  if(!destSearch) return;
  const open =()=>{ destDrop?.classList.add('open'); renderDestList(destSearch.value.trim()); };
  const close=()=>destDrop?.classList.remove('open');
  destSearch.addEventListener('focus', open);
  destSearch.addEventListener('input',()=>{ destDrop?.classList.add('open'); renderDestList(destSearch.value.trim()); });
  destSearch.addEventListener('keydown',e=>{
    if(e.key==='Enter'){ e.preventDefault(); const q=destSearch.value.trim(); if(q){ const m=(DATA?.dests||[]).find(d=>(d.name||'').toLowerCase()===q.toLowerCase()); addDest(m?m.name:q, !m); destSearch.value=''; renderDestList(''); } }
    if(e.key==='Escape'){ close(); }
    if(e.key==='Backspace' && !destSearch.value && S.dest.length){ removeDest(S.dest[S.dest.length-1].name); }
  });
  document.getElementById('cqAddCustom')?.addEventListener('click',()=>{
    const q=destSearch.value.trim(); if(q){ addDest(q,true); destSearch.value=''; renderDestList(''); destSearch.focus(); }
  });
  document.addEventListener('click',e=>{ if(!document.getElementById('cqMsel_dest')?.contains(e.target)) close(); });
}

/* ── Custom select ── */
function initSelect(wrap){
  const btn  = wrap.querySelector('.cq-sel-btn');
  const drop = wrap.querySelector('.cq-sel-drop');
  const si   = wrap.querySelector('.cq-sel-si');
  const ul   = wrap.querySelector('.cq-sel-ul');
  const allowCustom = wrap.dataset.custom==='1';
  let fi=-1;

  // Free-text option: shows "Use '<typed>'" so a city we don't stock is still usable.
  function updateCustomLi(raw){
    let cli=ul.querySelector('.cq-sel-customli');
    if(raw){
      if(!cli){
        cli=document.createElement('li');
        cli.className='cq-sel-li cq-sel-customli';
        cli.innerHTML='<i class="fa-solid fa-location-dot"></i> Use “<span class="cq-cust-q"></span>”';
        cli.addEventListener('click',()=>pickOpt(wrap,cli.dataset.custom,cli.dataset.custom,true));
        ul.appendChild(cli);
      }
      cli.dataset.custom=raw;
      cli.querySelector('.cq-cust-q').textContent=raw;
      cli.style.display='';
    } else if(cli){ cli.style.display='none'; }
  }

  function openDrop(){
    btn.classList.add('open'); drop.classList.add('open');
    si.value=''; showAll(ul);
    setTimeout(()=>si.focus(),20);
    document.querySelectorAll('.cq-sel-drop.open').forEach(d=>{
      if(d!==drop){ d.classList.remove('open'); d.closest('.cq-sel')?.querySelector('.cq-sel-btn')?.classList.remove('open'); }
    });
  }
  function closeDrop(){ btn.classList.remove('open'); drop.classList.remove('open'); }

  function showAll(ul){
    ul.querySelectorAll('.cq-sel-li').forEach(l=>l.style.display='');
    ul.querySelector('.cq-sel-customli')?.style.setProperty('display','none'); // hidden until typing
    fi=-1;
  }
  function moveFocus(d){
    const vis=[...ul.querySelectorAll('.cq-sel-li:not([style*="none"])')];
    if(!vis.length) return;
    vis[fi]?.classList.remove('focused');
    fi=Math.max(0,Math.min(vis.length-1,fi+d));
    vis[fi].classList.add('focused'); vis[fi].scrollIntoView({block:'nearest'});
  }

  btn.addEventListener('click',()=>drop.classList.contains('open')?closeDrop():openDrop());
  si.addEventListener('input',()=>{
    const raw=si.value.trim();
    const q=raw.toLowerCase();
    ul.querySelectorAll('.cq-sel-li:not(.cq-sel-customli)').forEach(l=>{
      l.style.display=(!q||l.textContent.toLowerCase().includes(q))?'':'none';
    });
    if(allowCustom) updateCustomLi(raw);
    fi=-1;
  });
  btn.addEventListener('keydown',e=>{ if(['Enter',' ','ArrowDown'].includes(e.key)){e.preventDefault();openDrop();} });
  si.addEventListener('keydown',e=>{
    if(e.key==='ArrowDown'){e.preventDefault();moveFocus(1);}
    if(e.key==='ArrowUp'){e.preventDefault();moveFocus(-1);}
    if(e.key==='Escape'){closeDrop();btn.focus();}
    if(e.key==='Enter'){
      e.preventDefault();
      const vis=[...ul.querySelectorAll('.cq-sel-li:not([style*="none"])')];
      // Use the focused row; otherwise prefer a real match, falling back to the typed city.
      let target = fi>=0 ? vis[fi]
        : (vis.find(l=>!l.classList.contains('cq-sel-customli')) || vis.find(l=>l.classList.contains('cq-sel-customli')));
      if(target){
        if(target.classList.contains('cq-sel-customli')) pickOpt(wrap,target.dataset.custom,target.dataset.custom,true);
        else pickOpt(wrap,target.dataset.val,target.textContent.trim());
      }
    }
  });
  document.addEventListener('click',e=>{ if(!wrap.contains(e.target)) closeDrop(); });

  wrap._selClose = closeDrop;
}

function pickOpt(wrap,val,label,isCustom){
  const key=wrap.dataset.key; if(!key) return;
  const btn=wrap.querySelector('.cq-sel-btn');
  btn.querySelector('.cq-sel-txt').textContent=label;
  btn.classList.add('filled');
  wrap.closest('.cq-field')?.classList.remove('has-err');

  if(key==='loc'){
    // Pickup city accepts free text: a DB city sends pickup_location_id,
    // a typed-in city we don't have sends pickup_custom instead.
    const hidId=document.getElementById('cqHid_loc');
    const hidCustom=document.getElementById('cqHid_loc_custom');
    if(isCustom){
      S.loc='custom'; S.locCustom=label;
      if(hidId)     hidId.value='';
      if(hidCustom) hidCustom.value=label;
      wrap.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.remove('chosen'));
    } else {
      S.loc=val; S.locCustom='';
      if(hidId)     hidId.value=val;
      if(hidCustom) hidCustom.value='';
      wrap.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.toggle('chosen',l.dataset.val===val));
    }
  } else {
    S[key]=val;
    const hid=document.getElementById('cqHid_'+key); if(hid) hid.value=val;
    wrap.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.toggle('chosen',l.dataset.val===val));
    if(key==='veh'){   // a pickable vehicle is always capacity-valid → clear the warning
      const capMsg=document.getElementById('cqVehCap'); if(capMsg) capMsg.hidden=true;
      document.getElementById('cqF_veh')?.classList.remove('has-err');
    }
  }
  wrap._selClose?.();
  recalc();
}

document.querySelectorAll('.cq-sel').forEach(initSelect);
initMultiSelect();

/* ── Stepper ── */
const travDisp=document.getElementById('cqTravDisp');
const travHid =document.getElementById('cqTravHid');
document.querySelectorAll('.cq-step-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    S.travelers=btn.dataset.op==='+'?Math.min(50,S.travelers+1):Math.max(1,S.travelers-1);
    travDisp.textContent=travHid.value=S.travelers;
    applyVehicleCapacity();   // keep vehicle options in sync with the passenger count
    recalc();
  });
});

/* ── Dates ── */
const pickupEl=document.getElementById('cqPickup');
const dropEl  =document.getElementById('cqDrop');
pickupEl.min=todayStr;
pickupEl.addEventListener('change',()=>{
  S.pickup=pickupEl.value;
  if(S.fixedDays>0){
    applyFixedDrop();                 // package mode: drop date follows the package length
  } else {
    dropEl.min=pickupEl.value;
    if(dropEl.value&&dropEl.value<=pickupEl.value){dropEl.value='';S.drop='';}
  }
  document.getElementById('cqF_pd')?.classList.remove('has-err');
  recalc();
});
dropEl.addEventListener('change',()=>{
  S.drop=dropEl.value;
  document.getElementById('cqF_dd')?.classList.remove('has-err');
  recalc();
});

/* ── Name / Phone ── */
document.getElementById('cqName').addEventListener('input', e=>{ S.name=e.target.value.trim(); });
document.getElementById('cqPhone').addEventListener('input',e=>{ S.phone=e.target.value.replace(/\D/g,''); });
document.getElementById('cqName').addEventListener('blur', function(){
  document.getElementById('cqF_name')?.classList.toggle('has-err', this.value.trim().length<3);
});
document.getElementById('cqPhone').addEventListener('blur', function(){
  const d=this.value.replace(/\D/g,'');
  document.getElementById('cqF_mob')?.classList.toggle('has-err', d.length!==10||!/^[6-9]/.test(d));
});

/* ── PRICE CALCULATION (NaN-proof) ── */
function calcPrice(){
  if(!DATA) return null;
  const veh=DATA.vehicles.find(v=>String(v.id)===S.veh);
  if(!veh||!S.pickup||!S.drop) return null;

  const days=daysDiff(S.pickup,S.drop);
  if(days<=0) return null;

  // sn() wraps every value — can never produce NaN
  const vehRate = sn(veh.rate);
  if(vehRate===0) return {error:'Pricing not configured for this vehicle.'};

  // Vehicle-only estimate — destinations/itineraries are quoted by our team, not priced here.
  const vehicleCost = vehRate * days;

  let seasonalAmt=0, seasonalPct=0;
  if(DATA.seasonal&&S.pickup){
    const pd=new Date(S.pickup);
    (DATA.seasonal||[]).forEach(s=>{
      if(pd>=new Date(s.start)&&pd<=new Date(s.end)){
        seasonalPct+=sn(s.pct);
        seasonalAmt+=Math.round(vehicleCost*sn(s.pct)/100);
      }
    });
  }

  const subtotal=vehicleCost+seasonalAmt;

  const taxLines=[];
  (DATA.taxes||[]).forEach(t=>{
    const tval=sn(t.value);
    // package_cost no longer exists → such taxes fall back to the vehicle subtotal.
    const base=t.apply_on==='vehicle_cost'?vehicleCost:subtotal;
    const amt=t.type==='percentage'?Math.round(base*tval/100):tval;
    if(amt>0) taxLines.push({name:t.name,amt});
  });
  const taxTotal=taxLines.reduce((a,t)=>a+sn(t.amt),0);
  const total=subtotal+taxTotal;

  // Advance to confirm — % from admin Settings (via quote data), default 25.
  const advPct=sn(DATA.advance_pct)||25;
  const advance=Math.round(total*advPct/100);

  return {vehicleCost,seasonalAmt,seasonalPct,subtotal,taxLines,taxTotal,total,advPct,advance,days,veh,error:null};
}

function buildBdRows(r){
  const rows=[];
  rows.push({ico:'fa-car-side',         lbl:`Vehicle (${r.days}d)`,                    amt:r.vehicleCost});
  if(r.seasonalAmt>0) rows.push({ico:'fa-sun',         lbl:`Peak Season (+${r.seasonalPct}%)`, amt:r.seasonalAmt, cls:'season-row'});
  rows.push({ico:null,lbl:'Subtotal',                                                   amt:r.subtotal,    cls:'sub-row'});
  r.taxLines.forEach(t=>rows.push({ico:'fa-percent',   lbl:t.name,                     amt:sn(t.amt),     cls:'tax-row'}));
  return rows.map(row=>`
    <div class="cq-bd-row ${row.cls||''}">
      <span class="cq-bd-row-lbl">
        ${row.ico?`<i class="fa-solid ${row.ico}"></i>`:'<span style="width:14px;display:inline-block"></span>'}
        ${row.lbl}
      </span>
      <span class="cq-bd-row-amt">${fmtINR(row.amt)}</span>
    </div>`).join('');
}

/* ── Live recalc on form ── */
function recalc(){
  const r=calcPrice();
  const ph=document.getElementById('cqBdPlaceholder');
  const bdRows=document.getElementById('cqBdRows');
  const bdTotal=document.getElementById('cqBdTotal');

  if(!r||r.error){
    ph.hidden=false; bdRows.hidden=true; bdTotal.hidden=true;
    if(r?.error) ph.innerHTML=`<i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b"></i> ${r.error}`;
    else ph.innerHTML='<i class="fa-solid fa-calculator"></i> Select your vehicle &amp; dates to see your estimate';
    return;
  }
  ph.hidden=true; bdRows.hidden=false; bdTotal.hidden=false;
  bdRows.innerHTML=buildBdRows(r);
  document.getElementById('cqBdAmt').textContent=fmtINR(r.total);
  document.getElementById('cqBdMeta').innerHTML=
    `${S.travelers} traveler${S.travelers>1?'s':''} · ${r.days} day${r.days>1?'s':''} · ${r.veh.name}`+
    `<br><b>${fmtINR(r.advance)}</b> advance (${r.advPct}%) to confirm · balance on trip`;
  lastCalc=r;
}

/* ── Validation ── */
function validate(){
  let ok=true;
  // At least one destination (DB or custom) is required.
  if(!S.dest.length){ document.getElementById('cqF_dest')?.classList.add('has-err'); ok=false; }
  else document.getElementById('cqF_dest')?.classList.remove('has-err');
  ['cqSel_loc','cqSel_veh'].forEach(id=>{
    const wrap=document.getElementById(id);
    if(wrap?.dataset.req==='1'&&!S[wrap.dataset.key]){ wrap.closest('.cq-field')?.classList.add('has-err'); ok=false; }
    else wrap?.closest('.cq-field')?.classList.remove('has-err');
  });
  // Capacity guard: a selected vehicle must seat all travelers.
  const selVeh=(DATA&&DATA.vehicles||[]).find(v=>String(v.id)===String(S.veh));
  const selCap=vehCapacity(selVeh);
  if(S.veh && selCap>0 && selCap<S.travelers){
    document.getElementById('cqF_veh')?.classList.add('has-err');
    const capMsg=document.getElementById('cqVehCap');
    if(capMsg){ capMsg.hidden=false; capMsg.textContent=`The selected vehicle cannot accommodate ${S.travelers} passengers. Please choose a vehicle with at least ${S.travelers} seats.`; }
    ok=false;
  }
  if(!S.pickup||S.pickup<todayStr){document.getElementById('cqF_pd')?.classList.add('has-err');ok=false;}
  else document.getElementById('cqF_pd')?.classList.remove('has-err');
  if(!S.drop||S.drop<=S.pickup){document.getElementById('cqF_dd')?.classList.add('has-err');ok=false;}
  else document.getElementById('cqF_dd')?.classList.remove('has-err');
  if(S.name.length<3){document.getElementById('cqF_name')?.classList.add('has-err');ok=false;}
  else document.getElementById('cqF_name')?.classList.remove('has-err');
  if(S.phone.length!==10||!/^[6-9]/.test(S.phone)){document.getElementById('cqF_mob')?.classList.add('has-err');ok=false;}
  else document.getElementById('cqF_mob')?.classList.remove('has-err');
  return ok;
}

function showErr(divId,msgId,msg){ document.getElementById(divId).hidden=false; document.getElementById(msgId).textContent=msg; }
function hideErr(divId){ document.getElementById(divId).hidden=true; }
function setBusy(btn,on){
  btn.disabled=on;
  btn.querySelector('.cq-btn-label').style.display=on?'none':'';
  const sp=btn.querySelector('.cq-btn-spin'); if(sp) sp.style.display=on?'flex':'none';
}

/* ── GET ESTIMATE → show result locally (no API) ── */
document.getElementById('cqEstBtn').addEventListener('click',()=>{
  hideErr('cqGlobalErr');
  if(!validate()){ showErr('cqGlobalErr','cqGlobalErrMsg','Please fill all required fields.'); cqCard.querySelector('.has-err')?.scrollIntoView({behavior:'smooth',block:'center'}); return; }
  const r=calcPrice();
  if(!r||r.error){ showErr('cqGlobalErr','cqGlobalErrMsg',r?.error||'Unable to generate estimate. Please check pricing configuration.'); return; }
  lastCalc=r;

  // Build result view
  const loc=DATA.locations.find(l=>String(l.id)===S.loc);
  const veh=DATA.vehicles.find(v=>String(v.id)===S.veh);

  document.getElementById('cqResPills').innerHTML=[
    ['fa-mountain-sun',    S.dest.map(d=>d.name).join(', ')||'—'],
    ['fa-location-dot',    loc?.name||S.locCustom||'—'],
    ['fa-car-side',        veh?`${veh.name} (${veh.seats})`:'—'],
    ['fa-users',           `${S.travelers} Traveler${S.travelers>1?'s':''}`],
    ['fa-calendar-days',   `${r.days} Day${r.days>1?'s':''}`],
  ].map(([ic,t])=>`<span class="cq-pill"><i class="fa-solid ${ic}"></i>${t}</span>`).join('');

  document.getElementById('cqResRows').innerHTML=buildBdRows(r);
  document.getElementById('cqResAmt').textContent=fmtINR(r.total);
  document.getElementById('cqResMeta').innerHTML=
    `${S.travelers} travelers · ${r.days} days · ${veh?.name||''}`+
    `<br><b>${fmtINR(r.advance)}</b> advance (${r.advPct}%) to confirm · balance on trip`;

  show('result');
  cqCard.scrollIntoView({behavior:'smooth',block:'start'});
});

/* ── Back to form ── */
document.getElementById('cqBackBtn').addEventListener('click',()=>{ show('form'); });

/* ── SUBMIT ENQUIRY → API saves to DB ── */
document.getElementById('cqSubmitBtn').addEventListener('click', async()=>{
  hideErr('cqSubmitErr');
  const btn=document.getElementById('cqSubmitBtn');
  setBusy(btn,true);
  try{
    const res=await fetch('api/get_price_estimate.php',{
      method:'POST', body:new FormData(document.getElementById('cqForm')),
      headers:{Accept:'application/json'}
    });
    const json=await res.json().catch(()=>({}));
    if(res.ok&&json.ok){
      cqSubmitted=true;
      lsSet('cqDone','1');            // enquiry submitted → never auto-open again
      fillSuccess(json);
      show('success');
      cqCard.scrollIntoView({behavior:'smooth',block:'start'});
    } else {
      showErr('cqSubmitErr','cqSubmitErrMsg',json.error||'Could not submit enquiry. Please try again.');
    }
  }catch{
    showErr('cqSubmitErr','cqSubmitErrMsg','Network error. Please try again.');
  }finally{
    setBusy(btn,false);
  }
});

function fillSuccess(api){
  // Use API response breakdown (server-authoritative) with sn() wrapping
  const bd=api.breakdown||{};
  const total=sn(bd.total||api.total);
  const advPct=sn(bd.advance_pct)||sn(DATA&&DATA.advance_pct)||25;
  const advance=sn(bd.advance_amount)||Math.round(total*advPct/100);
  document.getElementById('cqSuccessSummary').innerHTML=`
    <div class="cq-success-row"><span>Destination</span><strong>${api.destination||'—'}</strong></div>
    <div class="cq-success-row"><span>Pickup</span><strong>${api.pickup||'—'}</strong></div>
    <div class="cq-success-row"><span>Vehicle</span><strong>${api.vehicle||'—'}</strong></div>
    <div class="cq-success-row"><span>Dates</span><strong>${S.pickup} → ${S.drop}</strong></div>
    <div class="cq-success-row"><span>Travelers</span><strong>${sn(api.travelers)}</strong></div>
    <div class="cq-success-row" style="padding-top:.5rem;border-top:1px solid var(--line);margin-top:.3rem">
      <span><strong>Estimated Total</strong></span>
      <strong class="cq-success-total">${fmtINR(total)}</strong>
    </div>
    <div class="cq-success-row">
      <span>Advance to confirm (${advPct}%)</span><strong>${fmtINR(advance)}</strong>
    </div>
    <div class="cq-success-row">
      <span>Balance on trip</span><strong>${fmtINR(total-advance)}</strong>
    </div>
  `;

  // Build the WhatsApp handoff deep link with the full enquiry context.
  const waBtn=document.getElementById('cqWaBtn');
  if(waBtn && CQ_WA){
    const ref = api.enquiry_id ? `#${api.enquiry_id}` : '';
    const msg =
      `Hi Himachal Safar, I just submitted an enquiry${ref?` (Ref ${ref})`:''} and would like to confirm my trip.\n\n`+
      `• Destination: ${api.destination||'—'}\n`+
      `• Pickup: ${api.pickup||'—'}\n`+
      `• Vehicle: ${api.vehicle||'—'}\n`+
      `• Dates: ${S.pickup} → ${S.drop}\n`+
      `• Travelers: ${sn(api.travelers)}\n`+
      `• Estimated total: ${fmtINR(total)}\n`+
      `• Advance to confirm (${advPct}%): ${fmtINR(advance)}`;
    waBtn.href = `https://wa.me/${CQ_WA}?text=${encodeURIComponent(msg)}`;
  }
}

/* ── Reset / New Estimate ── */
document.getElementById('cqNewBtn').addEventListener('click',()=>{
  document.getElementById('cqForm').reset();
  S={dest:[],loc:null,locCustom:'',veh:null,pickup:'',drop:'',travelers:2,name:'',phone:'',packageId:'',fixedDays:0};
  travDisp.textContent=travHid.value='2';
  setTitle('');
  setPackageMode(null);
  if(destSearch) destSearch.value='';
  renderChips(); renderDestList('');
  document.querySelectorAll('.cq-sel-btn').forEach(b=>{ b.querySelector('.cq-sel-txt').textContent=b.closest('.cq-sel').dataset.key==='loc'?'Select or type your city…':'Choose your vehicle…'; b.classList.remove('filled','open'); });
  document.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.remove('chosen'));
  document.querySelectorAll('.cq-sel-customli').forEach(l=>l.style.display='none');
  document.querySelectorAll('.cq-sel-drop').forEach(d=>d.classList.remove('open'));
  document.getElementById('cqHid_loc').value=''; document.getElementById('cqHid_loc_custom').value=''; document.getElementById('cqHid_veh').value='';
  document.querySelectorAll('.cq-field.has-err').forEach(f=>f.classList.remove('has-err'));
  hideErr('cqGlobalErr'); hideErr('cqSubmitErr');
  applyVehicleCapacity();   // travelers reset to 2 → re-enable all vehicles, clear capacity warning
  lastCalc=null;
  recalc();
  show('form');
});

/* ── Preload options & render the estimate placeholder so the first
     open is instant. ── */
show('form');
loadData().then(recalc);

/* ── Close triggers: the ✕ button, backdrop click, and Escape key ── */
cqModal?.querySelectorAll('[data-cq-close]').forEach(el=>el.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&cqModal?.classList.contains('open')) closeModal(); });

/* ── Open triggers: hero CTA, floating button, and any [data-open-quote] element ── */
['openBookingModal','heroBookBtn','cqFab'].forEach(id=>{
  document.getElementById(id)?.addEventListener('click',e=>{e.stopImmediatePropagation();openCalcModal('');},true);
});
// Delegated: also catches [data-open-quote] elements rendered AFTER this script
// (e.g. the footer's "Plan a Custom Trip" link, which foot.php outputs later).
document.addEventListener('click',e=>{
  const el=e.target.closest('[data-open-quote]');
  if(!el) return;
  e.preventDefault();
  openCalcModal(el.getAttribute('data-open-quote')||'');
});

/* ── URL auto-open: ?calc=manali (or bare ?calc= for no preselection) ── */
const _dp=new URLSearchParams(window.location.search).get('calc');
if(_dp!==null) window.addEventListener('load',()=>setTimeout(()=>openCalcModal(_dp),250));

/* ── "Engaged visitor" auto-open policy ──
   Opens only after real interest — whichever comes first:
     · visitor scrolls 50% of the page
     · 40 seconds on the page
     · exit-intent (desktop: cursor leaves toward the tab bar)
   Frequency caps:
     · once per browser session
     · snoozed 3 days after being dismissed without submitting
     · max 3 auto-opens per rolling 30 days
     · never again once an enquiry has been submitted (cqDone)
   Manual opens (CTA buttons) are never limited. ── */
if(_dp===null && !window.CQ_NO_AUTOPOPUP){
  const DAY=24*60*60*1000;
  const now=Date.now();
  let shownSession=false; try{ shownSession=sessionStorage.getItem('cqShown')==='1'; }catch(e){}
  let hist=[]; try{ hist=JSON.parse(lsGet('cqAutoHist')||'[]'); }catch(e){}
  if(!Array.isArray(hist)) hist=[];
  hist=hist.filter(t=>now-t<30*DAY);
  const eligible =
    !shownSession &&
    lsGet('cqDone')!=='1' &&
    now>parseInt(lsGet('cqSnoozeUntil')||'0',10) &&
    hist.length<3;

  if(eligible){
    let fired=false;
    const fire=()=>{
      if(fired) return;
      if(cqModal?.classList.contains('open')) return;
      if(document.getElementById('wlModal')?.classList.contains('open')) return;
      let shown=false; try{ shown=sessionStorage.getItem('cqShown')==='1'; }catch(e){}
      if(shown){ cleanup(); return; }          // opened manually in the meantime
      fired=true; cleanup();
      hist.push(Date.now());
      lsSet('cqAutoHist', JSON.stringify(hist));
      cqAutoOpened=true;
      openCalcModal('');
    };
    const onScroll=()=>{
      const d=document.documentElement;
      const max=d.scrollHeight-window.innerHeight;
      if(max>200 && d.scrollTop/max>=.5) fire();
    };
    const onExit=e=>{ if(!e.relatedTarget && e.clientY<=0) fire(); };
    const dwell=setTimeout(fire, 40000);
    const cleanup=()=>{
      clearTimeout(dwell);
      window.removeEventListener('scroll', onScroll);
      document.removeEventListener('mouseout', onExit);
    };
    window.addEventListener('scroll', onScroll, {passive:true});
    if(window.matchMedia('(hover: hover) and (pointer: fine)').matches){
      document.addEventListener('mouseout', onExit);
    }
  }
}

})();
</script>
