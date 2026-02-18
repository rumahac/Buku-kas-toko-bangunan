<?php
require_once 'auth.php';

// Jika sudah login, redirect ke index
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($username) || empty($password) || empty($name)) {
        $error = 'Username, password, dan nama wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        // Coba registrasi
        $result = registerUser($username, $password, $name, $email);
        if ($result === true) {
            $success = 'Pendaftaran berhasil! Silakan login.';
            // Kosongkan form
            $_POST = [];
        } else {
            $error = $result; // Pesan error dari fungsi register
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Setia Jaya Kas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --bg-body: #f8fafc;
            --text-main: #1e293b;
            --border: #e2e8f0;
            --radius: 12px;
            --success: #10b981;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-body);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: var(--text-main);
        }
        .register-card {
            background: white;
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            width: 400px;
            max-width: 100%;
            border: 1px solid var(--border);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--primary);
        }
        .brand svg {
            width: 32px;
            height: 32px;
            fill: var(--primary);
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px #e0e7ff;
        }
        button {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
        }
        button:hover {
            background: #3730a3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79,70,229,0.3);
        }
        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #fecaca;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #a7f3d0;
        }
        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="brand">
            <svg width="32" height="32" viewBox="0 0 592.71 592.71">
                <path fill="currentColor" d="M564,429.43a295.19,295.19,0,0,0,30-130.07C594,135.68,461.32,3,297.64,3S1.29,135.68,1.29,299.36a295.19,295.19,0,0,0,30,130.07ZM69.64,215.72q16.65-14.49,46.75-14.51H215l-.32-.25,237.75.25-26.59,35.14-16.53-.15H350.22V359.89h89.35l0-.07h17.07q23,0,32.68-7.38c6.39-4.92,9.62-14.18,9.62-27.77V201.49h43.37V324.67q0,23.59-7.33,39.18t-24.4,23.2Q493.53,394.7,464,394.7H448.13v0H350.22v.06H306.85V236.2h-38.1v.26L121,236.19q-11.67,0-18.17,5.29c-4.34,3.51-6.51,9.17-6.51,16.94s2.17,13.41,6.51,16.94,10.39,5.29,18.17,5.29h97q30.1,0,46.76,14.77t16.68,43.78q0,28.74-16.68,43.64t-46.76,14.92H57.57l32-35.25,123.83.28q11.92,0,18.3-5.56t6.38-18q0-12.46-6.38-18c-4.25-3.69-10.35-5.56-18.3-5.56h-97q-30.09,0-46.75-14.49T53,258.42Q53,230.22,69.64,215.72Z"/>
                <path fill="currentColor" d="M97.75,518.14a296.33,296.33,0,0,0,399.78,0Z"/>
            </svg>
            Setia Jaya
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <input type="text" name="name" placeholder="Nama Lengkap" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email (opsional)" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password (min. 6 karakter)" required>
            </div>
            <button type="submit">Daftar</button>
        </form>

        <div class="link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>