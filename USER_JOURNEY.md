# Himachal Yatra Travels — Complete User Journey

---

## Table of Contents

1. [Site Overview](#site-overview)
2. [Public User Journey](#public-user-journey)
   - [Homepage](#homepage)
   - [Destination Pages](#destination-pages)
   - [Booking a Trip](#booking-a-trip)
   - [Submitting a Review](#submitting-a-review)
3. [Admin Journey](#admin-journey)
   - [Login](#admin-login)
   - [Lead Management](#lead-management)
   - [Review Moderation](#review-moderation)
   - [Photo Gallery](#photo-gallery-management)
   - [Pickup Locations](#pickup-locations)
   - [User Management](#user-management-superadmin)
   - [Audit Log](#audit-log-superadmin)
4. [Navigation Structure](#navigation-structure)
5. [Database Tables](#database-tables)
6. [API Endpoints](#api-endpoints)
7. [File Structure](#file-structure)
8. [Roles & Permissions](#roles--permissions)
9. [Security Features](#security-features)

---

## Site Overview

**Himachal Yatra Travels** is a premium cab and tour service based in Himachal Pradesh. The website serves two audiences:

- **Public visitors** — browse routes, explore destinations, request quotes, and submit reviews
- **Admin team** — manage incoming leads, moderate reviews, upload gallery photos, and handle team access

**Tech stack:** Vanilla PHP, MySQL, Bootstrap 5, Font Awesome, Vanilla JS (no frameworks)

---

## Public User Journey

### Homepage

**URL:** `index.php`

The homepage is the primary entry point. A visitor flows through these sections top to bottom:

#### 1. Hero Section
- Full-screen background with headline and tagline
- Two CTAs: **Get a Quote** (opens booking modal) and **Explore Routes** (scrolls to routes)
- Floating **WhatsApp button** visible throughout the page

#### 2. Stats Bar
Animated counters that trigger on scroll:
- 12,000+ Journeys Completed
- 200+ Vehicles in Fleet
- 40+ Destinations Covered
- 8+ Years of Experience

#### 3. Popular Routes
Grid of 6 signature routes, each showing:
- Route name and cover image
- Distance, duration, and starting price
- **Explore** button (links to destination page)
- **Request Quote** button (opens booking modal)

| Route | Highlight |
|-------|-----------|
| Delhi → Manali | Adventure hub, 570 km |
| Delhi → Shimla | Queen of Hills, 370 km |
| Chandigarh → Dharamshala | Little Lhasa, 250 km |
| Delhi → Dalhousie | Colonial charm, 555 km |
| Manali → Spiti | High-altitude circuit |
| Shimla → Kinnaur | Apple orchards & cliffs |

#### 4. Tour Packages
Three curated packages:
- **Shimla–Manali Honeymoon** (7 days)
- **Himachal Family Tour** (8 days)
- **Spiti Valley Adventure** (9 days)

Each card shows duration, highlights, and a **Request Quote** CTA.

#### 5. Fleet Showcase
Five vehicle options with photos, specs, and capacity:
- Swift Dzire (4 seats)
- Innova Hycross (6 seats)
- Innova Crysta (6 seats)
- Tempo Traveller 12-seater
- Tempo Traveller 17-seater

#### 6. Why Choose Us
Four trust pillars: experienced drivers, transparent pricing, 24/7 support, comfortable fleet.

#### 7. Customer Reviews
Horizontal scrollable carousel showing approved reviews with:
- Star rating (1–5)
- Review text
- Traveller name, city, and route taken
- Optional photo thumbnail

#### 8. Contact & Inquiry Form
Full inquiry form (also the main lead-capture form):
- Name, Phone, Email (optional)
- Pickup city (dropdown from DB)
- Destination, Travel date, Number of passengers
- Package preference (Budget / Classic / Luxury)
- Message
- Submit sends to `api/submit.php` and returns a **WhatsApp redirect** on success

#### 9. Footer
- Quick links: Home, Routes, Fleet, Packages, Reviews, Contact
- Destination links: Manali, Shimla, Dharamshala, Dalhousie, Spiti Valley
- Contact info: phone, WhatsApp, email, address

---

### Destination Pages

Five individual pages, one per destination:

| Page | Destination | Highlight |
|------|-------------|-----------|
| `manali.php` | Manali | 2,050m, adventure hub |
| `shimla.php` | Shimla | Colonial hill station |
| `dharamshala.php` | Dharamshala | Buddhist culture, McLeodganj |
| `dalhousie.php` | Dalhousie | Heritage architecture |
| `spiti.php` | Spiti Valley | High-altitude desert |

Each destination page includes:
- Breadcrumb: **Home > Destinations > [Name]**
- Hero with destination-specific imagery
- Key highlights and activities
- Photo gallery pulled from DB (`photos` table, filtered by destination)
- Booking CTA linking back to the contact form

---

### Booking a Trip

A visitor can request a quote through two paths:

**Path A — Booking Modal**
1. Click **Get a Quote** in hero or any **Request Quote** button
2. Modal opens with three service tabs:
   - Private Cab
   - Custom Tour
   - Group Travel
3. Fill in travel details and submit
4. On success: redirected to WhatsApp with pre-filled message

**Path B — Contact Form**
1. Scroll to Contact section or click **Contact** in nav
2. Fill in the full inquiry form
3. Submit → same WhatsApp redirect on success

**Rate limit:** 5 submissions per 15 minutes per session (honeypot field catches bots)

---

### Submitting a Review

1. Scroll to the Reviews section
2. Click **Write a Review**
3. Fill in:
   - Name and city
   - Route taken
   - Star rating (click to select 1–5)
   - Review text
   - Optional photo upload (JPEG/PNG/WebP, max 5 MB)
4. Submit → review saved with `status = pending`
5. Review appears publicly only after admin approval

---

## Admin Journey

### Admin Login

**URL:** `admin/login.php`

- Two-panel layout: branding on the left, login form on the right
- Enter username and password
- Incorrect credentials show an error message
- Successful login redirects to `admin/dashboard.php`
- All admin pages require an active session; unauthenticated access redirects to login

---

### Lead Management

**URL:** `admin/dashboard.php`  
**Access:** All roles (Staff, Admin, Superadmin)

The central hub for all incoming booking inquiries.

#### Stats Bar
Clickable filter cards:
- Total | New | Contacted | Confirmed | Cancelled

#### Lead Cards
Each lead shows:
- Traveller name, phone, email
- Pickup city → Destination
- Travel date and passenger count
- Message snippet
- Status badge (colour-coded)
- **Change Status** dropdown: New → Contacted → Confirmed → Cancelled
- **WhatsApp** button to message the lead directly

#### Search & Filter
- Search by name, phone, email, or destination
- Filter by status using the stats bar

---

### Review Moderation

**URL:** `admin/reviews.php`  
**Access:** All roles

Three tabs: **Pending** (amber) | **Approved** (green) | **Rejected** (red)

Each review card shows:
- Star rating and review text
- Traveller name, city, route
- Submission date
- Photo thumbnail (if uploaded)
- Action buttons: **Approve**, **Reject**, **Delete**

Approved reviews appear on the public homepage carousel.

---

### Photo Gallery Management

**URL:** `admin/photos.php`  
**Access:** All roles

#### Upload Photo
1. Select destination from dropdown
2. Add a caption
3. Choose image file
4. Submit → file saved to `uploads/photos/`, record inserted in DB

#### Manage Photos
- Grid view with thumbnails, captions, destination tags
- Filter by destination tab: Manali | Shimla | Dharamshala | Dalhousie | Spiti Valley | General
- **Reorder** — drag to rearrange; order saves instantly via AJAX
- **Delete** — removes file and DB record

**Limit:** Maximum 15 photos per destination

---

### Pickup Locations

**URL:** `admin/locations.php`  
**Access:** All roles

Manage the pickup city dropdown shown in the public booking form.

- **Add** a new city
- **Reorder** via drag-and-drop
- **Deactivate** a city (soft delete — hides from form but preserves data)
- Cannot delete the last remaining location

---

### User Management *(Superadmin)*

**URL:** `admin/users.php`  
**Access:** Superadmin only

#### Add User
- Username (3–30 chars, lowercase alphanumeric)
- Full name
- Password (min 6 chars, stored as bcrypt hash)
- Role: Admin or Staff

#### User Table
Shows all users with: ID, username, full name, role (icon + label), creation date

#### Actions Per User
- **Reset password**
- **Edit role**
- **Delete** (cannot delete own account or the superadmin)

**Roles:**
| Role | Icon | Permissions |
|------|------|-------------|
| Superadmin | Crown | All features + users + audit log |
| Admin | Shield | Leads, reviews, photos, locations |
| Staff | User | Leads, reviews, photos, locations |

---

### Audit Log *(Superadmin)*

**URL:** `admin/audit.php`  
**Access:** Superadmin only

Complete history of all admin actions.

**Columns:** ID | Timestamp | Username | Action | Details | IP Address

**Tracked actions** (colour-coded badges):
- `login` / `logout`
- `photo_upload` / `photo_delete`
- `status_change`
- `review_moderate` / `review_delete`
- `user_add` / `user_delete`
- `location_add` / `location_delete` / `location_reorder`

**Filters:** By username | By action type

---

## Navigation Structure

### Public Navigation Bar
```
Home | About | Routes | Packages | Fleet | Destinations ▾ | Reviews | Contact
                                              ├── Manali
                                              ├── Shimla
                                              ├── Dharamshala
                                              ├── Dalhousie
                                              └── Spiti Valley
```

### Admin Sidebar
```
Dashboard (Leads)
Reviews
Photo Gallery
Pickup Locations
Users          ← Superadmin only
Audit Log      ← Superadmin only
Logout
```

---

## Database Tables

| Table | Purpose |
|-------|---------|
| `bookings` | All lead/inquiry submissions |
| `reviews` | Customer reviews (pending → approved/rejected) |
| `photos` | Destination gallery images |
| `pickup_locations` | Pickup city options for the booking form |
| `admin_users` | Admin team accounts |
| `audit_log` | Complete admin action history |
| `settings` | Agency config (name, phone, WhatsApp, email) |

---

## API Endpoints

All endpoints are POST-only with CSRF protection.

### Public

| Endpoint | Action |
|----------|--------|
| `api/submit.php` | Submit booking inquiry (rate-limited, honeypot) |
| `api/submit_review.php` | Submit customer review with optional photo |

### Admin-Only *(require session + CSRF header)*

| Endpoint | Action |
|----------|--------|
| `api/upload_photo.php` | Upload gallery photo |
| `api/delete_photo.php` | Delete gallery photo |
| `api/reorder_photo.php` | Save new photo sort order |
| `api/moderate_review.php` | Approve or reject a review |
| `api/delete_review.php` | Delete a review |
| `api/update_status.php` | Change lead status |
| `api/add_user.php` | Create admin user (superadmin) |
| `api/edit_user.php` | Update admin user (superadmin) |
| `api/delete_user.php` | Delete admin user (superadmin) |

---

## File Structure

```
tourismsite/
├── index.php                   # Homepage
├── manali.php                  # Destination: Manali
├── shimla.php                  # Destination: Shimla
├── dharamshala.php             # Destination: Dharamshala
├── dalhousie.php               # Destination: Dalhousie
├── spiti.php                   # Destination: Spiti Valley
├── style.css                   # Public site styles
├── script.js                   # Public site JS (forms, modals, animations)
├── database.sql                # Schema + seed data
│
├── includes/
│   ├── vars.php                # Session init, security headers
│   ├── nav.php                 # Header navigation
│   └── foot.php                # Footer
│
├── api/
│   ├── config.php              # DB connection + helpers (git-ignored)
│   ├── config.example.php      # Config template
│   ├── submit.php
│   ├── submit_review.php
│   ├── upload_photo.php
│   ├── delete_photo.php
│   ├── reorder_photo.php
│   ├── moderate_review.php
│   ├── delete_review.php
│   ├── update_status.php
│   ├── add_user.php
│   ├── edit_user.php
│   └── delete_user.php
│
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── reviews.php
│   ├── photos.php
│   ├── users.php
│   ├── locations.php
│   ├── audit.php
│   ├── logout.php
│   ├── partials/
│   │   ├── sidebar.php
│   │   └── topbar.php
│   └── assets/
│       └── admin.css
│
├── assets/
│   ├── logo.svg
│   └── logo-icon.svg
│
└── uploads/
    ├── photos/                 # Gallery images (PHP execution blocked)
    ├── reviews/                # Review photos (PHP execution blocked)
    └── .htaccess               # Security: blocks script execution in uploads
```

---

## Roles & Permissions

| Feature | Staff | Admin | Superadmin |
|---------|:-----:|:-----:|:----------:|
| View & manage leads | ✓ | ✓ | ✓ |
| Moderate reviews | ✓ | ✓ | ✓ |
| Upload / manage photos | ✓ | ✓ | ✓ |
| Manage pickup locations | ✓ | ✓ | ✓ |
| Manage admin users | ✗ | ✗ | ✓ |
| View audit log | ✗ | ✗ | ✓ |

---

## Security Features

| Feature | Detail |
|---------|--------|
| CSRF protection | Per-session token on all forms and API calls |
| Password hashing | bcrypt via `PASSWORD_DEFAULT` |
| File upload validation | MIME type checked (JPEG/PNG/WebP only), 5 MB max |
| Rate limiting | 5 submissions per 15 min per session on public forms |
| Honeypot field | Hidden `website` field traps spam bots |
| Upload execution block | `.htaccess` prevents running scripts in `uploads/` |
| Session-gated admin | All admin routes redirect to login if unauthenticated |
| Audit trail | Every admin action logged with IP address |
