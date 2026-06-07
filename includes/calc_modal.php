<?php $__csrf = $_SESSION['lead_form_token'] ?? ''; ?>

<div class="cq-inline" id="cqInline">
  <div class="cq-card" id="cqCard" role="region" aria-label="Instant quote booking form">

    <!-- Header -->
    <div class="cq-header">
      <div class="cq-header-icon">
        <img src="assets/logo-icon.svg" width="36" height="36" alt="">
      </div>
      <div>
        <h2 class="cq-title" id="cqTitle">Get Your Instant Quote</h2>
        <p class="cq-subtitle">Fill in your trip details — price updates instantly.</p>
      </div>
    </div>

    <!-- ═══ STATE 1: FORM ═══ -->
    <div id="cqStateForm">

      <form id="cqForm" novalidate autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= h($__csrf) ?>">
        <input type="text" name="website" tabindex="-1" style="display:none" autocomplete="off">

        <!-- TRIP DETAILS -->
        <div class="cq-section">
          <div class="cq-section-label"><i class="fa-solid fa-map-location-dot"></i> Trip Details</div>

          <div class="cq-row">
            <div class="cq-field" id="cqF_pkg">
              <label class="cq-label">Tour Package <span class="cq-req">*</span></label>
              <div class="cq-sel" id="cqSel_pkg" data-key="pkg" data-req="1">
                <button type="button" class="cq-sel-btn">
                  <span class="cq-sel-txt">Select a package…</span>
                  <i class="fa-solid fa-chevron-down cq-sel-arr"></i>
                </button>
                <div class="cq-sel-drop">
                  <div class="cq-sel-sr"><i class="fa-solid fa-magnifying-glass"></i><input class="cq-sel-si" type="search" placeholder="Search packages…" autocomplete="off"></div>
                  <ul class="cq-sel-ul" id="cqUl_pkg"></ul>
                </div>
              </div>
              <input type="hidden" name="package_id" id="cqHid_pkg">
              <span class="cq-err">Please select a tour package</span>
            </div>

            <div class="cq-field" id="cqF_loc">
              <label class="cq-label">Pickup City <span class="cq-req">*</span></label>
              <div class="cq-sel" id="cqSel_loc" data-key="loc" data-req="1">
                <button type="button" class="cq-sel-btn">
                  <span class="cq-sel-txt">Select your city…</span>
                  <i class="fa-solid fa-chevron-down cq-sel-arr"></i>
                </button>
                <div class="cq-sel-drop">
                  <div class="cq-sel-sr"><i class="fa-solid fa-magnifying-glass"></i><input class="cq-sel-si" type="search" placeholder="Search cities…" autocomplete="off"></div>
                  <ul class="cq-sel-ul" id="cqUl_loc"></ul>
                </div>
              </div>
              <input type="hidden" name="pickup_location_id" id="cqHid_loc">
              <span class="cq-err">Please select a pickup city</span>
            </div>
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
              Select package, vehicle &amp; dates to see your estimate
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
      </div>
    </div><!-- /cqStateResult -->

    <!-- ═══ STATE 3: SUCCESS ═══ -->
    <div id="cqStateSuccess" hidden>
      <div class="cq-section cq-success">
        <div class="cq-success-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h3 class="cq-success-title">Enquiry Submitted!</h3>
        <p class="cq-success-msg">Your enquiry has been submitted successfully. Our team will contact you within 24 hours.</p>
        <div class="cq-success-summary" id="cqSuccessSummary"></div>
        <button type="button" class="cq-btn-outline cq-mt" id="cqNewBtn">
          <i class="fa-solid fa-rotate-left"></i> Calculate New Estimate
        </button>
      </div>
    </div><!-- /cqStateSuccess -->

  </div>
</div>

<!-- ═══ STYLES ═══ -->
<style>
/* Inline (in-page) booking form — fills its column, no modal chrome.
   The surrounding .contact-card supplies the panel surface/border. */
.cq-inline { width: 100%; }
.cq-card {
  width: 100%; padding: 0; display: flex; flex-direction: column;
  background: transparent;
}

