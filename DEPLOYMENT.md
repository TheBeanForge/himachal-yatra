# Deploying Himachal Safar — safe-update checklist

Follow this whenever you push a new version to the live server.
The golden rule: **code is replaceable, data is not.** Data lives in two places
only — the MySQL database and the `uploads/` folder — and neither should ever
be overwritten by a deploy.

---

## What is DATA (never touch on the server)

| Where | What lives there |
|---|---|
| MySQL database | enquiries/leads, reviews, photos records, packages, vehicles, destinations, pricing, settings (phone/WhatsApp/social links), admin users, subscribers, audit log |
| `uploads/` folder | every photo uploaded through the admin (destination photos, package photos, review photos) |

Deploying files **never touches MySQL** — the database is a separate service.
The only database risk is running an import by hand; see step 4.

## What is CODE (safe to replace)

Everything else: `*.php`, `style.css`, `script.js`, `assets/` (logo, fonts,
placeholder art), `admin/`, `api/`, `includes/`, `.htaccess`.

---

## Step-by-step (cPanel / FTP upload)

1. **Backup first (2 minutes).**
   - phpMyAdmin → your database → Export → Quick → Go. Save the `.sql` file.
   - Download the server's `uploads/` folder (or at least note it exists).
   - Download the server's `includes/vars.php` (it holds the LIVE DB password).

2. **Upload the new code — but skip two things:**
   - **Do NOT upload or delete the `uploads/` folder.** Upload everything else.
     If your FTP client asks about overwriting `uploads/`, say no/skip.
   - **Do NOT overwrite `includes/vars.php` blindly.** Upload it, then re-enter
     the live DB credentials (DB_HOST / DB_USER / DB_PASS / DB_NAME) at the top
     of the file — they are different from the local XAMPP ones.

3. **Do NOT re-import `database.sql`.** It exists for brand-new installs.
   (It is written defensively — `CREATE TABLE IF NOT EXISTS` / `INSERT IGNORE` —
   so even a mistaken import will not delete rows, but there is no reason to run
   it on a live database.)

4. **Run the migrations once** so the live DB gets any new columns/tables:
   - Easiest: just log into the admin and open **Enquiries** and **Tour
     Packages** once — those pages self-migrate missing columns silently.
   - The newsletter table creates itself on the first footer signup (or when
     you open Admin → Subscribers).
   - Optional belt-and-braces: run `php db/setup_pricing.php` from SSH if your
     host provides it (it is idempotent — safe to run repeatedly).

5. **Verify (1 minute):**
   - Homepage loads with the new logo in the tab and navbar.
   - A destination page still shows its uploaded photos (proves `uploads/` intact).
   - Admin → Enquiries lists your existing leads (proves DB intact).
   - Admin → Settings still shows your real phone/WhatsApp (settings live in DB).

## Step-by-step (git pull on the server)

If the server is a git checkout, `git pull` is already safe for data:
`uploads/*` is git-ignored (only its `.htaccess` is tracked), so pulls never
delete uploaded photos. After pulling, do steps 3–5 above. The one caution is
`includes/vars.php`: if the server has live credentials committed-over locally,
`git pull` may conflict — keep the server's version of the credential lines.

---

## FAQ

**Will the new version delete my enquiries/reviews/settings?**
No. Deploying files cannot touch the database. All admin content survives.

**Will the new logo appear everywhere automatically?**
Yes — the logo ships as files (`assets/logo-icon.svg`, `assets/favicon.svg`,
`assets/logo.svg`, `assets/brand/…`) referenced by every page, the admin, the
quote popup and the browser tab. Uploading the code is enough. If an old logo
lingers in the browser tab, hard-refresh (Ctrl+F5) — favicons cache hard.

**What about photos I uploaded through the admin on the live site?**
They live in `uploads/` on the server and in the `photos` DB table. As long as
you skip `uploads/` during upload (step 2), they are untouched.
