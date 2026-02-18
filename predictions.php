<?php
require_once 'auth.php';
redirectIfNotLoggedIn();

// Ambil parameter periode prediksi (default 7)
$forecast_days = isset($_GET['forecast_days']) ? (int)$_GET['forecast_days'] : 7;
$forecast_days = min(max($forecast_days, 1), 30); // batasi 1-30

// Fungsi mendapatkan saldo harian (dengan parameter jumlah hari historis)
function getDailyBalances($days = 30) {
    global $pdo;
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime("-$days days"));

    $stmt = $pdo->prepare("SELECT date, type, amount, paid_amount, status FROM transactions WHERE date >= ? ORDER BY date");
    $stmt->execute([$startDate]);
    $transactions = $stmt->fetchAll();

    $daily = [];
    $balance = 0;
    $currentDate = $startDate;
    while ($currentDate <= $endDate) {
        foreach ($transactions as $t) {
            if ($t['date'] == $currentDate) {
                if ($t['type'] == 'income') {
                    $balance += $t['amount'];
                } else {
                    if ($t['status'] == 'lunas') {
                        $balance -= $t['amount'];
                    } elseif ($t['status'] == 'cicilan') {
                        $balance -= ($t['paid_amount'] ?? 0);
                    }
                }
            }
        }
        $daily[] = ['date' => $currentDate, 'balance' => $balance];
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    }
    return $daily;
}

// Regresi linear
function linearRegression($data) {
    $n = count($data);
    if ($n < 2) return null;

    $x = range(1, $n);
    $y = array_column($data, 'balance');

    $sumX = array_sum($x);
    $sumY = array_sum($y);
    $sumXY = 0;
    $sumX2 = 0;

    for ($i = 0; $i < $n; $i++) {
        $sumXY += $x[$i] * $y[$i];
        $sumX2 += $x[$i] * $x[$i];
    }

    $b = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    $a = ($sumY - $b * $sumX) / $n;

    return ['a' => $a, 'b' => $b];
}

// Prediksi untuk n hari ke depan
function predictFuture($lastDayIndex, $model, $days) {
    $predictions = [];
    for ($i = 1; $i <= $days; $i++) {
        $x = $lastDayIndex + $i;
        $y = $model['a'] + $model['b'] * $x;
        $predictions[] = ['day' => $i, 'balance' => round($y, 0)];
    }
    return $predictions;
}

$daily = getDailyBalances(30); // historis 30 hari terakhir
$model = linearRegression($daily);
$predictions = $model ? predictFuture(count($daily), $model, $forecast_days) : [];

// Data untuk grafik
$chartData = [];
foreach ($daily as $d) {
    $chartData[] = ['date' => $d['date'], 'balance' => $d['balance'], 'type' => 'historis'];
}
foreach ($predictions as $idx => $p) {
    $futureDate = date('Y-m-d', strtotime("+" . ($idx+1) . " days"));
    $chartData[] = ['date' => $futureDate, 'balance' => $p['balance'], 'type' => 'prediksi'];
}