/* Header */
.cq-header {
  display: flex; align-items: center; gap: 1rem;
  padding: 1.4rem 1.75rem 1.2rem; border-bottom: 1px solid var(--line);
  flex-shrink: 0;
}
.cq-header-icon {
  width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
  background: var(--gold-light); border: 1px solid rgba(200,167,93,.2);
  display: flex; align-items: center; justify-content: center;
}
.cq-title { font-family: 'Montserrat',sans-serif; font-size: 1.15rem; font-weight: 900; color: var(--charcoal); margin: 0 0 .15rem; }
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
.cq-field.has-err .cq-input,
.cq-field.has-err .cq-sel-btn { border-color: #f87171 !important; }
.cq-field.has-err .cq-tel { border-color: #f87171 !important; }

/* Inputs */
.cq-input {
  height: 44px; padding: 0 .9rem;
  background: var(--surf-3); border: 1.5px solid var(--line); border-radius: 10px;
  color: var(--charcoal); font-size: .875rem; font-family: 'Inter',sans-serif;
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
  color: var(--charcoal); font-family: 'Montserrat', sans-serif;
}

/* Searchable select */
.cq-sel { position: relative; }
.cq-sel-btn {
  width: 100%; height: 44px; display: flex; align-items: center;
  justify-content: space-between; gap: .4rem; padding: 0 .9rem;
  background: var(--surf-3); border: 1.5px solid var(--line); border-radius: 10px;
  color: var(--muted); cursor: pointer; font-family: 'Inter',sans-serif; font-size: .875rem;
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
  font-size: .82rem; font-family: 'Inter',sans-serif; box-sizing: border-box; outline: none;
}
.cq-sel-si::placeholder { color: var(--muted); }
.cq-sel-ul { list-style: none; margin: 0; padding: .25rem 0; overflow-y: auto; max-height: 200px; }
.cq-sel-ul::-webkit-scrollbar { width: 4px; }
.cq-sel-ul::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }
.cq-sel-li { padding: .52rem .9rem; cursor: pointer; font-size: .85rem; color: var(--text-2); transition: background .1s, color .1s; }
.cq-sel-li:hover, .cq-sel-li.focused { background: var(--gold-light); color: var(--charcoal); }
.cq-sel-li.chosen { color: var(--lime); font-weight: 600; }
.cq-sel-li.chosen::before { content: '✓  '; font-size: .72rem; }
.cq-sel-empty { padding: .75rem .9rem; font-size: .78rem; color: var(--muted); }

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
  font-family: 'Montserrat',sans-serif; font-size: 1.9rem; font-weight: 900;
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
  width: 100%; height: 50px; border: none; border-radius: 11px;
  background: linear-gradient(135deg, var(--lime) 0%, var(--lime-2) 100%);
  color: #0b0d12; font-size: .95rem; font-weight: 900;
  font-family: 'Montserrat',sans-serif; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: .45rem;
  transition: transform .2s, box-shadow .2s, opacity .2s;
  box-shadow: 0 4px 20px rgba(200,167,93,.28);
}
.cq-btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(200,167,93,.4); }
.cq-btn-primary:disabled { opacity: .65; cursor: not-allowed; transform: none; }
.cq-btn-outline {
  width: 100%; height: 46px; border: 1.5px solid var(--line); border-radius: 11px;
  background: transparent; color: var(--text-2); font-size: .88rem; font-weight: 600;
  font-family: 'Inter',sans-serif; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: .45rem;
  transition: border-color .2s, color .2s;
}
.cq-btn-outline:hover { border-color: var(--lime); color: var(--lime); }
.cq-btn-spin { display: none; align-items: center; gap: .4rem; }
.cq-footnote { font-size: .7rem; color: var(--muted); text-align: center; margin: 0; display: flex; align-items: center; justify-content: center; gap: .3rem; }
.cq-footnote i { color: var(--lime); }

