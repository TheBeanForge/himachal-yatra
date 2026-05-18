<?php
require_once 'api/config.php';
$wa = agency_whatsapp();
$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Book a Trip — WanderVista Tours</title>
<meta name="description" content="Book your dream tour with WanderVista Tours. Fill the enquiry form and get a custom quote within 24 hours."/>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<style>
:root{
  --bg:#ffffff;--surface:#f0fdf4;--card:#ffffff;
  --border:#d1fae5;--border2:#bbf7d0;
  --accent:#16a34a;--accent-d:#15803d;--accent-l:#4ade80;
  --ink:#0f172a;--muted:#64748b;--text:#334155;
}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--ink);min-height:100vh}

/* ── NAV ── */
.enr-nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.95);backdrop-filter:blur(14px);border-bottom:1px solid var(--border);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.enr-logo{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:2px;color:var(--ink);text-decoration:none}
.enr-logo span{color:var(--accent)}
.nav-back{display:inline-flex;align-items:center;gap:7px;font-size:13px;color:var(--muted);text-decoration:none;transition:color .2s;font-weight:500}
.nav-back:hover{color:var(--accent)}
.nav-wa{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#25D366;font-weight:600;text-decoration:none;padding:8px 16px;border:1px solid rgba(37,211,102,.3);border-radius:30px;transition:all .2s}
.nav-wa:hover{background:rgba(37,211,102,.08)}
@media(max-width:600px){.nav-wa span{display:none}}

/* ── LAYOUT ── */
.enr-wrap{display:grid;grid-template-columns:340px 1fr;min-height:calc(100vh - 60px);max-width:1160px;margin:0 auto}
@media(max-width:960px){.enr-wrap{grid-template-columns:1fr}}

/* ── ASIDE ── */
.enr-aside{background:var(--surface);border-right:1px solid var(--border);padding:44px 32px;position:sticky;top:60px;height:calc(100vh - 60px);overflow-y:auto;display:flex;flex-direction:column;gap:28px}
@media(max-width:960px){.enr-aside{position:static;height:auto;border-right:none;border-bottom:1px solid var(--border);padding:28px 20px;gap:18px;flex-direction:row;flex-wrap:wrap;align-items:flex-start}}
@media(max-width:600px){.enr-aside{flex-direction:column;padding:22px 16px;gap:14px}}

.aside-logo{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:3px;color:var(--ink)}
.aside-logo span{color:var(--accent)}
.aside-heading{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:1px;color:var(--ink);line-height:1.1}
.aside-heading span{color:var(--accent)}
.aside-sub{font-size:13.5px;color:var(--muted);line-height:1.7}

.trust-item{display:flex;align-items:flex-start;gap:12px}
.trust-icon{width:36px;height:36px;border-radius:10px;background:rgba(22,163,74,.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:14px;flex-shrink:0;margin-top:1px}
.trust-item h5{font-size:13px;font-weight:700;margin-bottom:2px;color:var(--ink)}
.trust-item p{font-size:12px;color:var(--muted);line-height:1.55}

.wa-aside{display:flex;align-items:center;gap:10px;background:rgba(37,211,102,.08);border:1px solid rgba(37,211,102,.25);border-radius:12px;padding:14px 16px;text-decoration:none;color:var(--ink);transition:all .2s}
.wa-aside:hover{background:rgba(37,211,102,.14);color:var(--ink)}
.wa-aside i{font-size:20px;color:#25D366;flex-shrink:0}
.wa-aside span{font-size:12.5px;font-weight:600;display:block}
.wa-aside small{font-size:11px;color:var(--muted)}

/* ── FORM AREA ── */
.enr-form-area{padding:44px 48px;overflow-y:auto}
@media(max-width:960px){.enr-form-area{padding:32px 20px}}
@media(max-width:600px){.enr-form-area{padding:24px 16px}}

.form-section-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;color:var(--ink);margin-bottom:4px}
.form-section-sub{font-size:13px;color:var(--muted);margin-bottom:28px;line-height:1.6}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
@media(max-width:580px){.form-row{grid-template-columns:1fr}}
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:20px}
.form-group label{font-size:13px;font-weight:600;color:var(--ink)}
.form-group label .req{color:var(--accent)}
.form-control{padding:12px 16px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;font-family:inherit;outline:none;transition:border .2s,box-shadow .2s;background:#fafffe;color:var(--ink);width:100%}
.form-control:focus{border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px rgba(22,163,74,.1)}
select.form-control{cursor:pointer}
textarea.form-control{resize:vertical;min-height:100px}
.form-hint{font-size:11.5px;color:var(--muted);margin-top:4px}

/* ── SUBMIT ── */
.submit-row{display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-top:8px}
.btn-submit{background:var(--accent);color:#fff;font-weight:700;font-size:15px;padding:15px 40px;border:none;border-radius:50px;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:9px;transition:all .2s;letter-spacing:.02em}
.btn-submit:hover{background:var(--accent-d);transform:translateY(-2px);box-shadow:0 10px 30px rgba(22,163,74,.3)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none;box-shadow:none}
.submit-note{font-size:12px;color:var(--muted);line-height:1.6}

/* ── SUCCESS / ERROR ── */
.form-msg{padding:16px 20px;border-radius:12px;font-size:14px;font-weight:500;margin-bottom:20px;display:none}
.form-msg.success{background:#dcfce7;border:1px solid var(--border2);color:var(--accent-d)}
.form-msg.error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
.form-msg.show{display:block}
</style>
</head>
<body>

<nav class="enr-nav">
  <a href="index.php" class="nav-back"><i class="fas fa-arrow-left"></i> Back to Home</a>
  <a href="index.php" class="enr-logo">WANDER<span>VISTA</span></a>
  <a href="https://wa.me/<?= $wa ?>?text=Hi!+I%27d+like+to+enquire+about+a+tour." target="_blank" class="nav-wa">
    <i class="fab fa-whatsapp"></i>
    <span>WhatsApp Us</span>
  </a>
</nav>

<div class="enr-wrap">

  <!-- ASIDE -->
  <aside class="enr-aside">
    <div>
      <div class="aside-logo">WANDER<span>VISTA</span></div>
      <div class="aside-heading" style="margin-top:16px">Plan Your<br><span>Dream Trip</span></div>
      <p class="aside-sub" style="margin-top:10px">Fill the form in 3 minutes. We'll review your enquiry and send a personalised quote within 24 hours.</p>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px">
      <div class="trust-item">
        <div class="trust-icon"><i class="fas fa-clock"></i></div>
        <div><h5>24-Hour Response</h5><p>We review every enquiry personally and respond within 24 hours with a detailed quote.</p></div>
      </div>
      <div class="trust-item">
        <div class="trust-icon"><i class="fas fa-map"></i></div>
        <div><h5>Custom Itinerary</h5><p>Your trip is built from scratch — not a template. We plan around your dates and budget.</p></div>
      </div>
      <div class="trust-item">
        <div class="trust-icon"><i class="fas fa-shield-halved"></i></div>
        <div><h5>No Commitment Yet</h5><p>This form is just an enquiry. No payment until you're fully satisfied with the plan.</p></div>
      </div>
      <div class="trust-item">
        <div class="trust-icon"><i class="fas fa-headset"></i></div>
        <div><h5>24/7 Trip Support</h5><p>Once booked, our team is reachable on WhatsApp throughout your entire journey.</p></div>
      </div>
    </div>

    <a href="https://wa.me/<?= $wa ?>?text=Hi!+I%27d+like+to+enquire+about+a+tour." target="_blank" class="wa-aside">
      <i class="fab fa-whatsapp"></i>
      <div><span>Prefer WhatsApp?</span><small>Message us directly — we reply fast</small></div>
    </a>
  </aside>

  <!-- FORM AREA -->
  <div class="enr-form-area">
    <div class="form-section-title">Trip Enquiry Form</div>
    <div class="form-section-sub">All fields marked <span style="color:var(--accent)">*</span> are required.</div>

    <div id="formMsg" class="form-msg"></div>

    <form id="bookForm" novalidate>

      <div class="form-row">
        <div class="form-group">
          <label for="name">Full Name <span class="req">*</span></label>
          <input type="text" id="name" name="name" class="form-control" placeholder="Priya Sharma" required/>
        </div>
        <div class="form-group">
          <label for="phone">Phone / WhatsApp <span class="req">*</span></label>
          <input type="tel" id="phone" name="phone" class="form-control" placeholder="9876543210" required/>
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="priya@example.com"/>
        <div class="form-hint">Optional — we'll use WhatsApp/phone as primary contact.</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="destination">Destination <span class="req">*</span></label>
          <select id="destination" name="destination" class="form-control" required>
            <option value="" disabled selected>— Select destination —</option>
            <option value="Manali / Himachal Pradesh">Manali / Himachal Pradesh</option>
            <option value="Goa">Goa</option>
            <option value="Kerala">Kerala</option>
            <option value="Rajasthan">Rajasthan</option>
            <option value="Kashmir">Kashmir</option>
            <option value="Leh-Ladakh">Leh-Ladakh</option>
            <option value="Andaman & Nicobar">Andaman &amp; Nicobar</option>
            <option value="Uttarakhand">Uttarakhand</option>
            <option value="International Tour">International Tour</option>
            <option value="Custom / Other">Custom / Other</option>
          </select>
        </div>
        <div class="form-group">
          <label for="package">Preferred Package</label>
          <select id="package" name="package" class="form-control">
            <option value="">— Not sure yet —</option>
            <option value="budget">Budget (₹12,999/person)</option>
            <option value="classic">Classic (₹24,999/person)</option>
            <option value="luxury">Luxury (₹49,999/person)</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="travel_date">Preferred Travel Date</label>
          <input type="date" id="travel_date" name="travel_date" class="form-control"
                 min="<?= date('Y-m-d', strtotime('+1 day')) ?>"/>
        </div>
        <div class="form-group">
          <label for="pax">Number of Travelers <span class="req">*</span></label>
          <select id="pax" name="pax" class="form-control" required>
            <option value="1">1 — Solo</option>
            <option value="2" selected>2 — Couple</option>
            <option value="3">3 travelers</option>
            <option value="4">4 travelers</option>
            <option value="5">5 travelers</option>
            <option value="6">6 travelers</option>
            <option value="8">8 travelers</option>
            <option value="10">10+ travelers</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="message">Special Requirements / Message</label>
        <textarea id="message" name="message" class="form-control" placeholder="E.g. honeymoon trip, vegetarian meals preferred, need airport pickup, specific dates…"></textarea>
        <div class="form-hint">Tell us anything that'll help us plan the perfect trip for you.</div>
      </div>

      <div class="submit-row">
        <button type="submit" class="btn-submit" id="submitBtn">
          <i class="fas fa-paper-plane"></i> Send Enquiry
        </button>
        <p class="submit-note">No commitment required. We'll send you a personalised quote before any payment.</p>
      </div>

    </form>
  </div><!-- /enr-form-area -->

</div><!-- /enr-wrap -->

<script>
document.getElementById('bookForm').addEventListener('submit', function(e){
  e.preventDefault();
  var btn = document.getElementById('submitBtn');
  var msg = document.getElementById('formMsg');
  msg.className = 'form-msg';

  var name  = document.getElementById('name').value.trim();
  var phone = document.getElementById('phone').value.trim();
  var dest  = document.getElementById('destination').value;
  var pax   = document.getElementById('pax').value;

  if (!name || !phone || !dest || !pax) {
    msg.textContent = 'Please fill in all required fields.';
    msg.className = 'form-msg error show';
    msg.scrollIntoView({behavior:'smooth', block:'center'});
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';

  var fd = new FormData(this);
  fetch('api/submit.php', {method:'POST', body: fd})
    .then(function(r){ return r.json(); })
    .then(function(data){
      if (data.success) {
        document.getElementById('bookForm').style.display = 'none';
        msg.innerHTML = '<i class="fas fa-circle-check me-2"></i><strong>Enquiry sent!</strong> Our team will contact you within 24 hours. <br/><br/>'
          + '<a href="' + data.wa + '" target="_blank" style="color:var(--accent-d);font-weight:700"><i class="fab fa-whatsapp me-1"></i>Continue on WhatsApp →</a>';
        msg.className = 'form-msg success show';
        msg.scrollIntoView({behavior:'smooth', block:'center'});
      } else {
        msg.textContent = data.error || 'Something went wrong. Please try again.';
        msg.className = 'form-msg error show';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Enquiry';
      }
    })
    .catch(function(){
      msg.textContent = 'Network error. Please check your connection and try again.';
      msg.className = 'form-msg error show';
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Enquiry';
    });
});
</script>
</body>
</html>
