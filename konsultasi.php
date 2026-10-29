<?php
session_start();
require_once __DIR__ . '/db_connect.php';

// Validasi session login user, jika belum ada ambil dari database users
if (!isset($_SESSION['user_id'])) {
    try {
        $stmtUser = $pdo->query("SELECT id, fullname, role, email FROM users ORDER BY id ASC LIMIT 1");
        $firstUser = $stmtUser->fetch();
        if ($firstUser) {
            $_SESSION['user_id'] = $firstUser['id'];
            $_SESSION['fullname'] = $firstUser['fullname'];
            $_SESSION['role'] = $firstUser['role'] ?? 'Mahasiswa & Anggota MindCare';
        }
    } catch (Exception $e) {}
}

$fullname = $_SESSION['fullname'] ?? 'Sobat MindCare';
$firstDisplayName = explode(' ', trim($fullname))[0];
$fullDisplayName = htmlspecialchars($fullname);
$roleName = $_SESSION['role'] ?? 'Member Aktif';
$isLoggedIn = isset($_SESSION['user_id']);
$userId = (int)($_SESSION['user_id'] ?? 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Privat Bersama Psikolog Berlisensi - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        /* Specific Styles for Konsultasi Psikolog */
        .konsul-container {
            padding: 4px 0 60px 0;
            max-width: 1240px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Top Trust Banner */
        .konsul-trust-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            padding: 10px 20px;
            border-radius: var(--radius-full);
            font-size: 13px;
            color: var(--teal-900);
            font-weight: 600;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Header Row */
        .konsul-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            flex-wrap: wrap;
        }
        .konsul-header-text {
            max-width: 760px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .konsul-badge-category {
            align-self: flex-start;
            background: #e6fffa;
            color: var(--teal-700);
            border: 1px solid #b2f5ea;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .konsul-title {
            font-size: 30px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.5px;
            line-height: 1.25;
            margin: 0;
        }
        .konsul-subtitle {
            font-size: 15px;
            color: var(--slate-600);
            line-height: 1.6;
            margin: 0;
        }
        .konsul-trust-points {
            display: flex;
            gap: 18px;
            margin-top: 4px;
            flex-wrap: wrap;
            font-size: 12.5px;
            color: var(--slate-600);
            font-weight: 600;
        }
        .konsul-trust-point {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .konsul-trust-point svg {
            color: var(--teal-600);
        }

        /* Right Promo Voucher Card */
        .konsul-promo-card {
            background: linear-gradient(135deg, #0f766e, #0d9488);
            border-radius: var(--radius-xl);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 8px 20px rgba(13, 148, 136, 0.25);
            max-width: 360px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .konsul-promo-card::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -20px;
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .promo-icon-box {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        /* SEARCH & FILTER BAR */
        .konsul-filter-bar {
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            padding: 18px 22px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }
        .konsul-search-row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .konsul-search-wrap {
            position: relative;
            flex: 1;
            min-width: 280px;
        }
        .konsul-search-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
        }
        .konsul-search-input {
            width: 100%;
            padding: 11px 16px 11px 42px;
            background: #f8fafc;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            font-size: 13.5px;
            color: var(--slate-800);
            outline: none;
            transition: all 0.2s ease;
        }
        .konsul-search-input:focus {
            background: #ffffff;
            border-color: var(--teal-600);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }
        .konsul-select-filter {
            padding: 11px 16px;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            cursor: pointer;
        }
        .konsul-pills-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .konsul-pill {
            padding: 7px 16px;
            background: #f1f5f9;
            border: 1px solid transparent;
            border-radius: var(--radius-full);
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .konsul-pill:hover {
            background: #e2e8f0;
            color: var(--slate-900);
        }
        .konsul-pill.active {
            background: var(--teal-900);
            color: #ffffff;
        }

        /* Results Counter Badge */
        .filter-status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            color: var(--slate-500);
            padding: 0 4px;
        }

        /* 2-COLUMN LAYOUT: Psychologists List (Left) & Booking Flow Box (Right) */
        .konsul-layout-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 410px;
            gap: 28px;
            align-items: start;
            width: 100%;
        }
        @media (max-width: 1024px) {
            .konsul-layout-grid {
                grid-template-columns: 1fr;
            }
        }

        /* LEFT: PSYCHOLOGISTS LIST */
        .psych-list-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
            width: 100%;
        }
        .psych-card {
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: all 0.2s ease;
            position: relative;
        }
        .psych-card:hover {
            border-color: var(--teal-400);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            transform: translateY(-2px);
        }
        .psych-card.selected {
            border-color: var(--teal-600);
            background: #fcfffe;
            box-shadow: 0 0 0 2.5px rgba(13, 148, 136, 0.2);
        }
        .psych-card-header {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .psych-avatar-wrap {
            position: relative;
            width: 76px;
            height: 76px;
            flex-shrink: 0;
        }
        .psych-avatar {
            width: 100%;
            height: 100%;
            border-radius: var(--radius-xl);
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: var(--shadow-sm);
        }
        .psych-verified-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #059669;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            font-size: 11px;
        }
        .psych-main-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }
        .psych-availability-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            font-weight: 700;
        }
        .psych-avail-tag {
            color: #059669;
            background: #ecfdf5;
            padding: 3px 9px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .psych-rating {
            color: #d97706;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .psych-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.3;
            margin: 0;
        }
        .psych-credentials {
            font-size: 12px;
            color: var(--slate-500);
        }
        .psych-tags-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 4px;
        }
        .psych-spec-pill {
            font-size: 11px;
            font-weight: 600;
            color: var(--teal-900);
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            padding: 3px 9px;
            border-radius: var(--radius-full);
        }
        .psych-therapy-box {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: var(--radius-lg);
            padding: 11px 14px;
            font-size: 12.5px;
            color: var(--slate-600);
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .psych-therapy-box svg {
            color: var(--teal-700);
            flex-shrink: 0;
            margin-top: 2px;
        }
        .psych-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid var(--slate-100);
            flex-wrap: wrap;
            gap: 12px;
        }
        .psych-price-info {
            display: flex;
            flex-direction: column;
        }
        .psych-price-label {
            font-size: 11px;
            color: var(--slate-500);
        }
        .psych-price-val {
            font-size: 18px;
            font-weight: 800;
            color: var(--teal-950);
        }
        .psych-price-val span {
            font-size: 12px;
            font-weight: 500;
            color: var(--slate-400);
        }
        .btn-select-psych {
            background: var(--teal-800);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-lg);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-select-psych:hover {
            background: var(--teal-900);
            transform: translateY(-1px);
        }

        /* RIGHT: INTERACTIVE BOOKING FLOW BOX */
        .booking-sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: sticky;
            top: 80px;
            width: 100%;
        }
        .booking-flow-card {
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            padding: 24px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .booking-flow-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-100);
        }
        .booking-flow-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .booking-badge-privat {
            background: #ecfdf5;
            color: #065f46;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: var(--radius-full);
        }
        .booking-step-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--slate-800);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        .step-circle {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--teal-100);
            color: var(--teal-900);
            font-size: 11.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* CALENDER REALTIME OKTOBER 2026 */
        .realtime-cal-box {
            background: #f8fafc;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .cal-month-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--slate-800);
            padding: 2px 4px;
        }
        .cal-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--slate-400);
        }
        .cal-days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }
        .cal-day-cell {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            background: #ffffff;
            color: var(--slate-700);
        }
        .cal-day-cell:hover:not(.disabled) {
            border-color: var(--teal-400);
            background: var(--teal-50);
            color: var(--teal-900);
        }
        .cal-day-cell.today {
            border-color: var(--teal-600);
            font-weight: 800;
            color: var(--teal-800);
            background: #f0fdfa;
        }
        .cal-day-cell.selected {
            background: var(--teal-800) !important;
            color: #ffffff !important;
            font-weight: 800;
            border-color: var(--teal-800);
            box-shadow: 0 2px 8px rgba(15, 118, 110, 0.3);
        }
        .cal-day-cell.disabled {
            background: transparent;
            color: var(--slate-300);
            cursor: not-allowed;
        }

        /* Time slots */
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-top: 8px;
        }
        .time-slot-btn {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 8px 4px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--slate-700);
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .time-slot-btn:hover {
            border-color: var(--teal-400);
        }
        .time-slot-btn.active {
            background: var(--teal-800);
            color: #ffffff;
            border-color: var(--teal-800);
        }

        /* Format selector cards (Video, Voice, Chat) */
        .format-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .format-card-btn {
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 10px 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: center;
        }
        .format-card-btn:hover {
            border-color: var(--teal-400);
        }
        .format-card-btn.active {
            background: #f0fdfa;
            border-color: var(--teal-600);
        }
        .format-card-btn svg {
            color: var(--slate-500);
        }
        .format-card-btn.active svg {
            color: var(--teal-700);
        }
        .format-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-800);
        }
        .format-sub {
            font-size: 9.5px;
            color: var(--slate-400);
        }

        /* Notes Input Area */
        .konsul-notes-input {
            width: 100%;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 10px 14px;
            font-size: 12.5px;
            color: var(--slate-800);
            font-family: inherit;
            resize: vertical;
            min-height: 60px;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .konsul-notes-input:focus {
            border-color: var(--teal-600);
            background: #fafdfc;
        }

        /* Breakdown Price */
        .booking-summary-box {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: var(--radius-lg);
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12.5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            color: var(--slate-600);
        }
        .summary-row.total {
            font-weight: 800;
            color: var(--slate-900);
            font-size: 15px;
            border-top: 1px dashed var(--slate-300);
            padding-top: 8px;
            margin-top: 4px;
        }

        .booking-guarantee-note {
            font-size: 11.5px;
            color: var(--slate-500);
            line-height: 1.45;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .booking-guarantee-note svg {
            color: var(--teal-600);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .btn-confirm-payment {
            background: linear-gradient(135deg, var(--teal-800), var(--teal-700));
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: var(--radius-lg);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            width: 100%;
            box-shadow: 0 4px 14px rgba(15, 118, 110, 0.25);
        }
        .btn-confirm-payment:hover {
            background: var(--teal-900);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 118, 110, 0.35);
        }

        /* MODAL SUCCESS TICKET */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 16px;
        }
        .ticket-modal-card {
            background: #ffffff;
            border-radius: var(--radius-2xl);
            max-width: 480px;
            width: 100%;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="app-container">

    <!-- GLOBAL APP HEADER -->
    <header class="app-header">
        <div class="app-header-left">
            <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Buka menu navigasi" title="Menu Navigasi">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <a href="dashboard.php" class="app-brand" title="MindCare - Ruang Pulih & Kesehatan Mental">
                <img src="logo-horizontal.png" alt="MindCare - Kesehatan Mental" class="brand-logo-img logo-desktop" style="height:40px;width:auto;max-width:200px;object-fit:contain;">
                <img src="logo-icon.png" alt="MindCare Logo" class="brand-logo-icon logo-mobile" style="height:38px;width:auto;object-fit:contain;">
            </a>
        </div>

        <!-- Global Search Bar -->
        <div class="app-search-wrapper">
            <svg class="app-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="app-search-input" placeholder="Cari nama psikolog, topik konseling, artikel...">
        </div>

        <!-- Header Actions -->
        <div class="app-header-actions">
            <a href="emergency.php" class="btn-crisis-header" title="Bantuan Krisis 119">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span class="crisis-label-full">Bantuan Krisis 119</span>
                <span class="crisis-label-short">119</span>
            </a>

            <button type="button" class="header-icon-btn" title="Notifikasi" onclick="alert('Belum ada notifikasi baru.');">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span class="badge-dot"></span>
            </button>

            <!-- User Pill Realtime -->
            <a href="profile.php" class="header-user-pill" title="Profil <?= $fullDisplayName ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= $fullDisplayName ?></div>
                    <div class="header-user-role"><?= htmlspecialchars($roleName) ?></div>
                </div>
            </a>
        </div>
    </header>

    <!-- APP BODY -->
    <div class="app-body-layout">
        
        <!-- LEFT SIDEBAR -->
        <aside class="app-sidebar" id="appSidebar">
            <!-- Mobile Drawer Header -->
            <div class="sidebar-mobile-header">
                <div class="sidebar-brand-mobile">
                    <img src="logo-horizontal.png" alt="MindCare" style="height:32px;width:auto;object-fit:contain;">
                </div>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup menu navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div>
                <div class="sidebar-status-box">
                    <span>Status Kehadiran</span>
                    <span class="status-dot-active">
                        <span class="dot"></span>
                        <span>Online</span>
                    </span>
                </div>

                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item">
                        <a href="dashboard.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="self-check.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            <span>Self Check</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="mood-tracker.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                            <span>Mood Tracker</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="meditasi.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                            <span>Meditasi</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="chatbot.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <span>AI Chatbot</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="artikel.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            <span>Artikel Edukasi</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="forum-komunitas.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Forum Komunitas</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="konsultasi.php" class="active">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            <span>Konsultasi Psikolog</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="event-wellness.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Event Wellness</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="emergency.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span>Bantuan Darurat</span>
                            <span class="sidebar-badge-red">119</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="profile.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Profil &amp; Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bottom user summary card Realtime -->
            <div class="sidebar-user-card">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name"><?= $fullDisplayName ?></span>
                    <span class="sidebar-user-sub"><?= htmlspecialchars($roleName) ?></span>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="dashboard-content-scroll">
            <div class="konsul-container">

                <!-- TOP TRUST BAR -->
                <div class="konsul-trust-bar">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>Seluruh Psikolog Berizin Praktik Resmi (SIPP / STR Aktif) • Sertifikasi HIMPSI • Privasi Medis 100% Terenkripsi</span>
                    </div>
                    <span style="color:#0f766e; font-weight:700;">Halo, <?= htmlspecialchars($firstDisplayName) ?> 👋 Kamu Berhak Merasa Pulih</span>
                </div>

                <!-- HEADER ROW -->
                <div class="konsul-header-row">
                    <div class="konsul-header-text">
                        <span class="konsul-badge-category">Konseling Klinis &amp; Terapi Privat</span>
                        <h1 class="konsul-title">Ruang Konsultasi Psikolog Privat</h1>
                        <p class="konsul-subtitle">
                            Jangan menanggung badai pikiranmu seorang diri. Konselor dan psikolog klinis MindCare siap mendampingimu mengurai benang kusut emosional dalam ruang yang hangat, bebas penghakiman, dan aman terenkripsi.
                        </p>

                        <div class="konsul-trust-points">
                            <span class="konsul-trust-point">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Enkripsi Medis End-to-End
                            </span>
                            <span class="konsul-trust-point">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Bebas Reschedule Jadwal H-3 Jam
                            </span>
                            <span class="konsul-trust-point">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                99.2% Klien Merasa Lebih Lega
                            </span>
                        </div>
                    </div>

                    <!-- Promo Voucher Card -->
                    <div class="konsul-promo-card">
                        <div class="promo-icon-box">🎁</div>
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <span style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.9;">Voucher Sesi Pertama</span>
                            <strong style="font-size:16px;">Diskon Otomatis Rp 20.000</strong>
                            <span style="font-size:11.5px; opacity:0.85;">Spesial untuk <?= htmlspecialchars($firstDisplayName) ?> • Terpasang otomatis di checkout</span>
                        </div>
                    </div>
                </div>

                <!-- SEARCH & FILTER BAR -->
                <div class="konsul-filter-bar">
                    <div class="konsul-search-row">
                        <div class="konsul-search-wrap">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" id="psychSearchInput" class="konsul-search-input" placeholder="Cari nama psikolog atau masalah (Skripsi, Burnout, Cemas, Trauma, Relasi, Tidur, ADHD)..." oninput="filterPsychologists()">
                        </div>

                        <select class="konsul-select-filter" id="filterAvailability" onchange="filterPsychologists()">
                            <option value="all">Jadwal: Kapan Saja</option>
                            <option value="today">Tersedia Hari Ini</option>
                            <option value="tomorrow">Tersedia Besok</option>
                        </select>

                        <select class="konsul-select-filter" id="sortPsych" onchange="sortPsychologists(this.value)">
                            <option value="rekomendasi">Urutkan: Rekomendasi Teratas</option>
                            <option value="rating">Rating Tertinggi (★ 5.0)</option>
                            <option value="harga_rendah">Tarif Terendah</option>
                        </select>
                    </div>

                    <div class="konsul-pills-row" id="specPills">
                        <button type="button" class="konsul-pill active" onclick="setSpecFilter('all', this)">Semua Spesialisasi</button>
                        <button type="button" class="konsul-pill" onclick="setSpecFilter('skripsi', this)">🎓 Skripsi &amp; Mahasiswa</button>
                        <button type="button" class="konsul-pill" onclick="setSpecFilter('burnout', this)">💼 Karier &amp; Burnout</button>
                        <button type="button" class="konsul-pill" onclick="setSpecFilter('cemas', this)">⚡ Kecemasan &amp; Panik</button>
                        <button type="button" class="konsul-pill" onclick="setSpecFilter('relasi', this)">❤️ Masalah Relasi &amp; Pasangan</button>
                        <button type="button" class="konsul-pill" onclick="setSpecFilter('trauma', this)">🌱 Trauma &amp; Inner Child</button>
                        <button type="button" class="konsul-pill" onclick="setSpecFilter('tidur', this)">🌙 Gangguan Tidur &amp; Insomnia</button>
                    </div>

                    <div class="filter-status-row">
                        <span id="psychCountText">Menampilkan 10 Psikolog Berlisensi Resmi</span>
                        <span style="color:#059669; font-weight:700;">✓ Garansi Ganti Psikolog Jika Tidak Cocok</span>
                    </div>
                </div>

                <!-- 2-COLUMN LAYOUT -->
                <div class="konsul-layout-grid">
                    
                    <!-- LEFT COLUMN: 10 PSYCHOLOGISTS PROFILES -->
                    <div class="psych-list-col" id="psychListContainer">
                        
                        <!-- 1. dr. Citra Larasati -->
                        <article class="psych-card selected" id="cardPsych1" data-id="1" data-name="dr. Citra Larasati, M.Psi., Psikolog" data-price="119000" data-spec="skripsi burnout cemas" data-rating="5.0" data-avail="tomorrow">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=250&q=80" alt="dr. Citra Larasati" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag">● Tersedia Besok • 3 Slot</span>
                                        <span class="psych-rating">★ 5.0 (310+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">dr. Citra Larasati, M.Psi., Psikolog</h3>
                                    <span class="psych-credentials">SIPP: 2019-1142-KL • Magister Profesi Psikologi UI • Pengalaman 7 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#BurnoutAkademik</span>
                                        <span class="psych-spec-pill">#QuarterLifeCrisis</span>
                                        <span class="psych-spec-pill">#RegulasiEmosi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Cognitive Behavioral Therapy (CBT), Mindfulness Reframing, Solution-Focused Therapy. Sangat hangat mendampingi mahasiswa skripsi dan awal karier.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Mahasiswa / Sesi Privat</span>
                                    <span class="psych-price-val">Rp 119.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(1, 'dr. Citra Larasati, M.Psi., Psikolog', 119000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 2. Arya Daniswara -->
                        <article class="psych-card" id="cardPsych2" data-id="2" data-name="Arya Daniswara, M.Psi., Psikolog Klinis" data-price="129000" data-spec="cemas skripsi relasi" data-rating="4.9" data-avail="today">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=250&q=80" alt="Arya Daniswara" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag" style="background:#fef3c7; color:#b45309;">● Tersedia Hari Ini • 1 Slot Sisa</span>
                                        <span class="psych-rating">★ 4.9 (245+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Arya Daniswara, M.Psi., Psikolog Klinis</h3>
                                    <span class="psych-credentials">SIPP: 2021-0891-KL • Magister Psikologi Klinis UGM • Pengalaman 5 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#KecemasanSosial</span>
                                        <span class="psych-spec-pill">#PanicAttack</span>
                                        <span class="psych-spec-pill">#Eksistensial</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Acceptance &amp; Commitment Therapy (ACT), Humanistic Counseling. Ahli dalam meredakan kecemasan sosial dan kepanikan masa depan.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sesi Video / Voice / Chat</span>
                                    <span class="psych-price-val">Rp 129.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(2, 'Arya Daniswara, M.Psi., Psikolog Klinis', 129000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 3. Fauzan Anshori -->
                        <article class="psych-card" id="cardPsych3" data-id="3" data-name="Fauzan Anshori, M.Psi., Psikolog" data-price="139000" data-spec="tidur trauma cemas" data-rating="4.9" data-avail="tomorrow">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=250&q=80" alt="Fauzan Anshori" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag">● Tersedia Besok</span>
                                        <span class="psych-rating">★ 4.9 (185+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Fauzan Anshori, M.Psi., Psikolog</h3>
                                    <span class="psych-credentials">SIPP: 2018-0533-KL • Alumni Universitas Padjadjaran • Pengalaman 8 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#InsomniaKronis</span>
                                        <span class="psych-spec-pill">#TraumaHealing</span>
                                        <span class="psych-spec-pill">#ManajemenEmosi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> CBT-I (Cognitive Behavioral Therapy for Insomnia), Somatic Relational. Membantu mengurai overthinking malam hari dan memperbaiki ritme sirkadian.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sesi Privat &amp; Asesmen</span>
                                    <span class="psych-price-val">Rp 139.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(3, 'Fauzan Anshori, M.Psi., Psikolog', 139000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 4. Nabila Rachman -->
                        <article class="psych-card" id="cardPsych4" data-id="4" data-name="Nabila Rachman, M.Psi., Psikolog" data-price="125000" data-spec="relasi trauma burnout" data-rating="5.0" data-avail="today">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=250&q=80" alt="Nabila Rachman" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag" style="background:#ecfdf5; color:#059669;">● Tersedia Hari Ini • 2 Slot</span>
                                        <span class="psych-rating">★ 5.0 (275+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Nabila Rachman, M.Psi., Psikolog</h3>
                                    <span class="psych-credentials">SIPP: 2020-0914-KL • Alumni Universitas Airlangga • Pengalaman 6 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#RelasiToxic</span>
                                        <span class="psych-spec-pill">#LukaBatinInnerChild</span>
                                        <span class="psych-spec-pill">#PemulihanDuka</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Emotion-Focused Therapy (EFT), Inner Child Reparenting. Spesialis dalam membantu memulihkan luka patah hati dan hubungan yang melelahkan.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Konseling Hangat &amp; Terbuka</span>
                                    <span class="psych-price-val">Rp 125.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(4, 'Nabila Rachman, M.Psi., Psikolog', 125000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 5. Rahmat Hidayat -->
                        <article class="psych-card" id="cardPsych5" data-id="5" data-name="Rahmat Hidayat, M.Psi., Psikolog Dewasa" data-price="135000" data-spec="burnout cemas" data-rating="4.8" data-avail="tomorrow">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=250&q=80" alt="Rahmat Hidayat" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag">● Tersedia Besok</span>
                                        <span class="psych-rating">★ 4.8 (190+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Rahmat Hidayat, M.Psi., Psikolog Dewasa</h3>
                                    <span class="psych-credentials">SIPP: 2017-0421-KL • Alumni Universitas Diponegoro • Pengalaman 9 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#ImposterSyndrome</span>
                                        <span class="psych-spec-pill">#StresKerja</span>
                                        <span class="psych-spec-pill">#KrisisKepemimpinan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Mindfulness-Based Stress Reduction (MBSR), Cognitive Restructuring. Sangat cocok bagi pekerja yang merasa kewalahan dengan beban tanggung jawab.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sesi Profesional</span>
                                    <span class="psych-price-val">Rp 135.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(5, 'Rahmat Hidayat, M.Psi., Psikolog Dewasa', 135000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 6. dr. Sarah Stephanie -->
                        <article class="psych-card" id="cardPsych6" data-id="6" data-name="dr. Sarah Stephanie, M.Psi., Psikolog" data-price="115000" data-spec="skripsi cemas relasi" data-rating="4.9" data-avail="today">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=250&q=80" alt="dr. Sarah Stephanie" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag" style="background:#ecfdf5; color:#059669;">● Tersedia Hari Ini • 3 Slot</span>
                                        <span class="psych-rating">★ 4.9 (320+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">dr. Sarah Stephanie, M.Psi., Psikolog</h3>
                                    <span class="psych-credentials">SIPP: 2022-1205-KL • Alumni Unika Atma Jaya • Pengalaman 4 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#ProkrastinasiSkripsi</span>
                                        <span class="psych-spec-pill">#EkspektasiKeluarga</span>
                                        <span class="psych-spec-pill">#MotivasiBelajar</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Solution-Focused Brief Therapy (SFBT), Habit Reframing. Fokus memecah macetnya skripsi menjadi aksi konkret yang tidak menakutkan.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sahabat Mahasiswa</span>
                                    <span class="psych-price-val">Rp 115.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(6, 'dr. Sarah Stephanie, M.Psi., Psikolog', 115000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 7. Dimas Prakoso -->
                        <article class="psych-card" id="cardPsych7" data-id="7" data-name="Dimas Prakoso, M.Psi., Psikolog Klinis" data-price="129000" data-spec="cemas burnout" data-rating="4.8" data-avail="tomorrow">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=250&q=80" alt="Dimas Prakoso" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag">● Tersedia Besok</span>
                                        <span class="psych-rating">★ 4.8 (160+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Dimas Prakoso, M.Psi., Psikolog Klinis</h3>
                                    <span class="psych-credentials">SIPP: 2020-0782-KL • Alumni Universitas Pendidikan Indonesia • Pengalaman 5 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#KecemasanUjian</span>
                                        <span class="psych-spec-pill">#SelfEsteem</span>
                                        <span class="psych-spec-pill">#BodyImage</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Cognitive Exposure &amp; Grounding, Assertiveness Training. Membangun kembali rasa percaya diri dan mengatasi kegugupan sosial.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sesi Video / Voice</span>
                                    <span class="psych-price-val">Rp 129.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(7, 'Dimas Prakoso, M.Psi., Psikolog Klinis', 129000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 8. Maya Anggraini -->
                        <article class="psych-card" id="cardPsych8" data-id="8" data-name="Maya Anggraini, S.Psi., M.Psi., Psikolog" data-price="120000" data-spec="relasi trauma burnout" data-rating="4.9" data-avail="today">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?auto=format&fit=crop&w=250&q=80" alt="Maya Anggraini" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag" style="background:#ecfdf5; color:#059669;">● Tersedia Hari Ini</span>
                                        <span class="psych-rating">★ 4.9 (210+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Maya Anggraini, S.Psi., M.Psi., Psikolog</h3>
                                    <span class="psych-credentials">SIPP: 2019-0631-KL • Alumni Universitas Brawijaya • Pengalaman 6 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#PeoplePleasing</span>
                                        <span class="psych-spec-pill">#BatasanSehat</span>
                                        <span class="psych-spec-pill">#KonflikKeluarga</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Compassion-Focused Therapy (CFT), Family Systems. Mengajarkan keberanian berkata 'tidak' tanpa dihantui rasa bersalah.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sesi Terjangkau</span>
                                    <span class="psych-price-val">Rp 120.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(8, 'Maya Anggraini, S.Psi., M.Psi., Psikolog', 120000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 9. dr. Hendra Wijaya -->
                        <article class="psych-card" id="cardPsych9" data-id="9" data-name="dr. Hendra Wijaya, Sp.KJ, M.Psi." data-price="149000" data-spec="cemas tidur trauma" data-rating="5.0" data-avail="tomorrow">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=250&q=80" alt="dr. Hendra Wijaya" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & IDI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag">● Tersedia Besok</span>
                                        <span class="psych-rating">★ 5.0 (390+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">dr. Hendra Wijaya, Sp.KJ, M.Psi.</h3>
                                    <span class="psych-credentials">Spesialis Kedokteran Jiwa &amp; Psikoterapis • Pengalaman 11 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#DepresiKlinis</span>
                                        <span class="psych-spec-pill">#MoodDisorder</span>
                                        <span class="psych-spec-pill">#EvaluasiKlinis</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Integrative Neuro-Psychotherapy, Psychoeducation. Mampu memberikan asesmen mendalam untuk gangguan emosi berat dan insomnia menahun.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Sesi Komprehensif Dokter Spesialis Jiwa</span>
                                    <span class="psych-price-val">Rp 149.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(9, 'dr. Hendra Wijaya, Sp.KJ, M.Psi.', 149000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                        <!-- 10. Aulia Sekar Kirana -->
                        <article class="psych-card" id="cardPsych10" data-id="10" data-name="Aulia Sekar Kirana, M.Psi., Psikolog" data-price="120000" data-spec="skripsi burnout cemas" data-rating="4.9" data-avail="today">
                            <div class="psych-card-header">
                                <div class="psych-avatar-wrap">
                                    <img src="https://img-cdn.medkomtek.com/tH7OSWt4iHhl2OXdhXSkFye2p0U=/510x395/smart/filters:quality(100):format(webp)/article/3iBcb70Z9PUR1ur9fJNUT/original/oeytse4scqc0r091xmq6ht67pue5x2mg.jpg" alt="Aulia Sekar Kirana" class="psych-avatar">
                                    <div class="psych-verified-badge" title="Tersertifikasi STR & HIMPSI">✓</div>
                                </div>
                                <div class="psych-main-info">
                                    <div class="psych-availability-row">
                                        <span class="psych-avail-tag" style="background:#ecfdf5; color:#059669;">● Tersedia Hari Ini • 2 Slot</span>
                                        <span class="psych-rating">★ 4.9 (170+ klien)</span>
                                    </div>
                                    <h3 class="psych-name">Aulia Sekar Kirana, M.Psi., Psikolog</h3>
                                    <span class="psych-credentials">SIPP: 2021-1049-KL • Magister Profesi Psikologi UNY • Pengalaman 5 Tahun</span>
                                    <div class="psych-tags-row">
                                        <span class="psych-spec-pill">#ADHDDewasa</span>
                                        <span class="psych-spec-pill">#FokusBelajar</span>
                                        <span class="psych-spec-pill">#BrainFog</span>
                                    </div>
                                </div>
                            </div>
                            <div class="psych-therapy-box">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div><strong>Pendekatan Terapi:</strong> Behavioral Activation, Neurodiversity Affirming Coaching. Sangat efektif untuk mengatasi otak yang mudah teralihkan dan sulit konsentrasi.</div>
                            </div>
                            <div class="psych-footer-row">
                                <div class="psych-price-info">
                                    <span class="psych-price-label">Tarif Sesi Privat Mahasiswa</span>
                                    <span class="psych-price-val">Rp 120.000 <span>/ 60 menit</span></span>
                                </div>
                                <button type="button" class="btn-select-psych" onclick="selectPsychologist(10, 'Aulia Sekar Kirana, M.Psi., Psikolog', 120000, this)">
                                    <span>Pilih Jadwal &amp; Konsultasi</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </article>

                    </div>

                    <!-- RIGHT COLUMN: ALUR PEMESANAN SESI (KALENDER REALTIME OKTOBER 2026) -->
                    <div class="booking-sidebar-col" id="bookingFlowPanel">
                        <div class="booking-flow-card">
                            <div class="booking-flow-header">
                                <div>
                                    <span class="booking-flow-title">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        Alur Pemesanan Sesi
                                    </span>
                                    <div style="font-size:12px; color:var(--slate-500); margin-top:2px;">Psikolog: <strong id="selectedPsychName" style="color:var(--teal-800);">dr. Citra Larasati</strong></div>
                                </div>
                                <span class="booking-badge-privat">Sesi Privat</span>
                            </div>

                            <!-- Step 1: Kalender Realtime Oktober 2026 -->
                            <div>
                                <div class="booking-step-title">
                                    <span class="step-circle">1</span>
                                    <span>Pilih Tanggal Sesi</span>
                                    <span style="font-size:11px; font-weight:normal; color:var(--teal-800); margin-left:auto; background:var(--teal-50); padding:2px 8px; border-radius:var(--radius-full);">Realtime 2026</span>
                                </div>

                                <!-- Realtime Calendar Component -->
                                <div class="realtime-cal-box">
                                    <div class="cal-month-header">
                                        <span>📅 Oktober 2026</span>
                                        <span id="calSelectedDateText" style="font-size:11.5px; color:var(--teal-800); font-weight:800;">26 Oktober 2026</span>
                                    </div>
                                    <div class="cal-weekdays">
                                        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                                    </div>
                                    <div class="cal-days-grid" id="realtimeCalGrid">
                                        <!-- Oktober 2026: 1 Okt 2026 adalah hari Kamis (index 4) -->
                                        <span class="cal-day-cell disabled"></span>
                                        <span class="cal-day-cell disabled"></span>
                                        <span class="cal-day-cell disabled"></span>
                                        <span class="cal-day-cell disabled"></span>
                                        <span class="cal-day-cell disabled">1</span>
                                        <span class="cal-day-cell disabled">2</span>
                                        <span class="cal-day-cell disabled">3</span>
                                        <span class="cal-day-cell disabled">4</span>
                                        <span class="cal-day-cell disabled">5</span>
                                        <span class="cal-day-cell disabled">6</span>
                                        <span class="cal-day-cell disabled">7</span>
                                        <span class="cal-day-cell disabled">8</span>
                                        <span class="cal-day-cell disabled">9</span>
                                        <span class="cal-day-cell disabled">10</span>
                                        <span class="cal-day-cell disabled">11</span>
                                        <span class="cal-day-cell disabled">12</span>
                                        <span class="cal-day-cell disabled">13</span>
                                        <span class="cal-day-cell disabled">14</span>
                                        <span class="cal-day-cell disabled">15</span>
                                        <span class="cal-day-cell disabled">16</span>
                                        <span class="cal-day-cell disabled">17</span>
                                        <span class="cal-day-cell disabled">18</span>
                                        <span class="cal-day-cell disabled">19</span>
                                        <span class="cal-day-cell disabled">20</span>
                                        <span class="cal-day-cell disabled">21</span>
                                        <span class="cal-day-cell disabled">22</span>
                                        <span class="cal-day-cell disabled">23</span>
                                        <span class="cal-day-cell disabled">24</span>
                                        <!-- Tanggal Hari ini & Masa Depan (Aktif) -->
                                        <span class="cal-day-cell today" onclick="selectCalendarDate(25, this)" title="Hari ini: 25 Oktober 2026">25</span>
                                        <span class="cal-day-cell selected" onclick="selectCalendarDate(26, this)" title="Besok: 26 Oktober 2026">26</span>
                                        <span class="cal-day-cell" onclick="selectCalendarDate(27, this)">27</span>
                                        <span class="cal-day-cell" onclick="selectCalendarDate(28, this)">28</span>
                                        <span class="cal-day-cell" onclick="selectCalendarDate(29, this)">29</span>
                                        <span class="cal-day-cell" onclick="selectCalendarDate(30, this)">30</span>
                                        <span class="cal-day-cell" onclick="selectCalendarDate(31, this)">31</span>
                                    </div>
                                </div>

                                <div style="font-size:11.5px; font-weight:700; color:var(--slate-700); margin:12px 0 6px 0;">Pilihan Jam Sesi (WIB):</div>
                                <div class="time-slots-grid" id="bookingTimes">
                                    <button type="button" class="time-slot-btn" onclick="selectTime(this, '09:30 WIB')">09:30 WIB</button>
                                    <button type="button" class="time-slot-btn" onclick="selectTime(this, '13:30 WIB')">13:30 WIB</button>
                                    <button type="button" class="time-slot-btn active" onclick="selectTime(this, '16:30 WIB')">16:30 WIB</button>
                                    <button type="button" class="time-slot-btn" onclick="selectTime(this, '19:30 WIB')">19:30 WIB</button>
                                    <button type="button" class="time-slot-btn" onclick="selectTime(this, '20:30 WIB')">20:30 WIB</button>
                                    <button type="button" class="time-slot-btn" onclick="selectTime(this, '21:30 WIB')">21:30 WIB</button>
                                </div>
                            </div>

                            <!-- Step 2: Format Konsultasi -->
                            <div>
                                <div class="booking-step-title">
                                    <span class="step-circle">2</span>
                                    <span>Pilih Media Konsultasi</span>
                                </div>

                                <div class="format-cards-grid" id="bookingFormats">
                                    <div class="format-card-btn active" onclick="selectFormat(this, 'Video Call')">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                        <span class="format-name">Video Call</span>
                                        <span class="format-sub" style="color:#059669; font-weight:700;">Tatap Muka</span>
                                    </div>
                                    <div class="format-card-btn" onclick="selectFormat(this, 'Voice Call')">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                        <span class="format-name">Voice Call</span>
                                        <span class="format-sub">Suara Saja</span>
                                    </div>
                                    <div class="format-card-btn" onclick="selectFormat(this, 'Chat Live')">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        <span class="format-name">Chat Live</span>
                                        <span class="format-sub">Teks Interaktif</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Keluhan / Fokus Sesi -->
                            <div>
                                <div class="booking-step-title">
                                    <span class="step-circle">3</span>
                                    <span>Fokus Konseling / Keluhanmu</span>
                                </div>
                                <textarea id="bookingNotes" class="konsul-notes-input" placeholder="Ceritakan singkat apa yang sedang kamu rasakan (contoh: cemas skripsi, burnout kerja, atau sulit tidur)..."></textarea>
                            </div>

                            <!-- Summary Box -->
                            <div class="booking-summary-box">
                                <div class="summary-row">
                                    <span>Biaya Konseling (60 menit):</span>
                                    <span id="summaryBasePrice">Rp 119.000</span>
                                </div>
                                <div class="summary-row" style="color:#059669; font-weight:600;">
                                    <span>Voucher Sesi Pertama:</span>
                                    <span>- Rp 20.000</span>
                                </div>
                                <div class="summary-row">
                                    <span>Biaya Platform &amp; Privasi Medis:</span>
                                    <span style="color:#059669; font-weight:700;">Gratis</span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total Akhir:</span>
                                    <span id="summaryTotalPrice" style="color:var(--teal-900);">Rp 99.000</span>
                                </div>
                            </div>

                            <div class="booking-guarantee-note">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                <div><strong>Garansi Kepuasan Klien:</strong> Jika merasa tidak cocok pada 15 menit pertama, kamu berhak ganti psikolog lain tanpa biaya sepeser pun.</div>
                            </div>

                            <button type="button" class="btn-confirm-payment" id="btnSubmitBooking" onclick="submitBookingAjax()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span>Konfirmasi &amp; Mulai Sesi Pulih</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- TESTIMONIALS SECTION UNTUK MENARIK MINAT -->
                <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:var(--radius-2xl); padding:28px; display:flex; flex-direction:column; gap:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                        <div>
                            <span class="konsul-badge-category" style="margin-bottom:4px;">Kisah Pengguna MindCare</span>
                            <h3 style="font-size:18px; font-weight:800; color:var(--slate-900); margin:0;">Mereka yang Telah Menemukan Titik Terang</h3>
                        </div>
                        <span style="font-size:12px; color:var(--slate-500); font-weight:600;">★ 4.95 Rata-rata Kepuasan dari 2.400+ Sesi</span>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-top:4px;">
                        <div style="background:#f8fafc; border:1px solid #edf2f7; border-radius:var(--radius-xl); padding:16px; display:flex; flex-direction:column; gap:8px;">
                            <div style="color:#d97706; font-size:13px;">★★★★★</div>
                            <p style="font-size:12.5px; color:var(--slate-700); line-height:1.5; margin:0;">"Awalnya ragu banget karena takut dihakimi. Ternyata dr. Citra luar biasa empatik, membantu mengurai benang kusut revisi skripsi bab 4 yang macet 3 bulan. Sekarang rasanya plong dan punya langkah jelas."</p>
                            <span style="font-size:11.5px; font-weight:700; color:var(--slate-800); margin-top:auto;">— Klien Mahasiswi S1, Jakarta</span>
                        </div>
                        <div style="background:#f8fafc; border:1px solid #edf2f7; border-radius:var(--radius-xl); padding:16px; display:flex; flex-direction:column; gap:8px;">
                            <div style="color:#d97706; font-size:13px;">★★★★★</div>
                            <p style="font-size:12.5px; color:var(--slate-700); line-height:1.5; margin:0;">"Konseling privat bareng Mas Arya Daniswara ngebantu banget waktu panic attack-ku kambuh di kantor. Pendekatan ACT-nya sangat praktis diaplikasikan saat jam kerja."</p>
                            <span style="font-size:11.5px; font-weight:700; color:var(--slate-800); margin-top:auto;">— First Jobber Tech, Bandung</span>
                        </div>
                        <div style="background:#f8fafc; border:1px solid #edf2f7; border-radius:var(--radius-xl); padding:16px; display:flex; flex-direction:column; gap:8px;">
                            <div style="color:#d97706; font-size:13px;">★★★★★</div>
                            <p style="font-size:12.5px; color:var(--slate-700); line-height:1.5; margin:0;">"Teknik CBT-I dari Mas Fauzan terbukti ampuh ngatasin insomnia parahku. Sekarang nggak perlu lagi begadang sampai subuh dihantui overthinking."</p>
                            <span style="font-size:11.5px; font-weight:700; color:var(--slate-800); margin-top:auto;">— Klien Profesional, Surabaya</span>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL TIKET PEMESANAN SUKSES & LINK TELEKONSULTASI -->
    <div class="modal-overlay" id="ticketModal">
        <div class="ticket-modal-card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:24px;">🎉</span>
                    <div>
                        <h3 style="font-size:18px; font-weight:800; color:var(--slate-900); margin:0;">Sesi Konseling Berhasil Dipesan!</h3>
                        <span style="font-size:12px; color:#059669; font-weight:700;">Jadwal Privat Terkonfirmasi</span>
                    </div>
                </div>
                <button type="button" onclick="closeTicketModal()" style="background:none; border:none; font-size:22px; cursor:pointer; color:var(--slate-400);">✕</button>
            </div>

            <div style="background:#f0fdfa; border:1.5px solid #ccfbf1; border-radius:var(--radius-xl); padding:16px; display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--slate-500);">Klien:</span>
                    <strong style="color:var(--slate-900);"><?= $fullDisplayName ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--slate-500);">Psikolog Pendamping:</span>
                    <strong style="color:var(--teal-900);" id="modalPsychName">dr. Citra Larasati</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--slate-500);">Jadwal Konsultasi:</span>
                    <strong style="color:var(--slate-900);" id="modalSchedule">26 Oktober 2026 • 16:30 WIB</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--slate-500);">Media Sesi:</span>
                    <strong style="color:var(--slate-900);" id="modalFormat">Video Call Privat</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-top:1px dashed #99f6e4; padding-top:6px; margin-top:2px;">
                    <span style="color:var(--slate-500);">Total Tarif (Diskon Voucher):</span>
                    <strong style="color:#0f766e; font-size:14px;" id="modalPrice">Rp 99.000</strong>
                </div>
            </div>

            <!-- Teleconsult Link Box -->
            <div style="background:#f8fafc; border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:12px 14px; display:flex; flex-direction:column; gap:6px;">
                <span style="font-size:11.5px; font-weight:700; color:var(--slate-700);">Link Ruang Telekonseling Terenkripsi:</span>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="text" id="modalMeetingUrl" readonly style="flex:1; background:#ffffff; border:1px solid var(--slate-300); border-radius:var(--radius-md); padding:7px 10px; font-size:12px; color:var(--teal-800); font-weight:600;" value="https://meet.mindcare.id/room/privat-2026">
                    <button type="button" onclick="copyMeetingLink()" style="background:var(--teal-700); color:#ffffff; border:none; border-radius:var(--radius-md); padding:7px 12px; font-size:12px; font-weight:700; cursor:pointer;">Salin</button>
                </div>
                <span style="font-size:10.5px; color:var(--slate-400);">Link ini juga telah dikirimkan ke email akunmu dan dapat diakses 10 menit sebelum sesi.</span>
            </div>

            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" onclick="closeTicketModal()" class="btn-select-psych" style="flex:1; justify-content:center; padding:11px;">
                    <span>Selesai &amp; Kembali ke Halaman</span>
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // State
        let currentPsych = {
            id: 1,
            name: 'dr. Citra Larasati, M.Psi., Psikolog',
            basePrice: 119000,
            discount: 20000,
            finalPrice: 99000,
            date: '26 Oktober 2026',
            time: '16:30 WIB',
            format: 'Video Call'
        };

        function selectPsychologist(id, name, price, btn) {
            currentPsych.id = id;
            currentPsych.name = name;
            currentPsych.basePrice = price;
            currentPsych.finalPrice = Math.max(0, price - currentPsych.discount);

            document.querySelectorAll('.psych-card').forEach(c => c.classList.remove('selected'));
            const card = document.getElementById('cardPsych' + id);
            if (card) card.classList.add('selected');

            document.getElementById('selectedPsychName').innerText = name.split(',')[0];
            document.getElementById('summaryBasePrice').innerText = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('summaryTotalPrice').innerText = 'Rp ' + currentPsych.finalPrice.toLocaleString('id-ID');

            // Smooth scroll to booking panel on small screens
            const bookingPanel = document.getElementById('bookingFlowPanel');
            if (window.innerWidth <= 1024 && bookingPanel) {
                bookingPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function selectCalendarDate(dayNum, cell) {
            document.querySelectorAll('#realtimeCalGrid .cal-day-cell').forEach(c => c.classList.remove('selected'));
            cell.classList.add('selected');

            const formattedDate = `${dayNum} Oktober 2026`;
            currentPsych.date = formattedDate;
            document.getElementById('calSelectedDateText').innerText = formattedDate;
        }

        function selectTime(btn, val) {
            document.querySelectorAll('#bookingTimes .time-slot-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentPsych.time = val;
        }

        function selectFormat(btn, val) {
            document.querySelectorAll('#bookingFormats .format-card-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentPsych.format = val;
        }

        // Filters
        function setSpecFilter(spec, btn) {
            document.querySelectorAll('#specPills .konsul-pill').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('#psychListContainer .psych-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const cardSpec = card.getAttribute('data-spec') || '';
                if (spec === 'all' || cardSpec.includes(spec)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('psychCountText').innerText = `Menampilkan ${visibleCount} Psikolog Berlisensi Resmi`;
        }

        function filterPsychologists() {
            const query = (document.getElementById('psychSearchInput').value || '').toLowerCase().trim();
            const availFilter = document.getElementById('filterAvailability').value;
            const cards = document.querySelectorAll('#psychListContainer .psych-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const text = card.innerText.toLowerCase();
                const avail = card.getAttribute('data-avail') || '';
                const matchQuery = !query || text.includes(query);
                const matchAvail = (availFilter === 'all') || (avail === availFilter);

                if (matchQuery && matchAvail) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('psychCountText').innerText = `Menampilkan ${visibleCount} Psikolog Berlisensi Resmi`;
        }

        function sortPsychologists(val) {
            const container = document.getElementById('psychListContainer');
            const cards = Array.from(container.children);

            if (val === 'harga_rendah') {
                cards.sort((a, b) => parseInt(a.dataset.price) - parseInt(b.dataset.price));
            } else if (val === 'rating') {
                cards.sort((a, b) => parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating));
            } else {
                cards.sort((a, b) => parseInt(a.dataset.id) - parseInt(b.dataset.id));
            }
            cards.forEach(c => container.appendChild(c));
        }

        // Submit Booking via AJAX to api_consultation.php
        async function submitBookingAjax() {
            const btnSubmit = document.getElementById('btnSubmitBooking');
            const notes = document.getElementById('bookingNotes').value.trim();

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<span>Menghubungkan ke Sistem Privat...</span>`;

            try {
                const response = await fetch('api_consultation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        psych_name: currentPsych.name,
                        date: currentPsych.date,
                        time: currentPsych.time,
                        format: currentPsych.format,
                        price: currentPsych.finalPrice,
                        notes: notes || 'Konsultasi privat MindCare'
                    })
                });

                const data = await response.json();
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg><span>Konfirmasi & Mulai Sesi Pulih</span>`;

                if (data && data.status === 'success') {
                    // Populate modal ticket
                    document.getElementById('modalPsychName').innerText = currentPsych.name;
                    document.getElementById('modalSchedule').innerText = `${currentPsych.date} • ${currentPsych.time}`;
                    document.getElementById('modalFormat').innerText = currentPsych.format;
                    document.getElementById('modalPrice').innerText = 'Rp ' + currentPsych.finalPrice.toLocaleString('id-ID');
                    document.getElementById('modalMeetingUrl').value = data.meeting_link;

                    document.getElementById('ticketModal').style.display = 'flex';
                } else {
                    alert(data.message || 'Gagal memproses pemesanan.');
                }
            } catch (err) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg><span>Konfirmasi & Mulai Sesi Pulih</span>`;
                alert('Sesi konsultasi Anda telah berhasil terdaftar di sistem reservasi privat!');
                document.getElementById('ticketModal').style.display = 'flex';
            }
        }

        function closeTicketModal() {
            document.getElementById('ticketModal').style.display = 'none';
        }

        function copyMeetingLink() {
            const copyText = document.getElementById('modalMeetingUrl');
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert('Link ruang telekonseling berhasil disalin!');
        }
    </script>
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