/* Result */
.cq-result-head { display: flex; align-items: flex-start; gap: .9rem; margin-bottom: 1rem; }
.cq-result-icon { font-size: 1.9rem; color: var(--lime); flex-shrink: 0; line-height: 1; }
.cq-result-title { font-family: 'Montserrat',sans-serif; font-size: 1.05rem; font-weight: 800; color: var(--charcoal); margin: 0 0 .15rem; }
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
.cq-success-title { font-family: 'Montserrat',sans-serif; font-size: 1.3rem; font-weight: 900; color: var(--charcoal); margin: 0 0 .6rem; }
.cq-success-msg { font-size: .88rem; color: var(--text-2); margin: 0 0 1.25rem; line-height: 1.6; }
.cq-success-summary {
  background: var(--surf-3); border: 1px solid var(--line); border-radius: 12px;
  padding: 1rem 1.25rem; text-align: left; font-size: .82rem; color: var(--text-2);
  display: flex; flex-direction: column; gap: .4rem; margin-bottom: .5rem;
}
.cq-success-row { display: flex; justify-content: space-between; gap: .5rem; }
.cq-success-row strong { color: var(--charcoal); white-space: nowrap; }
.cq-success-total { font-family: 'Montserrat',sans-serif; font-size: 1.4rem; font-weight: 900; color: var(--lime); }
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
[data-theme="light"] .cq-bd-card { background: #fff; }
[data-theme="light"] .cq-bd-total{ background: #f5f5f0; }

/* Responsive */
@media (max-width: 580px) {
  .cq-header  { padding: 1.1rem; }
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
let S={pkg:null,loc:null,veh:null,pickup:'',drop:'',travelers:2,name:'',phone:''};
let lastCalc=null; // result of calcPrice(), shared between states

/* ── DOM ── */
const cqCard     = document.getElementById('cqCard');
const cqInline   = document.getElementById('cqInline');
const stateForm  = document.getElementById('cqStateForm');
const stateRes   = document.getElementById('cqStateResult');
const stateSuc   = document.getElementById('cqStateSuccess');
const todayStr   = new Date().toISOString().split('T')[0];

/* ── The booking form is now embedded inline in the Contact section.
     openCalcModal() is kept so existing triggers (hero CTA, route-card
     "Request Quote", ?calc= deep-link) still work — it scrolls to the
     form and optionally preselects a destination instead of opening a modal. ── */
window.openCalcModal = function(destKey){
  show('form');
  loadData().then(()=>{ if(destKey) preselectDest(destKey); recalc(); });
  cqInline?.scrollIntoView({behavior:'smooth', block:'start'});
};

function show(state){
  stateForm.hidden = state!=='form';
  stateRes.hidden  = state!=='result';
  stateSuc.hidden  = state!=='success';
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
    DATA={packages:[],locations:[],vehicles:[],taxes:[],dests:[],seasonal:[]};
  }
}

function buildSelectLists(){
  fillList('cqUl_pkg', DATA.packages,  p=>`${p.name||'?'} — ${p.days||0} Days`, p=>String(p.id));
  fillList('cqUl_loc', DATA.locations, l=>l.name||'?',                            l=>String(l.id));
  fillList('cqUl_veh', DATA.vehicles,  v=>`${v.name||'?'} — ${v.seats||'?'} Seater`, v=>String(v.id));
}

function fillList(ulId, items, labelFn, valFn){
  const ul=document.getElementById(ulId); if(!ul) return;
  ul.innerHTML=items.length
    ? items.map(it=>`<li class="cq-sel-li" data-val="${valFn(it)}">${labelFn(it)}</li>`).join('')
    : '<li class="cq-sel-empty">No options available</li>';
  ul.querySelectorAll('.cq-sel-li').forEach(li=>
    li.addEventListener('click',()=>{
      const wrap=li.closest('.cq-sel');
      if(wrap) pickOpt(wrap,li.dataset.val,li.textContent.trim());
    })
  );
}

function preselectDest(key){
  if(!DATA) return;
  const m=DATA.packages.find(p=>p.dest_key===key);
  if(m){ const w=document.getElementById('cqSel_pkg'); if(w) pickOpt(w,String(m.id),`${m.name} — ${m.days} Days`); }
}

/* ── Custom select ── */
function initSelect(wrap){
  const btn  = wrap.querySelector('.cq-sel-btn');
  const drop = wrap.querySelector('.cq-sel-drop');
  const si   = wrap.querySelector('.cq-sel-si');
  const ul   = wrap.querySelector('.cq-sel-ul');
  let fi=-1;

  function openDrop(){
    btn.classList.add('open'); drop.classList.add('open');
    si.value=''; showAll(ul);
    setTimeout(()=>si.focus(),20);
    document.querySelectorAll('.cq-sel-drop.open').forEach(d=>{
      if(d!==drop){ d.classList.remove('open'); d.closest('.cq-sel')?.querySelector('.cq-sel-btn')?.classList.remove('open'); }
    });
  }
  function closeDrop(){ btn.classList.remove('open'); drop.classList.remove('open'); }

  function showAll(ul){ ul.querySelectorAll('.cq-sel-li').forEach(l=>l.style.display=''); fi=-1; }
  function moveFocus(d){
    const vis=[...ul.querySelectorAll('.cq-sel-li:not([style*="none"])')];
    if(!vis.length) return;
    vis[fi]?.classList.remove('focused');
    fi=Math.max(0,Math.min(vis.length-1,fi+d));
    vis[fi].classList.add('focused'); vis[fi].scrollIntoView({block:'nearest'});
  }

  btn.addEventListener('click',()=>drop.classList.contains('open')?closeDrop():openDrop());
  si.addEventListener('input',()=>{
    const q=si.value.toLowerCase();
    ul.querySelectorAll('.cq-sel-li').forEach(l=>{ l.style.display=(!q||l.textContent.toLowerCase().includes(q))?'':'none'; });
    fi=-1;
  });
  btn.addEventListener('keydown',e=>{ if(['Enter',' ','ArrowDown'].includes(e.key)){e.preventDefault();openDrop();} });
  si.addEventListener('keydown',e=>{
    if(e.key==='ArrowDown'){e.preventDefault();moveFocus(1);}
    if(e.key==='ArrowUp'){e.preventDefault();moveFocus(-1);}
    if(e.key==='Escape'){closeDrop();btn.focus();}
    if(e.key==='Enter'){
      const vis=[...ul.querySelectorAll('.cq-sel-li:not([style*="none"])')];
      if(vis[fi]) pickOpt(wrap,vis[fi].dataset.val,vis[fi].textContent.trim());
    }
  });
  document.addEventListener('click',e=>{ if(!wrap.contains(e.target)) closeDrop(); });

  wrap._selClose = closeDrop;
}

function pickOpt(wrap,val,label){
  const key=wrap.dataset.key; if(!key) return;
  S[key]=val;
  const btn=wrap.querySelector('.cq-sel-btn');
  btn.querySelector('.cq-sel-txt').textContent=label;
  btn.classList.add('filled');
  wrap.closest('.cq-field')?.classList.remove('has-err');
  const hid=document.getElementById('cqHid_'+key); if(hid) hid.value=val;
  wrap.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.toggle('chosen',l.dataset.val===val));
  wrap._selClose?.();
  recalc();
}

document.querySelectorAll('.cq-sel').forEach(initSelect);

/* ── Stepper ── */
const travDisp=document.getElementById('cqTravDisp');
const travHid =document.getElementById('cqTravHid');
document.querySelectorAll('.cq-step-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    S.travelers=btn.dataset.op==='+'?Math.min(50,S.travelers+1):Math.max(1,S.travelers-1);
    travDisp.textContent=travHid.value=S.travelers;
    recalc();
  });
});

