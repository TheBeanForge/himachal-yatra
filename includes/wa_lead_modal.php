<?php
/**
 * Pre-chat WhatsApp lead popup.
 * Included once from includes/foot.php so it is available on every page.
 * Any element with [data-wa-lead] opens this; it captures name + phone,
 * saves an identified lead via api/log_whatsapp_lead.php, then opens WhatsApp
 * prefilled with the visitor's name. Repeat visitors skip straight to chat.
 */
$__waCsrf = $_SESSION['lead_form_token'] ?? '';
?>
<div class="wl-modal" id="wlModal" aria-hidden="true">
  <div class="wl-backdrop" data-wl-close></div>
  <div class="wl-dialog" role="dialog" aria-modal="true" aria-label="Start a WhatsApp chat">
    <button type="button" class="wl-x" data-wl-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>

    <div class="wl-head">
      <div class="wl-head-icon"><i class="fa-brands fa-whatsapp"></i></div>
      <div>
        <h3 class="wl-title">Chat with our travel desk</h3>
        <p class="wl-sub">Share your details and we'll open WhatsApp with your trip context ready.</p>
      </div>
    </div>

    <form id="wlForm" novalidate autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= h($__waCsrf) ?>">
      <input type="hidden" name="source" id="wlSource" value="other">
      <input type="text" name="website" tabindex="-1" style="display:none" autocomplete="off" aria-hidden="true">

      <div class="wl-field">
        <label class="wl-label" for="wlName">Full Name <span>*</span></label>
        <input type="text" id="wlName" name="customer_name" class="wl-input" placeholder="Your full name" maxlength="100" autocomplete="name">
      </div>

      <div class="wl-field">
        <label class="wl-label" for="wlPhone">Phone Number <span>*</span></label>
        <div class="wl-tel">
          <span class="wl-tel-pre"><img src="https://flagcdn.com/w20/in.png" width="18" height="13" alt="IN" loading="lazy"> +91</span>
          <input type="tel" id="wlPhone" name="mobile" class="wl-input wl-tel-inp" placeholder="98765 43210" maxlength="15" inputmode="numeric" autocomplete="tel">
        </div>
      </div>

      <div class="wl-field">
        <label class="wl-label" for="wlDest">Destination <span class="wl-opt">(optional)</span></label>
        <select id="wlDest" name="dest" class="wl-input">
          <option value="">Not sure yet</option>
          <option value="Shimla">Shimla</option>
          <option value="Manali">Manali</option>
          <option value="Dharamshala">Dharamshala</option>
          <option value="Dalhousie">Dalhousie</option>
          <option value="Spiti Valley">Spiti Valley</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="wl-err" id="wlErr" hidden><i class="fa-solid fa-circle-exclamation"></i> <span id="wlErrMsg"></span></div>

      <button type="submit" class="wl-btn" id="wlSubmit">
        <span class="wl-btn-label"><i class="fa-brands fa-whatsapp"></i> Start WhatsApp Chat</span>
        <span class="wl-btn-spin" hidden><i class="fa-solid fa-spinner fa-spin"></i> Opening…</span>
      </button>
      <p class="wl-note"><i class="fa-solid fa-lock"></i> We only use this to follow up on your enquiry.</p>
    </form>
  </div>
</div>

