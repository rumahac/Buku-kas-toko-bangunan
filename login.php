<?php
require_once 'auth.php';
require_once 'Security.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!Security::validateCSRFToken($csrf_token)) {
        Security::logSecurityEvent('csrf_validation_failed', ['action' => 'login']);
        $error = "Invalid request token.";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $result = login($username, $password);
        if ($result === true) {
            header('Location: index.php');
            exit;
        } else {
            $error = $result ?: "Username atau password salah.";
        }
    }
}

$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kas Setia Jaya</title>
     <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
         * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #fcfcfc 0%, #ebeaf1 100%); height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-container { background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .brand { text-align: center; margin-bottom: 30px; color: #ef4444; font-size: 24px; font-weight: bold; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        .form-input { width: 100%; padding: 14px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 16px; transition: border-color 0.3s; }
        .form-input:focus { outline: none; border-color: #4f46e5; }
        .btn-login { width: 100%; padding: 14px; background: #ef4444; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s; }
        .btn-login:hover { background: #dc2626; }
        .error-message { background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .footer-links { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
        .footer-links a { color: #4f46e5; text-decoration: none; }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="brand">
            <svg width="40" height="40" viewBox="0 0 592.71 592.71">
                <!-- SVG dari kode asli -->
                <path fill="#ef4444" d="M564,429.43a295.19,295.19,0,0,0,30-130.07C594,135.68,461.32,3,297.64,3S1.29,135.68,1.29,299.36a295.19,295.19,0,0,0,30,130.07ZM69.64,215.72q16.65-14.49,46.75-14.51H215l-.32-.25,237.75.25-26.59,35.14-16.53-.15H350.22V359.89h89.35l0-.07h17.07q23,0,32.68-7.38c6.39-4.92,9.62-14.18,9.62-27.77V201.49h43.37V324.67q0,23.59-7.33,39.18t-24.4,23.2Q493.53,394.7,464,394.7H448.13v0H350.22v.06H306.85V236.2h-38.1v.26L121,236.19q-11.67,0-18.17,5.29c-4.34,3.51-6.51,9.17-6.51,16.94s2.17,13.41,6.51,16.94,10.39,5.29,18.17,5.29h97q30.1,0,46.76,14.77t16.68,43.78q0,28.74-16.68,43.64t-46.76,14.92H57.57l32-35.25,123.83.28q11.92,0,18.3-5.56t6.38-18q0-12.46-6.38-18c-4.25-3.69-10.35-5.56-18.3-5.56h-97q-30.09,0-46.75-14.49T53,258.42Q53,230.22,69.64,215.72Z" transform="translate(-1.29 -3)"/>
            </svg>
            Setia Jaya
        </div>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="">
             <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" placeholder="Username" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" placeholder="Password" class="form-input" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>