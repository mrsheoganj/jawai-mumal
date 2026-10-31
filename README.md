# Jawai Mumal — Hotel · Restaurant · Safari

Website and admin panel for **Jawai Mumal**, Bera village, Bera–Bali Road, Pali, Rajasthan — jawaimumal.com
Owners: Nilesh Singh Chouhan (+91 63679 68353) · Devendra Singh (+91 95113 09522)

PHP 8 + MySQL (falls back to SQLite automatically). No build step.

## What is on the site
Home (hero, booking & enquiry panel, about, rooms, safari, restaurant, Explore Jawai, events, gallery, FAQ, map) ·
Rooms & room detail · Restaurant menu · Safari packages · Explore Jawai guide · Gallery with lightbox · Contact.
The booking panel has five tabs: **Stay, Table, Safari, Party & Events, Enquiry**. Requests land in the admin panel.
The home banner is a gentle slideshow (1 to many slides) with a “Now Open” badge. Booking requests are confirmed personally by phone/WhatsApp; no online payment.

## Look & feel
Mobile gets an app-style experience (bottom tab bar, slide-in menu, swipe carousels, 2-column forms). Animations: hero slideshow, word-by-word headings, count-up numbers, scroll progress, an animated Jawai landscape (day/night loop, leopard, flamingos, birds), and an animated route map. All motion respects *reduce motion* settings.

## Admin panel — `/admin/login.php`
First login: `admin` / `admin123` → **change it immediately** in *My account*.
Everything is editable: bookings (status, notes, CSV export), **home banner slides**, rooms (price, photos, amenities), restaurant menu,
safari packages, Explore Jawai places, gallery uploads, guest reviews, FAQs, **Page text & images** (every heading, paragraph and
picture on every page — found automatically) and *Contact, logo & rules* (phone numbers, address, **hotel latitude/longitude**, GST %, time slots, logo) and **Distances & travel** (airports, stations, cities with km, time and optional coordinates — the map, “Get directions” and every Directions link update automatically).

The restaurant is **100% vegetarian** (Rajasthani village food, tandoor, café). Safaris are Jawai Mumal's own, in open 4x4 jeeps at dawn and dusk.

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
Add editable text anywhere in a template with `<?= et('page.section.key', 'Default text') ?>` or an image with `imgu('page.key', 'assets/images/x.jpg')` — it appears in *Admin → Page text & images* automatically. Structured settings (phones, rules) live in `includes/settings_schema.php`.
Images are free stock photos used as placeholders; replace them with your own from the admin panel.
