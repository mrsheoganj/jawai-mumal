# Jawai Mumal — Hotel · Restaurant · Safari

Website and admin panel for **Jawai Mumal**, Bera village, Bera–Bali Road, Pali, Rajasthan — jawaimumal.com
Owners: Nilesh Singh Chouhan (+91 63679 68353) · Devendra Singh (+91 95113 09522)

PHP 8 + MySQL (falls back to SQLite automatically). No build step.

## What is on the site
Home (hero, booking & enquiry panel, about, rooms, safari, restaurant, Explore Jawai, events, gallery, FAQ, map) ·
Rooms & room detail · Restaurant menu · Safari packages · Explore Jawai guide · Gallery with lightbox · Contact.
The booking panel has five tabs: **Stay, Table, Safari, Party & Events, Enquiry**. Requests land in the admin panel.
While `Opening Soon` mode is on (default), the site collects *pre-booking requests* only.

## Admin panel — `/admin/login.php`
First login: `admin` / `admin123` → **change it immediately** in *My account*.
Everything is editable: bookings (status, notes, CSV export), rooms (price, photos, amenities), restaurant menu, safari packages,
Explore Jawai places, gallery uploads, guest reviews, FAQs, and *Site content & logo* (every heading, phone number, address,
GST %, time slots, banner photo, logo). Dashboard has a one-click switch from *Opening Soon* to *Open*.

Rooms, prices, safari packages and menu items shipped with the site are **demo content** — replace them from the admin panel.
Guest reviews are intentionally hidden until you add real ones.

## Run locally
```bash
php -S 127.0.0.1:8000
```
Open http://127.0.0.1:8000 — tables and demo content are created on first request (SQLite in `data/`).

## Deploy (Hostinger)
See [README_HOSTINGER.md](README_HOSTINGER.md).

## Project layout
```
index.php rooms.php room.php restaurant.php safari.php jawai.php gallery.php contact.php book.php
includes/   db.php (schema + seed), functions.php, settings_schema.php (all editable text), icons.php, header/footer, booking_panel.php
admin/      login, dashboard, bookings, rooms, menu, safaris, attractions, gallery, testimonials, faqs, content, account
assets/     css/, js/, brand/ (logo, favicon), images/, uploads/ (admin uploads)
```
Add a new editable text by adding one line to `includes/settings_schema.php` — it appears in *Site content* automatically.
Images are free stock photos used as placeholders; replace them with your own from the admin panel.
