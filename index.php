<?php
require_once 'auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
redirectIfNotLoggedIn();
$user = $_SESSION;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setia Jaya</title>
    <link rel="icon" type="image/png" href="assets/icon.ico"/>
    <!-- Libraries -->
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://unpkg.com/tesseract.js@v4.0.2/dist/tesseract.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ===== CSS SAMA SEPERTI ASLI (tidak diubah) ===== */
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
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 20px; animation: slideUp 1s ease-out backwards; }
        
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
        .badge-inc { background: #d1fae5; color: #065f46; }
        .badge-exp { background: #fee2e2; color: #991b1b; }
        .badge-cicil { background: #e0e7ff; color: #4338ca; }
        .badge-tempo { background: #fef3c7; color: #92400e; }

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

        /* Thumbnail gambar modern */
        /* ===== THUMBNAIL GAMBAR MODERN ===== */
        .img-preview {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 16px;
            border: 3px solid var(--bg-surface);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }

        .img-preview:hover {
            transform: scale(2) translateY(-4px);
            box-shadow: 0 20px 30px rgba(79, 70, 229, 0.25);
            border-color: var(--primary);
            z-index: 20;
            position: relative;
        }

        /* Efek grid untuk kolom gambar */
        td:has(.img-preview) {
            text-align: center;
            vertical-align: middle;
        }

        /* ===== MODAL PREVIEW GAMBAR PREMIUM ===== */
        #imageModal .modal {
            max-width: 900px;
            width: 90%;
            padding: 24px;
            background: var(--bg-surface);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        #imageModal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border);
        }

        #imageModal .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #imageModal .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            color: var(--text-muted);
            padding: 0 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        #imageModal .close-btn:hover {
            background: var(--bg-input);
            color: var(--danger);
            transform: scale(1.1);
        }

        #imageModal .image-container {
            max-height: 70vh;
            overflow: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--bg-body);
            border-radius: 16px;
            padding: 8px;
        }

        #imageModal .preview-image {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        #imageModal .preview-image:hover {
            transform: scale(1.02);
        }

        #imageModal .image-footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .download-btn {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .download-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(79,70,229,0.3);
        }
        /* ===== ZOOM CONTROLS ===== */
        .zoom-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 300px;
            transition: transform 0.1s ease;
        }

        .zoom-image {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1);
            transform-origin: center center;
            cursor: grab;
        }

        .zoom-image:active {
            cursor: grabbing;
        }

        .zoom-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            background: var(--bg-surface);
            padding: 6px 12px;
            border-radius: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
        }

        .zoom-btn {
            background: var(--bg-input);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .zoom-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .zoom-btn:active {
            transform: scale(0.95);
        }

        .zoom-reset {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 0 16px;
            border-radius: 30px;
            width: auto;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .zoom-level {
            font-size: 0.85rem;
            color: var(--text-muted);
            min-width: 60px;
            text-align: center;
        }
        /* ===== BURGER MENU ===== */
        .nav-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 30px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 10;
        }

        .hamburger span {
            width: 30px;
            height: 3px;
            background: var(--text-main);
            border-radius: 10px;
            transition: all 0.3s linear;
            transform-origin: 1px;
        }

        /* Animasi saat menu aktif */
        .hamburger.active span:first-child {
            transform: rotate(45deg);
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        .hamburger.active span:last-child {
            transform: rotate(-45deg);
        }

        /* Menu navigasi (desktop: tampil inline) */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        /* Tampilan mobile */
        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }
            .nav-menu {
                display: none;
                position: absolute;
                top: 60px;
                right: 0;
                flex-direction: column;
                background-color: transparent;
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 20px;
                gap: 15px;
                box-shadow: var(--shadow-lg);
                z-index: 1000;
                min-width: 200px;
            }
            .nav-menu.active {
                display: flex;
            }
            /* Pastikan tombol dalam menu tetap rapi */
            .nav-menu .theme-btn {
                width: 27%;
                justify-content: center;
            }
            /* Atur ulang posisi brand jika perlu */
            .brand {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div class="brand">
            <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 592.71 592.71"><defs><style>.cls-1{font-size:93.78px;fill:#231f20;font-family:YamakaPersonalUsed, Yamaka;}.cls-2{letter-spacing:-0.03em;}.cls-3{letter-spacing:-0.09em;}.cls-4{letter-spacing:-0.08em;}.cls-5{fill:#cb3233;}</style></defs><text class="cls-1" transform="translate(17.98 504.44)">SETIA <tspan class="cls-2" x="308.64" y="0">J</tspan><tspan class="cls-3" x="361.44" y="0">A</tspan><tspan class="cls-4" x="423.43" y="0">Y</tspan><tspan x="484.39" y="0">A</tspan></text><path class="cls-5" d="M564,429.43a295.19,295.19,0,0,0,30-130.07C594,135.68,461.32,3,297.64,3S1.29,135.68,1.29,299.36a295.19,295.19,0,0,0,30,130.07ZM69.64,215.72q16.65-14.49,46.75-14.51H215l-.32-.25,237.75.25-26.59,35.14-16.53-.15H350.22V359.89h89.35l0-.07h17.07q23,0,32.68-7.38c6.39-4.92,9.62-14.18,9.62-27.77V201.49h43.37V324.67q0,23.59-7.33,39.18t-24.4,23.2Q493.53,394.7,464,394.7H448.13v0H350.22v.06H306.85V236.2h-38.1v.26L121,236.19q-11.67,0-18.17,5.29c-4.34,3.51-6.51,9.17-6.51,16.94s2.17,13.41,6.51,16.94,10.39,5.29,18.17,5.29h97q30.1,0,46.76,14.77t16.68,43.78q0,28.74-16.68,43.64t-46.76,14.92H57.57l32-35.25,123.83.28q11.92,0,18.3-5.56t6.38-18q0-12.46-6.38-18c-4.25-3.69-10.35-5.56-18.3-5.56h-97q-30.09,0-46.75-14.49T53,258.42Q53,230.22,69.64,215.72Z" transform="translate(-1.29 -3)"/><path class="cls-5" d="M97.75,518.14a296.33,296.33,0,0 0,399.78,0Z" transform="translate(-1.29 -3)"/></svg>
            Kas - Setia Jaya
        </div>
          <div class="nav-container">
              <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
       		 </button>
            <div class="nav-menu" id="navMenu">
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <button class="theme-btn">
                <a href="users.php">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" id="Layer_1" width="20px" height="20px" viewBox="-7.04 -7.04 78.08 78.08" enable-background="new 0 0 64 64" xml:space="preserve" fill="#a3246e" stroke="#a3246e">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                    <g id="SVGRepo_iconCarrier"> <g> <g> <path fill="#4bdf0c" d="M63.329,57.781C62.954,57.219,53.892,44,31.999,44C10.112,44,1.046,57.219,0.671,57.781 c-1.223,1.84-0.727,4.32,1.109,5.547c1.836,1.223,4.32,0.727,5.547-1.109C7.397,62.117,14.347,52,31.999,52 c17.416,0,24.4,9.828,24.674,10.219C57.446,63.375,58.712,64,60.009,64c0.758,0,1.531-0.219,2.211-0.672 C64.056,62.102,64.556,59.621,63.329,57.781z"/> <path fill="#4bdf0c" d="M31.999,40c8.836,0,16-7.16,16-16v-8c0-8.84-7.164-16-16-16s-16,7.16-16,16v8 C15.999,32.84,23.163,40,31.999,40z M23.999,16c0-4.418,3.586-8,8-8c4.422,0,8,3.582,8,8v8c0,4.418-3.578,8-8,8 c-4.414,0-8-3.582-8-8V16z"/> </g> <path fill="#b0f000" d="M23.999,16c0-4.418,3.586-8,8-8c4.422,0,8,3.582,8,8v8c0,4.418-3.578,8-8,8c-4.414,0-8-3.582-8-8V16z"/> </g> </g>
                    </svg>
                </a>
            </button>
            <button class="theme-btn"> 
                <a href="predictions.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="none">
                    <path d="M17 10H21V19H17V10Z" stroke="#3730a3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 5H14V19H10V5Z" stroke="#be0b0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 13H7V19H3V13Z" stroke="#3730a3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </button>
             <button class="theme-btn"> 
                <a href="log.php">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="#253793" width="20px" height="20px" viewBox="0 0 512 512" stroke="#253793">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                    <g id="SVGRepo_iconCarrier">
                    <path d="M504 255.531c.253 136.64-111.18 248.372-247.82 248.468-59.015.042-113.223-20.53-155.822-54.911-11.077-8.94-11.905-25.541-1.839-35.607l11.267-11.267c8.609-8.609 22.353-9.551 31.891-1.984C173.062 425.135 212.781 440 256 440c101.705 0 184-82.311 184-184 0-101.705-82.311-184-184-184-48.814 0-93.149 18.969-126.068 49.932l50.754 50.754c10.08 10.08 2.941 27.314-11.313 27.314H24c-8.837 0-16-7.163-16-16V38.627c0-14.254 17.234-21.393 27.314-11.314l49.372 49.372C129.209 34.136 189.552 8 256 8c136.81 0 247.747 110.78 248 247.531zm-180.912 78.784l9.823-12.63c8.138-10.463 6.253-25.542-4.21-33.679L288 256.349V152c0-13.255-10.745-24-24-24h-16c-13.255 0-24 10.745-24 24v135.651l65.409 50.874c10.463 8.137 25.541 6.253 33.679-4.21z"/>
                    </g>
                    </svg>
                </a>
            </button>
            <?php endif; ?>
             <button class="theme-btn"> <a href="logout.php">
                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f20202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-power"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 6a7.75 7.75 0 1 0 10 0" /><path d="M12 4l0 8" /></svg>
                </a>
            </button>
            <button class="theme-btn" onclick="window.toggleTheme()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
          </div>
        </div>
    </header>
     <section class="ai-section card" style="padding: 30px;">
        <div class="ai-header">
            <div class="ai-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83-2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 4.93l2.83 2.83"/></svg>
                AI Setia Jaya
            </div>
            <span style="font-size:0.9rem; color:var(--text-muted)">Analisis Keuangan Real-Time</span>
        </div>

        <div class="analysis-grid">
            <!-- Left: Health Score -->
            <div class="health-card">
                <div class="health-circle" id="healthCircle">
                    <div class="health-score" id="healthScore">0</div>
                </div>
                <div class="health-text">Skor Kesehatan Keuangan</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;" id="healthLabel">Menghitung...</div>
            </div>

            <!-- Right: Metrics List -->
            <div class="insights-list">
                <div class="insight-item insight-neutral">
                    <div class="insight-icon">🔥</div>
                    <div class="insight-content">
                        <strong>Rasio Pengeluaran Harian</strong>
                        <span id="burnRate">Rp 0 / hari</span>
                    </div>
                </div>
                <div class="insight-item insight-good">
                    <div class="insight-icon">📅</div>
                    <div class="insight-content">
                        <strong>Runway (Aman Sampai)</strong>
                        <span id="runway">0 hari lagi</span>
                    </div>
                </div>
                <div class="insight-item insight-bad" id="debtItem" style="display:none;">
                    <div class="insight-icon">⚠️</div>
                    <div class="insight-content">
                        <strong>Rasio Hutang</strong>
                        <span id="debtRatio">0% dari Aset</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Tips -->
        <div class="ai-tips">
            <div class="ai-tips-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 .9-3.8 8.5 8.5 0 0 1 7.6-4.7 8.38 8.38 0 0 1 3.8.9L21 3l-1.9 5.7a8.38 8.38 0 0 1 .9 3.8z"/></svg>
                Rekomendasi Otomatis
            </div>
            <p style="font-size: 0.9rem; line-height: 1.6; color: var(--text-main);" id="aiTip">
                Sedang menganalisis data Anda...
            </p>
        </div>
    </section>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="card">
            <div class="stat-label">Saldo Kas</div>
            <div class="stat-value" id="valBalance">Rp 0</div>
        </div>
        <div class="card">
            <div class="stat-label" style="color: var(--success)">Pemasukan</div>
            <div class="stat-value" id="valIncome" style="color: var(--success)">Rp 0</div>
        </div>
        <div class="card">
            <div class="stat-label" style="color: var(--danger)">Pengeluaran</div>
            <div class="stat-value" id="valExpense" style="color: var(--danger)">Rp 0</div>
        </div>
        <div class="card">
            <div class="stat-label" style="color: var(--primary)">Sisa Hutang</div>
            <div class="stat-value" id="valDebt" style="color: var(--primary)">Rp 0</div>
        </div>
    </div>

    <!-- Top 5 Items Section -->
    <div class="top-section">
        <div class="card" style="animation: slideUp 1s ease-out backwards;">
            <div class="top-card-header">
                <div class="top-title" style="color: var(--success)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    Top 5 Pemasukan
                </div>
            </div>
            <div id="topIncomeList" class="top-list"></div>
        </div>

        <div class="card" style="animation: slideUp 1s ease-out backwards; animation-delay: 0.1s;">
            <div class="top-card-header">
                <div class="top-title" style="color: var(--danger)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                    Top 5 Pengeluaran
                </div>
            </div>
            <div id="topExpenseList" class="top-list"></div>
        </div>
    </div>

    <!-- Controls & Table -->
    <div class="card" style="padding: 24px;">
        <div class="toolbar">
            <div class="search-box">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="search-input" placeholder="Cari transaksi..." onkeyup="window.filterTable(this.value)">
            </div>
             <div class="toolbar-actions">
                <!-- Period Select -->
                <select id="downloadPeriod" class="form-select">
                    <option value="all">Semua Waktu</option>
                    <option value="day">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>

                <!-- Responsive Button Group -->
                <div class="button-group-mobile">
                    <button class="btn btn-info" onclick="window.downloadExcel()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span style="@media(max-width:400px){display:none;}">Excel</span>
                    </button>
                    <button class="btn btn-danger" onclick="window.downloadPDF()" title="Download PDF">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 2 14 2 14 2"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span style="@media(max-width:400px){display:none;}">PDF</span>
                    </button>
                    <!-- Tambah button spans 2 columns on mobile -->
                    <button class="btn btn-primary btn-primary-mobile" onclick="window.openModal()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah
                    </button>
                </div>
            </div>
        </div>
         <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                       <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                        <th>Total Tagihan</th>
                        <th>Sisa</th>
                        <th style="text-align:right;">Progress</th>
                        <th style="text-align:center;">Gambar</th> <!-- KOLOM BARU -->
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
             <div id="emptyState" style="text-align:center; padding: 40px; color: var(--text-muted); display:none;">Belum ada data.</div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;"></div>
            <span style="font-size: 0.9rem; color: var(--text-muted);">Menampilkan <span id="showCount">0</span> data</span>
            <div style="display:flex; gap: 6px;">
                <button class="btn btn-sm btn-outline" onclick="window.changePage(-1)">&lt;</button>
                <button class="btn btn-sm btn-outline" onclick="window.changePage(1)">&gt;</button>
            </div>
        </div>
    </div>    
</div>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div style="display:flex; justify-content:space-between; margin-bottom:24px;">
            <h2 style="font-size:1.25rem; font-weight:700;" id="modalTitle">Tambah Transaksi</h2>
            <button type="button" onclick="window.closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>

        <form id="transForm" onsubmit="window.saveTrans(event)">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" id="editId">
            
            <!-- OCR -->
            <div class="ocr-box" onclick="document.getElementById('ocrFile').click()">
                <input type="file" id="ocrFile" hidden accept="image/*" onchange="window.processOCR(this)">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <div style="font-weight:600; color:var(--primary); margin-top:6px;">Scan Struk Belanja</div>
                <div style="font-size:0.8rem; color:var(--text-muted)">AI akan membaca otomatis</div>
            </div>
            <div id="ocrStatus" style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px;"></div>
            <div id="ocrRawText" onclick="this.style.display='none'" title="Klik untuk tutup"></div>

             <div class="form-group">
                    <label class="form-label">Upload Gambar (Struk/Nota)</label>
                    <input type="file" class="form-input" id="inpImage" accept="image/*">
                    <small style="color:var(--text-muted);">Format: JPG, PNG (maks 2MB)</small>
            </div>

            <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-input" id="inpNotes" rows="2" placeholder="Opsional"></textarea>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-input" id="inpDate" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe</label>
                    <select class="form-select" id="inpType" onchange="window.updateCats()">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" class="form-input" id="inpDesc" placeholder="Semen MU 200 sak" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select" id="inpStatus" onchange="window.toggleCicilanFields()">
                    <option value="lunas">Lunas (Cash)</option>
                    <option value="tempo">Tempo (Belum Bayar)</option>
                    <option value="cicilan">Cicilan (Kredit)</option>
                </select>
            </div>

            <!-- Cicilan Fields -->
            <div class="cicilan-section" id="cicilanFields">
                <div class="form-group">
                    <label class="form-label">Total Harga (Rp)</label>
                    <input type="text" class="form-input" id="inpTotalPrice" placeholder="0" oninput="window.formatInputRp(this)">
                </div>
                <div class="form-group">
                    <label class="form-label">Sudah Dibayar (Rp)</label>
                    <input type="text" class="form-input" id="inpPaidAmount" placeholder="0" oninput="window.formatInputRp(this); window.calcRemainder()">
                    <small style="color:var(--text-muted)">Uang muka / angsuran</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Jatuh Tempo (Berikutnya)</label>
                    <input type="date" class="form-input" id="inpDueDate">
                </div>
                <div style="padding: 10px; background: rgba(255,255,255,0.5); border-radius:8px; margin-bottom:10px; border:1px solid var(--border);">
                    <span style="font-size:0.85rem; font-weight:600;">Sisa Cicilan: </span>
                    <span id="calcRemaining" style="font-size:0.9rem; font-weight:700; color:var(--danger);">Rp 0</span>
                </div>
            </div>

            <div class="form-group" id="normalAmountField">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="text" class="form-input" id="inpAmount" placeholder="0" oninput="window.formatInputRp(this)">
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select class="form-select" id="inpCategory"></select>
            </div>

            <button type="submit" class="btn btn-success" style="width:100%; justify-content:center; padding: 14px; font-size: 1rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Data
            </button>
        </form>
    </div>
</div>

<div class="toast" id="toast">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastMsg">Berhasil</span>
</div>
<!-- Modal Preview Gambar Premium -->
<!-- Modal Preview Gambar Premium + Zoom -->
<div class="modal-overlay" id="imageModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Preview Gambar
            </div>
            <button type="button" class="close-btn" onclick="window.closeImageModal()">&times;</button>
        </div>

        <!-- Container dengan scroll otomatis -->
        <div class="image-container" id="imageContainer">
            <div class="zoom-container" id="zoomContainer">
                <img id="previewImage" class="zoom-image" src="" alt="Preview transaksi">
            </div>
        </div>

        <div class="image-footer">
            <div style="display: flex; align-items: center; gap: 16px;">
                <span id="imageFileName">-</span>
                
                <!-- Kontrol Zoom -->
                <div class="zoom-controls">
                    <button class="zoom-btn" onclick="window.zoomOut()" title="Perkecil (Ctrl -)">−</button>
                    <span class="zoom-level" id="zoomLevel">100%</span>
                    <button class="zoom-btn" onclick="window.zoomIn()" title="Perbesar (Ctrl +)">+</button>
                    <button class="zoom-btn zoom-reset" onclick="window.zoomReset()" title="Reset zoom">Reset</button>
                </div>
            </div>
            
            <a id="downloadImageLink" href="#" download class="download-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Unduh
            </a>
        </div>
    </div>
</div>
<!-- Modal Riwayat Pembayaran -->
<div class="modal-overlay" id="paymentHistoryModal">
    <div class="modal" style="max-width: 700px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="font-size:1.25rem; font-weight:700;">Riwayat Pembayaran Cicilan</h3>
            <button type="button" onclick="closePaymentHistoryModal()" style="background:none; border:none; font-size:1.8rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>
        <div id="paymentHistoryContent" style="max-height: 400px; overflow-y: auto;">
            <!-- Akan diisi oleh JavaScript -->
            <div style="text-align:center; padding:30px; color:var(--text-muted);">Memuat data...</div>
        </div>
    </div>
</div>

<script>
    // ============================================
    //  SETIA JAYA - KAS SYSTEM (FIXED VERSION)
    // ============================================

    // ---------- GLOBAL VARIABLES ----------
    window.transactions = [];
    window.categories = { income: [], expense: [] };
    window.currentPage = 1;
    window.perPage = 10;
    window.searchQuery = "";
    window.userId = <?= isset($user['id']) ? $user['id'] : 0 ?>;
    window.csrfToken = '<?= $_SESSION['csrf_token'] ?>';

    // ---------- HELPER FUNCTIONS ----------
    window.formatRupiah = function(n) {
        if (n === undefined || n === null || n === 0) return "Rp 0";
        return "Rp " + new Intl.NumberFormat('id-ID').format(n);
    };

    window.formatInputRp = function(input) {
        let value = (input.value || "").toString().replace(/\D/g, '');
        input.value = value ? parseInt(value, 10).toLocaleString('id-ID') : '';
    };

    window.cleanRp = function(s) {
        if (!s) return 0;
        return parseInt(s.toString().replace(/\./g, ''), 10) || 0;
    };

    window.formatDate = function(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    window.showToast = function(msg, type = 'success') {
        const toast = document.getElementById('toast');
        document.getElementById('toastMsg').innerText = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    };

    window.toggleTheme = function() {
        const body = document.body;
        body.setAttribute('data-theme', body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    };

    // ---------- FETCH DATA FROM API (FIXED) ----------
    async function fetchTransactions() {
        try {
            const url = 'api.php?action=get_transactions'; // tanpa csrf_token
            const res = await fetch(url);
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            const data = await res.json();
            if (!Array.isArray(data)) {
                console.error('API mengembalikan bukan array:', data);
                window.transactions = [];
            } else {
                window.transactions = data;
            }
        } catch (e) {
            console.error('Gagal ambil transaksi:', e);
            window.transactions = [];
        }
        window.render();
    }

    async function fetchCategories() {
        try {
            const url = 'api.php?action=get_categories'; // tanpa csrf_token
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            if (data && typeof data === 'object' && data.income && data.expense) {
                window.categories = data;
            } else {
                console.warn('Kategori tidak valid, gunakan default');
                window.categories = { income: ['Umum'], expense: ['Umum'] };
            }
        } catch (e) {
            console.error('Gagal ambil kategori:', e);
            window.categories = { income: ['Umum'], expense: ['Umum'] };
        }
        window.updateCats();
    }

    // ---------- CATEGORY DROPDOWN ----------
    window.updateCats = function() {
        const type = document.getElementById('inpType').value;
        const sel = document.getElementById('inpCategory');
        if (window.categories && window.categories[type]) {
            sel.innerHTML = window.categories[type].map(c => `<option value="${c}">${c}</option>`).join('');
        } else {
            sel.innerHTML = '<option value="">Pilih kategori</option>';
        }
    };

    // ---------- CICILAN FIELDS TOGGLE ----------
    window.toggleCicilanFields = function() {
        const status = document.getElementById('inpStatus').value;
        const cicilanDiv = document.getElementById('cicilanFields');
        const amountDiv = document.getElementById('normalAmountField');
        const paidParent = document.getElementById('inpPaidAmount').parentElement;
        const dueParent = document.getElementById('inpDueDate').parentElement;

        if (status === 'cicilan') {
            cicilanDiv.style.display = 'block';
            amountDiv.style.display = 'none';
            paidParent.style.display = 'block';
            dueParent.style.display = 'block';
        } else if (status === 'tempo') {
            cicilanDiv.style.display = 'block';
            amountDiv.style.display = 'none';
            paidParent.style.display = 'none';
            dueParent.style.display = 'block';
        } else {
            cicilanDiv.style.display = 'none';
            amountDiv.style.display = 'block';
            paidParent.style.display = 'block';
            dueParent.style.display = 'block';
        }
        window.calcRemainder();
    };

    window.calcRemainder = function() {
        const total = window.cleanRp(document.getElementById('inpTotalPrice').value);
        const paid = window.cleanRp(document.getElementById('inpPaidAmount').value);
        document.getElementById('calcRemaining').innerText = window.formatRupiah(total - paid);
    };

    // ---------- MODAL CONTROL ----------
    window.openModal = function(id = null) {
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.add('active');

        document.getElementById('transForm').reset();
        document.getElementById('editId').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Transaksi';
        document.getElementById('ocrStatus').innerText = '';
        document.getElementById('ocrRawText').style.display = 'none';
        document.getElementById('inpNotes').value = '';
        document.getElementById('inpImage').value = '';

        // Hapus notifikasi gambar jika ada
        const oldNote = document.getElementById('imgNote');
        if (oldNote) oldNote.remove();

        // Default tanggal hari ini
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('inpDate').value = today;
        document.getElementById('inpStatus').value = 'lunas';
        window.toggleCicilanFields();
        window.updateCats();

        // Mode EDIT
        if (id !== null && id !== undefined) {
            const targetId = String(id);
            const t = window.transactions.find(tx => String(tx.id) === targetId);
            if (!t) return;

            document.getElementById('editId').value = t.id;
            document.getElementById('modalTitle').innerText = 'Edit Transaksi';
            document.getElementById('inpDate').value = t.date;
            document.getElementById('inpDesc').value = t.desc || '';
            document.getElementById('inpType').value = t.type;
            document.getElementById('inpStatus').value = t.status;
            window.updateCats();
            document.getElementById('inpCategory').value = t.category;
            document.getElementById('inpNotes').value = t.notes || '';

            window.toggleCicilanFields();

            if (t.status === 'cicilan' || t.status === 'tempo') {
                document.getElementById('inpTotalPrice').value = t.amount.toLocaleString('id-ID');
                document.getElementById('inpPaidAmount').value = (t.paidAmount || 0).toLocaleString('id-ID');
                document.getElementById('inpDueDate').value = t.dueDate || '';
            } else {
                document.getElementById('inpAmount').value = t.amount.toLocaleString('id-ID');
            }

            // Tampilkan info gambar jika ada
            if (t.image) {
                const imgLabel = document.querySelector('label[for="inpImage"]');
                if (imgLabel) {
                    const note = document.createElement('small');
                    note.id = 'imgNote';
                    note.style.display = 'block';
                    note.style.color = 'var(--success)';
                    note.innerText = '✅ File: ' + t.image.split('/').pop();
                    imgLabel.parentNode.insertBefore(note, imgLabel.nextSibling);
                }
            }
        }
    };

    window.closeModal = function() {
        document.getElementById('modalOverlay').classList.remove('active');
        const note = document.getElementById('imgNote');
        if (note) note.remove();
    };

    // ---------- SAVE TRANSACTION (via API) ----------
   window.saveTrans = async function(e) {
    e.preventDefault();

    const id = document.getElementById('editId').value;
    const date = document.getElementById('inpDate').value;
    const desc = document.getElementById('inpDesc').value;
    const type = document.getElementById('inpType').value;
    const category = document.getElementById('inpCategory').value;
    const status = document.getElementById('inpStatus').value;
    const notes = document.getElementById('inpNotes').value;

    let amount = 0, paidAmount = 0, dueDate = '';

    if (status === 'cicilan' || status === 'tempo') {
        amount = window.cleanRp(document.getElementById('inpTotalPrice').value);
        if (status === 'cicilan') {
            paidAmount = window.cleanRp(document.getElementById('inpPaidAmount').value);
        } else {
            paidAmount = 0;
        }
        dueDate = document.getElementById('inpDueDate').value || '';
    } else {
        amount = window.cleanRp(document.getElementById('inpAmount').value);
    }

    if (amount <= 0) {
        alert('Jumlah uang harus lebih dari 0');
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('date', date);
    formData.append('desc', desc);
    formData.append('type', type);
    formData.append('category', category);
    formData.append('status', status);
    formData.append('amount', amount);
    formData.append('paidAmount', paidAmount);
    formData.append('dueDate', dueDate);
    formData.append('notes', notes);
    formData.append('csrf_token', window.csrfToken); // pakai token global

    const fileInput = document.getElementById('inpImage');
    if (fileInput.files[0]) {
        formData.append('image', fileInput.files[0]);
    }

    try {
        const res = await fetch('api.php?action=save_transaction', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();
        if (result.success) {
            window.showToast('Data tersimpan');
            window.closeModal();
            fetchTransactions();
        } else {
            alert('Gagal menyimpan: ' + (result.error || 'Unknown error'));
        }
    } catch (err) {
        alert('Gagal menyimpan: ' + err.message);
    }
};

    // ---------- DELETE TRANSACTION ----------
    window.delTrans = async function(id) {
        if (!confirm('Hapus transaksi ini?')) return;
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', window.csrfToken);
        try {
            const res = await fetch('api.php?action=delete_transaction', {
                method: 'POST',
                body: fd
            });
            const result = await res.json();
            if (result.success) {
                window.showToast('Data dihapus');
                fetchTransactions();
            }
        } catch (err) {
            alert(err.message);
        }
    };

    // ---------- PAY INSTALLMENT ----------
    window.payInstallment = function(id) {
        const amount = prompt('Jumlah pembayaran (Rp):', '');
        if (!amount) return;
        const clean = amount.replace(/\D/g, '');
        if (!clean) return;

        const fd = new FormData();
        fd.append('id', id);
        fd.append('payment', clean);
        fd.append('csrf_token', window.csrfToken);

        fetch('api.php?action=pay_installment', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.showToast('Pembayaran dicatat');
                    fetchTransactions();
                } else {
                    alert('Gagal: ' + (data.error || 'Unknown error'));
                }
            });
    };

    // ---------- EDIT TRANSACTION ----------
    window.editTrans = function(id) {
        window.openModal(id);
    };

    // ---------- RENDER TABLE & STATS (dengan pengecekan) ----------
    window.render = function() {
        // Pastikan transactions adalah array
        if (!Array.isArray(window.transactions)) {
            console.warn('transactions bukan array, set ke []');
            window.transactions = [];
        }

        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        // Filter berdasarkan pencarian
        let data = window.transactions.filter(t =>
            (t.desc || '').toLowerCase().includes(window.searchQuery.toLowerCase())
        );

        // Urutkan dari terbaru
        data.sort((a, b) => new Date(b.date) - new Date(a.date));

        const total = data.length;
        const start = (window.currentPage - 1) * window.perPage;
        const end = start + window.perPage;
        const pageData = data.slice(start, end);

        document.getElementById('showCount').innerText = total > 0
            ? `${start + 1}-${Math.min(end, total)} dari ${total}`
            : '0';

        if (total === 0) {
            document.getElementById('emptyState').style.display = 'block';
        } else {
            document.getElementById('emptyState').style.display = 'none';
            const today = new Date().toISOString().split('T')[0];

            pageData.forEach(t => {
                const tr = document.createElement('tr');
                let statusBadge = '', dueDateDisplay = '-', remainingDisplay = '-', progressHtml = '-', totalDisplay = window.formatRupiah(t.amount);
                let isOverdue = false;

                if (t.status === 'lunas') {
                    statusBadge = `<span class="badge badge-inc">LUNAS</span>`;
                    totalDisplay = t.type === 'income'
                        ? `<span style="color:var(--success)">+${window.formatRupiah(t.amount)}</span>`
                        : `<span style="color:var(--danger)">-${window.formatRupiah(t.amount)}</span>`;
                } else if (t.status === 'tempo') {
                    statusBadge = `<span class="badge badge-tempo">TEMPO</span>`;
                    dueDateDisplay = window.formatDate(t.dueDate);
                    remainingDisplay = `<span style="color:var(--danger); font-weight:bold;">${window.formatRupiah(t.amount)}</span>`;
                    if (t.dueDate < today) { tr.classList.add('overdue-row'); isOverdue = true; }
                } else if (t.status === 'cicilan') {
                    statusBadge = `<span class="badge badge-cicil">CICILAN</span>`;
                    dueDateDisplay = window.formatDate(t.dueDate);
                    const paid = t.paidAmount || 0;
                    const totalVal = t.amount;
                    const remain = totalVal - paid;
                    const pct = Math.min(Math.round((paid / totalVal) * 100), 100);
                    remainingDisplay = `<span style="color:${remain > 0 ? 'var(--danger)' : 'var(--success)'}; font-weight:bold;">${window.formatRupiah(remain)}</span>`;
                    totalDisplay = window.formatRupiah(totalVal);
                    progressHtml = `<div style="text-align:right; font-size:0.7rem; margin-bottom:2px;">${pct}%</div>
                                    <div class="progress-track"><div class="progress-fill" style="width:${pct}%"></div></div>`;
                    if (t.dueDate < today) { tr.classList.add('overdue-row'); isOverdue = true; }
                }

                const safeId = parseInt(t.id, 10);
                // Kolom gambar
                let gambarHtml = '-';
                if (t.image) {
                    const fileName = t.image.split('/').pop();
                    gambarHtml = `<img src="${t.image}" class="img-preview" onclick="window.viewImage('${t.image}')" alt="${fileName}" title="Klik untuk perbesar">`;
                }

                tr.innerHTML = `
                    <td>${window.formatDate(t.date)}</td>
                    <td><div style="font-weight:600;">${t.desc || ''}</div><div style="font-size:0.75rem; color:var(--text-muted);">${t.category || ''}</div></td>
                    <td>${statusBadge}</td>
                    <td style="${isOverdue ? 'color:var(--danger); font-weight:bold;' : ''}">${dueDateDisplay}</td>
                    <td>${totalDisplay}</td>
                    <td>${remainingDisplay}</td>
                    <td>${progressHtml}</td>
                    <td style="text-align:center;">${gambarHtml}</td>
                    <td style="text-align:center;">
                        ${t.status === 'cicilan' ? `<button class="btn btn-success btn-icon" onclick="window.payInstallment(${safeId})" style="color:var(--success)" title="Bayar Cicilan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-coin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7v10" /></svg>
                        </button>` : ''}
                        ${t.status === 'cicilan' ? `
                        <button class="btn btn-primary btn-icon" onclick="window.viewPaymentHistory(${safeId})" style="color:var(--primary)" title="Riwayat Pembayaran">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            
                        </button>` : ''}
                        <button class="btn btn-warning btn-icon" onclick="window.editTrans(${safeId})" style="color:var(--warning)" title="Edit Transaksi">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn btn-danger btn-icon" onclick="window.delTrans(${safeId})" style="color:var(--danger)" title="Hapus Transaksi">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Update statistik dan analisis
        window.calcStats();
        window.runAIAnalysis();
        window.renderTopItems();
    };

    // ---------- FILTER & PAGINATION ----------
    window.filterTable = function(v) {
        window.searchQuery = v;
        window.currentPage = 1;
        window.render();
    };

    window.changePage = function(d) {
        window.currentPage += d;
        if (window.currentPage < 1) window.currentPage = 1;
        window.render();
    };

    // ---------- STATISTIK KEUANGAN ----------
    window.calcStats = function() {
        if (!Array.isArray(window.transactions)) return;
        let income = 0, expense = 0, debt = 0;
        window.transactions.forEach(t => {
            const val = t.amount || 0;
            if (t.type === 'income') income += val;
            else {
                if (t.status === 'lunas') expense += val;
                else if (t.status === 'cicilan') {
                    expense += (t.paidAmount || 0);
                    debt += (val - (t.paidAmount || 0));
                } else if (t.status === 'tempo') debt += val;
            }
        });
        document.getElementById('valIncome').innerText = window.formatRupiah(income);
        document.getElementById('valExpense').innerText = window.formatRupiah(expense);
        document.getElementById('valBalance').innerText = window.formatRupiah(income - expense);
        document.getElementById('valDebt').innerText = window.formatRupiah(debt);
    };

    // ---------- AI ANALYSIS ----------
    window.runAIAnalysis = function() {
        if (!Array.isArray(window.transactions)) return;
        let income = 0, expense = 0, debt = 0;
        window.transactions.forEach(t => {
            const val = t.amount || 0;
            if (t.type === 'income') income += val;
            else {
                if (t.status === 'lunas') expense += val;
                else if (t.status === 'cicilan') {
                    expense += (t.paidAmount || 0);
                    debt += (val - (t.paidAmount || 0));
                } else if (t.status === 'tempo') debt += val;
            }
        });

        const balance = income - expense;
        const burnRate = expense / 30 || 0;
        const runway = burnRate > 0 ? Math.floor(balance / burnRate) : 999;
        const runwayText = runway >= 999 ? "∞ (Aman Selamanya)" : `${runway} hari lagi`;

        let score = 100;
        if (expense > income) score -= 40;
        const debtRatio = debt / (income || 1);
        if (debtRatio > 0.5) score -= 30;
        else if (debtRatio > 0.2) score -= 15;
        if (runway < 30 && runway < 999) score -= 20;
        if (score < 0) score = 0;

        let tips = [];
        if (expense > income) tips.push("⚠️ Pengeluaran Anda melebihi pemasukan. Evaluasi kategori 'Piutang' dan 'Cicilan'.");
        else if (debtRatio > 0.5) tips.push("⚠️ Hutang Anda sudah > 50% dari pendapatan. Sebaiknya kurangi pengambilan kredit baru.");
        else if (burnRate > 0 && runway < 30) tips.push("⚠️ Aset kas Anda hanya bertahan kurang dari 1 bulan. Segera tambah dana darurat.");
        else if (burnRate === 0 && income > 0) tips.push("✅ Keuangan stabil! Pertimbangkan untuk berinvestasi keuangan yang tidak terpakai.");
        else tips.push("✅ Kondisi keuangan Anda sehat. Pertahankan pola hemat!");

        document.getElementById('burnRate').innerText = window.formatRupiah(burnRate) + " / hari";
        document.getElementById('runway').innerText = runwayText;
        document.getElementById('healthScore').innerText = score;
        document.getElementById('healthLabel').innerText =
            score > 80 ? "Sangat Sehat" : (score > 60 ? "Sehat" : (score > 40 ? "Kurang Sehat" : "Kritis"));

        const circle = document.getElementById('healthCircle');
        const color = score > 80 ? 'var(--success)' : (score > 50 ? 'var(--warning)' : 'var(--danger)');
        circle.style.background = `conic-gradient(${color} 0% ${score}%, var(--bg-input) ${score}% 100%)`;

        const debtItem = document.getElementById('debtItem');
        if (debt > 0) {
            debtItem.style.display = 'flex';
            const ratio = Math.round((debt / (income || 1)) * 100);
            document.getElementById('debtRatio').innerText = `${ratio}% dari Total Pemasukan`;
        } else {
            debtItem.style.display = 'none';
        }

        document.getElementById('aiTip').innerText = tips.join(' ');
    };

    // ---------- TOP 5 ITEMS ----------
    window.renderTopItems = function() {
        if (!Array.isArray(window.transactions)) return;
        const processTop = (type) => {
            const grouped = {};
            let maxVal = 0;

            window.transactions.forEach(t => {
                if (t.type === type) {
                    const val = t.amount || 0;
                    grouped[t.category] = (grouped[t.category] || 0) + val;
                    if (grouped[t.category] > maxVal) maxVal = grouped[t.category];
                }
            });

            const sorted = Object.entries(grouped)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);

            if (sorted.length === 0) {
                return `<div style="text-align:center; padding:20px; color:var(--text-muted);">Tidak ada data</div>`;
            }

            return sorted.map((item, index) => {
                const [name, amount] = item;
                const pct = maxVal > 0 ? (amount / maxVal) * 100 : 0;
                const colorClass = type === 'income' ? 'fill-inc' : 'fill-exp';
                const textColor = type === 'income' ? 'var(--success)' : 'var(--danger)';
                return `
                    <div class="top-item">
                        <div class="top-meta">
                            <div class="top-name">
                                <span class="top-rank">${index + 1}</span>
                                <span>${name}</span>
                            </div>
                            <span style="color:${textColor}">${window.formatRupiah(amount)}</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar-fill ${colorClass}" style="width: ${pct}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        };

        document.getElementById('topIncomeList').innerHTML = processTop('income');
        document.getElementById('topExpenseList').innerHTML = processTop('expense');
    };

    // ---------- DOWNLOAD EXCEL ----------
    window.downloadExcel = function() {
        if (typeof XLSX === 'undefined') {
            alert("Library Excel sedang dimuat. Coba lagi.");
            return;
        }
        if (!window.transactions || window.transactions.length === 0) {
            window.showToast("Belum ada data transaksi.");
            return;
        }

        const period = document.getElementById('downloadPeriod').value;
        const filtered = filterByPeriod(window.transactions, period);

        if (filtered.length === 0) {
            window.showToast("Tidak ada data di periode ini.");
            return;
        }

        const wsData = filtered.map(t => ({
            Tanggal: t.date,
            Keterangan: t.desc,
            Tipe: t.type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            Status: t.status.toUpperCase(),
            Kategori: t.category,
            Total: t.amount,
            Dibayar: t.paidAmount || 0,
            Sisa: (t.status === 'cicilan' || t.status === 'tempo') ? (t.amount - (t.paidAmount || 0)) : 0,
            JatuhTempo: t.dueDate || '-',
            Catatan: t.notes || '',
            Gambar: t.image || ''
        }));

        const ws = XLSX.utils.json_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Laporan");
        XLSX.writeFile(wb, `Laporan_Kas_STJ_${period}.xlsx`);
        window.showToast("Excel berhasil diunduh");
    };

    // ---------- FILTER BY PERIOD (helper) ----------
    function filterByPeriod(data, period) {
        const now = new Date();
        return data.filter(t => {
            const tDate = new Date(t.date);
            if (period === 'all') return true;
            if (period === 'day') return tDate.toDateString() === now.toDateString();
            if (period === 'month') return tDate.getMonth() === now.getMonth() && tDate.getFullYear() === now.getFullYear();
            if (period === 'year') return tDate.getFullYear() === now.getFullYear();
            if (period === 'week') {
                const curr = new Date(now);
                const first = curr.getDate() - curr.getDay() + 1;
                const last = first + 6;
                const firstDay = new Date(curr.setDate(first));
                const lastDay = new Date(curr.setDate(last));
                return tDate >= firstDay && tDate <= lastDay;
            }
            return true;
        });
    }

    // ---------- PERIOD LABEL (untuk PDF) ----------
    function getPeriodLabel(period) {
        const now = new Date();
        if (period === 'all') return "Semua Data Tersimpan";
        if (period === 'day') return `Hari Ini: ${now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`;
        if (period === 'week') {
            const day = now.getDay();
            const diff = now.getDate() - day + (day === 0 ? -6 : 1);
            const start = new Date(now);
            start.setDate(diff);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            const fmt = { day: 'numeric', month: 'short', year: 'numeric' };
            return `Minggu Ini: ${start.toLocaleDateString('id-ID', fmt)} - ${end.toLocaleDateString('id-ID', fmt)}`;
        }
        if (period === 'month') return `Bulan Ini: ${now.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}`;
        if (period === 'year') return `Tahun Ini: ${now.getFullYear()}`;
    }

    // ---------- DOWNLOAD PDF ----------
    window.downloadPDF = function() {
        const { jsPDF } = window.jspdf;
        if (!jsPDF) {
            alert("Library PDF belum siap. Coba refresh.");
            return;
        }
        if (!window.transactions || window.transactions.length === 0) {
            window.showToast("Belum ada data transaksi.");
            return;
        }

        const period = document.getElementById('downloadPeriod').value;
        const filtered = filterByPeriod(window.transactions, period);

        if (filtered.length === 0) {
            window.showToast("Tidak ada data di periode ini.");
            return;
        }

        const tableBody = filtered.map(t => [
            window.formatDate(t.date),
            t.desc,
            t.type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            t.status.toUpperCase(),
            t.category,
            window.formatRupiah(t.amount),
            (t.paidAmount || 0) > 0 ? window.formatRupiah(t.paidAmount) : '-',
            (t.status === 'cicilan' || t.status === 'tempo') ? window.formatRupiah(t.amount - (t.paidAmount || 0)) : '-',
            t.dueDate ? window.formatDate(t.dueDate) : '-'
        ]);

        const doc = new jsPDF();

        doc.setFontSize(18);
        doc.setTextColor(79, 70, 229);
        doc.text("Laporan Kas STJ", 14, 20);

        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        doc.text(getPeriodLabel(period), 14, 28);

        doc.autoTable({
            head: [['Tgl', 'Ket', 'Tipe', 'Status', 'Kat', 'Total', 'Bayar', 'Sisa', 'Jth Tempo']],
            body: tableBody,
            startY: 40,
            theme: 'grid',
            styles: { fontSize: 8, cellPadding: 3, valign: 'middle', lineColor: [220, 220, 220], lineWidth: 0.1 },
            headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold', halign: 'center' },
            columnStyles: {
                0: { cellWidth: 20 },
                1: { cellWidth: 40 },
                6: { halign: 'right', fontStyle: 'bold' }
            },
            alternateRowStyles: { fillColor: [245, 245, 245] }
        });

        doc.save(`Laporan_kas_STJ_${period}.pdf`);
        window.showToast("PDF berhasil diunduh");
    };

    // ---------- OCR (TESSERACT) ----------
    window.preprocessImage = function(file) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const scale = Math.min(1, 1000 / img.width);
                canvas.width = img.width * scale;
                canvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;
                for (let i = 0; i < data.length; i += 4) {
                    const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
                    const val = avg > 128 ? 255 : 0;
                    data[i] = data[i + 1] = data[i + 2] = val;
                }
                ctx.putImageData(imageData, 0, 0);
                resolve(canvas.toDataURL('image/jpeg'));
            };
            img.src = URL.createObjectURL(file);
        });
    };

    window.findTotalAmount = function(text) {
        const lines = text.split('\n');
        let bestCandidate = 0;
        const keywords = /total|jumlah|grand|bayar|amount|tagihan|tunai/i;
        lines.forEach(line => {
            if (keywords.test(line)) {
                const numbers = line.match(/[\d.,]+/g);
                if (numbers) {
                    const maxInLine = Math.max(...numbers.map(n => parseInt(n.replace(/\D/g, '')) || 0));
                    if (maxInLine > bestCandidate) bestCandidate = maxInLine;
                }
            }
        });
        if (bestCandidate < 1000) {
            const allNumbers = text.match(/[\d.,]+/g);
            if (allNumbers) {
                const allValues = allNumbers.map(n => parseInt(n.replace(/\D/g, '')) || 0);
                const largeValues = allValues.filter(v => v > 1000);
                if (largeValues.length > 0) bestCandidate = Math.max(...largeValues);
            }
        }
        return bestCandidate;
    };

    window.processOCR = function(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const statusEl = document.getElementById('ocrStatus');
        const rawTextEl = document.getElementById('ocrRawText');
        statusEl.innerHTML = `<span style="color:var(--text-muted)">1. Processing Image...</span>`;
        rawTextEl.style.display = 'none';

        window.preprocessImage(file).then(processedDataUrl => {
            statusEl.innerHTML = `<span style="color:var(--text-muted)">2. Reading Text...</span>`;
            Tesseract.recognize(processedDataUrl, 'eng', {
                logger: m => {
                    if (m.status === 'recognizing text') {
                        statusEl.innerHTML = `<span style="color:var(--text-muted)">2. Reading... ${Math.round(m.progress * 100)}%</span>`;
                    }
                }
            }).then(({ data: { text } }) => {
                const amount = window.findTotalAmount(text);
                rawTextEl.innerText = "Hasil Scan Raw (Klik untuk tutup):\n\n" + text;
                rawTextEl.style.display = 'block';
                if (amount > 0) {
                    document.getElementById('inpTotalPrice').value = amount.toLocaleString('id-ID');
                    document.getElementById('inpAmount').value = amount.toLocaleString('id-ID');
                    document.getElementById('inpDesc').value = "Scan Struk Nota";
                    statusEl.innerHTML = `<span style="color:var(--success)">✅ Found: ${window.formatRupiah(amount)}</span>`;
                } else {
                    statusEl.innerHTML = `<span style="color:var(--danger)">❌ Total not found.</span>`;
                }
            }).catch(err => {
                console.error(err);
                statusEl.innerHTML = `<span style="color:var(--danger)">Error</span>`;
            });
        });
        input.value = '';
    };

    // ---------- INITIALIZATION ----------
    document.addEventListener('DOMContentLoaded', () => {
        fetchCategories();
        fetchTransactions();
        document.getElementById('inpType').addEventListener('change', window.updateCats);
        document.getElementById('paymentHistoryModal').addEventListener('click', function(e) {
            if (e.target === this) closePaymentHistoryModal();
        });
    });

    // ===== IMAGE PREVIEW MODAL =====
    let currentZoom = 1;
    const ZOOM_MIN = 0.5;
    const ZOOM_MAX = 3;
    const ZOOM_STEP = 0.1;

    window.zoomIn = function() {
        if (currentZoom < ZOOM_MAX) {
            currentZoom = Math.min(currentZoom + ZOOM_STEP, ZOOM_MAX);
            applyZoom();
        }
    };

    window.zoomOut = function() {
        if (currentZoom > ZOOM_MIN) {
            currentZoom = Math.max(currentZoom - ZOOM_STEP, ZOOM_MIN);
            applyZoom();
        }
    };

    window.zoomReset = function() {
        currentZoom = 1;
        applyZoom();
    };

    function applyZoom() {
        const img = document.getElementById('previewImage');
        if (img) {
            img.style.transform = `scale(${currentZoom})`;
            document.getElementById('zoomLevel').innerText = Math.round(currentZoom * 100) + '%';
        }
    }

    window.viewImage = function(imageSrc) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('previewImage');
        const fileNameSpan = document.getElementById('imageFileName');
        const downloadLink = document.getElementById('downloadImageLink');

        currentZoom = 1;
        applyZoom();

        img.src = imageSrc;
        const fileName = imageSrc.split('/').pop();
        fileNameSpan.textContent = fileName || 'Gambar';
        downloadLink.href = imageSrc;
        downloadLink.download = fileName || 'image.jpg';

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeImageModal = function() {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            if (!modal.classList.contains('active')) {
                document.getElementById('previewImage').src = '';
                currentZoom = 1;
                applyZoom();
            }
        }, 300);
    };

    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            window.closeImageModal();
        }
    });

    document.getElementById('imageContainer').addEventListener('wheel', function(e) {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            if (e.deltaY < 0) {
                window.zoomIn();
            } else {
                window.zoomOut();
            }
        }
    }, { passive: false });

    // ===== PAYMENT HISTORY =====
    window.viewPaymentHistory = async function(transactionId) {
        const modal = document.getElementById('paymentHistoryModal');
        const content = document.getElementById('paymentHistoryContent');
        content.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Memuat data...</div>';
        modal.classList.add('active');

        try {
            const res = await fetch(`api.php?action=get_payment_history&transaction_id=${transactionId}`); // tanpa csrf_token
            const data = await res.json();
            
            if (data.error) {
                content.innerHTML = `<div style="text-align:center; padding:30px; color:var(--danger);">${data.error}</div>`;
                return;
            }

            if (data.length === 0) {
                content.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Belum ada pembayaran tercatat.</div>';
                return;
            }

            let html = `
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--bg-body);">
                            <th style="padding:12px; text-align:left;">Tanggal</th>
                            <th style="padding:12px; text-align:left;">Jumlah</th>
                            <th style="padding:12px; text-align:left;">Keterangan</th>
                            <th style="padding:12px; text-align:left;">Input oleh</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            data.forEach(p => {
                html += `
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px;">${window.formatDate(p.payment_date)}</td>
                        <td style="padding:12px; font-weight:600; color:var(--success);">${window.formatRupiah(p.amount)}</td>
                        <td style="padding:12px;">${p.notes || '-'}</td>
                        <td style="padding:12px;">${p.created_by_name || '-'}</td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            content.innerHTML = html;
        } catch (err) {
            content.innerHTML = `<div style="text-align:center; padding:30px; color:var(--danger);">Gagal memuat data: ${err.message}</div>`;
        }
    };

    window.closePaymentHistoryModal = function() {
        document.getElementById('paymentHistoryModal').classList.remove('active');
    };
    // Toggle burger menu
    window.toggleMenu = function() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    };

    // Tutup menu jika klik di luar (opsional)
    document.addEventListener('click', function(event) {
        const navMenu = document.getElementById('navMenu');
        const hamburger = document.querySelector('.hamburger');
        if (!navMenu || !hamburger) return;
        if (!navMenu.contains(event.target) && !hamburger.contains(event.target)) {
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });
</script>
</body>
</html>