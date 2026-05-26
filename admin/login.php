<?php
session_start();
if (!empty($_SESSION['admin_user'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../api/config.php';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $stmt = $conn->prepare('SELECT id, full_name, password_hash, role FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_user'] = [
                'id'        => $user['id'],
                'username'  => $username,
                'full_name' => $user['full_name'],
                'role'      => $user['role'],
            ];
            audit_log('login', 'Logged in');
            $conn->close();
            header('Location: dashboard.php');
            exit;
        }
    }
    $conn->close();
    $error = 'Incorrect username or password.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Admin Login — Himachal Yatra Travels</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<style>
*{box-sizing:border-box;margin:0;padding:0}

:root{
  --accent:  #c9a84c;
  --accent-d:#a8893d;
  --accent-l:#e0c46a;
  --bg:      #0d0d14;
  --surface: #161623;
  --border:  #2a2a3e;
  --ink:     #f5f0e8;
  --muted:   #9ca3af;
}

body{
  font-family:'Inter',sans-serif;
  min-height:100vh;
  display:flex;
  background:var(--bg);
}

/* ── LEFT PANEL ── */
.login-left{
  flex:0 0 58%;
  position:relative;
  overflow:hidden;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  padding:52px 60px;
}

.l-bg{
  position:absolute;inset:0;
  background:url('https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1600&q=90') center/cover no-repeat;
  animation:slowzoom 25s ease-in-out infinite alternate;
  will-change:transform;
}
.l-bg::after{
  content:'';
  position:absolute;inset:0;
  background:linear-gradient(160deg, rgba(0,0,0,.72) 0%, rgba(0,0,0,.90) 100%);
}
@keyframes slowzoom{
  from{transform:scale(1)}
  to{transform:scale(1.08)}
}

.l-top,.l-mid,.l-bot{position:relative;z-index:1;}

/* brand */
.l-brand{display:flex;align-items:center;gap:14px;}
.l-brand-icon{
  width:48px;height:48px;border-radius:13px;
  background:rgba(201,168,76,.18);
  border:1px solid rgba(201,168,76,.35);
  backdrop-filter:blur(10px);
  display:grid;place-items:center;
  font-size:21px;color:var(--accent);
  flex-shrink:0;
}
.l-brand-name{
  font-family:'Poppins',sans-serif;
  font-size:14px;font-weight:800;
  color:#fff;letter-spacing:.4px;line-height:1.2;
}
.l-brand-name span{color:var(--accent);}
.l-brand-tag{
  font-size:10px;font-weight:600;
  letter-spacing:.16em;text-transform:uppercase;
  color:rgba(255,255,255,.45);margin-top:2px;
}

/* headline */
.l-headline{
  font-family:'Poppins',sans-serif;
  font-size:48px;font-weight:900;
  color:#fff;line-height:1.08;
  letter-spacing:-.03em;
  margin-bottom:18px;
}
.l-headline span{
  color:transparent;
  -webkit-text-stroke:2px var(--accent);
}
.l-sub{
  font-size:15px;color:rgba(255,255,255,.68);
  line-height:1.65;max-width:400px;
}

