# Deploying Jawai Mumal on Hostinger (SSH + live sync with GitHub)

The site runs on plain PHP 8 — no build step. One setup script downloads it from GitHub, creates the database and
admin login, and installs a cron job that pulls new changes **every minute** (live sync).

## One-time setup
1. Connect: `ssh -p 65002 YOUR_USER@YOUR_SERVER_IP` (hPanel → Advanced → SSH Access; or use its *Browser terminal*).
2. Run (replace the password; choose the branch you want live — `main` once the work is merged):
   ```bash
   curl -fsSL https://raw.githubusercontent.com/mrsheoganj/jawai-mumal/main/deploy/server-setup.sh -o /tmp/jm-setup.sh
   JM_ADMIN_PASS='YOUR-ADMIN-PASSWORD' BRANCH=main bash /tmp/jm-setup.sh
   ```
   It auto-detects `~/domains/jawaimumal.com/public_html`. Use `SITE_DIR=/path/to/public_html` if yours differs.
3. Open `https://jawaimumal.com/admin/login.php` — user `admin`, the password you chose.

The setup script puts the site in **Coming soon** mode by default, launching **6 Nov 2026, 12:00 AM India time** (goes live by itself at that moment). Change it any time in Admin → *Site mode*; what you save there always wins. To change the default for a fresh install: `JM_LAUNCH_AT=2026-11-10T09:30 JM_MODE=coming_soon bash /tmp/jm-setup.sh`.

## What lives where
- **Code**: GitHub. Every push to the chosen branch goes live within a minute (`deploy/sync.sh`, log in `~/jm-sync.log`).
- **Database**: a SQLite file in `data/` on the server (no MySQL setup needed). It, the photos in `assets/uploads/` and
  `config.local.php` are *never* overwritten by a sync. To use MySQL instead, create a database in hPanel and run the
  setup script with `JM_DB_NAME`, `JM_DB_USER`, `JM_DB_PASS` (and `JM_DB_HOST` if not `localhost`).
- **Secrets**: only in `config.local.php` on the server (git-ignored). Never commit passwords to this public repository.

## Everyday use
- Change content (text, photos, prices, slides, modes) in the admin panel — it is stored on the server.
- Change code/design: commit to the live branch on GitHub and wait a minute.
- Switch the live branch later: `echo main > ~/domains/jawaimumal.com/public_html/.git/jm-branch`.
- Backups: download `data/jawai_mumal.sqlite` and `assets/uploads/` (File Manager or `scp -P 65002`).
- Enable free SSL in hPanel and force HTTPS.
