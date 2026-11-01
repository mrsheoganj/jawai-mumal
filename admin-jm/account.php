<?php
require __DIR__ . '/_init.php';
$me = row('SELECT * FROM jm_admins WHERE id = ?', [$_SESSION['admin_id']]);
foreach ($_POST as $k => $v) if (is_array($v)) unset($_POST[$k]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? ''); $user = trim($_POST['username'] ?? ''); $cur = $_POST['current'] ?? ''; $new = $_POST['new'] ?? ''; $new2 = $_POST['new2'] ?? '';
    if (!password_verify($cur, $me['password_hash'])) flash('err', 'Your current password is not correct.');
    elseif ($name !== '' && ($x = v_name($name, 'Your name'))) flash('err', $x);
    elseif (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $user)) flash('err', 'Username must be 3–40 letters, numbers or . _ -');
    elseif (val('SELECT COUNT(*) FROM jm_admins WHERE username = ? AND id <> ?', [$user, $me['id']])) flash('err', 'That username is already taken.');
    elseif ($new !== '' && (strlen($new) < 8 || strlen($new) > 100 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\\d/', $new) || $new !== $new2)) flash('err', $new !== $new2 ? 'The new passwords do not match.' : 'New password must be 8 to 100 characters and include at least one letter and one number.');
    else {
        q('UPDATE jm_admins SET username=?, full_name=? WHERE id=?', [$user, $name, $me['id']]);
        if ($new !== '') q('UPDATE jm_admins SET password_hash=? WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        $_SESSION['admin_name'] = $name ?: $user; $_SESSION['admin_user'] = $user;
        flash('ok', 'Account updated.');
    }
    redirect('account.php');
}
admin_head('My account', 'account');
?>
<form class="card" method="post" style="max-width:640px"><?= csrf_field() ?><h2>Login details</h2>
  <div class="grid"><div class="c6"><label for="fn">Your name</label><input id="fn" name="full_name" value="<?= e($me['full_name']) ?>"></div><div class="c6"><label for="un">Username</label><input id="un" name="username" value="<?= e($me['username']) ?>" required></div>
  <div class="c12"><label for="np">New password (leave empty to keep the current one)</label><input id="np" name="new" type="password" autocomplete="new-password"><p class="help">8 to 100 characters, with at least one letter and one number.</p></div>
  <div class="c12"><label for="np2">Repeat new password</label><input id="np2" name="new2" type="password" autocomplete="new-password"></div>
  <div class="c12"><label for="cp">Current password (required to save)</label><input id="cp" name="current" type="password" autocomplete="current-password" required></div></div>
  <p style="margin:1.2rem 0 0"><button class="btn btn--gold"><?= icon('check', 18) ?> Save</button></p></form>
<?php admin_foot(); ?>
