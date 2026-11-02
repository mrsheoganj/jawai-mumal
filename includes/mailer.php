<?php
/**
 * Email: built-in SMTP client (no libraries needed) + the booking emails.
 * Everything is controlled in Admin > Email (SMTP). Every message is written to the email log.
 */
function mail_cfg(): array {
    $g = fn($k, $d = '') => (($v = get_setting_raw('smtp.' . $k)) !== null) ? (string)$v : $d;
    return [
        'enabled' => $g('enabled', '0') === '1', 'host' => trim($g('host', 'smtp.hostinger.com')), 'port' => (int)($g('port', '465') ?: 465), 'secure' => $g('secure', 'ssl'),
        'user' => trim($g('user', 'booking@jawaimumal.com')), 'pass' => mail_secret_dec($g('pass')), 'insecure' => $g('insecure', '0') === '1',
        'from_email' => trim($g('from_email', 'booking@jawaimumal.com')), 'from_name' => $g('from_name', setting('site_name')), 'reply_to' => trim($g('reply_to', 'booking@jawaimumal.com')), 'bcc' => trim($g('bcc')),
        'notify_to' => trim($g('notify_to', 'info@jawaimumal.com, booking@jawaimumal.com')), 'notify_admin' => $g('notify_admin', '1') === '1', 'guest_confirm' => $g('guest_confirm', '1') === '1',
    ];
}
function mail_key(): string { return hash('sha256', (defined('APP_KEY') ? APP_KEY : 'jm|' . DB_NAME . '|' . DB_USER . '|' . DB_PASS . '|' . __DIR__), true); }
function mail_secret_enc(string $p): string { if ($p === '') return ''; $iv = random_bytes(16); return 'v1:' . base64_encode($iv . openssl_encrypt($p, 'aes-256-cbc', mail_key(), OPENSSL_RAW_DATA, $iv)); }
function mail_secret_dec(string $e): string { if (strpos($e, 'v1:') !== 0) return $e; $r = base64_decode(substr($e, 3)); $o = openssl_decrypt(substr($r, 16), 'aes-256-cbc', mail_key(), OPENSSL_RAW_DATA, substr($r, 0, 16)); return $o === false ? '' : $o; }
function mail_log(string $kind, string $to, string $subject, bool $ok, string $err = ''): void {
    try { q('INSERT INTO jm_mail_log (kind, to_addr, subject, status, error) VALUES (?,?,?,?,?)', [$kind, mb_substr($to, 0, 250), mb_substr($subject, 0, 300), $ok ? 'sent' : 'failed', mb_substr($err, 0, 2000)]);
          q('DELETE FROM jm_mail_log WHERE id < (SELECT m FROM (SELECT MAX(id) - 500 AS m FROM jm_mail_log) t)'); } catch (Throwable $e) {}
}
function mail_encode_header(string $s): string { return preg_match('/[^\x20-\x7e]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s; }
function mail_addr(string $email, string $name = ''): string { return $name !== '' ? mail_encode_header($name) . ' <' . $email . '>' : $email; }

/** Build a multipart text+html message. Returns [headers, body] with CRLF line endings. */
function mail_build(string $fromEmail, string $fromName, string $to, string $subject, string $text, string $html, string $replyTo = '', string $bcc = ''): array {
    $b = 'jm_' . bin2hex(random_bytes(8)); $host = preg_replace('/^.*@/', '', $fromEmail) ?: 'localhost';
    $h = ['Date: ' . date('r'), 'From: ' . mail_addr($fromEmail, $fromName), 'To: ' . $to];
    if ($replyTo !== '') $h[] = 'Reply-To: ' . $replyTo;
    $h[] = 'Subject: ' . mail_encode_header($subject); $h[] = 'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $host . '>'; $h[] = 'MIME-Version: 1.0';
    $h[] = 'X-Mailer: JawaiMumal';
    if ($html !== '') {
        $h[] = 'Content-Type: multipart/alternative; boundary="' . $b . '"';
        $body = "--$b\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($text)) . "--$b\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "--$b--\r\n";
    } else {
        $h[] = 'Content-Type: text/plain; charset=UTF-8'; $h[] = 'Content-Transfer-Encoding: base64'; $body = chunk_split(base64_encode($text));
    }
    return [implode("\r\n", $h), $body];
}

/** Talk SMTP. $c: host, port, secure(ssl|tls|none), user, pass, insecure. Returns ['ok'=>bool,'error'=>string,'log'=>string]. */
function smtp_deliver(array $c, string $fromEmail, array $rcpts, string $headers, string $body): array {
    $log = []; $fail = function ($m) use (&$log) { return ['ok' => false, 'error' => $m, 'log' => implode("\n", $log)]; };
    $ctx = stream_context_create(['ssl' => ['verify_peer' => !$c['insecure'], 'verify_peer_name' => !$c['insecure'], 'allow_self_signed' => (bool)$c['insecure'], 'SNI_enabled' => true]]);
    $fp = @stream_socket_client(($c['secure'] === 'ssl' ? 'ssl://' : 'tcp://') . $c['host'] . ':' . $c['port'], $en, $es, 15, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) { $why = trim($es . ' ' . (error_get_last()['message'] ?? '')); if (stripos($why, 'certificate') !== false || stripos($why, 'SSL') !== false) $why .= ' — tick “Skip the certificate check” if your host uses a certificate for a different name.'; return $fail("Could not connect to {$c['host']}:{$c['port']}" . ($why !== '' ? " ($why)" : '')); }
    stream_set_timeout($fp, 20);
    $read = function () use ($fp, &$log) { $r = ''; while (($l = fgets($fp, 1024)) !== false) { $r .= $l; if (strlen($l) < 4 || $l[3] === ' ') break; } $log[] = 'S: ' . rtrim($r); return $r; };
    $cmd = function ($line, $mask = false) use ($fp, &$log, $read) { fwrite($fp, $line . "\r\n"); $log[] = 'C: ' . ($mask ? '••••' : $line); return $read(); };
    $code = fn($r) => (int)substr($r, 0, 3);
    $r = $read(); if ($code($r) !== 220) return $fail('Server greeting failed: ' . trim($r));
    $helo = preg_replace('/[^a-z0-9.-]/i', '', $_SERVER['SERVER_NAME'] ?? 'localhost') ?: 'localhost';
    $r = $cmd("EHLO $helo"); if ($code($r) !== 250) return $fail('EHLO rejected: ' . trim($r));
    if ($c['secure'] === 'tls') {
        $r = $cmd('STARTTLS'); if ($code($r) !== 220) return $fail('STARTTLS refused: ' . trim($r));
        if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return $fail('TLS handshake failed (try the “skip certificate check” option or another port/encryption).');
        $r = $cmd("EHLO $helo"); if ($code($r) !== 250) return $fail('EHLO after TLS rejected: ' . trim($r));
    }
    if ($c['user'] !== '') {
        $r = $cmd('AUTH LOGIN'); if ($code($r) !== 334) return $fail('Server does not offer login: ' . trim($r));
        $r = $cmd(base64_encode($c['user']), true); if ($code($r) !== 334) return $fail('Username rejected: ' . trim($r));
        $r = $cmd(base64_encode($c['pass']), true); if ($code($r) !== 235) return $fail('Login failed — check the email address and password. (' . trim($r) . ')');
    }
    $r = $cmd("MAIL FROM:<$fromEmail>"); if ($code($r) !== 250) return $fail('Sender rejected: ' . trim($r));
    foreach ($rcpts as $to) { $r = $cmd("RCPT TO:<$to>"); if (!in_array($code($r), [250, 251], true)) return $fail("Recipient $to rejected: " . trim($r)); }
    $r = $cmd('DATA'); if ($code($r) !== 354) return $fail('DATA refused: ' . trim($r));
    $msg = $headers . "\r\n\r\n" . preg_replace('/^\./m', '..', str_replace(["\r\n", "\r", "\n"], "\n", $body)); $msg = str_replace("\n", "\r\n", $msg);
    fwrite($fp, $msg . "\r\n.\r\n"); $r = $read(); if ($code($r) !== 250) return $fail('Message rejected: ' . trim($r));
    $cmd('QUIT'); fclose($fp);
    return ['ok' => true, 'error' => '', 'log' => implode("\n", $log)];
}

/** Send one email using the saved settings (SMTP, or the server's mail() if SMTP is off). */
function jm_mail(string $to, string $subject, string $text, string $html = '', string $kind = 'general', string $replyTo = ''): array {
    $c = mail_cfg(); $to = trim($to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) { mail_log($kind, $to, $subject, false, 'Invalid recipient address'); return ['ok' => false, 'error' => 'Invalid recipient address', 'log' => '']; }
    $from = $c['from_email'] !== '' ? $c['from_email'] : ($c['user'] !== '' && strpos($c['user'], '@') ? $c['user'] : 'noreply@' . preg_replace('#^https?://(www\.)?#', '', rtrim(setting('site_url'), '/')));
    $rt = $replyTo !== '' ? $replyTo : $c['reply_to'];
    [$headers, $body] = mail_build($from, $c['from_name'], $to, $subject, $text, $html, $rt);
    if ($c['enabled'] && $c['host'] !== '') {
        $rcpts = [$to]; foreach (array_filter(array_map('trim', explode(',', $c['bcc']))) as $b) if (filter_var($b, FILTER_VALIDATE_EMAIL)) $rcpts[] = $b;
        $res = smtp_deliver($c, $from, $rcpts, $headers, $body);
    } else {
        $ok = @mail($to, mail_encode_header($subject), $body, preg_replace('/^(To|Subject): .*\r\n/mi', '', $headers . "\r\n") ?: $headers);
        $res = ['ok' => (bool)$ok, 'error' => $ok ? '' : 'SMTP is switched off and the server mail() function failed — set up SMTP in Admin > Email.', 'log' => 'php mail()'];
    }
    mail_log($kind, $to, $subject, $res['ok'], $res['error']);
    return $res;
}
/** Run after the visitor already got their page (so SMTP never slows the website down). */
function jm_defer(callable $fn): void {
    register_shutdown_function(function () use ($fn) {
        if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request(); elseif (function_exists('litespeed_finish_request')) @litespeed_finish_request();
        try { @set_time_limit(60); $fn(); } catch (Throwable $e) { error_log('Jawai Mumal mail: ' . $e->getMessage()); }
    });
}

// ---------------- the emails
function mail_tpl(string $key, string $default): string { $v = get_setting_raw('email.' . $key); return ($v !== null && $v !== '') ? (string)$v : $default; }
function mail_fill(string $t, array $b): string {
    $types = booking_types();
    $map = ['{name}' => $b['guest_name'], '{code}' => $b['code'], '{type}' => $types[$b['type']] ?? $b['type'], '{item}' => $b['item_name'], '{guests}' => (string)$b['guests'],
            '{date}' => $b['date_from'] ? fmt_date($b['date_from']) . ($b['date_to'] ? ' → ' . fmt_date($b['date_to']) : '') . ($b['time_slot'] ? ' · ' . $b['time_slot'] : '') : '—',
            '{estimate}' => (int)$b['amount'] > 0 ? money($b['amount'] + $b['tax']) : '—', '{phone}' => $b['phone'], '{email}' => (string)$b['email'], '{notes}' => (string)$b['notes'],
            '{hotel}' => setting('site_name'), '{whatsapp}' => 'https://wa.me/' . preg_replace('/\D/', '', setting('whatsapp'))];
    return strtr($t, $map);
}
function mail_html(string $heading, string $intro, array $rows, string $footer): string {
    $tr = ''; foreach ($rows as $k => $v) { if ($v === '' || $v === '—') continue; $tr .= '<tr><td style="padding:8px 12px;color:#6b5a4c;border-bottom:1px solid #eee;width:34%">' . e($k) . '</td><td style="padding:8px 12px;color:#2b180e;border-bottom:1px solid #eee"><strong>' . nl2br(e($v)) . '</strong></td></tr>'; }
    return '<!doctype html><html><body style="margin:0;background:#f6f6f4;font-family:Arial,Helvetica,sans-serif"><table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:24px 12px"><table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:12px;overflow:hidden">'
         . '<tr><td style="background:#2b180e;color:#e3b866;padding:22px 26px;font-size:22px;font-family:Georgia,serif">' . e(setting('site_name')) . '<div style="font-size:11px;letter-spacing:3px;color:#fff;margin-top:4px">HOTEL · RESTAURANT · SAFARI</div></td></tr>'
         . '<tr><td style="padding:26px"><h2 style="margin:0 0 10px;font-family:Georgia,serif;color:#2b180e">' . e($heading) . '</h2><p style="color:#4a3426;line-height:1.6;margin:0 0 16px">' . nl2br(e($intro)) . '</p><table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee;border-radius:8px">' . $tr . '</table>'
         . '<p style="color:#6b5a4c;line-height:1.6;margin:18px 0 0;font-size:14px">' . nl2br(e($footer)) . '</p></td></tr>'
         . '<tr><td style="background:#f6f6f4;color:#8a7a6c;font-size:12px;padding:14px 26px">' . e(str_replace("\n", ', ', setting('address'))) . '</td></tr></table></td></tr></table></body></html>';
}
function booking_rows(array $b): array {
    return ['Reference' => $b['code'], 'Request' => (booking_types()[$b['type']] ?? $b['type']) . ' — ' . $b['item_name'], 'Date' => mail_fill('{date}', $b), 'Guests' => (string)$b['guests'],
            'Estimate' => mail_fill('{estimate}', $b), 'Name' => $b['guest_name'], 'Phone' => $b['phone'], 'Email' => (string)$b['email'], 'Notes' => (string)$b['notes']];
}
/** Emails for a new booking/enquiry: one to the hotel team, one receipt to the guest (if they gave an email). */
function send_booking_emails(array $b): void {
    $c = mail_cfg();
    if ($c['notify_admin'] && $c['notify_to'] !== '') {
        $subj = mail_fill(mail_tpl('admin_subject', 'New {type} request {code} — {name}'), $b);
        $intro = mail_fill(mail_tpl('admin_intro', "A new request has arrived on the website.\nReply to the guest on WhatsApp: {whatsapp}"), $b);
        $link = rtrim(setting('site_url'), '/') . '/' . ADMIN_DIR . '/bookings.php?q=' . $b['code'];
        $text = $intro . "\n\n" . implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys(booking_rows($b)), booking_rows($b))) . "\n\nManage: $link";
        $html = mail_html('New ' . strtolower(booking_types()[$b['type']] ?? 'request'), $intro, booking_rows($b), 'Open in admin: ' . $link);
        foreach (array_filter(array_map('trim', explode(',', $c['notify_to']))) as $to) jm_mail($to, $subj, $text, $html, 'admin-notice', (string)$b['email']);
    }
    if ($c['guest_confirm'] && filter_var((string)$b['email'], FILTER_VALIDATE_EMAIL)) {
        $subj = mail_fill(mail_tpl('guest_subject', 'We received your request {code} — {hotel}'), $b);
        $intro = mail_fill(mail_tpl('guest_intro', "Dear {name},\nThank you for choosing {hotel}. We have received your request and our team will confirm availability and the final rate personally on WhatsApp."), $b);
        $foot = mail_fill(mail_tpl('guest_footer', "Questions? Message us on WhatsApp: {whatsapp}\nThis email is an acknowledgement, not a confirmed booking."), $b);
        $text = $intro . "\n\n" . implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys(booking_rows($b)), booking_rows($b))) . "\n\n" . $foot;
        jm_mail((string)$b['email'], $subj, $text, mail_html('Your request is received', $intro, booking_rows($b), $foot), 'guest-receipt');
    }
}
