<?php
class Security {
    
    /**
     * Sanitasi input untuk mencegah XSS
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validasi CSRF token
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }
    
    /**
     * Rate limiting sederhana
     */
    public static function checkRateLimit($action, $maxAttempts = 5, $timeWindow = 300) {
        $key = 'rate_limit_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => time()];
        
        // Reset jika sudah lewat waktu
        if (time() - $attempts['first_attempt'] > $timeWindow) {
            $attempts = ['count' => 1, 'first_attempt' => time()];
            $_SESSION[$key] = $attempts;
            return true;
        }
        
        // Cek batas
        if ($attempts['count'] >= $maxAttempts) {
            return false;
        }
        
        // Increment
        $attempts['count']++;
        $_SESSION[$key] = $attempts;
        return true;
    }
    
    /**
     * Log aktivitas keamanan
     */
    public static function logSecurityEvent($event, $details = []) {
        global $pdo;
        $user_id = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO security_logs (user_id, event, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $event, json_encode($details), $ip, $user_agent]);
    }
    
    /**
     * Validasi password strength
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Password minimal 8 karakter";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password harus mengandung huruf besar";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password harus mengandung huruf kecil";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password harus mengandung angka";
        }
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errors[] = "Password harus mengandung karakter khusus";
        }
        
        return empty($errors) ? true : $errors;
    }
}
?>