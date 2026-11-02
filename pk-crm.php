<?php
/** Private CRM: every booking and enquiry in one place. Sign in with the same user name and password as the admin panel. */
require_once __DIR__ . '/includes/db.php';
header('X-Robots-Tag: noindex, nofollow'); header('Cache-Control: no-store, private');
$self = url('pk-crm');
foreach ($_POST as $k => $v) if (is_array($v)) unset($_POST[$k]);

// ---------------------------------------------------------------- sign in
$err = '';
if (!admin_logged_in()) {
    if (($_POST['action'] ?? '') === 'login') {
        $ip = substr(track_ip(), 0, 60); $tag = 'crm:' . $ip;
        q('DELETE FROM jm_attempts WHERE created_at < ?', [time() - 900]);
        $fails = (int)val('SELECT COUNT(*) FROM jm_attempts WHERE ip = ? AND created_at > ?', [$tag, time() - 600]);
        if (!csrf_ok()) $err = 'Session expired. Please try again.';
        elseif ($fails >= 6) $err = 'Too many attempts. Please wait 10 minutes.';
        else {
            $u = trim((string)($_POST['username'] ?? '')); $pw = (string)($_POST['password'] ?? '');
            if (strlen($u) > 60 || strlen($pw) > 200) { $u = ''; $pw = ''; }
            $user = row('SELECT * FROM jm_admins WHERE username = ?', [$u]);
            if ($user && password_verify($pw, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int)$user['id']; $_SESSION['admin_user'] = $user['username']; $_SESSION['admin_name'] = $user['full_name'] ?: $user['username'];
                q('DELETE FROM jm_attempts WHERE ip = ?', [$tag]); redirect($self);
            }
            q('INSERT INTO jm_attempts (ip, created_at) VALUES (?, ?)', [$tag, time()]); usleep(600000); $err = 'Incorrect username or password.';
        }
    }
    ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Sign in · CRM</title><link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>"><?= font_head() ?><link rel="stylesheet" href="<?= asset('assets/css/pk.css') ?>"></head>
<body class="login"><form class="pk-card" method="post" novalidate><?= csrf_field() ?><input type="hidden" name="action" value="login">
<img src="<?= e(img(setting('logo_dark'))) ?>" alt="<?= e(setting('site_name')) ?>">
<h1>Bookings CRM</h1><p class="muted" style="margin:0">Sign in with your admin user name and password.</p>
<?php if ($err): ?><div class="alert alert--err" role="alert" style="margin-top:1rem"><?= e($err) ?></div><?php endif; ?>
<label for="u">Username</label><input id="u" name="username" autocomplete="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
<label for="p">Password</label><input id="p" name="password" type="password" autocomplete="current-password" required>
<button class="btn btn--gold">Sign in</button>
<p class="muted" style="text-align:center;margin:1rem 0 0;font-size:.85rem"><a href="<?= url(ADMIN_DIR . '/forgot.php') ?>">Forgot your password?</a></p></form></body></html><?php
    exit;
}

// ---------------------------------------------------------------- actions
$f = crm_filters($_GET);
$page = max(1, (int)($_GET['page'] ?? 1)); $per = 25;
$keep = fn(array $over = []) => '?' . http_build_query(array_filter(array_merge($f, ['page' => $page > 1 ? $page : ''], $over), fn($v) => $v !== '' && $v !== null && $v !== 'new'));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { http_response_code(400); exit('Security check failed. Go back, refresh and try again.'); }
    if (($_POST['action'] ?? '') === 'logout') { unset($_SESSION['admin_id'], $_SESSION['admin_user'], $_SESSION['admin_name']); redirect($self); }
    crm_post();
    $back = (string)($_POST['back'] ?? ''); redirect($self . (preg_match('/^\?[A-Za-z0-9_=&%.\-]*$/', $back) ? $back : ''));
}
if (isset($_GET['export'])) {
    [$w, $p] = crm_where($f); $all = rows("SELECT * FROM jm_bookings$w ORDER BY " . crm_order($f['sort']), $p);
    crm_export(in_array($_GET['export'], ['xlsx', 'pdf', 'csv'], true) ? $_GET['export'] : 'xlsx', $all, crm_subtitle($f, count($all)));
}

