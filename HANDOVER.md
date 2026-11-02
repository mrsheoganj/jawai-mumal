# Jawai Mumal – Handover guide

Live site: https://jawaimumal.com · Admin: https://jawaimumal.com/admin-jm/login.php
Code: https://github.com/mrsheoganj/jawai-mumal (branch `main` only)

No passwords are stored in this repository. They live on the server (`config.local.php`, the database) and with the owner.

## 1. How the site is updated
1. Changes are committed and pushed to the `main` branch on GitHub.
2. Hostinger pulls `main` (hPanel → Advanced → Git → Deploy, or the auto-deploy webhook, or Admin → *Update from GitHub*).
3. Your database, uploaded photos and `config.local.php` are never overwritten by an update.

## 2. Admin panel (what each menu does)
Bookings & enquiries (status, notes, confirmation email, CSV export) · Rooms · Menu · Safaris · Explore Jawai places · Gallery · Journal (blog) · FAQs · Testimonials · Home banner slides · Page text & images · Site content · SEO · Email (SMTP) · Site mode (Coming soon / Live / Maintenance) · Go-live cleanup · My account.

**Forgot the admin password?** Use *Forgot your password?* on the sign-in page. A one-hour reset link is emailed to the admin email (info@jawaimumal.com). Change that address under *My account*.

## 3. Email – how it works
- Mailbox used for sending: `booking@jawaimumal.com` (Hostinger SMTP `smtp.hostinger.com`, port 465, SSL). Its password is saved **encrypted in the database** (Admin → Email (SMTP)); change it there if the mailbox password changes.
- Every new booking or enquiry: one alert to `info@jawaimumal.com` and one to `booking@jawaimumal.com`; the guest receives an acknowledgement from `booking@jawaimumal.com` (replies go to the same address).
- *Bookings → Send confirmation* emails the guest a confirmed booking.
- Admin → Email (SMTP) has a *Send test email* button and a log of every message sent or failed.

## 4. Before going live (owner to confirm / provide)
| # | What | Where to enter it |
|---|------|-------------------|
| 1 | Real photos of the three room types (and the restaurant, if available) | Rooms, Gallery |
| 2 | Final room rates, sizes, capacities; GST rates (ask the accountant) | Rooms, Site content → Booking rules |
| 3 | Safari details: timings, vehicles, permitted routes, any permit/forest-department rules | Safaris |
| 4 | Final menu items and prices | Menu |
| 5 | Instagram and Facebook page links | Site content → Social links |
| 6 | Exact Google Maps pin (latitude and longitude) and the Google Business Profile link | Site content → Contact |
| 7 | Phone and WhatsApp numbers, reception hours – confirm they are correct | Site content → Contact |
| 8 | Real guest reviews (the only sample review is hidden) | Testimonials |
| 9 | Bank/UPI or payment instructions if you want them shown to guests | Policies text (Page text & images) |
| 10 | Change the admin password, and rotate the booking mailbox password | My account · Email (SMTP) |
| 11 | Launch: the site shows *Coming soon* until 6 Nov 2026 12:00 AM IST, then opens by itself | Site mode |

## 5. Photos
All photographs of Jawai, its leopards, people and food come from Wikimedia Commons under open licences and are credited at `/credits` (linked in the footer). Keep that page. Photos of the hotel exterior are the hotel's own. Room photos are representative until real ones are uploaded.

## 6. Routine care
- Check *Bookings* daily; answer on WhatsApp or email.
- Admin → Email (SMTP) → log: look for *failed* rows.
- Back up the database monthly (hPanel → Databases → phpMyAdmin → Export) and the `assets/uploads` folder (File Manager → compress → download).
- Keep PHP at 8.2 or newer in hPanel.
