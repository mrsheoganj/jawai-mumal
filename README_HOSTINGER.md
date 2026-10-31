# Deploying Jawai Mumal on Hostinger

1. **Upload** all files to `public_html` (File Manager or Git deploy). Keep the `.htaccess`.
2. **Database (recommended):** hPanel → Databases → create a MySQL database + user.
   Create `config.local.php` next to `config.php`:
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'u123456789_jawai');
   define('DB_PASS', 'your-password');
   define('DB_NAME', 'u123456789_jawai');
   ```
   Tables and demo content are created automatically on the first visit — no SQL import needed.
   (Without it the site still works using a SQLite file in `data/`, but MySQL is better for production.)
3. **Permissions:** `assets/uploads/` and `data/` must be writable (755/775).
4. Visit `https://jawaimumal.com/admin/login.php`, sign in with `admin` / `admin123` and **change the password** (My account).
5. In *Contact, logo & rules*, set the email, Instagram/Facebook links, and replace demo rooms/prices/photos.
6. Enable free SSL in hPanel (force HTTPS).

Backups: download `data/` (if SQLite) or export the MySQL database from phpMyAdmin, plus `assets/uploads/`.
