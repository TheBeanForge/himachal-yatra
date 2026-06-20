<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
if ($_SESSION['admin_user']['role'] !== 'superadmin') { header('Location: dashboard.php'); exit; }

require_once '../includes/vars.php';
$users   = $conn->query('SELECT id, username, full_name, role, created_at FROM admin_users ORDER BY id ASC')->fetch_all(MYSQLI_ASSOC);
$conn->close();

$self_id    = (int)$_SESSION['admin_user']['id'];
$page_title = 'User Management';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>User Management — Himachal Safar Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
.add-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:28px}
.add-card h3{font-family:'Poppins',sans-serif;font-size:16px;font-weight:800;margin-bottom:20px;color:var(--ink)}
.user-form{display:grid;grid-template-columns:1fr 1fr 1fr 150px auto;gap:12px;align-items:end}
.form-field label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:6px}
.form-field input,.form-field select{height:42px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;width:100%;background:var(--surface-2);color:var(--ink);transition:border .2s}
.form-field input:focus,.form-field select:focus{border-color:var(--accent);background:var(--surface-2)}
.btn-add{height:42px;padding:0 20px;background:var(--accent);color:#0d0d14;border:none;border-radius:8px;font:700 13px 'Inter',sans-serif;cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;transition:background .2s}
.btn-add:hover{background:var(--accent-d)}
.flash{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-top:14px}
.flash.ok{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.25)}
.flash.err{background:rgba(185,28,28,.12);color:#fca5a5;border:1px solid rgba(185,28,28,.25)}
.role-select{font-size:12px;padding:5px 8px;border:1.5px solid var(--border);border-radius:6px;outline:none;font-family:inherit;cursor:pointer;background:var(--surface-2);color:var(--ink)}
.badge-super{background:rgba(147,51,234,.15);color:#c084fc;border:1px solid rgba(147,51,234,.25);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.06em;text-transform:uppercase;display:inline-flex;align-items:center;gap:4px}
.btn-pw{background:rgba(214,199,161,.12);color:#D6C7A1;border:1px solid rgba(214,199,161,.20);border-radius:6px;padding:5px 11px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s}
.btn-pw:hover{background:rgba(214,199,161,.22)}
.btn-del-u{background:rgba(185,28,28,.12);color:#fca5a5;border:1px solid rgba(185,28,28,.20);border-radius:6px;padding:5px 11px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s}
.btn-del-u:hover{background:rgba(185,28,28,.25)}
.actions-cell{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
@media(max-width:900px){.user-form{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.user-form{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <!-- Add User -->
      <div class="add-card">
        <h3><i class="fas fa-user-plus me-2" style="color:var(--accent)"></i>Add New User</h3>
        <form id="addUserForm" class="user-form" autocomplete="off">
          <div class="form-field">
            <label>Username</label>
            <input type="text" name="username" placeholder="e.g. rahul_ops" required pattern="[a-z0-9_]{3,30}" title="3–30 lowercase letters, numbers or underscore">
          </div>
          <div class="form-field">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="e.g. Rahul Sharma" required>
          </div>
          <div class="form-field">
            <label>Password</label>
            <input type="password" name="password" placeholder="Min. 6 characters" required minlength="6">
          </div>
          <div class="form-field">
            <label>Role</label>
            <select name="role" required>
              <option value="admin">Admin</option>
              <option value="staff">Staff</option>
            </select>
          </div>
          <div class="form-field">
            <label>&nbsp;</label>
            <button type="submit" class="btn-add"><i class="fas fa-plus"></i> Add User</button>
          </div>
        </form>
        <div id="addMsg"></div>
      </div>

      <!-- Users Table -->
      <div class="table-wrap">
        <div style="overflow-x:auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Full Name</th>
              <th>Role</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersBody">
          <?php foreach ($users as $u): ?>
          <tr id="user-<?= $u['id'] ?>">
            <td style="color:var(--muted);font-size:12px"><?= $u['id'] ?></td>
            <td><div class="td-name"><?= htmlspecialchars($u['username']) ?></div></td>
            <td style="font-size:13.5px"><?= htmlspecialchars($u['full_name']) ?></td>
            <td>
              <?php if ($u['role'] === 'superadmin'): ?>
              <span class="badge-super"><i class="fas fa-crown"></i> Superadmin</span>
              <?php else: ?>
              <select class="role-select" data-id="<?= $u['id'] ?>" onchange="changeRole(this)">
                <option value="admin"  <?= $u['role']==='admin' ?'selected':'' ?>>Admin</option>
                <option value="staff"  <?= $u['role']==='staff' ?'selected':'' ?>>Staff</option>
              </select>
              <?php endif; ?>
            </td>
            <td style="font-size:12px;color:var(--muted)"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            <td>
              <?php if ($u['role'] === 'superadmin'): ?>
              <span style="font-size:12px;color:var(--muted)"><i class="fas fa-lock"></i> Protected</span>
              <?php else: ?>
              <div class="actions-cell">
                <button class="btn-pw" onclick="resetPassword(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>')">
                  <i class="fas fa-key"></i> Reset PW
                </button>
                <button class="btn-del-u" onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>')">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}

const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const csrfHeader = { 'X-CSRF-Token': CSRF };

document.getElementById('addUserForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = e.target.querySelector('.btn-add');
  const msg = document.getElementById('addMsg');
  btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
  msg.innerHTML = '';
  try {
    const res  = await fetch('../api/add_user.php', { method: 'POST', body: new FormData(e.target), headers: csrfHeader });
    const data = await res.json();
    if (data.ok) {
      msg.innerHTML = '<div class="flash ok"><i class="fas fa-check-circle me-1"></i> User added. Refreshing…</div>';
      e.target.reset();
      setTimeout(() => location.reload(), 1200);
    } else {
      msg.innerHTML = `<div class="flash err"><i class="fas fa-circle-exclamation me-1"></i> ${data.error || 'Failed'}</div>`;
    }
  } catch { msg.innerHTML = '<div class="flash err">Network error.</div>'; }
  btn.disabled = false; btn.innerHTML = '<i class="fas fa-plus"></i> Add User';
});

async function changeRole(sel) {
  sel.disabled = true;
  const fd = new FormData();
  fd.append('id', sel.dataset.id); fd.append('action', 'role'); fd.append('role', sel.value);
  try {
    const data = await fetch('../api/edit_user.php', { method: 'POST', body: fd, headers: csrfHeader }).then(r => r.json());
    if (!data.ok) { alert(data.error || 'Failed to update role.'); location.reload(); }
  } catch { alert('Network error.'); }
  sel.disabled = false;
}

async function resetPassword(id, username) {
  const pw = prompt(`New password for "${username}" (min. 6 chars):`);
  if (!pw) return;
  if (pw.length < 6) { alert('Password must be at least 6 characters.'); return; }
  const fd = new FormData();
  fd.append('id', id); fd.append('action', 'password'); fd.append('password', pw);
  const data = await fetch('../api/edit_user.php', { method: 'POST', body: fd, headers: csrfHeader }).then(r => r.json());
  alert(data.ok ? `Password for "${username}" has been reset.` : (data.error || 'Failed to reset password.'));
}

async function deleteUser(id, username) {
  if (!confirm(`Delete user "${username}"? This cannot be undone.`)) return;
  const fd = new FormData();
  fd.append('id', id);
  const data = await fetch('../api/delete_user.php', { method: 'POST', body: fd, headers: csrfHeader }).then(r => r.json());
  if (data.ok) { document.getElementById('user-' + id)?.remove(); }
  else { alert(data.error || 'Could not delete user.'); }
}
</script>
</body>
</html>
