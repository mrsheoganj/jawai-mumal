# Deploying Jawai Mumal on Hostinger

## Recommended: Hostinger's built-in Git (no zip, no terminal)
1. hPanel → **Advanced → Git** → Repository `https://github.com/mrsheoganj/jawai-mumal.git`, branch `main`, install path = your site root (`public_html`) → **Create**, then **Deploy**.
2. For deploy-on-push, copy the *Auto deployment* webhook URL shown there into GitHub → repo **Settings → Webhooks**.
3. **First login**: nothing secret lives in GitHub, so open hPanel → File Manager → `data/first-login.txt` (a random admin password is created on the first visit), sign in at `/admin-jm/login.php`, change the password under *My account*, then delete that file.
   Or create your own password first: in File Manager add a file `config.local.php` next to `index.php` containing
   `<?php define('DEFAULT_ADMIN_PASS', 'your password');` — it is applied automatically and is never overwritten by deploys.
4. The site starts in **Coming soon** mode until **6 Nov 2026, 12:00 AM IST**, then goes live by itself. Change it any time in Admin → *Site mode*. Your database (`data/`), photos (`assets/uploads/`) and `config.local.php` are not in Git, so deploys never overwrite them.


## Automatic hosting with GitHub Actions (recommended)
Every push to `main` is copied to Hostinger by `.github/workflows/deploy.yml` over SFTP/SSH — nothing to run by hand.
1. GitHub repo → **Settings → Secrets and variables → Actions → New repository secret**, add: `SSH_HOST`, `SSH_PORT` (65002), `SSH_USER`, `SSH_PASSWORD` (from hPanel → Advanced → SSH Access). Optional: `DEPLOY_PATH` (default `domains/jawaimumal.com/public_html`) and `ADMIN_PASSWORD` (creates the server-only `config.local.php` — Coming soon until 6 Nov 2026 12:00 AM IST — on the very first deploy).
2. Run it: **Actions → Deploy to Hostinger → Run workflow**, or just push. The log shows `Deployed <commit>`.
3. The database (`data/`), photos (`assets/uploads/`) and `config.local.php` on the server are never overwritten.

## Alternative: no terminal needed (File Manager + built-in GitHub sync)
1. Build the package: `JM_ADMIN_PASS='your-password' BRANCH=main python3 deploy/build_package.py` → `dist/jawai-mumal-hosting.zip` (contains your private `config.local.php`; never commit it).
2. hPanel → Websites → *Manage* → **File Manager** → `public_html` → delete `default.php` → **Upload** the zip → right-click it → **Extract** (extract *here*) → delete the zip.
3. Open the site. It starts in the mode set in the package (Coming soon until the launch time). Admin: `/admin-jm/login.php`.
4. **Live sync**: Admin → *Update from GitHub*. The site checks GitHub every few minutes and updates itself; *Update now* does it instantly. Optional instant mode: add the GitHub webhook shown on that page.

The SSH route below does the same thing if your plan has a terminal.

# Deploying with SSH (live sync with GitHub)

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
3. Open `https://jawaimumal.com/admin-jm/login.php` — user `admin`, the password you chose.

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