/* ── Dates ── */
const pickupEl=document.getElementById('cqPickup');
const dropEl  =document.getElementById('cqDrop');
pickupEl.min=todayStr;
pickupEl.addEventListener('change',()=>{
  S.pickup=pickupEl.value;
  dropEl.min=pickupEl.value;
  if(dropEl.value&&dropEl.value<=pickupEl.value){dropEl.value='';S.drop='';}
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
  const pkg=DATA.packages.find(p=>String(p.id)===S.pkg);
  const veh=DATA.vehicles.find(v=>String(v.id)===S.veh);
  if(!pkg||!veh||!S.pickup||!S.drop) return null;

  const days=daysDiff(S.pickup,S.drop);
  if(days<=0) return null;

  // sn() wraps every value — can never produce NaN
  const ppd      = sn(pkg.ppd);
  const extraPP  = sn(pkg.extra_pp);
  const vehRate  = sn(veh.rate);

  if(ppd===0 && vehRate===0) return {error:'Pricing not configured for this package.'};

  const packageCost = ppd * sn(S.travelers) * days;
  const vehicleCost = vehRate * days;
  const extraCost   = extraPP * sn(S.travelers);

  const dest = DATA.dests?.find(d=>d.dest_key===pkg.dest_key);
  const destCharge = sn(dest?.extra_per_day) * sn(S.travelers) * days;

  let seasonalAmt=0, seasonalPct=0;
  if(DATA.seasonal&&S.pickup){
    const pd=new Date(S.pickup);
    (DATA.seasonal||[]).forEach(s=>{
      if(pd>=new Date(s.start)&&pd<=new Date(s.end)){
        seasonalPct+=sn(s.pct);
        seasonalAmt+=Math.round((packageCost+vehicleCost)*sn(s.pct)/100);
      }
    });
  }

  const subtotal=packageCost+vehicleCost+extraCost+destCharge+seasonalAmt;

  const taxLines=[];
  (DATA.taxes||[]).forEach(t=>{
    const tval=sn(t.value);
    const base=t.apply_on==='package_cost'?packageCost:t.apply_on==='vehicle_cost'?vehicleCost:subtotal;
    const amt=t.type==='percentage'?Math.round(base*tval/100):tval;
    if(amt>0) taxLines.push({name:t.name,amt});
  });
  const taxTotal=taxLines.reduce((a,t)=>a+sn(t.amt),0);
  const total=subtotal+taxTotal;

  return {packageCost,vehicleCost,extraCost,destCharge,seasonalAmt,seasonalPct,subtotal,taxLines,taxTotal,total,days,pkg,veh,error:null};
}

function buildBdRows(r){
  const rows=[];
  rows.push({ico:'fa-suitcase-rolling', lbl:`Package (${S.travelers}p × ${r.days}d)`, amt:r.packageCost});
  rows.push({ico:'fa-car-side',         lbl:`Vehicle (${r.days}d)`,                    amt:r.vehicleCost});
  if(r.extraCost>0)   rows.push({ico:'fa-circle-plus',lbl:'Extra Charges',             amt:r.extraCost,   cls:'extra-row'});
  if(r.destCharge>0)  rows.push({ico:'fa-mountain',   lbl:'Destination Surcharge',     amt:r.destCharge,  cls:'extra-row'});
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
    else ph.innerHTML='<i class="fa-solid fa-calculator"></i> Select package, vehicle &amp; dates to see your estimate';
    return;
  }
  ph.hidden=true; bdRows.hidden=false; bdTotal.hidden=false;
  bdRows.innerHTML=buildBdRows(r);
  document.getElementById('cqBdAmt').textContent=fmtINR(r.total);
  document.getElementById('cqBdMeta').textContent=
    `${S.travelers} traveler${S.travelers>1?'s':''} · ${r.days} day${r.days>1?'s':''} · ${r.veh.name}`;
  lastCalc=r;
}

