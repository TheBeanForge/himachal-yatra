// Shrinks phone-camera photos in the browser before an admin form uploads
// them, so saves never trip the server's upload limits (camera photos are
// routinely 8-15 MB). Applies to any <input type="file" data-shrink>.
// If the image can't be decoded the original file is sent unchanged — the
// server still validates size and type either way.
(function () {
  'use strict';

  async function shrinkPhoto(file) {
    if (!file || (file.size <= 600 * 1024 && /^image\/(jpeg|png|webp)$/.test(file.type))) return null;
    let img = await createImageBitmap(file).catch(() => null);
    if (!img) {
      img = await new Promise((ok, err) => {
        const el = new Image();
        const url = URL.createObjectURL(file);
        el.onload = () => { URL.revokeObjectURL(url); ok(el); };
        el.onerror = () => { URL.revokeObjectURL(url); err(new Error('decode')); };
        el.src = url;
      });
    }
    const w = img.naturalWidth || img.width, h = img.naturalHeight || img.height;
    if (!w || !h) return null;
    const scale  = Math.min(1, 1600 / Math.max(w, h));
    const canvas = document.createElement('canvas');
    canvas.width  = Math.max(1, Math.round(w * scale));
    canvas.height = Math.max(1, Math.round(h * scale));
    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
    if (img.close) img.close();
    for (const q of [0.85, 0.75, 0.62]) {
      const blob = await new Promise((r) => canvas.toBlob(r, 'image/jpeg', q));
      if (blob && blob.size <= 1.5 * 1024 * 1024) {
        return new File([blob], 'photo.jpg', { type: 'image/jpeg' });
      }
    }
    return null;
  }

  // For pages that upload via fetch() instead of a plain form submit.
  window.shrinkAdminPhoto = shrinkPhoto;

  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.dataset.shrunk === '1') return;
    const inp  = form.querySelector('input[type="file"][data-shrink]');
    const file = inp && inp.files && inp.files[0];
    if (!file || (file.size <= 600 * 1024 && /^image\/(jpeg|png|webp)$/.test(file.type))) return;
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    if (btn) btn.disabled = true;
    shrinkPhoto(file).then((small) => {
      if (small) {
        const dt = new DataTransfer();
        dt.items.add(small);
        inp.files = dt.files;
      }
    }).catch(() => {}).finally(() => {
      form.dataset.shrunk = '1';   // guard against loops; form.submit() skips this handler anyway
      if (btn) btn.disabled = false;
      form.submit();
    });
  }, true);
})();
