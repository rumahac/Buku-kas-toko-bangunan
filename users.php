<?php
require_once 'auth.php';
require_once 'Security.php';
redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$users = getAllUsers(); // untuk tampilan awal
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Setia Jaya</title>
    <link rel="icon" type="image/png" href="assets/icon.ico"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== CSS SAMA SEPERTI SEBELUMNYA ===== */
         :root {
            /* Modern Minimalist Palette */
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --primary-dark: #3730a3;
            
            --success: #10b981;
            --success-light: #d1fae5;
            --success-dark: #047857;
            
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --danger-dark: #b91c1c;
            
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            
            --info: #3b82f6;
            --info-light: #dbeafe;
            
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --bg-input: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            
            --radius: 12px;
        }

        [data-theme="dark"] {
            --bg-body: #0f172a;
            --bg-surface: #1e293b;
            --bg-input: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.5);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; transition: background-color 0.2s, color 0.2s, border-color 0.2s; }
        body { background-color: var(--bg-body); color: var(--text-main); padding-bottom: 80px; overflow-x: hidden; }

        /* Animations */
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); } 100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); } }

        .container { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }
        
        /* Header */
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .brand { font-size: 1.4rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px; animation: slideUp 0.6s ease-out; }
        .brand svg { color: var(--primary); width: 40px; height: 40px; }
        .theme-btn { background: var(--bg-surface); border: 1px solid var(--border); padding: 10px; border-radius: 50%; cursor: pointer; transition: transform 0.2s; animation: slideUp 0.7s ease-out; }
        .theme-btn:hover { transform: rotate(15deg) scale(1.1); border-color: var(--primary); }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px; }
        
        .card { background: var(--bg-surface); padding: 24px; border-radius: var(--radius); box-shadow: var(--shadow-sm); border: 1px solid var(--border); transition: all 0.3s ease; animation: slideUp 0.6s ease-out backwards; }
        .card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-light); }
        
        .stat-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 1.8rem; font-weight: 700; margin-top: 8px; letter-spacing: -0.5px; }

        /* --- Buttons CSS --- */
        .btn { padding: 10px 20px; border-radius: var(--radius); border: none; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.9rem; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;}
        .btn:active { transform: scale(0.96); }
        .btn svg { width: 18px; height: 18px; flex-shrink: 0; }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }

        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: var(--success-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }

        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: var(--danger-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

        .btn-warning { background: var(--warning); color: #fff; }
        .btn-warning:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }

        .btn-info { background: var(--info); color: white; }
        .btn-info:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-main); box-shadow: none; }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--bg-body); }

        /* --- Top 5 Items Section --- */
        .top-section { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px; }
        @media (max-width: 900px) { .top-section { grid-template-columns: 1fr; } }

        .top-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .top-title { font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        
        .top-list { display: flex; flex-direction: column; gap: 16px; }
        .top-item { display: flex; flex-direction: column; gap: 6px; }
        
        .top-meta { display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; }
        .top-name { display: flex; align-items: center; gap: 8px; }
        .top-rank { width: 24px; height: 24px; border-radius: 6px; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); }
        .top-item:nth-child(1) .top-rank { background: #fee2e2; color: #ef4444; } 
        .top-item:nth-child(2) .top-rank { background: #ffedd5; color: #f97316; } 
        .top-item:nth-child(3) .top-rank { background: #fef9c3; color: #eab308; } 

        .bar-container { width: 100%; height: 8px; background: var(--bg-input); border-radius: 4px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 4px; transition: width 1s ease-out; }
        .fill-inc { background: var(--success); }
        .fill-exp { background: var(--danger); }

        /* --- AI Analysis Section --- */
        .ai-section { margin-bottom: 30px; animation: slideUp 0.9s ease-out backwards; }
        .ai-header { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .ai-badge { background: linear-gradient(135deg, #6366f1, #a855f7); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 6px; }
        
        .analysis-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; }
        @media (max-width: 900px) { .analysis-grid { grid-template-columns: 1fr; } }

        .health-card { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 30px; }
        
        .health-circle {
            width: 120px; height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--success) 0% 0%, var(--bg-input) 0% 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative; margin-bottom: 15px;
            transition: background 1s ease;
        }
        .health-circle::before {
            content: ''; position: absolute; width: 100px; height: 100px;
            background: var(--bg-surface); border-radius: 50%;
        }
        .health-score { position: relative; font-size: 2rem; font-weight: 800; color: var(--text-main); }
        .health-text { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }

        .insights-list { display: flex; flex-direction: column; gap: 12px; }
        .insight-item { background: var(--bg-body); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border); display: flex; align-items: center; gap: 12px; font-size: 0.9rem; }
        .insight-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
        .insight-content strong { display: block; font-weight: 600; font-size: 0.95rem; }
        .insight-content span { font-size: 0.85rem; color: var(--text-muted); }
        .insight-bad .insight-icon { background: #fee2e2; color: var(--danger); }
        .insight-good .insight-icon { background: #d1fae5; color: var(--success); }
        .insight-neutral .insight-icon { background: #e0e7ff; color: var(--primary); }

        .ai-tips { background: var(--bg-body); padding: 20px; border-radius: var(--radius); border: 1px dashed var(--primary); margin-top: 24px; }
        .ai-tips-title { color: var(--primary); font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }

        /* --- RESPONSIVE TOOLBAR FIX (UPDATED) --- */
        .toolbar { display: flex; justify-content: flex-end; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 20px; animation: slideUp 1s ease-out backwards; }
        
        .search-box { flex: 1; min-width: 250px; position: relative; }
        .search-input { width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--radius); border: 1px solid var(--border); background: var(--bg-input); color: var(--text-main); font-size: 0.95rem; transition: all 0.2s; outline: none; }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); background: var(--bg-surface); }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; pointer-events: none; }

        /* Container for Select + Buttons */
        .toolbar-actions {
            display: flex;
            flex-direction: column; /* Default mobile: vertical */
            gap: 12px;
            width: 100%;
        }

        /* Desktop Styles: Inline alignment */
        @media (min-width: 769px) {
            .toolbar {
                flex-wrap: nowrap; /* Prevent wrapping if possible */
            }
            
            .toolbar-actions {
                flex-direction: row; /* Horizontal line */
                align-items: center;
                width: auto; /* Let it take content width */
                gap: 10px;
            }

            #downloadPeriod {
                width: 160px; /* Fixed width for neat alignment */
                margin-bottom: 0;
            }

            .button-group-mobile {
                display: flex; /* Overwrite grid */
                gap: 10px;
                grid-template-columns: none; /* Reset grid */
            }
            
            /* Reset span overrides */
            .btn-primary-mobile {
                grid-column: auto; 
            }
        }

        /* Mobile Styles: Stack */
        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .search-box {
                width: 100%;
                margin-bottom: 0;
            }

            #downloadPeriod {
                width: 100%;
                padding: 12px;
                margin-bottom: 0;
            }

            .button-group-mobile {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                width: 100%;
            }
            
            .btn-primary-mobile {
                grid-column: span 2;
            }
            
            .brand { font-size: 1.2rem; }
            .brand svg { width: 35px; height: 35px; }
            .stat-value { font-size: 1.5rem; }
        }

        /* Table */
        .table-wrapper { overflow-x: auto; border-radius: var(--radius); border: 1px solid var(--border); background: var(--bg-surface); box-shadow: var(--shadow-sm); animation: slideUp 1.1s ease-out backwards; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th { text-align: left; padding: 16px; background: var(--bg-body); color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 16px; border-bottom: 1px solid var(--border); font-size: 0.95rem; color: var(--text-main); transition: background 0.1s; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: var(--bg-input); cursor: default; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
        .badge-admin { background: #e0e7ff; color: #4338ca; }
        .badge-operator { background: #d1fae5; color: #065f46; }
        .badge-viewer { background: #fef3c7; color: #92400e; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        tr.overdue-row { background-color: rgba(239, 68, 68, 0.05); }

        .progress-track { width: 80px; height: 6px; background: var(--bg-body); border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--primary); transition: width 1s ease-in-out; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); z-index: 100; display: flex; justify-content: center; align-items: center; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: all; }
        .modal { background: var(--bg-surface); width: 90%; max-width: 500px; border-radius: 16px; padding: 30px; box-shadow: var(--shadow-lg); transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: 90vh; overflow-y: auto; border: 1px solid var(--border); }
        .modal-overlay.active .modal { transform: scale(1); }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 500; color: var(--text-main); }
        .form-input, .form-select { width: 100%; padding: 12px; border-radius: var(--radius); border: 1px solid var(--border); background: var(--bg-input); color: var(--text-main); font-size: 1rem; transition: border 0.2s, box-shadow 0.2s; }
        .form-input:focus, .form-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .cicilan-section { background: var(--bg-body); padding: 16px; border-radius: var(--radius); border: 1px dashed var(--primary); display: none; }

        /* OCR */
        .ocr-box { border: 2px dashed var(--border); padding: 20px; text-align: center; border-radius: var(--radius); margin-bottom: 20px; cursor: pointer; transition: all 0.2s; background: var(--bg-surface); }
        .ocr-box:hover { border-color: var(--primary); background: var(--primary-light); transform: scale(1.01); }
        #ocrRawText { font-size: 0.75rem; color: var(--text-muted); font-family: monospace; white-space: pre-wrap; max-height: 120px; overflow-y: auto; margin-top: 10px; padding: 12px; background: var(--bg-body); border-radius: 8px; display: none; }

        /* Toast */
        .toast { position: fixed; bottom: 30px; right: 30px; background: #1e293b; color: white; padding: 14px 24px; border-radius: 12px; box-shadow: var(--shadow-lg); transform: translateY(100px); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 200; display: flex; gap: 12px; align-items: center; font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.1); }
        .toast.show { transform: translateY(0); }
        .table-wrapper th:last-child, .table-wrapper td:last-child { text-align: center; }
        .img-preview { width: 30px; height: 30px; object-fit: cover; border-radius: 4px; cursor: pointer; }
        /* Action Buttons */
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; border-radius: 8px; }
        .btn-icon { border: none; background: none; cursor: pointer; color: var(--text-muted); padding: 6px; border-radius: 50%; transition: all 0.2s; }
        .btn-icon:hover { background: var(--bg-body); color: var(--primary); }
    </style>
    <meta name="csrf-token" content="<?= Security::generateCSRFToken() ?>">
</head>
<body>
    <div class="container">
        <header>
            <div class="brand">
                 <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 592.71 592.71"><defs><style>.cls-1{font-size:93.78px;fill:#231f20;font-family:YamakaPersonalUsed, Yamaka;}.cls-2{letter-spacing:-0.03em;}.cls-3{letter-spacing:-0.09em;}.cls-4{letter-spacing:-0.08em;}.cls-5{fill:#cb3233;}</style></defs><text class="cls-1" transform="translate(17.98 504.44)">SETIA <tspan class="cls-2" x="308.64" y="0">J</tspan><tspan class="cls-3" x="361.44" y="0">A</tspan><tspan class="cls-4" x="423.43" y="0">Y</tspan><tspan x="484.39" y="0">A</tspan></text><path class="cls-5" d="M564,429.43a295.19,295.19,0,0,0,30-130.07C594,135.68,461.32,3,297.64,3S1.29,135.68,1.29,299.36a295.19,295.19,0,0,0,30,130.07ZM69.64,215.72q16.65-14.49,46.75-14.51H215l-.32-.25,237.75.25-26.59,35.14-16.53-.15H350.22V359.89h89.35l0-.07h17.07q23,0,32.68-7.38c6.39-4.92,9.62-14.18,9.62-27.77V201.49h43.37V324.67q0,23.59-7.33,39.18t-24.4,23.2Q493.53,394.7,464,394.7H448.13v0H350.22v.06H306.85V236.2h-38.1v.26L121,236.19q-11.67,0-18.17,5.29c-4.34,3.51-6.51,9.17-6.51,16.94s2.17,13.41,6.51,16.94,10.39,5.29,18.17,5.29h97q30.1,0,46.76,14.77t16.68,43.78q0,28.74-16.68,43.64t-46.76,14.92H57.57l32-35.25,123.83.28q11.92,0,18.3-5.56t6.38-18q0-12.46-6.38-18c-4.25-3.69-10.35-5.56-18.3-5.56h-97q-30.09,0-46.75-14.49T53,258.42Q53,230.22,69.64,215.72Z" transform="translate(-1.29 -3)"/><path class="cls-5" d="M97.75,518.14a296.33,296.33,0,0 0,399.78,0Z" transform="translate(-1.29 -3)"/></svg>
                Manajemen User
            </div>
        </header>
             <div class="card" style="padding: 24px;">
                <div class="toolbar">
                    <div class="button-group-mobile">
                    <button class="btn btn-success" onclick="window.location.href='index.php'">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#fdfdfd" height="800px" width="800px" version="1.1" id="Layer_1" viewBox="0 0 512 512" xml:space="preserve">
                        <g>
                        <g>
                            <path d="M320,112H192V48h-32L0,208l160,160h32v-64h160c88.365,0,112,71.635,112,160h48V304C512,197.962,426.038,112,320,112z"/>
                        </g>
                    </g>
                    </svg>
                    </button>
                    <button class="btn btn-primary" onclick="openAddModal()">+ Tambah User</button>
                    </div>
                </div>
             

            <div id="alertContainer"></div>
                <div class="table-wrapper">
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Last Login</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <?php foreach ($users as $user): ?>
                            <tr data-id="<?= $user['id'] ?>">
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                <td><span class="badge badge-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span></td>
                                <td><span class="badge badge-<?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span></td>
                                <td><?= date('d-m-Y', strtotime($user['created_at'])) ?></td>
                                <td><?= $user['last_login'] ? date('d-m-Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                                <td style="text-align:center;">
                                    <div class="button-group-mobile">
                                        <button class="btn btn-info btn-sm" onclick="editUser(<?= $user['id'] ?>)">Edit</button>
                                        <button class="btn btn-warning btn-sm" onclick="resetPassword(<?= $user['id'] ?>)">Reset</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser(<?= $user['id'] ?>)">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
        </div>

    <!-- Modal Tambah User -->
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div style="display:flex; justify-content:space-between; margin-bottom:24px;">
                <h2 style="font-size:1.25rem;">Tambah User</h2>
                <button type="button" onclick="closeModal('addModal')" style="background:none; border:none; font-size:1.8rem; cursor:pointer;">&times;</button>
            </div>
            <form id="addForm" onsubmit="submitAdd(event)">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" id="add_username" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="add_password" class="form-input" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="add_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="add_email" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select id="add_role" class="form-select">
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="add_status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" style="width:100%;">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <div style="display:flex; justify-content:space-between; margin-bottom:24px;">
                <h2 style="font-size:1.25rem;">Edit User</h2>
                <button type="button" onclick="closeModal('editModal')" style="background:none; border:none; font-size:1.8rem; cursor:pointer;">&times;</button>
            </div>
            <form id="editForm" onsubmit="submitEdit(event)">
                <input type="hidden" id="edit_id">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" id="edit_username" class="form-input" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="edit_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="edit_email" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select id="edit_role" class="form-select">
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="edit_status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" style="width:100%;">Perbarui</button>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal-overlay" id="resetModal">
        <div class="modal">
            <div style="display:flex; justify-content:space-between; margin-bottom:24px;">
                <h2 style="font-size:1.25rem;">Reset Password</h2>
                <button type="button" onclick="closeModal('resetModal')" style="background:none; border:none; font-size:1.8rem; cursor:pointer;">&times;</button>
            </div>
            <form id="resetForm" onsubmit="submitReset(event)">
                <input type="hidden" id="reset_id">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" id="reset_password" class="form-input" required minlength="6">
                </div>
                <button type="submit" class="btn btn-warning" style="width:100%;">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        function getCsrfToken() {
   		return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
		}
        function showAlert(message, type = 'success') {
            const container = document.getElementById('alertContainer');
            const className = type === 'success' ? 'alert-success' : 'alert-error';
            container.innerHTML = `<div class="${className}">${message}</div>`;
            setTimeout(() => container.innerHTML = '', 3000);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        async function submitAdd(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'add_user');
            formData.append('username', document.getElementById('add_username').value);
            formData.append('password', document.getElementById('add_password').value);
            formData.append('name', document.getElementById('add_name').value);
            formData.append('email', document.getElementById('add_email').value);
            formData.append('role', document.getElementById('add_role').value);
            formData.append('status', document.getElementById('add_status').value);

            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                showAlert('User berhasil ditambahkan');
                closeModal('addModal');
                location.reload(); // sederhana, reload table
            } else {
                showAlert(result.error || 'Gagal menambah user', 'error');
            }
        }

        async function editUser(id) {
            const res = await fetch('api.php?action=get_user&id=' + id);
            const user = await res.json();
            if (user.error) {
                showAlert(user.error, 'error');
                return;
            }
            document.getElementById('edit_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_status').value = user.status;
            document.getElementById('editModal').classList.add('active');
        }

        async function submitEdit(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'update_user');
            formData.append('id', document.getElementById('edit_id').value);
            formData.append('name', document.getElementById('edit_name').value);
            formData.append('email', document.getElementById('edit_email').value);
            formData.append('role', document.getElementById('edit_role').value);
            formData.append('status', document.getElementById('edit_status').value);

            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                showAlert('User berhasil diperbarui');
                closeModal('editModal');
                location.reload();
            } else {
                showAlert(result.error || 'Gagal update user', 'error');
            }
        }

        function resetPassword(id) {
            document.getElementById('reset_id').value = id;
            document.getElementById('resetModal').classList.add('active');
        }

        async function submitReset(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'reset_password');
            formData.append('id', document.getElementById('reset_id').value);
            formData.append('new_password', document.getElementById('reset_password').value);

            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                showAlert('Password berhasil direset');
                closeModal('resetModal');
            } else {
                showAlert(result.error || 'Gagal reset password', 'error');
            }
        }

        async function deleteUser(id) {
            if (!confirm('Hapus user ini?')) return;
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'delete_user');
            formData.append('id', id);

            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                showAlert('User dihapus');
                location.reload();
            } else {
                showAlert(result.error || 'Gagal hapus user', 'error');
            }
        }

        // Tutup modal jika klik overlay
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('active');
            });
        });
    </script>
</body>
</html>