// ---------------------------------------------------------------- data
[$w, $p] = crm_where($f);
$total = (int)val("SELECT COUNT(*) FROM jm_bookings$w", $p);
$pages = max(1, (int)ceil($total / $per)); $page = min($page, $pages);
$list = rows("SELECT * FROM jm_bookings$w ORDER BY " . crm_order($f['sort']) . " LIMIT $per OFFSET " . (($page - 1) * $per), $p);
[$w2, $p2] = crm_where($f, false);
$cnt = array_fill_keys(booking_statuses(), 0); foreach (rows("SELECT status, COUNT(*) c FROM jm_bookings$w2 GROUP BY status", $p2) as $r) $cnt[$r['status']] = (int)$r['c'];
$todayStart = date('Y-m-d') . ' 00:00:00'; $weekStart = date('Y-m-d', strtotime('-6 days')) . ' 00:00:00';
$k = ['new' => (int)val("SELECT COUNT(*) FROM jm_bookings WHERE status = 'New'"), 'today' => (int)val('SELECT COUNT(*) FROM jm_bookings WHERE created_at >= ?', [$todayStart]), 'week' => (int)val('SELECT COUNT(*) FROM jm_bookings WHERE created_at >= ?', [$weekStart]),
      'confirmed' => (int)val("SELECT COUNT(*) FROM jm_bookings WHERE status = 'Confirmed'"), 'all' => (int)val('SELECT COUNT(*) FROM jm_bookings'),
      'value' => (int)val("SELECT COALESCE(SUM(amount + tax),0) FROM jm_bookings WHERE status IN ('New','Contacted','Confirmed')")];
$ago = function ($t) { $s = time() - strtotime($t); if ($s < 60) return 'just now'; if ($s < 3600) return floor($s / 60) . ' min ago'; if ($s < 86400) return floor($s / 3600) . ' h ago'; if ($s < 86400 * 7) return floor($s / 86400) . ' d ago'; return date('d M Y', strtotime($t)); };
$types = booking_types(); $icon = ['New' => '●', 'Contacted' => '☎', 'Confirmed' => '✓', 'Completed' => '★', 'Cancelled' => '✕'];
$act = fn($id, $a, $label, $cls = 'btn btn--sm', $extra = '', $confirm = '') => '<form method="post"' . ($confirm ? ' onsubmit="return confirm(\'' . e($confirm) . '\')"' : '') . '>' . csrf_field() . '<input type="hidden" name="id" value="' . (int)$id . '"><input type="hidden" name="back" value="' . e($keep()) . '">'
    . '<input type="hidden" name="action" value="' . ($a === 'email_confirm' || $a === 'delete' ? $a : 'status') . '">' . ($a !== 'email_confirm' && $a !== 'delete' ? '<input type="hidden" name="status" value="' . e($a) . '">' : '') . '<button class="' . $cls . '">' . $label . '</button></form>';
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>CRM · <?= e(setting('site_name')) ?></title><link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>"><?= font_head() ?><link rel="stylesheet" href="<?= asset('assets/css/pk.css') ?>"></head>
<body>
<header class="pk-top"><div class="pk-top__in"><img src="<?= e(img(setting('logo_light'))) ?>" alt="<?= e(setting('site_name')) ?>"><h1>Bookings &amp; enquiries</h1>
  <nav><a href="<?= url(ADMIN_DIR . '/index.php') ?>">Admin panel</a><a href="<?= url('index.php') ?>" target="_blank" rel="noopener">Website</a>
  <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="logout"><button>Sign out</button></form></nav></div></header>
<main class="pk-wrap">
<?php foreach (flashes() as $fl): ?><div class="alert alert--<?= $fl[0] === 'ok' ? 'ok' : 'err' ?>" role="status"><?= e($fl[1]) ?></div><?php endforeach; ?>

<section class="pk-grid g-kpi" aria-label="Summary">
  <div class="pk-card kpi"><small>Need a reply</small><b><?= $k['new'] ?></b><span>status “New”</span></div>
  <div class="pk-card kpi"><small>Received today</small><b><?= $k['today'] ?></b><span><?= date('D, d M') ?></span></div>
  <div class="pk-card kpi"><small>Last 7 days</small><b><?= $k['week'] ?></b><span>requests</span></div>
  <div class="pk-card kpi"><small>Confirmed</small><b><?= $k['confirmed'] ?></b><span>of <?= $k['all'] ?> total</span></div>
  <div class="pk-card kpi"><small>Open pipeline (est.)</small><b><?= $k['value'] ? e(money($k['value'])) : '—' ?></b><span>new, contacted &amp; confirmed, incl. GST</span></div>
