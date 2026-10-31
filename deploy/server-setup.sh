#!/bin/bash
# One-time setup of Jawai Mumal on Hostinger (run over SSH). Safe to run again.
#   JM_ADMIN_PASS='your-password' BRANCH=main bash server-setup.sh
# Optional: SITE_DIR=/home/USER/domains/jawaimumal.com/public_html   REPO_URL=...
#           JM_DB_HOST/JM_DB_NAME/JM_DB_USER/JM_DB_PASS to use MySQL instead of the built-in SQLite database.
set -euo pipefail
REPO_URL="${REPO_URL:-https://github.com/mrsheoganj/jawai-mumal.git}"
BRANCH="${BRANCH:-main}"
say() { printf '\n\033[1;33m==> %s\033[0m\n' "$*"; }

command -v git >/dev/null || { echo "git is not installed on this server."; exit 1; }
command -v php >/dev/null || { echo "php command not found."; exit 1; }

# --- where is the website folder?
if [ -z "${SITE_DIR:-}" ]; then
  if   [ -d "$HOME/domains/jawaimumal.com/public_html" ]; then SITE_DIR="$HOME/domains/jawaimumal.com/public_html"
  elif [ "$(ls -d "$HOME"/domains/*/public_html 2>/dev/null | wc -l)" = "1" ]; then SITE_DIR="$(ls -d "$HOME"/domains/*/public_html)"
  elif [ -d "$HOME/public_html" ]; then SITE_DIR="$HOME/public_html"
  else echo "Could not find public_html. Re-run with SITE_DIR=/full/path/to/public_html"; exit 1; fi
fi
say "Website folder: $SITE_DIR   |   branch: $BRANCH"
mkdir -p "$SITE_DIR"; cd "$SITE_DIR"

# --- admin password (never stored in the repository)
if [ -z "${JM_ADMIN_PASS:-}" ]; then read -r -s -p "Choose the admin password: " JM_ADMIN_PASS; echo; fi
[ ${#JM_ADMIN_PASS} -ge 8 ] || { echo "Password must be at least 8 characters."; exit 1; }

# --- get the code (keeps existing database / uploads / config)
say "Downloading the website from GitHub"
if [ ! -d .git ]; then git init -q; git remote add origin "$REPO_URL"; fi
git remote set-url origin "$REPO_URL"
git fetch --quiet origin "$BRANCH"
git checkout -q -f -B "$BRANCH" FETCH_HEAD
echo "$BRANCH" > .git/jm-branch
mkdir -p data assets/uploads; chmod 755 data assets/uploads

# --- server-only settings (config.local.php is ignored by git)
say "Writing config.local.php (server only, not in GitHub)"
esc() { printf '%s' "$1" | sed "s/\\\\/\\\\\\\\/g; s/'/\\\\'/g"; }
{
  echo "<?php"
  echo "// Server-only settings. Created by deploy/server-setup.sh"
  echo "define('DEFAULT_ADMIN_PASS', '$(esc "$JM_ADMIN_PASS")');"
  if [ -n "${JM_DB_NAME:-}" ]; then
    echo "define('DB_HOST', '$(esc "${JM_DB_HOST:-localhost}")');"
    echo "define('DB_NAME', '$(esc "$JM_DB_NAME")');"
    echo "define('DB_USER', '$(esc "${JM_DB_USER:-}")');"
    echo "define('DB_PASS', '$(esc "${JM_DB_PASS:-}")');"
  else
    echo "define('DB_DRIVER', 'sqlite');   // database file lives in data/ on this server"
  fi
} > config.local.php
chmod 600 config.local.php

# --- create the database and set the admin password
say "Creating the database and admin login"
JM_PASS="$JM_ADMIN_PASS" php -r '
  $_SERVER["DOCUMENT_ROOT"] = getcwd(); $_SERVER["SCRIPT_NAME"] = "/index.php";
  require "includes/db.php";
  q("UPDATE jm_admins SET password_hash = ? WHERE username = ?", [password_hash(getenv("JM_PASS"), PASSWORD_DEFAULT), "admin"]);
  echo "Database: ", db_type(), " | rooms: ", val("SELECT COUNT(*) FROM jm_rooms"), " | admin password set\n";'

# --- live sync with GitHub (every minute)
say "Setting up live sync with GitHub"
chmod +x deploy/sync.sh
CRONLINE="* * * * * /bin/bash $SITE_DIR/deploy/sync.sh # jm-sync"
if command -v crontab >/dev/null 2>&1; then
  ( crontab -l 2>/dev/null | grep -v 'jm-sync' || true; echo "$CRONLINE" ) | crontab -
  echo "Cron installed: the site now updates itself within a minute of every change on GitHub ($BRANCH)."
else
  echo "Add this in hPanel > Advanced > Cron Jobs (every minute):"; echo "  /bin/bash $SITE_DIR/deploy/sync.sh"
fi
say "Done"
echo "Website : https://jawaimumal.com/"
echo "Admin   : https://jawaimumal.com/admin/login.php   (user: admin)"
echo "Sync log: $HOME/jm-sync.log"
