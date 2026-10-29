<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'db_connect.php';
$userId = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$firstDisplayName = explode(' ', $fullname)[0];
$fullDisplayName = htmlspecialchars($fullname);
$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
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
            <input type="text" class="app-search-input" placeholder="Cari meditasi, nama psikolog, artikel...">
        </div>

        <!-- Header Actions -->
        <div class="app-header-actions">
            <a href="emergency.php" class="btn-crisis-header" title="Bantuan Krisis 119">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span class="crisis-label-full">Bantuan Krisis 119</span>
                <span class="crisis-label-short">119</span>
            </a>

            <button type="button" class="header-icon-btn" title="Notifikasi">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span class="badge-dot"></span>
            </button>

            <!-- User Pill -->
            <a href="<?= $isLoggedIn ? 'profile.php' : 'login.php' ?>" class="header-user-pill" title="Profil <?= $fullDisplayName ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= $fullDisplayName ?></div>
                    <div class="header-user-role"><?= $isLoggedIn ? 'Member Aktif' : 'Masuk Akun' ?></div>
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
                        <a href="dashboard.php" class="active">
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
                        <a href="<?= $isLoggedIn ? 'profile.php' : 'login.php' ?>">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Profil & Pengaturan</span>
                        </a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                    <li class="sidebar-nav-item">
                        <a href="logout.php" style="color:#ef4444;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span>Keluar</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="sidebar-nav-item">
                        <a href="login.php" style="color:var(--teal-700);font-weight:700;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                            <span>Masuk Akun</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Bottom user summary card -->
            <div class="sidebar-user-card">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name"><?= $fullDisplayName ?></span>
                    <span class="sidebar-user-sub">Mahasiswi & Anggota MindCare</span>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN SCROLLABLE DASHBOARD -->
        <main class="dashboard-content-scroll">
            
            <!-- HERO GREETING BANNER -->
            <div class="dash-hero-container">
                <div class="dash-hero-text-block">
                    <div class="dash-date-pill">
                        <span class="dot">●</span>
                        <?php
                        $bulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                        $todayFormattedLong = $hariIndo[date('l')] . ', ' . date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');
                        ?>
                        <span><?= $todayFormattedLong ?></span>
                    </div>
                    <h1 class="dash-hero-title">Halo, selamat datang kembali, <?= $firstDisplayName ?>.</h1>
                    <p class="dash-hero-desc">
                        Ambil waktu sejenak. Kamu sudah berjuang dengan baik.
                    </p>
                </div>

                <!-- Weather widget -->
                <div class="dash-weather-widget">
                    <div class="weather-icon-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a5 5 0 0 0-5 5v1a5 5 0 0 0 10 0V7a5 5 0 0 0-5-5z"></path><path d="M5 13a7 7 0 0 0 14 0"></path><path d="M12 20v2"></path></svg>
                    </div>
                    <div>
                        <div class="weather-title-sub">CUACA BATIN HARI INI</div>
                        <div class="weather-state-bold">Teduh & Mengalir</div>
                        <div class="weather-index-muted">Indeks Ketenangan 78%</div>
                    </div>
                </div>
            </div>

            <!-- ROW 1: MOOD CHECK-IN & WEEKLY MENTAL SCREENING -->
            <div class="dash-two-cols-grid">
                               <!-- Card 1: Bagaimana Perasaanmu Saat Ini? -->
                <section class="mood-checkin-card">
                    <div class="mood-card-head">
                        <h2 class="mood-card-title">Bagaimana perasaanmu saat ini?</h2>
                        <span class="badge-tag-pill" style="background:#f1f5f9;color:var(--slate-600);border:none;">Refleksi Harian</span>
                    </div>
                    <p style="font-size:12px;color:var(--slate-500);margin-top:-8px;">
                        Setiap emosi berharga dan layak diakui.
                    </p>

                    <!-- 5 Mood Emojis -->
                    <div class="mood-emojis-row" id="moodEmojiSelector">
                        <div class="mood-emoji-btn" data-mood="sangat-baik">
                            <span class="mood-emoji-icon">✨</span>
                            <span class="mood-emoji-title">Sangat Baik</span>
                            <span class="mood-emoji-desc">Senyum berseri</span>
                        </div>
                        <div class="mood-emoji-btn active" data-mood="baik-tenang">
                            <span class="mood-emoji-icon">🍃</span>
                            <span class="mood-emoji-title">Baik/Tenang</span>
                            <span class="mood-emoji-desc">Tersenyum lembut</span>
                        </div>
                        <div class="mood-emoji-btn" data-mood="biasa-saja">
                            <span class="mood-emoji-icon">😐</span>
                            <span class="mood-emoji-title">Biasa Saja</span>
                            <span class="mood-emoji-desc">Cukup netral</span>
                        </div>
                        <div class="mood-emoji-btn" data-mood="cemas-lelah">
                            <span class="mood-emoji-icon">😮‍💨</span>
                            <span class="mood-emoji-title">Cemas/Lelah</span>
                            <span class="mood-emoji-desc">Menghela nafas</span>
                        </div>
                        <div class="mood-emoji-btn" data-mood="kewalahan">
                            <span class="mood-emoji-icon">🫂</span>
                            <span class="mood-emoji-title">Kewalahan</span>
                            <span class="mood-emoji-desc">Butuh pelukan</span>
                        </div>
                    </div>

                    <!-- Heart note input & save -->
                    <div class="mood-note-container">
                        <div class="mood-note-head-row">Catatan Hati & Keluhan Singkat</div>
                        <div class="mood-note-input-row">
                            <input type="text" id="moodNoteInput" class="mood-note-input" placeholder="Tuliskan keluhan atau uneg-uneg yang kamu rasakan...">
                            <button type="button" id="btnSaveMood" class="btn-mood-save">
                                Simpan Catatan
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Solution / Psychological Response Card -->
                    <div id="moodResponseCard" style="display:none;" class="mood-response-box">
                        <div class="mood-response-badge">
                            <span>💡 Refleksi & Solusi Untukmu</span>
                        </div>
                        <p id="moodResponseText" class="mood-response-analysis"></p>
                        <a id="moodResponseLink" href="#" class="mood-response-cta">
                            <span id="moodResponseBtnText">Buka Rekomendasi Terkait</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>

                    <!-- User History of Input Records -->
                    <div id="moodHistorySection" class="mood-history-accordion" style="display:none;">
                        <div class="mood-history-head">
                            <span>Riwayat Catatan Hati & Keluhan Terakhir</span>
                            <span id="moodTotalBadge" style="font-weight:normal;color:var(--slate-400);"></span>
                        </div>
                        <div id="moodHistoryList" class="mood-history-list">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mood-footer-meta">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>Privasi terenkripsi</span>
                        </div>
                        <div id="moodStreakDisplay" style="color:#d97706;font-weight:700;">
                            Check-in Hari Ini 🔥
                        </div>
                    </div>
                </section>

                <!-- Card 2: Cek Kondisi Mental Mingguanmu -->
                <section class="mental-screening-card">
                    <div class="screening-card-header">
                        <div class="screening-top-meta">
                            <span class="badge-tag-pill" style="background:rgba(255,255,255,0.18);color:#5eead4;border:1px solid rgba(94,234,212,0.3);font-weight:700;">● Skrining Rutin</span>
                            <span style="display:flex;align-items:center;gap:5px;color:rgba(255,255,255,0.85);font-size:12px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Hanya butuh ~3 menit
                            </span>
                        </div>

                        <h2 class="screening-main-heading">Cek Kondisi Mental Mingguanmu</h2>
                        
                        <p class="screening-desc-text">
                            Sudah 7 hari sejak skrining terakhir. Cek kondisimu lewat DASS-21.
                        </p>
                    </div>

                    <!-- Inspiring Photo & Quote Box to fill the card beautifully -->
                    <div class="screening-quote-card" id="screeningQuoteContainer">
                        <div class="screening-quote-thumb-wrap">
                            <img id="screeningQuoteImg" src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=400&q=80" alt="Mindfulness Reflection" class="screening-quote-thumb">
                            <span class="screening-quote-pill" id="screeningQuoteTag">🌱 Refleksi Diri</span>
                        </div>
                        <div class="screening-quote-body">
                            <div class="screening-quote-top-row">
                                <span class="screening-quote-marks">“</span>
                                <button type="button" id="btnNextQuote" class="btn-refresh-quote" title="Ganti Kutipan Inspiratif">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                    <span>Ganti Quote</span>
                                </button>
                            </div>
                            <p class="screening-quote-text" id="screeningQuoteText">
                                "Merawat kesehatan mental bukanlah tanda kelemahan, melainkan keberanian terbesar untuk mengenali dan memeluk dirimu apa adanya."
                            </p>
                            <div class="screening-quote-footer">
                                <span class="screening-quote-author" id="screeningQuoteAuthor">— Carl Rogers</span>
                                <span class="screening-quote-subtag" id="screeningQuoteSubtag">MindCare Quote</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Area -->
                    <div class="screening-action-area">
                        <a href="self-check.php" class="btn-screening-white">
                            <span>Mulai Skrining Sekarang</span>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <div class="screening-trust-meta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            <span>100% Rahasia & Bebas Biaya • Evaluasi DASS-21 Instan</span>
                        </div>
                    </div>
                </section>

            </div>

            <!-- ROW 2: MOOD TREND 7 DAYS & UPCOMING CONSULTATION -->
            <div class="dash-two-cols-grid">
                
                <!-- Card 3: Tren Suasana Hati 7 Hari -->
                <section class="mood-trend-card">
                    <div class="trend-card-head">
                        <h2 class="trend-card-title">Tren Suasana Hati (7 Hari Terakhir)</h2>
                        <span class="badge-tag-pill" id="trendDateBadge" style="background:#f1f5f9;color:var(--slate-600);border:none;">Menunggu Input</span>
                    </div>
                    <div class="trend-card-sub" id="trendSubText">
                        Kondisi dominan minggu ini: <strong>Menunggu check-in suasana hatimu</strong>
                    </div>

                    <!-- SVG 7-Day Trend Chart or Empty State -->
                    <div class="trend-svg-container" id="trendSvgContainer">
                        <!-- Rendered in real-time by JavaScript -->
                    </div>

                    <div class="trend-footer-row">
                        <div style="display:flex;gap:14px;align-items:center;">
                            <span style="display:inline-flex;align-items:center;gap:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#0f766e;"></span> Positif / Tenang</span>
                            <span style="display:inline-flex;align-items:center;gap:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#f59e0b;"></span> Fluktuasi Stres</span>
                        </div>
                        <a href="mood-tracker.php">Lihat Riwayat & Pola Lengkap &gt;</a>
                    </div>
                </section>

                <!-- Card 4: Jadwal Konsultasi Terdekat -->
                <section class="upcoming-consultation-panel">
                    <div class="consult-panel-head">
                        <h2 class="mood-card-title">Jadwal Konsultasi Terdekat</h2>
                        <span class="badge-tag-pill" style="background:#f0fdf4;color:#15803d;border:none;">Terkonfirmasi</span>
                    </div>

                    <div class="consult-doctor-header">
                        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%230f766e'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%23134e4a'/></svg>" alt="Sarah Amelia" class="consult-doctor-avatar">
                        <div class="consult-doctor-info">
                            <div class="tag">KONSELING PSIKOLOGI KLINIS</div>
                            <div class="name">Sarah Amelia, M.Psi., Psikolog</div>
                            <div class="spec">Spesialisasi: Burnout Akademik & Quarter-life Crisis</div>
                        </div>
                    </div>

                    <div class="consult-time-details">
                        <div class="consult-detail-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Besok, 25 Okt</span>
                        </div>
                        <div class="consult-detail-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>19:30 - 20:30 WIB</span>
                        </div>
                        <div class="consult-detail-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Sesi Privat 1-on-1</span>
                        </div>
                    </div>

                    <div class="consult-notice-muted">
                        Tautan sesi video dibuka 10 menit sebelum jadwal.
                    </div>

                    <div class="consult-action-buttons">
                        <a href="konsultasi.php" class="btn-consult-enter">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                            <span>Masuk Ruang Sesi</span>
                        </a>
                        <button type="button" class="btn-sm-outline" style="padding:9px 16px;" onclick="alert('Detail reservasi konsultasi psikolog.');">
                            Lihat Detail
                        </button>
                    </div>
                </section>

            </div>

            <!-- ROW 3: PUSAT AKTIVITAS CEPAT -->
            <section class="quick-activities-section">
                <div class="quick-activities-head">
                    <div>
                        <h2 class="quick-activities-title">Pusat Aktivitas Cepat</h2>
                        <div class="quick-activities-sub">Langkah kecil harian untuk ketenangan pikiranmu</div>
                    </div>
                    <span style="font-size:11.5px;color:var(--slate-500);font-weight:600;">Akses Terarah</span>
                </div>

                <div class="quick-activities-grid">
                    
                    <!-- Act 1: Meditasi 5 Menit -->
                    <a href="meditasi.php" class="quick-activity-card">
                        <span class="act-badge-tag">AUDIO DIPANDU</span>
                        <div class="act-main-title">Meditasi 5 Menit</div>
                        <p class="act-desc-snippet">Relaksasi singkat sebelum belajar…</p>
                        <div class="act-action-link">
                            <span>Mulai Dengarkan</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>

                    <!-- Act 2: MindCare AI Chat -->
                    <a href="chatbot.php" class="quick-activity-card">
                        <span class="act-badge-tag">SIAGA 24/7</span>
                        <div class="act-main-title">MindCare AI Chat</div>
                        <p class="act-desc-snippet">Obrolan curhat hangat tanpa takut…</p>
                        <div class="act-action-link">
                            <span>Buka Percakapan</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>

                    <!-- Act 3: Imposter Syndrome -->
                    <a href="artikel.php" class="quick-activity-card">
                        <span class="act-badge-tag">EDUKASI POPULER</span>
                        <div class="act-main-title">Imposter Syndrome</div>
                        <p class="act-desc-snippet">Trik praktis mengatasi rasa ragu…</p>
                        <div class="act-action-link">
                            <span>Baca Selengkapnya</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>

                    <!-- Act 4: Forum Anonim -->
                    <a href="forum-komunitas.php" class="quick-activity-card">
                        <span class="act-badge-tag">RUANG CERITA</span>
                        <div class="act-main-title">Forum Anonim</div>
                        <p class="act-desc-snippet">Thread baru: "Cara kamu redakan…</p>
                        <div class="act-action-link">
                            <span>Gabung Diskusi</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>

                    <!-- Act 5: Temukan Psikolog -->
                    <a href="konsultasi.php" class="quick-activity-card">
                        <span class="act-badge-tag">KONSELING AHLI</span>
                        <div class="act-main-title">Temukan Psikolog</div>
                        <p class="act-desc-snippet">Pilih psikolog berlisensi sesuai…</p>
                        <div class="act-action-link">
                            <span>Cari Jadwal</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>

                </div>
            </section>

            <!-- ROW 4: ARTIKEL EDUKASI & CRISIS HOTLINE -->
            <div class="curated-and-emergency-row">
                
                <!-- Left: Artikel Edukasi Terkurasi Buatmu -->
                <section class="curated-articles-box">
                    <div class="curated-head-row">
                        <h2 class="curated-title">Artikel Edukasi Terkurasi Buatmu</h2>
                        <a href="artikel.php" style="font-size:12px;color:var(--teal-700);text-decoration:none;font-weight:700;">Lihat Semua</a>
                    </div>

                    <div class="articles-duo-grid">
                        
                        <!-- Article 1 -->
                        <div class="article-card-item">
                            <div class="article-thumb-banner" style="background:linear-gradient(135deg, #134e4a, #0f766e);">
                                <span class="article-tag-pill">#ManajemenStres</span>
                            </div>
                            <div class="article-body-content">
                                <div class="article-meta-info">⏱ 4 mnt baca • Ditinjau Psikolog</div>
                                <h3 class="article-title-bold">Seni Melambat: Mengatasi Sindrom Kelelahan Batin Mahasiswa</h3>
                                <p class="article-snippet-text">
                                    Memahami batas kapasitas mental dan cara memulihkan ritme belajar…
                                </p>
                                <div class="article-author-row">
                                    <span>Oleh Tim Klinis MindCare</span>
                                    <span class="read-btn">Baca &gt;</span>
                                </div>
                            </div>
                        </div>

                        <!-- Article 2 -->
                        <div class="article-card-item">
                            <div class="article-thumb-banner" style="background:linear-gradient(135deg, #1e293b, #334155);">
                                <span class="article-tag-pill">#WorkLifeBalance</span>
                            </div>
                            <div class="article-body-content">
                                <div class="article-meta-info">⏱ 4 mnt baca • Tips Praktis</div>
                                <h3 class="article-title-bold">Menjaga Batas Digital: Berhenti Mengorbankan Kualitas Tidurmu</h3>
                                <p class="article-snippet-text">
                                    Langkah konkret menciptakan batasan sehat antara notifikasi kuliah,…
                                </p>
                                <div class="article-author-row">
                                    <span>Oleh Sarah Amelia, M.Psi.</span>
                                    <span class="read-btn">Baca &gt;</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Right: Pusat Bantuan Krisis Darurat -->
                <section class="crisis-support-box">
                    <div class="crisis-head-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>Pusat Bantuan Krisis Darurat</span>
                    </div>

                    <h2 class="crisis-bold-heading">Kamu Tidak Sendirian</h2>
                    
                    <p class="crisis-desc-para">
                        Jika kamu mengalami tekanan berat atau kondisi darurat:
                    </p>

                    <div class="crisis-hotline-item">
                        <div>
                            <div class="lbl">Layanan Sejiwa Kemenkes</div>
                            <div class="val">119 Ext 8</div>
                        </div>
                        <a href="tel:119" class="btn-phone-call" title="Hubungi 119 Ext 8">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </a>
                    </div>

                    <div class="crisis-hotline-item">
                        <div>
                            <div class="lbl">Hotline LISA Cinta Pulih</div>
                            <div class="val">110 / 24 Jam</div>
                        </div>
                        <a href="tel:110" class="btn-phone-call" title="Hubungi 110">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </a>
                    </div>

                    <a href="emergency.php" class="crisis-bottom-guide-link">
                        <span>Buka Panduan Penanganan Krisis</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                </section>

            </div>

        </main>
    </div>

    <script src="script.js?v=<?= time() ?>"></script>
    <script>
        // Mood Selection & Realtime Tracking Script
        document.addEventListener('DOMContentLoaded', () => {
            const emojiButtons = document.querySelectorAll('.mood-emoji-btn');
            const noteInput = document.getElementById('moodNoteInput');
            const saveBtn = document.getElementById('btnSaveMood');
            const responseCard = document.getElementById('moodResponseCard');
            const responseText = document.getElementById('moodResponseText');
            const responseLink = document.getElementById('moodResponseLink');
            const responseBtnText = document.getElementById('moodResponseBtnText');
            const historySection = document.getElementById('moodHistorySection');
            const historyList = document.getElementById('moodHistoryList');
            const totalBadge = document.getElementById('moodTotalBadge');
            const trendDateBadge = document.getElementById('trendDateBadge');
            const trendSubText = document.getElementById('trendSubText');
            const trendSvgContainer = document.getElementById('trendSvgContainer');

            let activeMood = 'baik-tenang';

            // Mood button selection interaction
            emojiButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    emojiButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeMood = btn.getAttribute('data-mood') || 'baik-tenang';
                });
            });

            // Helper to escape HTML
            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            // Map mood to badge color & styles
            function getMoodColor(moodType) {
                switch(moodType) {
                    case 'sangat-baik': return '#10b981';
                    case 'baik-tenang': return '#0f766e';
                    case 'biasa-saja':  return '#64748b';
                    case 'cemas-lelah': return '#f59e0b';
                    case 'kewalahan':   return '#ef4444';
                    default:            return '#0f766e';
                }
            }

            // Render Dynamic 7-Day Trend Chart
            function renderTrendChart(data) {
                if (!data || !data.has_data || !data.chart_points || data.chart_points.length === 0) {
                    trendDateBadge.innerText = 'Menunggu Input';
                    trendSubText.innerHTML = 'Kondisi dominan: <strong>Menunggu check-in suasana hatimu</strong>';
                    trendSvgContainer.innerHTML = `
                        <div class="trend-empty-state">
                            <div class="trend-empty-icon">🌱</div>
                            <div class="trend-empty-title">Grafik Menunggu Input Suasana Hatimu</div>
                            <p class="trend-empty-desc">
                                Silakan pilih perasaanmu dan simpan catatan hati pada kartu di sebelah kiri. Grafik tren 7 hari akan langsung tergambar secara real-time!
                            </p>
                        </div>
                    `;
                    return;
                }

                trendDateBadge.innerText = data.date_range || 'Pekan Ini';
                trendSubText.innerHTML = `Kondisi dominan: <strong>${escapeHtml(data.dominant_mood)}</strong> (${data.dominant_count} dari ${data.total_in_period} catatan)`;

                const pts = data.chart_points;
                const N = pts.length;
                const leftPad = 50;
                const rightPad = 490;

                // Assign calculated X positions
                pts.forEach((p, idx) => {
                    if (N === 1) {
                        p.x = 270;
                    } else {
                        p.x = Math.round(leftPad + (idx * (rightPad - leftPad) / (N - 1)));
                    }
                });

                // Generate smooth curve
                let pathD = '';
                let areaD = '';
                if (N === 1) {
                    pathD = `M 40 ${pts[0].y} L 500 ${pts[0].y}`;
                    areaD = `M 40 ${pts[0].y} L 500 ${pts[0].y} L 500 100 L 40 100 Z`;
                } else {
                    pathD = `M ${pts[0].x} ${pts[0].y}`;
                    areaD = `M ${pts[0].x} ${pts[0].y}`;
                    for (let i = 0; i < N - 1; i++) {
                        const p0 = pts[i];
                        const p1 = pts[i + 1];
                        const cpX = (p0.x + p1.x) / 2;
                        pathD += ` C ${cpX} ${p0.y}, ${cpX} ${p1.y}, ${p1.x} ${p1.y}`;
                        areaD += ` C ${cpX} ${p0.y}, ${cpX} ${p1.y}, ${p1.x} ${p1.y}`;
                    }
                    areaD += ` L ${pts[N - 1].x} 100 L ${pts[0].x} 100 Z`;
                }

                // Render Nodes & Labels
                let nodesSvg = '';
                pts.forEach((p, idx) => {
                    const isLast = (idx === N - 1);
                    const color = getMoodColor(p.mood_type);
                    const r = isLast ? '6' : '4.5';
                    const stroke = isLast ? '#2dd4bf' : '#ffffff';
                    const strokeW = isLast ? '3' : '2';
                    const textFill = isLast ? '#0f766e' : '#64748b';
                    const textWeight = isLast ? '800' : '700';

                    nodesSvg += `
                        <g class="chart-node" style="cursor:pointer;">
                            <title>${escapeHtml(p.date)}: ${escapeHtml(p.label)} - "${escapeHtml(p.note)}"</title>
                            <circle cx="${p.x}" cy="${p.y}" r="${r}" fill="${color}" stroke="${stroke}" stroke-width="${strokeW}"></circle>
                            <text x="${p.x}" y="106" text-anchor="middle" font-size="11" fill="${textFill}" font-weight="${textWeight}">${escapeHtml(p.day)}</text>
                            <text x="${p.x}" y="117" text-anchor="middle" font-size="9" fill="${isLast ? '#0f766e' : '#94a3b8'}" font-weight="${isLast ? '700' : '500'}">${escapeHtml(isLast ? 'Terakhir' : p.label)}</text>
                        </g>
                    `;
                });

                const svgMarkup = `
                    <svg viewBox="0 0 540 120" style="width:100%;height:100%;overflow:visible;">
                        <defs>
                            <linearGradient id="trendGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0f766e" stop-opacity="0.18"/>
                                <stop offset="100%" stop-color="#0f766e" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <!-- Grid Reference Lines -->
                        <line x1="20" y1="25" x2="520" y2="25" stroke="#f1f5f9" stroke-width="1"></line>
                        <line x1="20" y1="55" x2="520" y2="55" stroke="#f1f5f9" stroke-width="1"></line>
                        <line x1="20" y1="85" x2="520" y2="85" stroke="#f1f5f9" stroke-width="1"></line>

                        <!-- Area Fill -->
                        <path d="${areaD}" fill="url(#trendGradient)"></path>

                        <!-- Trend Stroke Line -->
                        <path d="${pathD}" fill="none" stroke="#0f766e" stroke-width="3" stroke-linecap="round"></path>

                        <!-- Interactive Nodes -->
                        ${nodesSvg}
                    </svg>
                `;

                trendSvgContainer.innerHTML = svgMarkup;
            }

            // Render History List
            function renderHistoryList(history, total) {
                if (!history || history.length === 0) {
                    historySection.style.display = 'none';
                    return;
                }
                historySection.style.display = 'block';
                if (totalBadge) totalBadge.innerText = `(${total} total)`;

                historyList.innerHTML = history.slice(0, 4).map(item => {
                    const color = getMoodColor(item.mood_type);
                    const noteText = item.note ? escapeHtml(item.note) : 'Tanpa catatan tertulis';
                    const dateObj = new Date(item.created_at.replace(' ', 'T'));
                    const timeFormatted = isNaN(dateObj.getTime()) ? item.created_at : dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });

                    return `
                        <div class="mood-history-item">
                            <div class="mood-history-item-left">
                                <span class="mood-history-pill" style="background:${color}15;color:${color};border:1px solid ${color}30;">
                                    ${escapeHtml(item.mood_label)}
                                </span>
                                <span class="mood-history-note" title="${noteText}">
                                    ${noteText}
                                </span>
                            </div>
                            <span class="mood-history-date">${timeFormatted}</span>
                        </div>
                    `;
                }).join('');
            }

            // Load Existing Mood Data
            function loadMoodData() {
                fetch('api_mood.php?action=get')
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            renderTrendChart(data);
                            renderHistoryList(data.history, data.total_entries);

                            // If there is an existing latest recommendation, show it
                            if (data.latest_log && data.latest_log.recommendation) {
                                responseText.innerText = data.latest_log.recommendation;
                                responseLink.href = data.latest_log.relevant_link || 'self-check.php';
                                responseBtnText.innerText = data.latest_log.relevant_action || 'Buka Rekomendasi Terkait';
                                responseCard.style.display = 'flex';
                            }
                        }
                    })
                    .catch(err => console.error('Gagal memuat mood:', err));
            }

            // Save Mood Action
            function handleSaveMood() {
                const noteVal = noteInput.value.trim();

                saveBtn.disabled = true;
                saveBtn.innerText = 'Menyimpan...';

                fetch('api_mood.php?action=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        mood_type: activeMood,
                        note: noteVal
                    })
                })
                .then(r => r.json())
                .then(data => {
                    saveBtn.disabled = false;
                    saveBtn.innerText = 'Simpan Catatan';

                    if (data.success) {
                        // Show insightful recommendation tailored to user's complaint
                        if (data.latest_log && data.latest_log.recommendation) {
                            responseText.innerText = data.latest_log.recommendation;
                            responseLink.href = data.latest_log.relevant_link || 'self-check.php';
                            responseBtnText.innerText = data.latest_log.relevant_action || 'Buka Rekomendasi Terkait';
                            responseCard.style.display = 'flex';
                            responseCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }

                        // Update trend chart in realtime!
                        renderTrendChart(data);

                        // Update recorded history list
                        renderHistoryList(data.history, data.total_entries);

                        // Clear input and show visual feedback
                        noteInput.value = '';
                        saveBtn.innerText = 'Tersimpan!';
                        setTimeout(() => {
                            saveBtn.innerText = 'Simpan Catatan';
                        }, 2200);
                    } else {
                        alert(data.error || 'Gagal menyimpan catatan.');
                    }
                })
                .catch(err => {
                    saveBtn.disabled = false;
                    saveBtn.innerText = 'Simpan Catatan';
                    console.error('Error saving mood:', err);
                    alert('Terjadi kendala saat menyimpan. Mohon coba lagi.');
                });
            }

            saveBtn.addEventListener('click', handleSaveMood);

            noteInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSaveMood();
                }
            });

            // Initial load
            loadMoodData();

            // Inspiring Quotes Rotation for Mental Screening Card
            const screeningQuotes = [
                {
                    quote: "Merawat kesehatan mental bukanlah tanda kelemahan, melainkan keberanian terbesar untuk mengenali dan memeluk dirimu apa adanya.",
                    author: "Carl Rogers",
                    tag: "🌱 Refleksi Diri",
                    subtag: "Self-Compassion",
                    img: "https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=400&q=80"
                },
                {
                    quote: "Kamu tidak harus mengendalikan semua pikiranmu. Cukup berhenti membiarkan pikiran cemas mendikte setiap langkah hidupmu.",
                    author: "Dan Millman",
                    tag: "🌿 Ketenangan",
                    subtag: "Mindfulness",
                    img: "https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&w=400&q=80"
                },
                {
                    quote: "Beri dirimu ruang untuk bernapas dan waktu untuk pulih. Kemajuan tidak selalu harus cepat, yang terpenting tetap melangkah.",
                    author: "MindCare Wisdom",
                    tag: "✨ Pemulihan",
                    subtag: "Healing Journey",
                    img: "https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=400&q=80"
                },
                {
                    quote: "Perasaanmu valid, perjalananmu berharga, dan kamu tidak pernah harus memikul semua beban ini seorang diri.",
                    author: "Psikologi Positif",
                    tag: "💛 Penerimaan",
                    subtag: "Dukungan Jiwa",
                    img: "https://images.unsplash.com/photo-1470246973918-29a93221c455?auto=format&fit=crop&w=400&q=80"
                },
                {
                    quote: "Kedamaian batin dimulai saat kamu memilih untuk tidak membiarkan orang atau peristiwa lain mengendalikan emosimu.",
                    author: "Pema Chödrön",
                    tag: "🕊️ Kedamaian",
                    subtag: "Inner Peace",
                    img: "https://images.unsplash.com/photo-1517021897933-0e0319cfbc28?auto=format&fit=crop&w=400&q=80"
                }
            ];

            let currentQuoteIdx = 0;
            const quoteTextEl = document.getElementById('screeningQuoteText');
            const quoteAuthorEl = document.getElementById('screeningQuoteAuthor');
            const quoteTagEl = document.getElementById('screeningQuoteTag');
            const quoteSubtagEl = document.getElementById('screeningQuoteSubtag');
            const quoteImgEl = document.getElementById('screeningQuoteImg');
            const btnNextQuote = document.getElementById('btnNextQuote');

            function applyQuote(idx, animate = true) {
                const item = screeningQuotes[idx];
                if (!item || !quoteTextEl) return;

                if (animate) {
                    quoteTextEl.style.opacity = '0';
                    if (quoteImgEl) quoteImgEl.style.opacity = '0.6';
                    setTimeout(() => {
                        quoteTextEl.innerText = `"${item.quote}"`;
                        quoteAuthorEl.innerText = `— ${item.author}`;
                        quoteTagEl.innerText = item.tag;
                        quoteSubtagEl.innerText = item.subtag;
                        if (quoteImgEl) {
                            quoteImgEl.src = item.img;
                            quoteImgEl.style.opacity = '1';
                        }
                        quoteTextEl.style.opacity = '1';
                    }, 180);
                } else {
                    quoteTextEl.innerText = `"${item.quote}"`;
                    quoteAuthorEl.innerText = `— ${item.author}`;
                    quoteTagEl.innerText = item.tag;
                    quoteSubtagEl.innerText = item.subtag;
                    if (quoteImgEl) quoteImgEl.src = item.img;
                }
            }

            if (btnNextQuote) {
                btnNextQuote.addEventListener('click', () => {
                    currentQuoteIdx = (currentQuoteIdx + 1) % screeningQuotes.length;
                    applyQuote(currentQuoteIdx, true);
                });
            }
        });
    </script>
</body>
</html>
