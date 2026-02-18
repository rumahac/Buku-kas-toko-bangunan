<?php
session_start();
require_once 'db.php';
require_once 'Security.php';

// Set session timeout (30 menit)
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Regenerasi session ID untuk mencegah session fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

// Validasi session (cek IP dan User Agent)
function validateSession() {
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
        $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        return false;
    }
    return true;
}

function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) return false;
    return validateSession();
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        Security::logSecurityEvent('unauthorized_access', ['url' => $_SERVER['REQUEST_URI']]);
        header('Location: login.php');
        exit;
    }
}

function login($username, $password) {
    global $pdo;
    
    // Rate limiting
    if (!Security::checkRateLimit('login', 5, 300)) {
        Security::logSecurityEvent('rate_limit_exceeded', ['username' => $username]);
        return "Terlalu banyak percobaan. Silakan coba lagi nanti.";
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Regenerasi session ID
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        Security::logSecurityEvent('login_success', ['user_id' => $user['id']]);
        return true;
    }
    
    Security::logSecurityEvent('login_failed', ['username' => $username]);
    return false;
}

function logout() {
    Security::logSecurityEvent('logout', ['user_id' => $_SESSION['user_id'] ?? null]);
    session_destroy();
    header('Location: login.php');
    exit;
}

function registerUser($username, $password, $name, $email = null) {
    global $pdo;
    
    // Validasi password
    $passwordCheck = Security::validatePassword($password);
    if ($passwordCheck !== true) {
        return implode(", ", $passwordCheck);
    }
    
    // Cek username sudah ada?
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return "Username sudah digunakan.";
    }
    
    // Hash password dengan cost tinggi
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, name, email, role, status) VALUES (?, ?, ?, ?, 'operator', 'active')");
    if ($stmt->execute([$username, $hashedPassword, $name, $email])) {
        Security::logSecurityEvent('user_registered', ['username' => $username]);
        return true;
    }
    return "Gagal mendaftarkan user.";
}

// Cek session timeout (30 menit)
function checkSessionTimeout() {
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
        Security::logSecurityEvent('session_timeout', ['user_id' => $_SESSION['user_id'] ?? null]);
        logout();
    }
}

// Jalankan pengecekan timeout untuk halaman yang membutuhkan login
if (isLoggedIn()) {
    checkSessionTimeout();
}
function getAllUsers() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, username, name, email, role, status, last_login, created_at FROM users ORDER BY id DESC");
    return $stmt->fetchAll();
}

// Mendapatkan satu user berdasarkan id
function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, name, email, role, status FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Menambah user baru (oleh admin)
function addUser($username, $password, $name, $email, $role, $status) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return "Username sudah digunakan.";
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, name, email, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $hashed, $name, $email, $role, $status])) {
        return true;
    }
    return "Gagal menambah user.";
}

// Update user
function updateUser($id, $name, $email, $role, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
    return $stmt->execute([$name, $email, $role, $status, $id]);
}

// Reset password user
function resetUserPassword($id, $newPassword) {
    global $pdo;
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([$hashed, $id]);
}

// Hapus user
function deleteUser($id) {
    global $pdo;
    if ($id == $_SESSION['user_id']) {
        return "Tidak dapat menghapus akun sendiri.";
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

?>