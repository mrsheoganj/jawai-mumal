<?php
require __DIR__ . '/_init.php'; require_once __DIR__ . '/_crud.php';
$testResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? '';
    $p = fn($k, $d = '') => trim((string)($_POST[$k] ?? $d));
    if ($a === 'save' || $a === 'test') {
        $errs = [];
        $host = $p('host'); $port = $p('port', '465') ?: '465';
        if ($host !== '' && ($x = v_host($host, 'SMTP host'))) $errs[] = $x;
        if ($x = v_int($port, 1, 65535, 'Port')) $errs[] = $x;
        if ($p('user') !== '' && mb_strlen($p('user')) > 150) $errs[] = 'Username is too long.';
        if (mb_strlen((string)($_POST['pass'] ?? '')) > 200) $errs[] = 'Password is too long.';
        if (!empty($_POST['enabled']) && ($host === '' || $p('user') === '')) $errs[] = 'Enter the SMTP host and username before switching SMTP on.';
        if ($x = v_email($p('from_email'), !empty($_POST['enabled']), 'From address')) $errs[] = $x;
        if ($x = v_email($p('reply_to'), false, 'Reply-to address')) $errs[] = $x;
        if ($x = v_email_list($p('bcc'), 'Copy (BCC) address')) $errs[] = $x;
        if ($x = v_email_list($p('notify_to'), 'Booking alert address')) $errs[] = $x;
        if ($x = v_len($p('from_name'), 0, 80, 'From name')) $errs[] = $x;
        foreach (['admin_subject', 'admin_intro', 'guest_subject', 'guest_intro', 'guest_footer', 'confirm_subject', 'confirm_intro'] as $k) if ($x = v_len(trim((string)($_POST['t_' . $k] ?? '')), 0, 1000, 'Email template text')) { $errs[] = $x; break; }
        if ($a === 'test' && ($x = v_email($p('test_to'), true, 'Send the test to'))) $errs[] = $x;
        if ($errs) { foreach (array_unique($errs) as $x) flash('err', $x); redirect('email.php'); }
        set_setting('smtp.enabled', !empty($_POST['enabled']) ? '1' : '0');
        set_setting('smtp.host', $p('host')); set_setting('smtp.port', (string)(int)($p('port', '465') ?: 465));
        set_setting('smtp.secure', in_array($p('secure'), ['ssl', 'tls', 'none'], true) ? $p('secure') : 'ssl');
        set_setting('smtp.user', $p('user'));
        if (!empty($_POST['clear_pass'])) set_setting('smtp.pass', ''); elseif ($p('pass') !== '') set_setting('smtp.pass', mail_secret_enc((string)$_POST['pass']));
        set_setting('smtp.insecure', !empty($_POST['insecure']) ? '1' : '0');
        foreach (['from_email', 'from_name', 'reply_to', 'bcc', 'notify_to'] as $k) set_setting('smtp.' . $k, $p($k));
        set_setting('smtp.notify_admin', !empty($_POST['notify_admin']) ? '1' : '0'); set_setting('smtp.guest_confirm', !empty($_POST['guest_confirm']) ? '1' : '0');
        foreach (['admin_subject', 'admin_intro', 'guest_subject', 'guest_intro', 'guest_footer', 'confirm_subject', 'confirm_intro'] as $k) set_setting('email.' . $k, trim((string)($_POST['t_' . $k] ?? '')));
        if ($a === 'save') { flash('ok', 'Email settings saved.'); redirect('email.php'); }
        $to = $p('test_to');
        $r = jm_mail($to, 'Test email from ' . setting('site_name'), "This is a test email from your website.\nIf you can read this, your email settings work.\n\nSent " . date('d M Y, g:i a'), mail_html('Email settings work', "This is a test message from your website's admin panel.\nIf you can read this, your email settings are correct.", ['Sent to' => $to, 'Server' => mail_cfg()['enabled'] ? mail_cfg()['host'] . ':' . mail_cfg()['port'] : 'PHP mail()'], 'You can now receive booking emails.'), 'test');
        $_SESSION['mail_test'] = $r; redirect('email.php#test');
    }
    if ($a === 'clearlog') { q('DELETE FROM jm_mail_log'); flash('ok', 'Email log cleared.'); redirect('email.php'); }
}
if (!empty($_SESSION['mail_test'])) { $testResult = $_SESSION['mail_test']; unset($_SESSION['mail_test']); }
$c = mail_cfg(); $hasPass = $c['pass'] !== '';
$logs = rows('SELECT * FROM jm_mail_log ORDER BY id DESC LIMIT 40');
$tv = fn($k, $d) => e(mail_tpl($k, $d));
admin_head('Email (SMTP)', 'email');
?>
<?php if ($testResult): ?><div class="alert <?= $testResult['ok'] ? 'alert--ok' : 'alert--err' ?>" id="test"><strong><?= $testResult['ok'] ? 'Test email sent.' : 'Test email failed.' ?></strong> <?= e($testResult['error']) ?><?php if (!empty($testResult['log'])): ?><details style="margin-top:.5rem"><summary>Technical details</summary><pre style="white-space:pre-wrap;font-size:.8rem;margin:.5rem 0 0"><?= e($testResult['log']) ?></pre></details><?php endif; ?></div><?php endif; ?>
<form method="post" class="card"><?= csrf_field() ?>
  <h2>SMTP server</h2>
  <p class="muted" style="margin-top:0">Create a mailbox in hPanel → Emails (for example <code>bookings@jawaimumal.com</code>) and enter its details here. Everything the website sends — new-booking alerts to you and receipts to guests — goes through this mailbox.</p>
  <div class="grid">
    <div class="c12"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px"><input type="checkbox" name="enabled" value="1" <?= $c['enabled'] ? 'checked' : '' ?>> Send emails through this SMTP server <span class="muted">(off = the web server's basic mail function, which is often blocked or goes to spam)</span></label></div>
    <div class="c4"><label for="pre">Quick fill</label><select id="pre"><option value="">Choose a provider…</option><option value="smtp.hostinger.com|465|ssl">Hostinger email</option><option value="smtp.gmail.com|465|ssl">Gmail (needs an app password)</option><option value="smtp.zoho.in|465|ssl">Zoho Mail (India)</option><option value="smtp.office365.com|587|tls">Microsoft 365 / Outlook</option></select></div>
    <div class="c4"><label for="host">SMTP host</label><input id="host" name="host" maxlength="253" pattern="[A-Za-z0-9.\-]+" title="A server name like smtp.example.com" value="<?= e($c['host']) ?>" placeholder="smtp.hostinger.com" autocomplete="off"></div>
    <div class="c2"><label for="port">Port</label><input id="port" name="port" type="number" min="1" max="65535" step="1" value="<?= (int)$c['port'] ?>"></div>
    <div class="c2"><label for="secure">Encryption</label><select id="secure" name="secure"><?php foreach (['ssl' => 'SSL (465)', 'tls' => 'STARTTLS (587)', 'none' => 'None (25)'] as $k => $v): ?><option value="<?= $k ?>" <?= $c['secure'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
    <div class="c6"><label for="user">Username (full email address)</label><input id="user" name="user" value="<?= e($c['user']) ?>" autocomplete="off" placeholder="bookings@jawaimumal.com"></div>
    <div class="c6"><label for="pass">Password <?= $hasPass ? '<span class="muted">— saved (leave empty to keep it)</span>' : '' ?></label><input id="pass" name="pass" type="password" autocomplete="new-password" placeholder="<?= $hasPass ? '••••••••••' : 'mailbox password' ?>"><?php if ($hasPass): ?><label style="display:flex;gap:.5rem;align-items:center;margin-top:.4rem;font-weight:400"><input type="checkbox" name="clear_pass" value="1"> remove the saved password</label><?php endif; ?></div>
    <div class="c12"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px;font-weight:400"><input type="checkbox" name="insecure" value="1" <?= $c['insecure'] ? 'checked' : '' ?>> Skip the certificate check <span class="muted">(only if your host's certificate does not match its name; less secure)</span></label></div>
  </div>
  <h2 style="margin-top:1.4rem">Sender &amp; recipients</h2>
  <div class="grid">
    <div class="c6"><label for="fe">From email</label><input id="fe" name="from_email" type="email" maxlength="120" value="<?= e($c['from_email']) ?>" placeholder="bookings@jawaimumal.com"><p class="help">Use the same address as the username, otherwise most servers reject the message.</p></div>
    <div class="c6"><label for="fn">From name</label><input id="fn" name="from_name" value="<?= e($c['from_name']) ?>"></div>
    <div class="c6"><label for="nt">Send booking alerts to <span class="muted">(comma separated)</span></label><input id="nt" name="notify_to" value="<?= e($c['notify_to']) ?>" placeholder="owner@example.com, manager@example.com"></div>
    <div class="c3"><label for="rt">Reply-to (optional)</label><input id="rt" name="reply_to" type="email" maxlength="120" value="<?= e($c['reply_to']) ?>"></div>
    <div class="c3"><label for="bc">Bcc copy (optional)</label><input id="bc" name="bcc" value="<?= e($c['bcc']) ?>"></div>
    <div class="c6"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px;font-weight:400"><input type="checkbox" name="notify_admin" value="1" <?= $c['notify_admin'] ? 'checked' : '' ?>> Email me every new booking / enquiry</label></div>
    <div class="c6"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px;font-weight:400"><input type="checkbox" name="guest_confirm" value="1" <?= $c['guest_confirm'] ? 'checked' : '' ?>> Send guests a receipt when they give an email address</label></div>
  </div>
  <h2 style="margin-top:1.4rem">Email wording</h2>
  <p class="muted" style="margin-top:0">Placeholders you can use: <code>{name}</code> <code>{code}</code> <code>{type}</code> <code>{item}</code> <code>{date}</code> <code>{guests}</code> <code>{estimate}</code> <code>{phone}</code> <code>{email}</code> <code>{notes}</code> <code>{hotel}</code> <code>{whatsapp}</code>. Clear a box to go back to the standard wording.</p>
  <div class="grid">
    <div class="c6"><label>Alert to you — subject</label><input name="t_admin_subject" value="<?= $tv('admin_subject', 'New {type} request {code} — {name}') ?>"></div>
    <div class="c6"><label>Receipt to guest — subject</label><input name="t_guest_subject" value="<?= $tv('guest_subject', 'We received your request {code} — {hotel}') ?>"></div>
    <div class="c6"><label>Alert to you — message</label><textarea name="t_admin_intro" rows="3"><?= $tv('admin_intro', "A new request has arrived on the website.\nReply to the guest on WhatsApp: {whatsapp}") ?></textarea></div>
    <div class="c6"><label>Receipt to guest — message</label><textarea name="t_guest_intro" rows="3"><?= $tv('guest_intro', "Dear {name},\nThank you for choosing {hotel}. We have received your request and our team will confirm availability and the final rate personally on WhatsApp.") ?></textarea></div>
    <div class="c12"><label>Receipt to guest — footer</label><textarea name="t_guest_footer" rows="2"><?= $tv('guest_footer', "Questions? Message us on WhatsApp: {whatsapp}\nThis email is an acknowledgement, not a confirmed booking.") ?></textarea></div>
    <div class="c6"><label>Confirmation (button in Bookings) — subject</label><input name="t_confirm_subject" value="<?= $tv('confirm_subject', 'Your booking {code} is confirmed — {hotel}') ?>"></div>
    <div class="c6"><label>Confirmation — message</label><textarea name="t_confirm_intro" rows="3"><?= $tv('confirm_intro', "Dear {name},\nWe are pleased to confirm your {type}: {item} — {date}.\nWe look forward to welcoming you. For any change, reply on WhatsApp: {whatsapp}") ?></textarea></div>
  </div>
  <div class="card" style="margin:1.4rem 0 0;background:#fafaf8" id="testbox"><h2 style="font-size:1.2rem">Send a test email</h2>
    <div class="grid"><div class="c6"><label for="tt">Send to</label><input id="tt" name="test_to" type="email" value="<?= e($c['notify_to'] ? trim(explode(',', $c['notify_to'])[0]) : '') ?>" placeholder="you@example.com"></div>
      <div class="c6" style="display:flex;align-items:end"><button class="btn btn--line" name="action" value="test" type="submit"><?= icon('send', 16) ?> Save &amp; send test</button></div></div></div>
  <p style="margin:1.2rem 0 0" class="actions"><button class="btn btn--gold" name="action" value="save" type="submit"><?= icon('check', 18) ?> Save email settings</button></p>
</form>
<div class="card"><div class="toolbar" style="margin:0 0 .6rem"><h2 style="margin:0">Email log</h2>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="clearlog"><button class="btn btn--danger btn--sm" data-confirm="Clear the email log?"><?= icon('trash', 16) ?> Clear log</button></form></div>
  <?php if ($logs): ?><div class="tablewrap" style="border:0"><table><thead><tr><th>When</th><th>Type</th><th>To</th><th>Subject</th><th>Result</th></tr></thead><tbody>
    <?php foreach ($logs as $l): ?><tr><td><?= e(date('d M, g:i a', strtotime($l['created_at']))) ?></td><td><span class="pill"><?= e($l['kind']) ?></span></td><td><?= e($l['to_addr']) ?></td><td><?= e($l['subject']) ?></td><td><?= $l['status'] === 'sent' ? pill_status('Confirmed') : '<span class="pill pill--New">failed</span><br><span class="muted">' . e($l['error']) . '</span>' ?></td></tr><?php endforeach; ?>
  </tbody></table></div><?php else: ?><p class="muted">No emails have been sent yet.</p><?php endif; ?></div>
<script>document.getElementById('pre').addEventListener('change',function(){var v=this.value.split('|');if(v.length===3){document.getElementById('host').value=v[0];document.getElementById('port').value=v[1];document.getElementById('secure').value=v[2]}});</script>
<?php admin_foot(); ?>