</section>

<nav class="chips" aria-label="Status"><?php $allc = array_sum($cnt); ?>
  <a class="chip <?= $f['status'] === '' ? 'is-on' : '' ?>" href="<?= e($keep(['status' => '', 'page' => ''])) ?>"><b>All</b><i><?= $allc ?></i></a>
  <?php foreach (booking_statuses() as $s): ?><a class="chip <?= $f['status'] === $s ? 'is-on' : '' ?>" href="<?= e($keep(['status' => $s, 'page' => ''])) ?>"><b><?= e($s) ?></b><i><?= $cnt[$s] ?></i></a><?php endforeach; ?>
</nav>

<form class="pk-card flt" method="get" style="margin-bottom:1rem">
  <?php if ($f['status'] !== ''): ?><input type="hidden" name="status" value="<?= e($f['status']) ?>"><?php endif; ?>
  <label>Type<select name="type"><option value="">All types</option><?php foreach ($types as $tk => $tv): ?><option value="<?= e($tk) ?>" <?= $f['type'] === $tk ? 'selected' : '' ?>><?= e($tv) ?></option><?php endforeach; ?></select></label>
  <label>Search<input name="q" value="<?= e($f['q']) ?>" placeholder="Name, phone, email, ref" style="min-width:210px"></label>
  <label>Received from<input type="date" name="from" value="<?= e($f['from']) ?>"></label>
  <label>to<input type="date" name="to" value="<?= e($f['to']) ?>"></label>
  <label>Sort<select name="sort"><option value="new" <?= $f['sort'] === 'new' ? 'selected' : '' ?>>Newest first</option><option value="old" <?= $f['sort'] === 'old' ? 'selected' : '' ?>>Oldest first</option><option value="arrival" <?= $f['sort'] === 'arrival' ? 'selected' : '' ?>>Arrival date</option></select></label>
  <button class="btn btn--gold" style="align-self:flex-end">Apply</button>
  <a class="btn" style="align-self:flex-end" href="<?= e($self) ?>">Reset</a>
  <span style="margin-left:auto;display:flex;gap:.4rem;align-self:flex-end;flex-wrap:wrap"><a class="btn btn--sm" href="<?= e($keep(['export' => 'xlsx', 'page' => ''])) ?>">⬇ Excel</a><a class="btn btn--sm" href="<?= e($keep(['export' => 'pdf', 'page' => ''])) ?>">⬇ PDF</a><a class="btn btn--sm" href="<?= e($keep(['export' => 'csv', 'page' => ''])) ?>">⬇ CSV</a></span>
</form>

