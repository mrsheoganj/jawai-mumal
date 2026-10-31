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

## Site modes
Admin → **Site mode**: *Live*, *Coming soon* (public countdown to a date/time you choose, optional automatic go-live) or *Maintenance* (503 “back soon” page, optional back-by countdown). While a mode is on, share the **secret preview link** with clients so they can browse the full site; you stay signed in as admin and always see the real site. Fonts can be switched in *Contact, logo & rules* (Playfair Display + Inter by default).

## Communication
Every form has **Request booking** (saved in admin) and **Book on WhatsApp** (saved *and* opens WhatsApp with all details). There are no phone-call links anywhere: contact is WhatsApp only, shown as icon-only buttons (a floating one, one per owner, and on every form). In Admin → Bookings, one tap sends the guest a confirmation message on WhatsApp. WhatsApp cannot be started as a *voice call* from a web link, so we open the chat (or ask for a call-back) from where the call icon is one tap away.

## Real map & distances
The “How far is Jawai?” section uses OpenStreetMap: real driving routes from the hotel, true compass bearings and real kilometres. Set the hotel latitude/longitude in *Contact, logo & rules*; add places in *Distances & travel* and press **Calculate** to measure real road distance and drive time.

## Credits
Icons: [Lucide](https://lucide.dev) (ISC) and [Simple Icons](https://simpleicons.org) (CC0) brand logos. Fonts (self-hosted): Inter and Playfair Display (SIL OFL). Map data © OpenStreetMap contributors.

## Admin panel — `/admin/login.php`
Username `admin`. The password is never in this repository: put `define('DEFAULT_ADMIN_PASS', '…');` in `config.local.php` on the server, or open `data/first-login.txt` (created automatically with a random password) in hPanel → File Manager, sign in and change it under *My account*.
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