/* feature pills */
.l-features{display:flex;flex-direction:column;gap:14px;}
.l-feature{display:flex;align-items:center;gap:14px;}
.l-feat-icon{
  width:40px;height:40px;border-radius:11px;
  background:rgba(201,168,76,.12);
  border:1px solid rgba(201,168,76,.22);
  display:grid;place-items:center;
  font-size:16px;color:var(--accent);
  flex-shrink:0;
}
.l-feat-text strong{display:block;font-size:13.5px;font-weight:600;color:#fff;}
.l-feat-text span{font-size:11.5px;color:rgba(255,255,255,.48);margin-top:1px;display:block;}

/* bottom strip */
.l-strip{
  display:flex;gap:32px;
  padding-top:28px;
  border-top:1px solid rgba(255,255,255,.10);
}
.l-stat .val{
  font-family:'Poppins',sans-serif;
  font-size:26px;font-weight:800;color:var(--accent);
  line-height:1;
}
.l-stat .lbl{font-size:11px;color:rgba(255,255,255,.45);margin-top:3px;}

/* mountain silhouette */
.l-mountain{
  position:absolute;bottom:0;left:0;right:0;z-index:1;
  pointer-events:none;
}

/* ── RIGHT PANEL ── */
.login-right{
  flex:1;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:48px 56px;
  background:var(--surface);
}
.lf-wrap{width:100%;max-width:380px;}

.lf-logo{
  width:58px;height:58px;border-radius:16px;
  background:rgba(201,168,76,.15);
  border:1px solid rgba(201,168,76,.30);
  display:grid;place-items:center;
  font-size:25px;color:var(--accent);
  margin-bottom:32px;
  box-shadow:0 8px 28px rgba(201,168,76,.18);
}
.lf-title{
  font-family:'Poppins',sans-serif;
  font-size:28px;font-weight:800;
  color:var(--ink);
  letter-spacing:-.03em;margin-bottom:6px;
}
.lf-desc{font-size:13.5px;color:var(--muted);line-height:1.55;margin-bottom:36px;}
.lf-desc strong{color:var(--accent);}

.form-group{margin-bottom:20px;}
.form-label{
  display:block;font-size:12px;font-weight:700;
  color:rgba(255,255,255,.45);margin-bottom:8px;
  letter-spacing:.04em;text-transform:uppercase;
}

.inp-wrap{position:relative;}
.inp-icon{
  position:absolute;left:14px;top:50%;transform:translateY(-50%);
  font-size:14px;color:var(--muted);pointer-events:none;
}
.inp{
  width:100%;height:50px;
  padding:0 42px 0 42px;
  border:1.5px solid var(--border);
  border-radius:11px;font-size:14px;
  font-family:'Inter',sans-serif;
  outline:none;transition:border .2s,box-shadow .2s;
  background:var(--bg);color:var(--ink);
}
.inp::placeholder{color:var(--muted);}
.inp:focus{
  border-color:var(--accent);background:var(--bg);
  box-shadow:0 0 0 3px rgba(201,168,76,.14);
}
.pw-toggle{
  position:absolute;right:13px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:var(--muted);cursor:pointer;
  font-size:15px;padding:4px;transition:color .2s;
}
.pw-toggle:hover{color:var(--accent);}

.err-box{
  background:rgba(239,68,68,.10);border:1px solid rgba(239,68,68,.28);
  color:#f87171;font-size:13px;padding:12px 14px;
  border-radius:9px;margin-bottom:22px;
  display:flex;align-items:center;gap:9px;font-weight:500;
}

.btn-login{
  width:100%;height:52px;
  background:linear-gradient(135deg,var(--accent),var(--accent-d));
  color:#0d0d14;font-weight:700;font-size:15px;
  border:none;border-radius:12px;cursor:pointer;
  font-family:'Inter',sans-serif;
  display:flex;align-items:center;justify-content:center;gap:9px;
  transition:transform .2s,box-shadow .2s;
  box-shadow:0 4px 16px rgba(201,168,76,.28);
  letter-spacing:.02em;
}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(201,168,76,.40);}
.btn-login:active{transform:translateY(0);}

.lf-divider{
  display:flex;align-items:center;gap:12px;
  margin:28px 0 20px;color:rgba(255,255,255,.22);font-size:12px;
}
.lf-divider::before,.lf-divider::after{content:'';flex:1;height:1px;background:var(--border);}