<p class="muted" style="margin:.2rem 0 .8rem"><?= $total ?> record<?= $total === 1 ? '' : 's' ?><?= $total > $per ? ' · page ' . $page . ' of ' . $pages : '' ?></p>
<?php foreach ($list as $b): $st = (string)$b['status']; $mail = filter_var((string)$b['email'], FILTER_VALIDATE_EMAIL); ?>
  <article class="pk-card rec is-<?= e($st) ?>"><div class="rec__bar"></div><div class="rec__in">
    <div class="rec__top"><span class="code"><?= e($b['code']) ?></span><span class="pill"><?= e($types[$b['type']] ?? $b['type']) ?></span><span class="pill st-<?= e($st) ?>"><?= $icon[$st] ?? '' ?> <?= e($st) ?></span><span class="when" title="<?= e($b['created_at']) ?>"><?= e($ago($b['created_at'])) ?> · <?= e(date('d M, g:i a', strtotime($b['created_at']))) ?></span></div>
    <div class="rec__grid">
      <div><h3>Guest</h3><p><strong><?= e($b['guest_name']) ?></strong></p><p><?= e($b['phone']) ?></p><?php if ($mail): ?><p><a href="mailto:<?= e($b['email']) ?>"><?= e($b['email']) ?></a></p><?php endif; ?>
        <p style="margin-top:.4rem;display:flex;gap:.4rem;flex-wrap:wrap"><a class="btn btn--ok btn--sm" target="_blank" rel="noopener" href="<?= e(wa_to($b['phone'], booking_wa_message($b, true))) ?>">WhatsApp</a><a class="btn btn--sm" href="tel:+<?= e(wa_digits($b['phone'])) ?>">Call</a><?php if ($mail): ?><a class="btn btn--sm" href="mailto:<?= e($b['email']) ?>?subject=<?= rawurlencode('Your request ' . $b['code'] . ' — ' . setting('site_name')) ?>">Email</a><?php endif; ?></p></div>
      <div><h3>Request</h3><p><strong><?= e($b['item_name'] ?: ($types[$b['type']] ?? '')) ?></strong></p>
        <p><?= $b['date_from'] ? e(fmt_date($b['date_from'])) . ($b['date_to'] ? ' → ' . e(fmt_date($b['date_to'])) . ' (' . (int)$b['nights'] . ' night' . ((int)$b['nights'] === 1 ? '' : 's') . ')' : '') : 'No date given' ?><?= $b['time_slot'] ? ' · ' . e($b['time_slot']) : '' ?></p>
        <p><?= (int)$b['guests'] ?> guest<?= (int)$b['guests'] === 1 ? '' : 's' ?><?= (int)$b['amount'] > 0 ? ' · est. <strong>' . e(money($b['amount'] + $b['tax'])) . '</strong> <span class="muted">incl. GST</span>' : '' ?></p></div>
      <div><h3>Notes</h3><?php if ($b['notes']): ?><div class="quote">“<?= e($b['notes']) ?>”</div><?php else: ?><p class="muted">No message from the guest.</p><?php endif; ?>
        <?php if ($b['admin_notes']): ?><p style="margin-top:.4rem"><span class="muted">Team note:</span> <?= e($b['admin_notes']) ?></p><?php endif; ?></div>
    </div>
    <div class="rec__act">
      <?php if ($st === 'New') echo $act($b['id'], 'Contacted', 'Mark contacted'); ?>
      <?php if ($mail && in_array($st, ['New', 'Contacted'], true)) echo $act($b['id'], 'email_confirm', '✓ Confirm &amp; email guest', 'btn btn--sm btn--gold'); ?>
      <?php if (in_array($st, ['New', 'Contacted'], true)) echo $act($b['id'], 'Confirmed', 'Confirm only'); ?>
      <?php if ($st === 'Confirmed') echo $act($b['id'], 'Completed', 'Mark completed', 'btn btn--sm btn--ok'); ?>
      <?php if (in_array($st, ['New', 'Contacted', 'Confirmed'], true)) echo $act($b['id'], 'Cancelled', 'Cancel', 'btn btn--sm btn--bad', '', 'Mark this request as cancelled?'); ?>
      <?php if (in_array($st, ['Completed', 'Cancelled'], true)) echo $act($b['id'], 'New', 'Re-open'); ?>
      <span style="margin-left:auto"><?= $act($b['id'], 'delete', 'Delete', 'btn btn--sm btn--bad', '', 'Delete this record permanently?') ?></span>
      <details><summary>Edit status &amp; team notes</summary>
        <form method="post" style="display:grid;gap:.5rem;max-width:520px"><?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="back" value="<?= e($keep()) ?>">
          <select name="status" aria-label="Status"><?php foreach (booking_statuses() as $s): ?><option <?= $st === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
          <textarea name="admin_notes" maxlength="2000" placeholder="Private notes (never shown to the guest)"><?= e($b['admin_notes']) ?></textarea><button class="btn btn--gold btn--sm" style="justify-self:start">Save</button></form></details>
    </div></div></article>
<?php endforeach; ?>
<?php if (!$list): ?><div class="pk-card empty"><strong>No records match.</strong><br>New website requests appear here the moment they arrive.</div><?php endif; ?>
<?php if ($pages > 1): ?><nav class="pager" aria-label="Pages"><?php if ($page > 1): ?><a class="btn btn--sm" href="<?= e($keep(['page' => $page - 1])) ?>">← Newer</a><?php endif; ?><span class="muted">Page <?= $page ?> of <?= $pages ?></span><?php if ($page < $pages): ?><a class="btn btn--sm" href="<?= e($keep(['page' => $page + 1])) ?>">Older →</a><?php endif; ?></nav><?php endif; ?>
<p class="note">Private page. Do not share this link. Changes here are the same as in Admin → Bookings &amp; enquiries.</p>
</main></body></html>