// Hitung beberapa statistik tambahan
$min_balance = $daily ? min(array_column($daily, 'balance')) : 0;
$max_balance = $daily ? max(array_column($daily, 'balance')) : 0;
$avg_expense = 0; // bisa dihitung dari data pengeluaran, tapi sederhanakan
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Arus Kas - Setia Jaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/icon.ico"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
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
            color: var(--text-muted);
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
        <!-- Header -->
        <div class="header">
            <div class="brand">
                <svg width="32" height="32" viewBox="0 0 592.71 592.71">
                    <path fill="currentColor" d="M564,429.43a295.19,295.19,0,0,0,30-130.07C594,135.68,461.32,3,297.64,3S1.29,135.68,1.29,299.36a295.19,295.19,0,0,0,30,130.07ZM69.64,215.72q16.65-14.49,46.75-14.51H215l-.32-.25,237.75.25-26.59,35.14-16.53-.15H350.22V359.89h89.35l0-.07h17.07q23,0,32.68-7.38c6.39-4.92,9.62-14.18,9.62-27.77V201.49h43.37V324.67q0,23.59-7.33,39.18t-24.4,23.2Q493.53,394.7,464,394.7H448.13v0H350.22v.06H306.85V236.2h-38.1v.26L121,236.19q-11.67,0-18.17,5.29c-4.34,3.51-6.51,9.17-6.51,16.94s2.17,13.41,6.51,16.94,10.39,5.29,18.17,5.29h97q30.1,0,46.76,14.77t16.68,43.78q0,28.74-16.68,43.64t-46.76,14.92H57.57l32-35.25,123.83.28q11.92,0,18.3-5.56t6.38-18q0-12.46-6.38-18c-4.25-3.69-10.35-5.56-18.3-5.56h-97q-30.09,0-46.75-14.49T53,258.42Q53,230.22,69.64,215.72Z"/>
                    <path fill="currentColor" d="M97.75,518.14a296.33,296.33,0,0,0,399.78,0Z"/>
                </svg>
                Prediksi Arus Kas
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
                <button class="theme-btn" onclick="toggleTheme()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls">
            <div class="select-group">
                <label for="forecast_days">Prediksi untuk</label>
                <form method="get" id="forecastForm">
                    <select name="forecast_days" id="forecast_days" class="form-select" onchange="this.form.submit()">
                        <option value="7" <?= $forecast_days == 7 ? 'selected' : '' ?>>7 Hari</option>
                        <option value="14" <?= $forecast_days == 14 ? 'selected' : '' ?>>14 Hari</option>
                        <option value="30" <?= $forecast_days == 30 ? 'selected' : '' ?>>30 Hari</option>
                    </select>
                </form>
            </div>
        </div>

        <?php if (!$model): ?>
            <div class="glass-card" style="text-align: center; padding: 60px;">
                <div style="font-size: 3rem; margin-bottom: 20px;">📊</div>
                <h3>Data Historis Belum Cukup</h3>
                <p style="color: var(--text-muted); margin-top: 10px;">Butuh minimal 2 hari data untuk memulai prediksi.</p>
            </div>
        <?php else: ?>
            <!-- Chart -->
            <div class="chart-card">
                <canvas id="cashChart" style="width:100%; height:350px;"></canvas>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Tren Harian</div>
                    <div class="stat-value <?= $model['b'] >= 0 ? 'trend-up' : 'trend-down' ?>">
                        <?= number_format($model['b'], 0) ?>
                    </div>
                    <div class="stat-desc">perubahan saldo per hari</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Prediksi Akhir</div>
                    <div class="stat-value <?= end($predictions)['balance'] >= 0 ? 'trend-up' : 'trend-down' ?>">
                        Rp <?= number_format(end($predictions)['balance'], 0, ',', '.') ?>
                    </div>
                    <div class="stat-desc">pada H+<?= $forecast_days ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Saldo Terakhir</div>
                    <div class="stat-value">
                        Rp <?= number_format(end($daily)['balance'], 0, ',', '.') ?>
                    </div>
                    <div class="stat-desc"><?= date('d M Y', strtotime(end($daily)['date'])) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Min / Max</div>
                    <div class="stat-val" style="font-size: 1.4rem;">
                        Rp <?= number_format($min_balance,0,',','.') ?> - Rp <?= number_format($max_balance,0,',','.') ?>
                    </div>
                    <div class="stat-desc">rentang saldo historis</div>
                </div>
            </div>

            <!-- Prediction Table -->
            <div class="glass-card">
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Detail Prediksi <?= $forecast_days ?> Hari Ke Depan
                </h3>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Hari ke-</th>
                                <th>Tanggal</th>
                                <th>Saldo Diprediksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($predictions as $idx => $p): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= date('d-m-Y', strtotime('+' . ($idx+1) . ' days')) ?></td>
                                <td class="<?= $p['balance'] >= 0 ? 'trend-up' : 'trend-down' ?>" style="font-weight:600;">
                                    Rp <?= number_format($p['balance'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:0.85rem; color: var(--text-muted); margin-top:16px;">
                    *Prediksi berdasarkan tren 30 hari terakhir. Akurasi bergantung pada konsistensi data.
                </p>
            </div>

            <!-- AI Insights (Savings Tips + Wawasan) -->
            <div class="insights-grid">
                <div class="insight-card">
                    <div class="insight-icon">💡</div>
                    <div class="insight-content">
                        <div class="insight-title">Rekomendasi AI</div>
                        <div class="insight-value">Analisis Cepat</div>
                        <div class="insight-note">
                            <?php
                            // Rekomendasi sederhana berdasarkan tren
                            if ($model['b'] < -5000) {
                                echo "Tren pengeluaran tinggi. Coba evaluasi pos terbesar.";
                            } elseif ($model['b'] > 5000) {
                                echo "Pemasukan meningkat. Pertimbangkan investasi.";
                            } else {
                                echo "Kondisi stabil. Pertahankan pola saat ini.";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="insight-card">
                    <div class="insight-icon">⏳</div>
                    <div class="insight-content">
                        <div class="insight-title">Estimasi Ketahanan</div>
                        <div class="insight-value">
                            <?php
                            $last_balance = end($daily)['balance'];
                            $avg_daily_expense = 0;
                            // hitung rata-rata pengeluaran per hari (hanya expense)
                            $expenses = array_filter($daily, function($d) use ($pdo) {
                                // sederhana: ambil dari data transaksi, tapi untuk demo kita pakai dummy
                                return true;
                            });
                            // Simplifikasi: jika tren turun, maka asumsi pengeluaran = -$model['b']
                            $avg_expense = abs($model['b']) > 0 ? abs($model['b']) : 100000;
                            $days_left = $avg_expense > 0 ? floor($last_balance / $avg_expense) : 999;
                            echo $days_left > 30 ? "> 30 hari" : "$days_left hari";
                            ?>
                        </div>
                        <div class="insight-note">dengan tren saat ini</div>
                    </div>
                </div>
                <div class="insight-card">
                    <div class="insight-icon">📌</div>
                    <div class="insight-content">
                        <div class="insight-title">Kategori Terbesar</div>
                        <div class="insight-value">
                            <?php
                            // Ambil kategori pengeluaran terbesar dari 30 hari terakhir
                            $stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE type='expense' AND date >= ? GROUP BY category ORDER BY total DESC LIMIT 1");
                            $stmt->execute([date('Y-m-d', strtotime('-30 days'))]);
                            $top = $stmt->fetch();
                            echo $top ? htmlspecialchars($top['category']) : 'Belum ada';
                            ?>
                        </div>
                        <div class="insight-note">30 hari terakhir</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-note">
            Setia Jaya · Prediksi Arus Kas · <?= date('Y') ?>
        </div>
    </div>

    <script>
        // Theme toggle
        function toggleTheme() {
            const body = document.body;
            const isDark = body.getAttribute('data-theme') === 'dark';
            body.setAttribute('data-theme', isDark ? 'light' : 'dark');
        }

        // Chart initialization
        <?php if ($model): ?>
        const ctx = document.getElementById('cashChart').getContext('2d');
        const chartData = <?= json_encode($chartData) ?>;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [
                    {
                        label: 'Saldo Historis',
                        data: chartData.filter(d => d.type === 'historis').map(d => d.balance),
                        borderColor: '#ee3d26',
                        backgroundColor: 'rgba(229, 81, 70, 0.1)',
                        tension: 0.2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        fill: true
                    },
                    {
                        label: 'Prediksi',
                        data: chartData.filter(d => d.type === 'prediksi').map(d => d.balance),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.05)',
                        borderDash: [5, 5],
                        tension: 0.2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { color: getComputedStyle(document.body).getPropertyValue('--text-main') } },
                    tooltip: { 
                        callbacks: { 
                            label: (ctx) => `Rp ${ctx.raw.toLocaleString('id-ID')}` 
                        } 
                    }
                },
                scales: { 
                    y: { 
                        ticks: { 
                            callback: (v) => 'Rp ' + v.toLocaleString('id-ID'),
                            color: getComputedStyle(document.body).getPropertyValue('--text-muted')
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { 
                        ticks: { color: getComputedStyle(document.body).getPropertyValue('--text-muted') },
                        grid: { display: false }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>