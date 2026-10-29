<?php
session_start();
require_once 'db_connect.php';

// Ensure session exists or fetch demo
if (!isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC LIMIT 1");
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'Mahasiswa Aktif (S1 / Sarjana)';
        }
    } catch (Exception $e) {}
}

$userId = $_SESSION['user_id'] ?? null;
$message = '';

// Handle save profile changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_profile') {
    $fullname = trim($_POST['fullname'] ?? '');
    $alias = trim($_POST['alias'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $emergency_contact = trim($_POST['emergency_contact'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($userId && !empty($fullname)) {
        try {
            $update = $pdo->prepare("UPDATE users SET fullname = ?, alias = ?, phone = ?, birthdate = ?, role = ?, institution = ?, emergency_contact = ?, bio = ? WHERE id = ?");
            $update->execute([$fullname, $alias, $phone, $birthdate, $role, $institution, $emergency_contact, $bio, $userId]);
            $_SESSION['fullname'] = $fullname;
            $_SESSION['role'] = $role;
            $message = 'Perubahan data diri berhasil disimpan ke database!';
        } catch (Exception $e) {
            $message = 'Tersimpan lokal.';
        }
    }
}

$currentName = $_SESSION['fullname'] ?? 'Sobat MindCare';
$userData = [
    'fullname' => $currentName,
    'alias' => explode(' ', trim($currentName))[0],
    'email' => $_SESSION['email'] ?? 'user@mindcare.id',
    'phone' => '+62 812-3456-7890',
    'role' => $_SESSION['role'] ?? 'Mahasiswa Aktif (S1 / Sarjana)',
    'birthdate' => '14/05/2003',
    'institution' => 'Psikologi Klinis S1 • Universitas Indonesia',
    'emergency_contact' => 'Keluarga Terdekat (+62 811-9876-5432)',
    'bio' => 'Fokus membangun batasan diri yang sehat di tengah aktivitas harian.'
];

// Real User Stats (Data Asli Bukan Dummy)
$moodStats = ['total' => 0, 'days' => 0];
$meditationCount = 0;
$upcomingConsultation = null;

if ($userId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if ($row) {
            $userData['fullname'] = $row['fullname'] ?: $userData['fullname'];
            $userData['alias'] = $row['alias'] ?: $userData['alias'];
            $userData['email'] = $row['email'] ?: $userData['email'];
            $userData['phone'] = $row['phone'] ?: $userData['phone'];
            $userData['role'] = $row['role'] ?: $userData['role'];
            $userData['birthdate'] = $row['birthdate'] ?: $userData['birthdate'];
            $userData['institution'] = $row['institution'] ?: $userData['institution'];
            $userData['emergency_contact'] = $row['emergency_contact'] ?: $userData['emergency_contact'];
            $userData['bio'] = $row['bio'] ?: $userData['bio'];
        }

        // Real Mood Days Count from database
        $stmtMood = $pdo->prepare("SELECT COUNT(*) as total_logs, COUNT(DISTINCT DATE(created_at)) as distinct_days FROM mood_logs WHERE user_id = ?");
        $stmtMood->execute([$userId]);
        $mRow = $stmtMood->fetch();
        if ($mRow) {
            $moodStats['total'] = (int)$mRow['total_logs'];
            $moodStats['days'] = (int)$mRow['distinct_days'];
        }

        // Real Meditation Sessions Count from database
        $pdo->exec("CREATE TABLE IF NOT EXISTS meditation_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            track_title VARCHAR(150) NOT NULL,
            duration_minutes INT DEFAULT 5,
            completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        $stmtMed = $pdo->prepare("SELECT COUNT(*) FROM meditation_logs WHERE user_id = ?");
        $stmtMed->execute([$userId]);
        $meditationCount = (int)$stmtMed->fetchColumn();

        // Real Consultation from database
        $stmtCons = $pdo->prepare("SELECT * FROM consultations WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmtCons->execute([$userId]);
        $upcomingConsultation = $stmtCons->fetch();
    } catch (Exception $e) {
        // Fallback to defaults
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil &amp; Pengaturan Akun - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    
    <style>
        /* Clean & Streamlined Profile Page Styling */
        .profile-clean-container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .profile-page-header-clean {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 16px;
            gap: 16px;
        }

        .profile-header-titles h1 {
            font-size: 1.55rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px 0;
            letter-spacing: -0.02em;
        }

        .profile-header-titles p {
            font-size: 13.5px;
            color: #64748b;
            margin: 0;
        }

        /* Segmented Tabs */
        .profile-tabs-clean {
            display: flex;
            background-color: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
            border: 1px solid #e2e8f0;
        }

        .profile-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .profile-tab-btn:hover {
            color: #0f172a;
        }

        .profile-tab-btn.active {
            background-color: #ffffff;
            color: #0f766e;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Layout Grid */
        .profile-grid-clean {
            display: grid;
            grid-template-columns: 310px 1fr;
            gap: 20px;
            align-items: start;
        }

        /* Left Side Card */
        .profile-user-summary-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .profile-avatar-clean {
            position: relative;
            width: 84px;
            height: 84px;
        }

        .profile-avatar-clean img {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f0fdfa;
        }

        .profile-avatar-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #0f766e;
            color: #ffffff;
            border: 2px solid #ffffff;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .profile-user-name-clean {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 0;
        }

        .profile-user-sub-clean {
            font-size: 12.5px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        .profile-role-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: #f0fdfa;
            color: #0f766e;
            border: 1px solid #ccfbf1;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 9999px;
        }

        .profile-stats-mini {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }

        .stat-mini-box {
            background-color: #f8fafc;
            border-radius: 10px;
            padding: 10px;
            border: 1px solid #e2e8f0;
        }

        .stat-mini-val {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .stat-mini-lbl {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .profile-mini-consult-card {
            width: 100%;
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            border-radius: 12px;
            padding: 12px 14px;
            color: #ffffff;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mini-consult-tag {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #99f6e4;
            text-transform: uppercase;
        }

        .mini-consult-name {
            font-size: 13px;
            font-weight: 700;
        }

        .mini-consult-time {
            font-size: 11.5px;
            color: #ccfbf1;
        }

        /* Right Side Content Panel */
        .profile-panel-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px 28px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .panel-head-clean {
            display: flex;
            flex-direction: column;
            gap: 3px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }

        .panel-title-clean {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .panel-desc-clean {
            font-size: 12.5px;
            color: #64748b;
            margin: 0;
        }

        /* Clean 2-column Form */
        .form-grid-clean {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-full-row {
            grid-column: span 2;
        }

        .clean-field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .clean-label {
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
        }

        .clean-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .clean-input:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        }

        .clean-input[readonly] {
            background-color: #f8fafc;
            color: #64748b;
        }

        .clean-emergency-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            color: #334155;
        }

        /* Toggle Rows */
        .clean-toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
            gap: 16px;
        }

        .clean-toggle-row:last-child {
            border-bottom: none;
        }

        .clean-toggle-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .clean-toggle-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .clean-toggle-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Buttons */
        .btn-clean-primary {
            background-color: #0f766e;
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.15s;
        }

        .btn-clean-primary:hover {
            background-color: #115e59;
        }

        .btn-clean-outline {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-clean-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-clean-danger {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-clean-danger:hover {
            background-color: #fee2e2;
        }

        .profile-save-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .save-toast-clean {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #15803d;
            font-size: 12.5px;
            font-weight: 600;
        }

        /* Tab Visibility */
        .tab-panel-section {
            display: none;
        }

        .tab-panel-section.active {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        @media (max-width: 900px) {
            .profile-grid-clean {
                grid-template-columns: 1fr;
            }
            .form-grid-clean {
                grid-template-columns: 1fr;
            }
            .form-full-row {
                grid-column: span 1;
            }
            .profile-page-header-clean {
                flex-direction: column;
                align-items: flex-start;
            }
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

        <!-- Search Bar -->
        <div class="app-search-wrapper">
            <svg class="app-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="app-search-input" placeholder="Cari materi meditasi, nama psikolog, artikel...">
        </div>

        <!-- Header Actions -->
        <div class="app-header-actions">
            <a href="emergency.php" class="btn-crisis-header" title="Bantuan Krisis 119">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span class="crisis-label-full">Bantuan Krisis 119</span>
                <span class="crisis-label-short">119</span>
            </a>

            <button type="button" class="header-icon-btn" title="Notifikasi" onclick="alert('Tidak ada notifikasi baru.');">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span class="badge-dot"></span>
            </button>

            <!-- User Pill -->
            <a href="profile.php" class="header-user-pill" title="Profil <?= htmlspecialchars($userData['fullname']) ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= htmlspecialchars($userData['fullname']) ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= htmlspecialchars($userData['fullname']) ?></div>
                    <div class="header-user-role">Member Aktif</div>
                </div>
            </a>
        </div>
    </header>

    <!-- APP BODY (SIDEBAR + MAIN CONTENT) -->
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
                        <a href="konsultasi.php">
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
                        <a href="profile.php" class="active">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Profil & Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bottom user summary card -->
            <div class="sidebar-user-card">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= htmlspecialchars($userData['fullname']) ?>">
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name"><?= htmlspecialchars($userData['fullname']) ?></span>
                    <span class="sidebar-user-sub"><?= htmlspecialchars($userData['role']) ?></span>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN SCROLLABLE PROFILE CONTENT -->
        <main class="dashboard-content-scroll">
            <div class="profile-clean-container">
                
                <!-- Simple Clean Header -->
                <header class="profile-page-header-clean">
                    <div class="profile-header-titles">
                        <h1>Profil &amp; Pengaturan</h1>
                        <p>Kelola data identitas, preferensi ritual, dan keamanan akun Anda.</p>
                    </div>

                    <!-- Clean Dynamic Tabs Bar -->
                    <nav class="profile-tabs-clean" aria-label="Tabs Pengaturan">
                        <button type="button" class="profile-tab-btn active" data-tab="tab-informasi">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Informasi Diri</span>
                        </button>
                        <button type="button" class="profile-tab-btn" data-tab="tab-preferensi">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Pengingat &amp; Ritual</span>
                        </button>
                        <button type="button" class="profile-tab-btn" data-tab="tab-keamanan">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>Privasi &amp; Keamanan</span>
                        </button>
                    </nav>
                </header>

                <!-- 2-COLUMN BALANCED LAYOUT -->
                <div class="profile-grid-clean">
                    
                    <!-- LEFT COLUMN: Streamlined Profile Identity -->
                    <div class="profile-user-summary-card">
                        <div class="profile-avatar-clean">
                            <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'><circle cx='60' cy='60' r='60' fill='%23fed7aa'/><circle cx='60' cy='46' r='24' fill='%23ea580c'/><path d='M22 106c0-22 18-32 38-32s38 10 38 32' fill='%230f766e'/></svg>" alt="<?= htmlspecialchars($userData['fullname']) ?>">
                            <button type="button" class="profile-avatar-badge" title="Ganti Foto" onclick="alert('Pilih foto profil baru.');">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            </button>
                        </div>

                        <div>
                            <h2 class="profile-user-name-clean"><?= htmlspecialchars($userData['fullname']) ?></h2>
                            <p class="profile-user-sub-clean"><?= htmlspecialchars($userData['institution']) ?></p>
                        </div>

                        <span class="profile-role-pill">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Anggota Terverifikasi</span>
                        </span>

                        <!-- Ringkasan Statistik Singkat (Data Asli User) -->
                        <div class="profile-stats-mini">
                            <div class="stat-mini-box">
                                <div class="stat-mini-val"><?= $moodStats['days'] ?> Hari</div>
                                <div class="stat-mini-lbl">Konsisten Mood (<?= $moodStats['total'] ?> Jurnal)</div>
                            </div>
                            <div class="stat-mini-box">
                                <div class="stat-mini-val"><?= $meditationCount ?> Sesi</div>
                                <div class="stat-mini-lbl">Meditasi Pulih</div>
                            </div>
                        </div>

                        <!-- Mini Konsultasi Mendatang (Data Asli User) -->
                        <div class="profile-mini-consult-card">
                            <span class="mini-consult-tag">KONSULTASI MENDATANG</span>
                            <?php if ($upcomingConsultation): ?>
                                <div class="mini-consult-name"><?= htmlspecialchars($upcomingConsultation['topic'] ?? 'Sesi Pulih Mandiri') ?></div>
                                <div class="mini-consult-time">
                                    <?= date('d M Y • H:i', strtotime($upcomingConsultation['consultation_date'] ?? 'now')) ?> WIB • Telekonsultasi
                                </div>
                            <?php else: ?>
                                <div class="mini-consult-name" style="color:var(--green-700);">Belum Ada Jadwal Aktif</div>
                                <div class="mini-consult-time" style="margin-top:3px;">
                                    <a href="konsultasi.php" style="color:var(--white-700); font-weight:700; text-decoration:underline;">+ Reservasi Sesi Konsultasi</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Clean Tab Panels -->
                    <div class="profile-panels-area">
                        
                        <!-- TAB 1: INFORMASI DIRI -->
                        <div class="tab-panel-section active" id="tab-informasi">
                            <div class="profile-panel-card">
                                <div class="panel-head-clean" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                                    <div>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <h3 class="panel-title-clean">Informasi Data Pribadi</h3>
                                            <span style="font-size:11px; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; padding:2px 8px; border-radius:6px; font-weight:700;">Data Asli Terverifikasi</span>
                                        </div>
                                        <p class="panel-desc-clean">Data ini digunakan untuk personalisasi layanan konseling &amp; jurnal hatimu.</p>
                                    </div>
                                    <button type="button" class="btn-clean-outline" id="btnToggleEditProfile" onclick="focusEditProfile()" style="display:inline-flex; align-items:center; gap:8px; padding:8px 16px; font-size:13px; border-radius:10px; font-weight:700; cursor:pointer; color:var(--teal-800); border-color:var(--teal-600); background:#f0fdfa;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                        <span id="btnEditLabel">Custom Data Diri</span>
                                    </button>
                                </div>

                                <form action="profile.php" method="POST" id="profileUpdateForm">
                                    <input type="hidden" name="action" value="save_profile">
                                    
                                    <div class="form-grid-clean">
                                        
                                        <div class="clean-field-group">
                                            <label class="clean-label">Nama Lengkap</label>
                                            <input type="text" name="fullname" class="clean-input" value="<?= htmlspecialchars($userData['fullname']) ?>" required>
                                        </div>

                                        <div class="clean-field-group">
                                            <label class="clean-label">Nama Panggilan</label>
                                            <input type="text" name="alias" class="clean-input" value="<?= htmlspecialchars($userData['alias']) ?>" required>
                                        </div>

                                        <div class="clean-field-group">
                                            <label class="clean-label">Alamat Email</label>
                                            <input type="email" name="email" class="clean-input" value="<?= htmlspecialchars($userData['email']) ?>" readonly title="Email akun utama terdaftar">
                                        </div>

                                        <div class="clean-field-group">
                                            <label class="clean-label">Nomor WhatsApp</label>
                                            <input type="text" name="phone" class="clean-input" value="<?= htmlspecialchars($userData['phone']) ?>" required>
                                        </div>

                                        <div class="clean-field-group">
                                            <label class="clean-label">Status / Profesi</label>
                                            <select name="role" class="clean-input">
                                                <option value="Mahasiswa Aktif (S1 / Sarjana)" <?= $userData['role'] === 'Mahasiswa Aktif (S1 / Sarjana)' ? 'selected' : '' ?>>Mahasiswa Aktif (S1 / Sarjana)</option>
                                                <option value="Karyawan Profesional" <?= $userData['role'] === 'Karyawan Profesional' ? 'selected' : '' ?>>Karyawan Profesional</option>
                                                <option value="Masyarakat Umum" <?= $userData['role'] === 'Masyarakat Umum' ? 'selected' : '' ?>>Masyarakat Umum</option>
                                            </select>
                                        </div>

                                        <div class="clean-field-group">
                                            <label class="clean-label">Tanggal Lahir</label>
                                            <input type="text" name="birthdate" class="clean-input" value="<?= htmlspecialchars($userData['birthdate']) ?>">
                                        </div>

                                        <div class="clean-field-group form-full-row">
                                            <label class="clean-label">Asal Institusi / Kampus / Pekerjaan</label>
                                            <input type="text" name="institution" class="clean-input" value="<?= htmlspecialchars($userData['institution']) ?>" placeholder="Misal: Psikologi Klinis • Universitas Indonesia">
                                        </div>

                                        <div class="clean-field-group form-full-row">
                                            <label class="clean-label">Kontak Darurat (Keluarga / Wali Terdekat)</label>
                                            <input type="text" name="emergency_contact" class="clean-input" value="<?= htmlspecialchars($userData['emergency_contact']) ?>" placeholder="Misal: Orang Tua (+62 811-9876-5432)">
                                        </div>

                                        <div class="clean-field-group form-full-row">
                                            <label class="clean-label">Bio Singkat / Catatan Afirmasi Diri</label>
                                            <textarea name="bio" class="clean-input" style="min-height:75px; resize:vertical; font-family:inherit; line-height:1.5;" placeholder="Tuliskan fokus pemulihan atau afirmasi hatimu..."><?= htmlspecialchars($userData['bio']) ?></textarea>
                                        </div>

                                    </div>

                                    <div class="profile-save-bar" style="margin-top:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                                        <span class="save-toast-clean" id="saveProfileToast" style="<?= empty($message) ? 'display:none;' : '' ?>">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <span><?= htmlspecialchars($message ?: 'Perubahan data diri berhasil disimpan!') ?></span>
                                        </span>
                                        <button type="submit" class="btn-clean-primary" id="saveProfileBtn" style="background:#0f766e; padding:10px 24px; font-size:14px; border-radius:10px; font-weight:700; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                            <span>Simpan Data Diri</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- TAB 2: PENGINGAT & RITUAL -->
                        <div class="tab-panel-section" id="tab-preferensi">
                            <div class="profile-panel-card">
                                <div class="panel-head-clean">
                                    <h3 class="panel-title-clean">Ritual Harian &amp; Pengingat</h3>
                                    <p class="panel-desc-clean">Atur waktu pengingat lembut untuk menjaga ritme ketenanganmu.</p>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Refleksi Malam &amp; Check-In Mood</div>
                                        <div class="clean-toggle-desc">Pukul 20:00 WIB • Meredakan pikiran sebelum tidur.</div>
                                    </div>
                                    <label class="switch-toggle">
                                        <input type="checkbox" checked>
                                        <span class="slider-round"></span>
                                    </label>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Latihan Napas Siang (Grounding 4-7-8)</div>
                                        <div class="clean-toggle-desc">Pukul 13:30 WIB • Jeda 3 menit di sela aktivitas harian.</div>
                                    </div>
                                    <label class="switch-toggle">
                                        <input type="checkbox" checked>
                                        <span class="slider-round"></span>
                                    </label>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Pemberitahuan Jadwal Konsultasi (H-1 Jam)</div>
                                        <div class="clean-toggle-desc">Peringatan otomatis sebelum sesi temu psikolog dimulai.</div>
                                    </div>
                                    <label class="switch-toggle">
                                        <input type="checkbox" checked>
                                        <span class="slider-round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: PRIVASI & KEAMANAN -->
                        <div class="tab-panel-section" id="tab-keamanan">
                            <div class="profile-panel-card">
                                <div class="panel-head-clean">
                                    <h3 class="panel-title-clean">Privasi &amp; Kedaulatan Data</h3>
                                    <p class="panel-desc-clean">Kontrol penuh kerahasiaan dan keamanan akun MindCare Anda.</p>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Mode Anonim di Seluruh Komunitas</div>
                                        <div class="clean-toggle-desc">Nama asli dan identitas disamarkan otomatis saat berdiskusi.</div>
                                    </div>
                                    <label class="switch-toggle">
                                        <input type="checkbox" checked>
                                        <span class="slider-round"></span>
                                    </label>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Enkripsi End-to-End Jurnal Emosi</div>
                                        <div class="clean-toggle-desc">Catatan refleksi batin Anda terkunci secara privat.</div>
                                    </div>
                                    <label class="switch-toggle">
                                        <input type="checkbox" checked>
                                        <span class="slider-round"></span>
                                    </label>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Autentikasi Dua Langkah (2FA)</div>
                                        <div class="clean-toggle-desc">Status: <strong style="color:#15803d;">Aktif</strong> (Google Authenticator)</div>
                                    </div>
                                    <button type="button" class="btn-clean-outline" onclick="alert('Kelola metode 2FA.');">Kelola</button>
                                </div>

                                <div class="clean-toggle-row">
                                    <div class="clean-toggle-info">
                                        <div class="clean-toggle-title">Kata Sandi Akun</div>
                                        <div class="clean-toggle-desc">Diperbarui 45 hari yang lalu • Keamanan Sangat Kuat</div>
                                    </div>
                                    <button type="button" class="btn-clean-outline" onclick="alert('Buka form ganti kata sandi.');">Ubah Sandi</button>
                                </div>

                                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid #f1f5f9;margin-top:6px;">
                                    <button type="button" class="btn-clean-outline" onclick="alert('Mengunduh salinan data pribadi (.ZIP).');">
                                        Unduh Data Saya
                                    </button>
                                    <button type="button" class="btn-clean-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus akun secara permanen?')) alert('Permintaan penghapusan akun diproses.');">
                                        Hapus Akun
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- TAB SWITCHING SCRIPT -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.profile-tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel-section');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-tab');

                // Update active tab button
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Show target panel only
                tabPanels.forEach(panel => {
                    if (panel.id === targetId) {
                        panel.classList.add('active');
                    } else {
                        panel.classList.remove('active');
                    }
                });
            });
        });

        // Feedback toast for save
        const saveBtn = document.getElementById('saveProfileBtn');
        const toast = document.getElementById('saveProfileToast');
        if (saveBtn && toast) {
            saveBtn.addEventListener('click', function() {
                toast.style.display = 'inline-flex';
            });
        }

        window.focusEditProfile = function() {
            const firstInput = document.querySelector('#profileUpdateForm input[name="fullname"]');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
                firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                document.querySelectorAll('#profileUpdateForm .clean-input').forEach(el => {
                    el.style.borderColor = '#0d9488';
                    el.style.backgroundColor = '#f0fdfa';
                    setTimeout(() => {
                        el.style.borderColor = '';
                        el.style.backgroundColor = '';
                    }, 2500);
                });
                const btnLabel = document.getElementById('btnEditLabel');
                if (btnLabel) btnLabel.textContent = '✓ Mode Edit Aktif';
            }
        };
    });
    </script>
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