.lf-role-chips{display:flex;gap:10px;justify-content:center;}
.role-chip{
  display:flex;align-items:center;gap:7px;
  padding:8px 16px;border-radius:30px;
  border:1.5px solid var(--border);
  font-size:12px;font-weight:600;color:var(--muted);
  background:rgba(255,255,255,.04);
}
.role-chip i{font-size:13px;}
.role-chip.super{border-color:rgba(201,168,76,.35);color:var(--accent);background:rgba(201,168,76,.10);}
.role-chip.admin{border-color:rgba(59,130,246,.30);color:#60a5fa;background:rgba(59,130,246,.08);}
.role-chip.staff{border-color:rgba(255,255,255,.10);color:var(--muted);background:rgba(255,255,255,.04);}

.lf-footer{
  margin-top:36px;padding-top:24px;
  border-top:1px solid var(--border);
  font-size:11.5px;color:rgba(255,255,255,.28);text-align:center;
  line-height:1.6;
}

/* ── ANIMATIONS ── */
@keyframes fadeUp{
  from{opacity:0;transform:translateY(18px)}
  to{opacity:1;transform:translateY(0)}
}
.l-top{animation:fadeUp .7s .1s ease both;}
.l-mid{animation:fadeUp .7s .25s ease both;}
.l-bot{animation:fadeUp .7s .4s ease both;}
.lf-wrap{animation:fadeUp .7s .15s ease both;}

/* ── MOBILE ── */
@media(max-width:860px){
  body{flex-direction:column;}
  .login-left{
    flex:0 0 auto;padding:32px 28px 40px;
    justify-content:flex-start;gap:24px;
  }
  .l-headline{font-size:32px;}
  .l-sub{display:none;}
  .l-strip{display:none;}
  .l-mountain{display:none;}
  .login-right{padding:36px 28px;}
}
@media(max-width:480px){
  .login-left{padding:24px 20px 32px;}
  .login-right{padding:28px 20px;}
  .lf-role-chips{flex-wrap:wrap;}
}
</style>
</head>
<body>

<!-- ── LEFT PANEL ── -->
<div class="login-left">
  <div class="l-bg"></div>

  <!-- Mountain silhouette SVG -->
  <svg class="l-mountain" viewBox="0 0 1200 180" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,180 L0,120 L80,80 L160,110 L280,30 L380,90 L500,10 L600,70 L720,0 L840,60 L940,25 L1040,75 L1120,40 L1200,85 L1200,180 Z" fill="rgba(255,255,255,0.04)"/>
    <path d="M0,180 L0,145 L100,120 L200,140 L320,95 L430,125 L560,75 L660,115 L780,65 L900,105 L1000,80 L1100,115 L1200,95 L1200,180 Z" fill="rgba(255,255,255,0.06)"/>
  </svg>

  <div class="l-top">
    <div class="l-brand">
      <div class="l-brand-icon"><i class="fa-solid fa-mountain-sun"></i></div>
      <div>
        <div class="l-brand-name">HIMACHAL <span>YATRA</span></div>
        <div class="l-brand-tag">Travels · Admin Portal</div>
      </div>
    </div>
  </div>

  <div class="l-mid">
    <div class="l-headline">Manage your<br><span>Himachal</span><br>journeys.</div>
    <p class="l-sub">Everything you need to run your travel agency — leads, bookings, gallery, team, and a full audit trail — all in one place.</p>
  </div>

  <div class="l-bot">
    <div class="l-features">
      <div class="l-feature">
        <div class="l-feat-icon"><i class="fas fa-suitcase-rolling"></i></div>
        <div class="l-feat-text">
          <strong>Lead Management</strong>
          <span>Track, filter and contact every enquiry instantly</span>
        </div>
      </div>
      <div class="l-feature">
        <div class="l-feat-icon"><i class="fas fa-images"></i></div>
        <div class="l-feat-text">
          <strong>Photo Gallery</strong>
          <span>Upload real destination photos that appear on the site</span>
        </div>
      </div>
      <div class="l-feature">
        <div class="l-feat-icon"><i class="fas fa-clipboard-list"></i></div>
        <div class="l-feat-text">
          <strong>Audit Trail</strong>
          <span>Every action is logged — who did what and when</span>
        </div>
      </div>
    </div>

    <div class="l-strip">
      <div class="l-stat"><div class="val">5</div><div class="lbl">Destinations</div></div>
      <div class="l-stat"><div class="val">24/7</div><div class="lbl">Support</div></div>
      <div class="l-stat"><div class="val">100%</div><div class="lbl">Secure</div></div>
    </div>
  </div>
</div>

<!-- ── RIGHT PANEL ── -->
<div class="login-right">
  <div class="lf-wrap">

    <div class="lf-logo"><i class="fa-solid fa-mountain-sun"></i></div>
    <div class="lf-title">Welcome back</div>
    <div class="lf-desc">Sign in to your <strong>Himachal Yatra</strong> admin account to manage your agency.</div>

    <?php if ($error): ?>
    <div class="err-box"><i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label class="form-label">Username</label>
        <div class="inp-wrap">
          <i class="inp-icon fas fa-user"></i>
          <input class="inp" type="text" name="username" placeholder="Enter your username" autofocus required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="inp-wrap">
          <i class="inp-icon fas fa-lock"></i>
          <input class="inp" type="password" name="password" id="pwField" placeholder="Enter your password" required>
          <button type="button" class="pw-toggle" id="pwToggle" tabindex="-1" aria-label="Toggle password visibility">
            <i class="fas fa-eye" id="pwIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <i class="fas fa-right-to-bracket"></i> Sign In to Admin
      </button>
    </form>

    <div class="lf-divider">Access levels</div>
    <div class="lf-role-chips">
      <div class="role-chip super"><i class="fas fa-crown"></i> Superadmin</div>
      <div class="role-chip admin"><i class="fas fa-user-shield"></i> Admin</div>
      <div class="role-chip staff"><i class="fas fa-user"></i> Staff</div>
    </div>

    <div class="lf-footer">
      &copy; <?= date('Y') ?> Himachal Yatra Travels &nbsp;·&nbsp; Secure Admin Portal<br>
      Unauthorized access is strictly prohibited.
    </div>
  </div>
</div>

<script>
const pwField  = document.getElementById('pwField');
const pwToggle = document.getElementById('pwToggle');
const pwIcon   = document.getElementById('pwIcon');
pwToggle.addEventListener('click', () => {
  const show = pwField.type === 'password';
  pwField.type = show ? 'text' : 'password';
  pwIcon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
});
</script>
</body>
</html>
