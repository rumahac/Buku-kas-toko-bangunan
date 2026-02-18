<?php
require_once 'auth.php';
require_once 'Security.php';

redirectIfNotLoggedIn();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Aktifkan error reporting untuk debugging (nonaktifkan di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = null;
$logs = [];
$total = 0;
$pages = 1;
$limit = 100;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

try {
    // Cek apakah tabel security_logs ada
    $stmt = $pdo->query("SHOW TABLES LIKE 'security_logs'");
    if ($stmt->rowCount() == 0) {
        // Tabel tidak ada, buat sekarang
        $sql = "CREATE TABLE IF NOT EXISTS `security_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `event` VARCHAR(100) NOT NULL,
            `details` TEXT,
            `ip_address` VARCHAR(45),
            `user_agent` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (`user_id`),
            INDEX idx_event (`event`),
            INDEX idx_created_at (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
        $pdo->exec($sql);
        // Tabel baru saja dibuat, tidak ada data
        $logs = [];
        $total = 0;
    } else {
        // Hitung total baris
        $stmt = $pdo->query("SELECT COUNT(*) FROM security_logs");
        $total = $stmt->fetchColumn();
        $pages = max(1, ceil($total / $limit));

        // Ambil data log dengan binding parameter integer
        $stmt = $pdo->prepare("SELECT * FROM security_logs ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $logs = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log - Setia Jaya</title>
    <link rel="icon" type="image/png" href="assets/icon.ico"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== Gaya Minimalis agar sesuai tema ===== */
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --primary-dark: #3730a3;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --bg-body: #f8fafc;
            --bg-surface: rgba(255,255,255,0.8);
            --bg-surface-solid: #ffffff;
            --text-main: #1e293b;
            --text-ungu : #4f46e5;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            --radius: 16px;
            --blur: blur(12px);
        }
        [data-theme="dark"] {
            --bg-body: #0f172a;
            --bg-surface: rgba(30,41,59,0.8);
            --bg-surface-solid: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.5);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            padding: 30px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Glassmorphism Card */
        .glass-card {
            background: var(--bg-surface);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: var(--shadow-xl);
            transform: translateY(-2px);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        

        .brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--danger);
            background: var(--bg-surface);
            padding: 12px 24px;
            border-radius: 60px;
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .brand svg {
            width: 32px;
            height: 32px;
            fill: currentColor;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 40px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
            background: var(--bg-surface);
            color: var(--text-main);
            border: 1px solid var(--border);
            backdrop-filter: var(--blur);
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            color: white;
        }

        .theme-btn {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .theme-btn:hover {
            transform: rotate(15deg) scale(1.1);
            border-color: var(--primary);
        }

        /* Controls */
        .controls {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .select-group {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg-surface);
            padding: 8px 16px;
            border-radius: 40px;
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
        }

        .select-group label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-select {
            background: var(--bg-surface-solid);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
            cursor: pointer;
            outline: none;
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-surface);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--info));
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-ungu);
            line-height: 1.2;
        }

        .stat-val {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
        }

        .stat-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .trend-up { color: var(--success); }
        .trend-down { color: var(--danger); }

        /* Chart Card */
        .chart-card {
            background: var(--bg-surface);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 30px;
        }

        /* Table */
        .table-wrapper {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--bg-surface);
            backdrop-filter: var(--blur);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            text-align: left;
            padding: 16px;
            background: rgba(0,0,0,0.02);
            color: var(--text-main);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: rgba(79,70,229,0.05);
        }

        /* Insights */
        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .insight-card {
            background: var(--bg-surface);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .insight-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .insight-content {
            flex: 1;
        }

        .insight-title {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .insight-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .insight-note {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand { font-size: 1.2rem; padding: 8px 16px; }
            .stat-value { font-size: 1.5rem; }
        }
    </style>
</head>
<body data-theme="light">
    <div class="container">
        <div class="header">
            <div class="brand">
                <svg width="32" height="32" viewBox="0 0 592.71 592.71">
                    <path fill="currentColor" d="M564,429.43a295.19,295.19,0,0,0,30-130.07C594,135.68,461.32,3,297.64,3S1.29,135.68,1.29,299.36a295.19,295.19,0,0,0,30,130.07ZM69.64,215.72q16.65-14.49,46.75-14.51H215l-.32-.25,237.75.25-26.59,35.14-16.53-.15H350.22V359.89h89.35l0-.07h17.07q23,0,32.68-7.38c6.39-4.92,9.62-14.18,9.62-27.77V201.49h43.37V324.67q0,23.59-7.33,39.18t-24.4,23.2Q493.53,394.7,464,394.7H448.13v0H350.22v.06H306.85V236.2h-38.1v.26L121,236.19q-11.67,0-18.17,5.29c-4.34,3.51-6.51,9.17-6.51,16.94s2.17,13.41,6.51,16.94,10.39,5.29,18.17,5.29h97q30.1,0,46.76,14.77t16.68,43.78q0,28.74-16.68,43.64t-46.76,14.92H57.57l32-35.25,123.83.28q11.92,0,18.3-5.56t6.38-18q0-12.46-6.38-18c-4.25-3.69-10.35-5.56-18.3-5.56h-97q-30.09,0-46.75-14.49T53,258.42Q53,230.22,69.64,215.72Z"/>
                    <path fill="currentColor" d="M97.75,518.14a296.33,296.33,0,0,0,399.78,0Z"/>
                </svg>
                Log Keamanan
            </div>
            <div class="header-actions">
                <button class="theme-btn" onclick="window.location.href='index.php'">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#05b32a" height="20px" width="20px" version="1.1" id="Layer_1" viewBox="0 0 512 512" xml:space="preserve">
                        <g>
                        <g>
                            <path d="M320,112H192V48h-32L0,208l160,160h32v-64h160c88.365,0,112,71.635,112,160h48V304C512,197.962,426.038,112,320,112z"/>
                        </g>
                    </g>
                    </svg>
                    </button>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
		<div class="glass-card">
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Detail Log 
                </h3>
       	 <div class="table-wrapper">
            <table>
                <thead style="background-color: #f59e0b;">
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Event</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                                Belum ada data log.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= $log['id']?? '-'?></td> 
                            <td><?= $log['user_id'] ?? '-' ?></td>
                            <td><?= htmlspecialchars($log['event']) ?></td>
                            <td><pre><?= htmlspecialchars($log['details'] ?? '') ?></pre></td>

                            <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                            <td><?= date('d-m-Y H:i:s', strtotime($log['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
           

        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="btn <?= $i == $page ? 'btn-primary' : 'btn-outline' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    </div>
    <script>
   </body>
</html>