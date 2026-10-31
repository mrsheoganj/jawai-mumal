#!/bin/bash
# Keeps the live website in step with GitHub. Runs every minute from cron (installed by server-setup.sh).
# Safe: only tracked files are replaced. Your database (data/), uploaded photos (assets/uploads/) and config.local.php are never touched.
SITE_DIR="${SITE_DIR:-$(cd "$(dirname "$0")/.." && pwd)}"
BRANCH="${BRANCH:-$(cat "$SITE_DIR/.git/jm-branch" 2>/dev/null || echo main)}"
LOG="${JM_LOG:-$HOME/jm-sync.log}"
exec 9>"$SITE_DIR/.git/jm-sync.lock"; flock -n 9 || exit 0          # never run twice at once
cd "$SITE_DIR" || exit 1
git fetch --quiet origin "$BRANCH" 2>>"$LOG" || { echo "$(date '+%F %T') fetch failed" >> "$LOG"; exit 1; }
NEW=$(git rev-parse FETCH_HEAD); OLD=$(git rev-parse HEAD 2>/dev/null)
if [ "$NEW" != "$OLD" ]; then
  git reset --hard --quiet FETCH_HEAD && echo "$(date '+%F %T') deployed ${NEW:0:7} (was ${OLD:0:7}) from $BRANCH" >> "$LOG"
  mkdir -p data assets/uploads && chmod 755 data assets/uploads 2>/dev/null
  # after an update, let PHP create any new tables / seed data on the next request (nothing to do here)
fi
