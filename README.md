# Himachal Yatra Travels

PHP/MySQL tourism website for a Himachal Pradesh travel agency — public marketing site, lead capture form (DB + WhatsApp), and an admin panel for managing leads, photos, users, and audit log.

## Stack

- PHP 8.x (no framework — vanilla PHP + mysqli)
- MySQL 5.7+ / MariaDB
- Bootstrap 5.3 + Font Awesome 6 (CDN)
- Vanilla JS, no build step

## Local Setup (XAMPP)

1. Clone into `C:\xampp\htdocs\tourismsite` (or `<your-xampp>/htdocs/tourismsite`).
2. Start Apache and MySQL from the XAMPP control panel. This project assumes MySQL is on **port 3307** — adjust `api/config.php` if yours runs on 3306.
3. Import the schema:
   ```
   mysql -u root -P 3307 < database.sql
   ```
   Or open phpMyAdmin and run `database.sql`.
4. Copy `api/config.example.php` → `api/config.php` and edit the DB creds + agency WhatsApp number if needed. `api/config.php` is git-ignored.
5. Visit http://localhost/tourismsite/

## Admin

- URL: http://localhost/tourismsite/admin/login.php
- Default credentials: **admin / tour@123**
- **Change the default password immediately** after first login (User Management → Reset PW). The default is published in `database.sql` and known to everyone with repo access.

Roles:
- `superadmin` — full access, can manage users and view audit log
- `admin`, `staff` — leads + photo gallery only

## Project Layout

```
/                       public marketing site
  index.php             home (hero, routes, reviews, contact form, booking modal)
  {manali,shimla,...}.php   destination pages — load gallery photos from DB
  style.css             public-site styles (dark + gold theme)
  script.js             public-site behavior
  includes/
    vars.php            session + security headers + shared template vars
    nav.php / foot.php  shared header/footer
  api/
    config.php          DB connection + helpers (git-ignored)
    config.example.php  template — copy to config.php
    submit.php          POST endpoint for lead form
    {add,edit,delete}_user.php   superadmin endpoints
    {upload,delete}_photo.php    admin photo endpoints
    update_status.php   lead status changes
  admin/                admin SPA-style pages, share admin/assets/admin.css
  uploads/photos/       user-uploaded gallery photos (git-ignored)
  database.sql          schema + seed data
```

## Security Notes

- Public lead form: per-session CSRF token + honeypot field.
- Admin endpoints: session check + per-session CSRF token sent as `X-CSRF-Token` header.
- Photo upload: MIME-validated via `finfo`, restricted to JPG/PNG/WebP, 5 MB cap, PHP execution disabled in `uploads/` via `.htaccess`.
- DB credentials live only in `api/config.php` (git-ignored).
