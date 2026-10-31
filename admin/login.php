<?php
/**
 * Jawai Mumal - Admin Login Portal
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$error = null;

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Query user
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'] ?? 'Super Admin';
            $_SESSION['admin_name'] = $user['full_name'] ?? 'General Manager';

            header("Location: index.php");
            exit;
        } elseif ($username === DEFAULT_ADMIN_USER && $password === DEFAULT_ADMIN_PASS) {
            // Safe fallback if default
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_user'] = 'admin';
            $_SESSION['admin_role'] = 'Super Admin';
            $_SESSION['admin_name'] = 'General Manager';

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid administrative credentials. Please verify username and password.";
        }
    } else {
        $error = "Please provide both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal Login | <?php echo SITE_NAME; ?></title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: radial-gradient(circle at center, #161c24 0%, #0b0f14 100%);">

  <div style="width: 100%; max-width: 440px; padding: 2rem;">
    
    <div style="background: var(--admin-card); border: 1px solid var(--gold-border); border-radius: var(--radius-lg); padding: 2.8rem; box-shadow: var(--shadow-luxury); position: relative;">
      
      <!-- Logo & Branding -->
      <div style="text-align: center; margin-bottom: 2rem;">
        <img src="../assets/images/favicon.svg" alt="Jawai Mumal" style="width: 54px; height: 54px; margin: 0 auto 0.8rem auto;">
        <h1 class="font-royal" style="font-size: 1.5rem; letter-spacing: 2px; color: #fff;">JAWAI MUMAL</h1>
        <p style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; color: var(--gold-primary);">
          Resort & Safari Administration
        </p>
      </div>

      <?php if ($error): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 0.8rem 1rem; color: #ef4444; font-size: 0.85rem; margin-bottom: 1.5rem;">
          ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form action="login.php" method="POST">
        <div class="admin-form-group">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" class="admin-input" required value="admin" placeholder="e.g. admin">
        </div>

        <div class="admin-form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="admin-input" required value="admin123" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-gold" style="width: 100%; margin-top: 1rem; font-size: 0.92rem;">
          Authenticate to Dashboard
        </button>
      </form>

      <!-- Hostinger & Demo Hint -->
      <div style="background: var(--admin-sidebar); border: 1px solid var(--admin-border); border-radius: 8px; padding: 0.8rem; margin-top: 1.8rem; font-size: 0.8rem; color: var(--admin-muted); text-align: center;">
        Default Credentials: <strong style="color: var(--gold-light);">admin</strong> / <strong style="color: var(--gold-light);">admin123</strong>
      </div>

      <div style="text-align: center; margin-top: 1.5rem;">
        <a href="../index.php" style="color: var(--admin-muted); font-size: 0.85rem; text-decoration: none;">
          ← Return to Sanctuary Guest Website
        </a>
      </div>

    </div>

  </div>

</body>
</html>