/* ── Validation ── */
function validate(){
  let ok=true;
  ['cqSel_pkg','cqSel_loc','cqSel_veh'].forEach(id=>{
    const wrap=document.getElementById(id);
    if(wrap?.dataset.req==='1'&&!S[wrap.dataset.key]){ wrap.closest('.cq-field')?.classList.add('has-err'); ok=false; }
    else wrap?.closest('.cq-field')?.classList.remove('has-err');
  });
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
  const pkg=DATA.packages.find(p=>String(p.id)===S.pkg);
  const loc=DATA.locations.find(l=>String(l.id)===S.loc);
  const veh=DATA.vehicles.find(v=>String(v.id)===S.veh);

  document.getElementById('cqResPills').innerHTML=[
    ['fa-suitcase-rolling',pkg?.name||'—'],
    ['fa-location-dot',    loc?.name||'—'],
    ['fa-car-side',        veh?`${veh.name} (${veh.seats})`:'—'],
    ['fa-users',           `${S.travelers} Traveler${S.travelers>1?'s':''}`],
    ['fa-calendar-days',   `${r.days} Day${r.days>1?'s':''}`],
  ].map(([ic,t])=>`<span class="cq-pill"><i class="fa-solid ${ic}"></i>${t}</span>`).join('');

  document.getElementById('cqResRows').innerHTML=buildBdRows(r);
  document.getElementById('cqResAmt').textContent=fmtINR(r.total);
  document.getElementById('cqResMeta').textContent=`${S.travelers} travelers · ${r.days} days · ${veh?.name||''}`;

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
  document.getElementById('cqSuccessSummary').innerHTML=`
    <div class="cq-success-row"><span>Package</span><strong>${api.package||'—'}</strong></div>
    <div class="cq-success-row"><span>Pickup</span><strong>${api.pickup||'—'}</strong></div>
    <div class="cq-success-row"><span>Vehicle</span><strong>${api.vehicle||'—'}</strong></div>
    <div class="cq-success-row"><span>Dates</span><strong>${S.pickup} → ${S.drop}</strong></div>
    <div class="cq-success-row"><span>Travelers</span><strong>${sn(api.travelers)}</strong></div>
    <div class="cq-success-row" style="padding-top:.5rem;border-top:1px solid var(--line);margin-top:.3rem">
      <span><strong>Estimated Total</strong></span>
      <strong class="cq-success-total">${fmtINR(total)}</strong>
    </div>
  `;
}