<style>
.wl-modal { position: fixed; inset: 0; z-index: 1100; display: none; align-items: flex-start; justify-content: center; padding: 6vh 1rem; }
.wl-modal.open { display: flex; }
.wl-backdrop { position: fixed; inset: 0; background: rgba(8,10,14,.72); backdrop-filter: blur(5px); animation: wlFade .25s ease; }
.wl-dialog {
  position: relative; z-index: 1; margin: auto; width: 100%; max-width: 420px;
  background: var(--surf-1, #14181f); border: 1px solid var(--line, #2a2f3a);
  border-radius: 18px; padding: 1.5rem 1.5rem 1.25rem; box-shadow: 0 30px 80px rgba(0,0,0,.5);
  animation: wlPop .28s cubic-bezier(.2,.8,.2,1);
}
[data-theme="light"] .wl-dialog { background: #faf9f5; }
@keyframes wlFade { from { opacity: 0; } to { opacity: 1; } }
@keyframes wlPop  { from { opacity: 0; transform: translateY(16px) scale(.98); } to { opacity: 1; transform: none; } }
.wl-x {
  position: absolute; top: .8rem; right: .8rem; width: 34px; height: 34px; border-radius: 50%;
  border: 1px solid var(--line, #2a2f3a); background: var(--surf-3, #1c212b); color: var(--text-2, #c9cdd6);
  cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .15s, color .15s, transform .2s;
}
.wl-x:hover { background: #25D366; color: #06231a; transform: rotate(90deg); }
.wl-head { display: flex; align-items: flex-start; gap: .85rem; margin-bottom: 1.1rem; }
.wl-head-icon {
  width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0; font-size: 1.3rem;
  background: rgba(37,211,102,.14); color: #25D366; display: flex; align-items: center; justify-content: center;
}
.wl-title { font-family: 'Montserrat',sans-serif; font-size: 1.1rem; font-weight: 900; color: var(--charcoal, #f3f3f0); margin: 0 0 .2rem; }
.wl-sub { font-size: .8rem; color: var(--muted, #9aa0ab); margin: 0; line-height: 1.45; }
.wl-field { display: flex; flex-direction: column; gap: .3rem; margin-bottom: .85rem; }
.wl-label { font-size: .76rem; font-weight: 600; color: var(--text-2, #c9cdd6); }
.wl-label span { color: #25D366; }
.wl-label .wl-opt { color: var(--muted, #9aa0ab); font-weight: 400; }
.wl-input {
  height: 46px; padding: 0 .9rem; width: 100%; box-sizing: border-box;
  background: var(--surf-3, #1c212b); border: 1.5px solid var(--line, #2a2f3a); border-radius: 10px;
  color: var(--charcoal, #f3f3f0); font: .9rem 'Inter',sans-serif; transition: border-color .2s, box-shadow .2s;
}
.wl-input:focus { outline: none; border-color: #25D366; box-shadow: 0 0 0 3px rgba(37,211,102,.16); }
.wl-input::placeholder { color: var(--muted, #9aa0ab); }
select.wl-input { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%2394A3B8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .7rem center; background-size: 14px; padding-right: 2rem; }
.wl-tel { display: flex; align-items: stretch; border: 1.5px solid var(--line, #2a2f3a); border-radius: 10px; overflow: hidden; background: var(--surf-3, #1c212b); transition: border-color .2s, box-shadow .2s; }
.wl-tel:focus-within { border-color: #25D366; box-shadow: 0 0 0 3px rgba(37,211,102,.16); }
.wl-tel-pre { display: flex; align-items: center; gap: .35rem; padding: 0 .7rem; border-right: 1px solid var(--line, #2a2f3a); font-size: .8rem; font-weight: 600; color: var(--text-2, #c9cdd6); background: var(--surf-4, #232936); white-space: nowrap; }
.wl-tel-inp { flex: 1; border: none !important; border-radius: 0 !important; box-shadow: none !important; background: transparent !important; }
.wl-err { display: flex; align-items: flex-start; gap: .4rem; padding: .55rem .75rem; margin-bottom: .75rem; background: rgba(248,113,113,.08); border: 1px solid rgba(248,113,113,.22); border-radius: 9px; font-size: .78rem; color: #f87171; }
.wl-btn {
  width: 100%; height: 50px; border: none; border-radius: 11px; background: #25D366; color: #06231a;
  font: 800 .95rem 'Montserrat',sans-serif; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .5rem;
  box-shadow: 0 4px 20px rgba(37,211,102,.3); transition: transform .2s, box-shadow .2s, opacity .2s;
}
.wl-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,211,102,.45); }
.wl-btn:disabled { opacity: .65; cursor: not-allowed; transform: none; }
.wl-btn-label, .wl-btn-spin { display: inline-flex; align-items: center; gap: .45rem; }
.wl-btn i { font-size: 1.05rem; }
.wl-note { font-size: .7rem; color: var(--muted, #9aa0ab); text-align: center; margin: .7rem 0 0; display: flex; align-items: center; justify-content: center; gap: .3rem; }
.wl-note i { color: #25D366; }
</style>

<script>
(function(){
'use strict';
var WA_NUM = "<?= h($whatsappNumber) ?>";
var modal  = document.getElementById('wlModal');
if (!modal) return;
var form   = document.getElementById('wlForm');
var errBox = document.getElementById('wlErr');
var errMsg = document.getElementById('wlErrMsg');
var btn    = document.getElementById('wlSubmit');
var state  = { source: 'other', dest: '' };

function waUrl(name, dest){
  var msg = 'Hi Himachal Safar, this is ' + (name || 'there') + '. '
          + "I'd like to plan a Himachal trip" + (dest ? ' to ' + dest : '') + '.';
  return 'https://wa.me/' + WA_NUM + '?text=' + encodeURIComponent(msg);
}
function go(url){
  var w = window.open(url, '_blank');
  if (!w) window.location.href = url;   // popup blocked → same tab
}
function openModal(){ modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; setTimeout(function(){document.getElementById('wlName').focus();},40); }
function closeModal(){ modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }
function showErr(m){ errBox.hidden=false; errMsg.textContent=m; }
function busy(on){ btn.disabled=on; btn.querySelector('.wl-btn-label').hidden=on; btn.querySelector('.wl-btn-spin').hidden=!on; }

window.openWaLead = function(source, dest){
  source = source || 'other'; dest = dest || '';
  // Known visitor → straight to chat, no re-prompt.
  if (localStorage.getItem('waLeadDone')) {
    go(waUrl(localStorage.getItem('waLeadName') || '', dest));
    return;
  }
  state.source = source; state.dest = dest;
  document.getElementById('wlSource').value = source;
  var sel = document.getElementById('wlDest');
  if (dest) { for (var i=0;i<sel.options.length;i++){ if (sel.options[i].value===dest){ sel.selectedIndex=i; break; } } }
  errBox.hidden = true;
  openModal();
};

form.addEventListener('submit', async function(e){
  e.preventDefault();
  errBox.hidden = true;
  var name  = document.getElementById('wlName').value.trim();
  var phone = document.getElementById('wlPhone').value.replace(/\D/g,'');
  if (name.length < 3) { showErr('Please enter your full name.'); return; }
  if (!/^[6-9]\d{9}$/.test(phone)) { showErr('Enter a valid 10-digit mobile number.'); return; }
  var dest = document.getElementById('wlDest').value;
  busy(true);
  try {
    var res  = await fetch('api/log_whatsapp_lead.php', { method:'POST', body:new FormData(form), headers:{Accept:'application/json'} });
    var json = await res.json().catch(function(){return {};});
    if (res.ok && json.ok) {
      localStorage.setItem('waLeadDone','1');
      localStorage.setItem('waLeadName', name);
      closeModal();
      go(waUrl(name, dest));
    } else {
      showErr(json.error || 'Could not start the chat. Please try again.');
    }
  } catch (_) {
    showErr('Network error. Please try again.');
  } finally {
    busy(false);
  }
});

// Delegated opener: any [data-wa-lead] element opens the pre-chat popup.
document.addEventListener('click', function(e){
  var t = e.target.closest('[data-wa-lead]');
  if (!t) return;
  e.preventDefault();
  openWaLead(t.getAttribute('data-source') || 'other', t.getAttribute('data-dest') || '');
});
// Close: ✕, backdrop, Escape.
modal.querySelectorAll('[data-wl-close]').forEach(function(el){ el.addEventListener('click', closeModal); });
document.addEventListener('keydown', function(e){ if (e.key==='Escape' && modal.classList.contains('open')) closeModal(); });
})();
</script>
