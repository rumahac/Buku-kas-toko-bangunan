<?php
$code = $_GET['code'] ?? '404';
$messages = [
    '403' => 'Akses Ditolak',
    '404' => 'Halaman Tidak Ditemukan',
    '500' => 'Kesalahan Server'
];
$title = $messages[$code] ?? 'Kesalahan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= $code ?> - Setia Jaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .error-message {
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #3730a3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79,70,229,0.4);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code"><?= $code ?></div>
        <div class="error-title"><?= $title ?></div>
        <div class="error-message">
            <?php if ($code == '403'): ?>
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            <?php elseif ($code == '404'): ?>
                Halaman yang Anda cari mungkin telah dipindahkan atau dihapus.
            <?php elseif ($code == '500'): ?>
                Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.
            <?php endif; ?>
        </div>
        <a href="index.php" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>