<?php
require __DIR__ . '/_init.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? '';
    if ($a === 'update') { $r = upd_run(); flash($r['ok'] ? 'ok' : 'err', $r['msg']); }
    if ($a === 'save') {
        $b = trim($_POST['update_branch'] ?? ''); if ($b !== '' && !preg_match('#^[A-Za-z0-9._/-]{1,100}$#', $b)) { flash('err', 'That branch name is not valid.'); redirect('update.php'); }
        set_setting('update_branch', $b); set_setting('auto_update', !empty($_POST['auto_update']) ? '1' : '0'); flash('ok', 'Update settings saved.');
    }
    redirect('update.php');
}
$remote = upd_remote_sha(); $local = upd_local_sha();
$log = is_file(upd_dir() . '/update.log') ? array_slice(file(upd_dir() . '/update.log', FILE_IGNORE_NEW_LINES), -12) : [];
$hook = rtrim(setting('site_url'), '/') . '/deploy-hook.php';
admin_head('Update from GitHub', 'update');
?>
<div class="card"><h2>Live sync with GitHub</h2>
  <p class="muted" style="margin-top:0">Repository <strong><?= e(upd_repo()) ?></strong> · branch <strong><?= e(upd_branch()) ?></strong>. Updating replaces the website's code only — your database, uploaded photos and server settings are never touched.</p>
  <div class="stats" style="margin-bottom:0">
    <div class="stat"><div><b style="font-size:1.1rem"><?= $local ? e(substr($local, 0, 7)) : 'unknown' ?></b><span>Installed version</span></div></div>
    <div class="stat"><div><b style="font-size:1.1rem"><?= $remote ? e(substr($remote, 0, 7)) : 'can’t reach GitHub' ?></b><span>Latest on GitHub</span></div></div>
    <div class="stat"><div><b style="font-size:1.1rem"><?= !$remote ? '—' : ($remote === $local ? 'Up to date' : 'Update available') ?></b><span>Status</span></div></div>
  </div>
  <form method="post" style="margin-top:1rem"><?= csrf_field() ?><input type="hidden" name="action" value="update"><button class="btn btn--gold" data-confirm="Install the latest version from GitHub now?"><?= icon('refresh', 18) ?> Update now</button></form>
</div>
<form method="post" class="card"><?= csrf_field() ?><input type="hidden" name="action" value="save"><h2>Automatic updates</h2>
  <div class="grid"><div class="c6"><label for="ub">Branch to follow</label><input id="ub" name="update_branch" value="<?= e(upd_branch()) ?>"><p class="help">Use <code>main</code> once your changes are merged there.</p></div>
  <div class="c6"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px;margin-top:1.5rem"><input type="checkbox" name="auto_update" value="1" <?= upd_auto() ? 'checked' : '' ?>> Check GitHub every few minutes and update automatically</label></div></div>
  <p style="margin:1rem 0 0"><button class="btn btn--gold"><?= icon('check', 18) ?> Save</button></p></form>
<div class="card"><h2>Instant updates (optional webhook)</h2>
  <p class="muted" style="margin-top:0">For updates the moment you push: on GitHub open the repository → Settings → Webhooks → Add webhook. Payload URL: <code><?= e($hook) ?></code> · Content type: <code>application/json</code> · Secret: the value of <code>DEPLOY_SECRET</code> in your server's <code>config.local.php</code> · Event: just the push event.</p>
  <?= defined('DEPLOY_SECRET') && DEPLOY_SECRET !== '' ? '<p><strong>Secret:</strong> <code>' . e(DEPLOY_SECRET) . '</code></p>' : '<div class="alert alert--warn">No <code>DEPLOY_SECRET</code> is set in config.local.php, so the webhook is switched off.</div>' ?></div>
<?php if ($log): ?><div class="card"><h2>Recent updates</h2><pre style="margin:0;white-space:pre-wrap;font-size:.85rem"><?= e(implode("\n", $log)) ?></pre></div><?php endif; ?>
<?php admin_foot(); ?>