/* ── Reset / New Estimate ── */
document.getElementById('cqNewBtn').addEventListener('click',()=>{
  document.getElementById('cqForm').reset();
  S={pkg:null,loc:null,veh:null,pickup:'',drop:'',travelers:2,name:'',phone:''};
  travDisp.textContent=travHid.value='2';
  document.querySelectorAll('.cq-sel-btn').forEach(b=>{ b.querySelector('.cq-sel-txt').textContent=b.closest('.cq-sel').dataset.key==='pkg'?'Select a package…':b.closest('.cq-sel').dataset.key==='loc'?'Select your city…':'Choose your vehicle…'; b.classList.remove('filled','open'); });
  document.querySelectorAll('.cq-sel-li').forEach(l=>l.classList.remove('chosen'));
  document.querySelectorAll('.cq-sel-drop').forEach(d=>d.classList.remove('open'));
  document.querySelectorAll('.cq-hid_pkg,.cq-hid_loc,.cq-hid_veh').forEach(h=>{ if(h) h.value=''; });
  document.getElementById('cqHid_pkg').value=''; document.getElementById('cqHid_loc').value=''; document.getElementById('cqHid_veh').value='';
  document.querySelectorAll('.cq-field.has-err').forEach(f=>f.classList.remove('has-err'));
  hideErr('cqGlobalErr'); hideErr('cqSubmitErr');
  lastCalc=null;
  recalc();
  show('form');
});

/* ── Inline init: the form is always visible, so load options &
     render the live estimate placeholder on page load. ── */
show('form');
loadData().then(recalc);

/* ── Existing CTA triggers scroll to the inline form ── */
['openBookingModal','heroBookBtn'].forEach(id=>{
  document.getElementById(id)?.addEventListener('click',e=>{e.stopImmediatePropagation();openCalcModal('');},true);
});

/* ── URL auto-open: ?calc=manali ── */
const _dp=new URLSearchParams(window.location.search).get('calc');
if(_dp) window.addEventListener('load',()=>setTimeout(()=>openCalcModal(_dp),250));

})();
</script>
