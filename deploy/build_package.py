#!/usr/bin/env python3
"""Builds dist/jawai-mumal-hosting.zip — upload it in hPanel > File Manager > public_html > Upload, then Extract.
Includes a server-only config.local.php (admin password, Coming soon default, update settings). Do NOT commit the zip.
  JM_ADMIN_PASS='...' BRANCH=main python3 deploy/build_package.py
"""
import os, secrets, subprocess, sys, zipfile
root = subprocess.check_output(['git', 'rev-parse', '--show-toplevel'], text=True).strip(); os.chdir(root)
pw = os.environ.get('JM_ADMIN_PASS') or sys.exit('Set JM_ADMIN_PASS')
branch = os.environ.get('BRANCH', 'main'); launch = os.environ.get('JM_LAUNCH_AT', '2026-11-06T00:00'); mode = os.environ.get('JM_MODE', 'coming_soon')
sha = subprocess.check_output(['git', 'rev-parse', 'HEAD'], text=True).strip()
q = lambda s: s.replace('\\', '\\\\').replace("'", "\\'")
cfg = f"""<?php
// Server-only settings (not in GitHub).
define('DEFAULT_ADMIN_PASS', '{q(pw)}');
define('DEFAULT_SITE_MODE', '{mode}');        // coming_soon | live | maintenance
define('DEFAULT_LAUNCH_AT', '{launch}');      // India time; goes live automatically
define('DB_DRIVER', 'sqlite');                // database file lives in data/
define('UPDATE_REPO', 'mrsheoganj/jawai-mumal');
define('UPDATE_BRANCH', '{branch}');
define('DEFAULT_AUTO_UPDATE', true);          // check GitHub every few minutes
define('DEPLOY_SECRET', '{secrets.token_hex(16)}');   // GitHub webhook secret (optional)
"""
files = [f for f in subprocess.check_output(['git', 'ls-files'], text=True).split('\n') if f and not f.startswith(('.claude/', '.github/', 'deploy/', 'dist/'))]
os.makedirs('dist', exist_ok=True); out = 'dist/jawai-mumal-hosting.zip'
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    for f in files: z.write(f, f)
    z.writestr('config.local.php', cfg); z.writestr('data/.version', sha)
print(f'{out}: {len(files)+2} files, {os.path.getsize(out)/1e6:.1f} MB, commit {sha[:7]}, branch {branch}, mode {mode} launching {launch} IST')
