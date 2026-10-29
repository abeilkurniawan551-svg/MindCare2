<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'db_connect.php';
$fullname = $_SESSION['fullname'];
$firstDisplayName = explode(' ', $fullname)[0];
$fullDisplayName = htmlspecialchars($fullname);
$userId = $_SESSION['user_id'];

$bulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$currentDayNum = date('j');
$currentMonthNum = (int)date('n');
$currentYear = date('Y');
$todayDateFull = $currentDayNum . ' ' . $bulanIndo[$currentMonthNum] . ' ' . $currentYear;
$currentMonthYear = $bulanIndo[$currentMonthNum] . ' ' . $currentYear;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Tracker: Rekam Jejak Emosi - MindCare</title>
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
            <input type="text" class="app-search-input" placeholder="Cari materi, nama psikolog, artikel...">
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

            <!-- User Pill -->
            <a href="profile.php" class="header-user-pill" title="Profil <?= $fullDisplayName ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= $fullDisplayName ?></div>
                    <div class="header-user-role">Member Aktif</div>
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
                        <a href="mood-tracker.php" class="active">
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
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <span style="color:#ef4444;font-weight:600;">Bantuan Darurat</span>
                            <span class="badge-count-red">119</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="profile.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Profil & Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- User profile summary card at bottom -->
            <div class="sidebar-user-card">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= $fullDisplayName ?></div>
                    <div class="sidebar-user-sub">Mahasiswa & Anggota</div>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="app-main-content dashboard-content-scroll">
            
            <!-- Page Header Row -->
            <div class="mood-header-row">
                <div>
                    <h1 style="font-size:1.55rem;font-weight:800;color:var(--slate-900);margin:0 0 4px 0;letter-spacing:-0.02em;">
                        Mood Tracker
                    </h1>
                    <p style="color:var(--slate-500);font-size:13px;margin:0;">
                        Rekam suasana hati harianmu untuk mengenali pola emosi secara bertahap.
                    </p>
                </div>

                <!-- Consistency Streak Badge -->
                <div class="mood-streak-badge">
                    <span style="font-size:16px;">🔥</span>
                    <span id="moodTrackerStreakText" style="font-size:13px;color:var(--teal-900);">Aktif Hari Ini</span>
                </div>
            </div>

            <!-- Main Layout Grid: Journal Form (Left) + Analytics (Right) -->
            <div class="mood-main-layout">
                
                <!-- Left Card: Jurnal Harian -->
                <div class="mood-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <h2 style="font-size:1.15rem;font-weight:700;color:var(--slate-900);margin:0;">
                            Perasaanmu hari ini, <?= $firstDisplayName ?>?
                        </h2>
                        <span id="moodTrackerTodayPill" style="font-size:11px;font-weight:600;color:var(--teal-800);background:var(--teal-50);padding:3px 10px;border-radius:var(--radius-full);border:1px solid var(--teal-200);">
                            <?= $todayDateFull ?>
                        </span>
                    </div>

                    <!-- 1. Emotion Spectrum Row -->
                    <div class="mood-section-label" style="font-size:12px;margin-bottom:8px;">
                        <span>Pilih Emosi Utama</span>
                    </div>
                    <div class="mood-spectrum-grid">
                        
                        <!-- 1. Sangat Bahagia -->
                        <div class="mood-spectrum-btn" data-mood="bahagia">
                            <div class="mood-spectrum-icon" style="background:#ecfdf5;color:#10b981;">😄</div>
                            <div class="mood-spectrum-name">Bahagia</div>
                        </div>

                        <!-- 2. Tenang -->
                        <div class="mood-spectrum-btn selected" data-mood="tenang">
                            <div class="mood-spectrum-icon" style="background:#f0fdf9;color:#0f766e;">😌</div>
                            <div class="mood-spectrum-name">Tenang</div>
                        </div>

                        <!-- 3. Biasa Saja -->
                        <div class="mood-spectrum-btn" data-mood="netral">
                            <div class="mood-spectrum-icon" style="background:#f1f5f9;color:#64748b;">😐</div>
                            <div class="mood-spectrum-name">Netral</div>
                        </div>

                        <!-- 4. Lelah / Cemas -->
                        <div class="mood-spectrum-btn" data-mood="lelah">
                            <div class="mood-spectrum-icon" style="background:#f5f3ff;color:#8b5cf6;">😰</div>
                            <div class="mood-spectrum-name">Lelah</div>
                        </div>

                        <!-- 5. Sedih -->
                        <div class="mood-spectrum-btn" data-mood="sedih">
                            <div class="mood-spectrum-icon" style="background:#fff1f2;color:#f43f5e;">😢</div>
                            <div class="mood-spectrum-name">Sedih</div>
                        </div>

                    </div>

                    <!-- 2. Emotional Tags -->
                    <div class="mood-section-label" style="font-size:12px;margin-bottom:8px;">
                        <span>Tag Emosi (Opsional)</span>
                    </div>
                    <div class="mood-tags-row" id="moodTagsContainer">
                        <span class="mood-tag-pill">#Tenang</span>
                        <span class="mood-tag-pill">#Fokus</span>
                        <span class="mood-tag-pill">#Lega</span>
                        <span class="mood-tag-pill">#Cemas</span>
                        <span class="mood-tag-pill">#LelahMental</span>
                        <span class="mood-tag-pill">#DeadlineDekat</span>
                        <span class="mood-tag-pill">#Bersyukur</span>
                    </div>

                    <!-- 3. Triggers -->
                    <div class="mood-section-label" style="font-size:12px;margin-bottom:8px;">
                        <span>Pemicu Emosi (Opsional)</span>
                    </div>
                    <div class="mood-triggers-grid" id="moodTriggersContainer">
                        <div class="mood-trigger-box">🎓 Kuliah/Tugas</div>
                        <div class="mood-trigger-box">💼 Pekerjaan</div>
                        <div class="mood-trigger-box">💤 Kurang Tidur</div>
                        <div class="mood-trigger-box">👥 Hubungan</div>
                        <div class="mood-trigger-box">🏃 Fisik/Tubuh</div>
                        <div class="mood-trigger-box">🌦️ Lingkungan</div>
                    </div>

                    <!-- 4. Note Textarea -->
                    <div class="mood-section-label" style="font-size:12px;margin-bottom:8px;">
                        <span>Catatan Pribadi & Keluhan</span>
                    </div>
                    <div class="mood-textarea-wrap">
                        <textarea class="mood-textarea" id="moodNoteInput" placeholder="Tuliskan keluhan atau apa yang ada di pikiranmu hari ini..."></textarea>
                    </div>

                    <!-- Footer -->
                    <div class="mood-journal-footer">
                        <span style="font-size:11.5px;color:var(--slate-400);">🔒 Tersimpan secara privat</span>
                        <button type="button" id="btnSubmitMoodJournal" class="btn-submit-teal" style="padding:9px 18px;font-size:12.5px;">
                            Simpan Jurnal
                        </button>
                    </div>
                </div>

                <!-- Right Column: Analytics & Monthly Distribution -->
                <div class="mood-right-col">
                    
                    <!-- Weekly Chart Card -->
                    <div class="mood-chart-card">
                        <div class="mood-chart-header">
                            <div>
                                <h3 style="font-size:1.05rem;font-weight:700;color:var(--slate-900);margin:0;">
                                    Fluktuasi Mingguan
                                </h3>
                            </div>
                            <span id="weeklyPekanBadge" style="font-size:11px;font-weight:600;color:var(--slate-500);background:var(--slate-100);padding:3px 9px;border-radius:var(--radius-full);">
                                Pekan Ini
                            </span>
                        </div>

                        <div class="mood-chart-stats">
                            <span style="color:var(--teal-700);font-weight:600;">● Rata-rata Ketenangan</span>
                            <span id="weeklyAvgScore"><strong>- / 10</strong></span>
                        </div>

                        <!-- Dynamic SVG Curve Graph or Empty State -->
                        <div id="weeklyCurveContainer" style="background:#f8fafc;border-radius:var(--radius-md);padding:10px 8px;border:1px solid var(--slate-100);min-height:90px;display:flex;align-items:center;justify-content:center;">
                            <!-- Injected dynamically via JS -->
                        </div>

                        <!-- Days row -->
                        <div id="weeklyDaysRow" style="display:flex;justify-content:space-between;font-size:11px;color:var(--slate-400);font-weight:600;padding:6px 10px 0;">
                        </div>

                        <!-- Concise Insights -->
                        <div class="mood-insights-wrap" id="weeklyInsightsWrap" style="margin-top:12px;padding-top:10px;">
                        </div>
                    </div>

                    <!-- Monthly Calendar Card (Real-time 2026 Calendar) -->
                    <div class="mood-calendar-card">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <h3 id="calMonthYearTitle" style="font-size:1.05rem;font-weight:700;color:var(--slate-900);margin:0;">
                                <?= $currentMonthYear ?>
                            </h3>
                            <div id="calFilledCount" style="font-size:11px;color:var(--slate-400);">
                                0 Terisi
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <table class="mood-cal-table">
                            <thead>
                                <tr>
                                    <th>S</th><th>S</th><th>R</th><th>K</th><th>J</th><th>S</th><th>M</th>
                                </tr>
                            </thead>
                            <tbody id="calTableBody">
                                <!-- Real 2026 Calendar days populated by JS -->
                            </tbody>
                        </table>

                        <!-- Legend -->
                        <div class="mood-cal-legend" style="margin-top:10px;padding-top:8px;">
                            <span class="mood-legend-item"><span class="mood-cal-dot green" style="position:static;"></span> Bahagia</span>
                            <span class="mood-legend-item"><span class="mood-cal-dot teal" style="position:static;"></span> Tenang</span>
                            <span class="mood-legend-item"><span class="mood-cal-dot slate" style="position:static;"></span> Netral</span>
                            <span class="mood-legend-item"><span class="mood-cal-dot purple" style="position:static;"></span> Lelah</span>
                            <span class="mood-legend-item"><span class="mood-cal-dot red" style="position:static;"></span> Sedih</span>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Breathing Quick Banner -->
            <div class="mood-breathe-banner" style="padding:14px 20px;">
                <div class="mood-breathe-left">
                    <div class="mood-breathe-icon" style="width:38px;height:38px;">
                        🧘
                    </div>
                    <div>
                        <strong style="font-size:13.5px;color:#0f766e;display:block;">
                            Butuh jeda sejenak?
                        </strong>
                        <span style="font-size:12px;color:#134e4a;">
                            Latihan napas 3 menit 'Grounding Box' untuk meredakan ketegangan.
                        </span>
                    </div>
                </div>
                <a href="meditasi.php" class="btn-submit-teal" style="padding:8px 16px;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;">
                    Mulai Napas
                </a>
            </div>

            <!-- Bottom Section: Riwayat Entri Jurnal (Hanya dari Input Nyata Pengguna) -->
            <div class="mood-history-card">
                <div class="mood-history-header" style="margin-bottom:14px;">
                    <div>
                        <h2 style="font-size:1.15rem;font-weight:700;color:var(--slate-900);margin:0;">
                            Riwayat Jurnal
                        </h2>
                    </div>

                    <!-- Filter buttons -->
                    <div class="mood-history-filters">
                        <button type="button" class="mood-filter-btn active" data-filter="7">7 Hari</button>
                        <button type="button" class="mood-filter-btn" data-filter="30">30 Hari</button>
                        <button type="button" class="mood-filter-btn" data-filter="all">Semua</button>
                    </div>
                </div>

                <!-- Clean Real Entries List (Populated via JS from mood_logs) -->
                <div class="mood-entries-list" id="moodJournalEntriesList">
                </div>
            </div>

        </main>
    </div>

    <script src="script.js?v=<?= time() ?>"></script>
    <!-- Script for real-time interactions, real 2026 calendar, and dynamic sync -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let activeMood = 'tenang';
        let currentFilter = '7';
        let allJournalData = [];

        // Mood buttons
        const moodBtns = document.querySelectorAll('.mood-spectrum-btn');
        moodBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                moodBtns.forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                activeMood = btn.getAttribute('data-mood') || 'tenang';
            });
        });

        // Tags toggle
        const tagPills = document.querySelectorAll('#moodTagsContainer .mood-tag-pill');
        tagPills.forEach(tag => {
            tag.addEventListener('click', () => {
                tag.classList.toggle('active');
            });
        });

        // Trigger boxes toggle
        const triggerBoxes = document.querySelectorAll('#moodTriggersContainer .mood-trigger-box');
        triggerBoxes.forEach(trig => {
            trig.addEventListener('click', () => {
                trig.classList.toggle('active');
            });
        });

        // History filter buttons
        const filterBtns = document.querySelectorAll('.mood-filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.getAttribute('data-filter') || '7';
                renderJournalEntries(allJournalData);
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function getMoodColor(moodType) {
            switch(moodType) {
                case 'sangat-baik': return '#10b981';
                case 'baik-tenang': return '#0f766e';
                case 'biasa-saja':  return '#64748b';
                case 'cemas-lelah': return '#8b5cf6';
                case 'kewalahan':   return '#ef4444';
                default:            return '#0f766e';
            }
        }

        function getMoodIcon(moodType) {
            switch(moodType) {
                case 'sangat-baik': return '😄';
                case 'baik-tenang': return '😌';
                case 'biasa-saja':  return '😐';
                case 'cemas-lelah': return '😰';
                case 'kewalahan':   return '😢';
                default:            return '🌱';
            }
        }

        // Render Fluktuasi Mingguan Curve
        function renderWeeklyCurve(data) {
            const container = document.getElementById('weeklyCurveContainer');
            const avgScoreEl = document.getElementById('weeklyAvgScore');
            const daysRow = document.getElementById('weeklyDaysRow');
            const insightsWrap = document.getElementById('weeklyInsightsWrap');
            const pekanBadge = document.getElementById('weeklyPekanBadge');

            if (!container) return;

            if (!data.has_data || !data.chart_points || data.chart_points.length === 0) {
                if (avgScoreEl) avgScoreEl.innerHTML = '<strong>- / 10</strong>';
                if (pekanBadge) pekanBadge.innerText = 'Pekan Ini';
                if (daysRow) daysRow.innerHTML = '';
                container.innerHTML = `
                    <div class="trend-empty-state" style="padding:16px 12px;width:100%;">
                        <div class="trend-empty-icon" style="font-size:20px;">📊</div>
                        <div class="trend-empty-title" style="font-size:12px;">Belum Ada Riwayat Fluktuasi</div>
                        <p class="trend-empty-desc" style="font-size:11px;">
                            Grafik fluktuasi mingguan akan otomatis tergambar saat kamu menginput perasaanmu di Dashboard atau form ini.
                        </p>
                    </div>
                `;
                if (insightsWrap) {
                    insightsWrap.innerHTML = `
                        <div class="mood-insight-item" style="padding:8px 10px;font-size:11.5px;background:var(--slate-50);border-radius:var(--radius-md);color:var(--slate-600);">
                            <span>🌱</span>
                            <div>Mulai catat emosimu secara teratur untuk melihat pola fluktuasi suasana hati.</div>
                        </div>
                    `;
                }
                return;
            }

            if (avgScoreEl) {
                avgScoreEl.innerHTML = data.average_score ? `<strong>${data.average_score} / 10</strong>` : '<strong>- / 10</strong>';
            }
            if (pekanBadge) {
                pekanBadge.innerText = data.date_range || 'Pekan Ini';
            }

            const pts = data.chart_points;
            const N = pts.length;
            const leftPad = 25;
            const rightPad = 325;

            // Map points to 350x90 canvas
            pts.forEach((p, idx) => {
                p.cx = (N === 1) ? 175 : Math.round(leftPad + (idx * (rightPad - leftPad) / (N - 1)));
                // Score 1-5 maps to y 15 (score 5) to 70 (score 1)
                p.cy = Math.round(70 - ((p.score - 1) * 13.5));
            });

            // Smooth path
            let pathD = '';
            let areaD = '';
            if (N === 1) {
                pathD = `M 15 ${pts[0].cy} L 335 ${pts[0].cy}`;
                areaD = `M 15 ${pts[0].cy} L 335 ${pts[0].cy} L 335 85 L 15 85 Z`;
            } else {
                pathD = `M ${pts[0].cx} ${pts[0].cy}`;
                areaD = `M ${pts[0].cx} ${pts[0].cy}`;
                for (let i = 0; i < N - 1; i++) {
                    const p0 = pts[i];
                    const p1 = pts[i + 1];
                    const cpX = (p0.cx + p1.cx) / 2;
                    pathD += ` C ${cpX} ${p0.cy}, ${cpX} ${p1.cy}, ${p1.cx} ${p1.cy}`;
                    areaD += ` C ${cpX} ${p0.cy}, ${cpX} ${p1.cy}, ${p1.cx} ${p1.cy}`;
                }
                areaD += ` L ${pts[N - 1].cx} 85 L ${pts[0].cx} 85 Z`;
            }

            let nodesSvg = '';
            pts.forEach((p, idx) => {
                const color = getMoodColor(p.mood_type);
                const isLast = (idx === N - 1);
                const r = isLast ? '4.5' : '3.5';
                const stroke = isLast ? '#2dd4bf' : '#ffffff';
                const strokeW = isLast ? '2' : '1.5';
                nodesSvg += `
                    <g style="cursor:pointer;">
                        <title>${escapeHtml(p.date)}: ${escapeHtml(p.label)} (${p.score * 2}/10)</title>
                        <circle cx="${p.cx}" cy="${p.cy}" r="${r}" fill="${color}" stroke="${stroke}" stroke-width="${strokeW}"></circle>
                    </g>
                `;
            });

            container.innerHTML = `
                <svg width="100%" height="90" viewBox="0 0 350 90" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="curveGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2dd4bf" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#2dd4bf" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="${areaD}" fill="url(#curveGradient)" />
                    <path d="${pathD}" fill="none" stroke="#0f766e" stroke-width="2.5" stroke-linecap="round" />
                    ${nodesSvg}
                </svg>
            `;

            if (daysRow) {
                daysRow.innerHTML = pts.map((p, idx) => {
                    const isLast = (idx === pts.length - 1);
                    const color = isLast ? 'var(--teal-800)' : 'var(--slate-500)';
                    const fw = isLast ? '700' : '600';
                    return `<span style="color:${color};font-weight:${fw};">${escapeHtml(p.day)}</span>`;
                }).join('');
            }

            if (insightsWrap) {
                insightsWrap.innerHTML = `
                    <div class="mood-insight-item positive" style="padding:8px 10px;font-size:11.5px;">
                        <span>💡</span>
                        <div>Kondisi dominan: <strong>${escapeHtml(data.dominant_mood)}</strong> (${data.dominant_count} dari ${data.total_in_period} catatan).</div>
                    </div>
                `;
            }
        }

        // Render Real 2026 Calendar
        function renderCalendar(dateInfo, calendarDays) {
            const calTitle = document.getElementById('calMonthYearTitle');
            const calCount = document.getElementById('calFilledCount');
            const tableBody = document.getElementById('calTableBody');

            if (!tableBody || !dateInfo) return;

            if (calTitle) calTitle.innerText = `${dateInfo.month_name} ${dateInfo.year}`;
            const filledCount = Object.keys(calendarDays || {}).length;
            if (calCount) calCount.innerText = `${filledCount} Hari Terisi`;

            const year = dateInfo.year;
            const month = dateInfo.month; // 1-12
            const todayDay = dateInfo.day;

            // Total days in this month
            const totalDays = new Date(year, month, 0).getDate();

            // Day of week of day 1: 0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu
            const firstDayOfWeek = new Date(year, month - 1, 1).getDay();
            // In table header: S(Senin), S(Selasa), R(Rabu), K(Kamis), J(Jumat), S(Sabtu), M(Minggu)
            // Senin = 0, Minggu = 6
            const startCol = (firstDayOfWeek === 0) ? 6 : (firstDayOfWeek - 1);

            // Previous month total days
            const prevMonthTotalDays = new Date(year, month - 1, 0).getDate();

            let rowsHtml = '';
            let currentDay = 1;
            let nextMonthDay = 1;

            for (let row = 0; row < 6; row++) {
                let rowCells = '';
                for (let col = 0; col < 7; col++) {
                    const cellIndex = row * 7 + col;

                    if (cellIndex < startCol) {
                        // Days from previous month
                        const pDay = prevMonthTotalDays - startCol + col + 1;
                        rowCells += `
                            <td class="mood-cal-day">
                                <div class="mood-cal-num" style="color:var(--slate-300);">${pDay}</div>
                            </td>
                        `;
                    } else if (currentDay <= totalDays) {
                        // Current month day
                        const isToday = (currentDay === todayDay);
                        const hasEntry = calendarDays && calendarDays[currentDay];
                        let dotHtml = '';

                        if (hasEntry) {
                            let dotColor = 'teal';
                            switch(hasEntry.mood_type) {
                                case 'sangat-baik': dotColor = 'green'; break;
                                case 'baik-tenang': dotColor = 'teal'; break;
                                case 'biasa-saja':  dotColor = 'slate'; break;
                                case 'cemas-lelah': dotColor = 'purple'; break;
                                case 'kewalahan':   dotColor = 'red'; break;
                            }
                            const dotTitle = `${currentDay} ${dateInfo.month_name}: ${escapeHtml(hasEntry.mood_label)}`;
                            dotHtml = `<span class="mood-cal-dot ${dotColor}" title="${dotTitle}"></span>`;
                        }

                        rowCells += `
                            <td class="mood-cal-day">
                                <div class="mood-cal-num ${isToday ? 'today' : ''}" style="${hasEntry ? 'cursor:pointer;font-weight:700;' : ''}">
                                    ${currentDay}
                                    ${dotHtml}
                                </div>
                            </td>
                        `;
                        currentDay++;
                    } else {
                        // Days from next month to pad
                        rowCells += `
                            <td class="mood-cal-day">
                                <div class="mood-cal-num" style="color:var(--slate-300);">${nextMonthDay}</div>
                            </td>
                        `;
                        nextMonthDay++;
                    }
                }
                rowsHtml += `<tr>${rowCells}</tr>`;
                if (currentDay > totalDays) break;
            }

            tableBody.innerHTML = rowsHtml;
        }

        // Render Real User Journal Entries
        function renderJournalEntries(entries) {
            const listEl = document.getElementById('moodJournalEntriesList');
            if (!listEl) return;

            if (!entries || entries.length === 0) {
                listEl.innerHTML = `
                    <div class="trend-empty-state" style="padding:32px 20px;border-radius:var(--radius-lg);background:var(--slate-50);border:1.5px dashed var(--slate-200);text-align:center;">
                        <div class="trend-empty-icon" style="font-size:26px;margin-bottom:8px;">📖</div>
                        <div class="trend-empty-title" style="font-size:13.5px;font-weight:700;color:var(--slate-700);margin-bottom:4px;">Belum Ada Riwayat Jurnal</div>
                        <p class="trend-empty-desc" style="font-size:12px;color:var(--slate-500);max-width:380px;margin:0 auto;line-height:1.45;">
                            Riwayat jurnal hanya akan terisi saat kamu menuliskan keluhan atau emosimu lalu menekan "Simpan Jurnal" (atau melalui Dashboard).
                        </p>
                    </div>
                `;
                return;
            }

            // Filter by period
            let filtered = entries;
            if (currentFilter === '7') {
                filtered = entries.slice(0, 7);
            } else if (currentFilter === '30') {
                filtered = entries.slice(0, 30);
            }

            listEl.innerHTML = filtered.map(item => {
                const color = getMoodColor(item.mood_type);
                const icon = getMoodIcon(item.mood_type);
                const scoreScaled = (item.mood_score ? item.mood_score * 2 : 6) + '/10';
                
                // Format timestamp
                const dateObj = new Date(item.created_at.replace(' ', 'T'));
                let timeStr = item.created_at;
                if (!isNaN(dateObj.getTime())) {
                    timeStr = dateObj.toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'short', year: 'numeric'
                    }) + ' • ' + dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
                }

                const noteText = item.note ? escapeHtml(item.note) : 'Tanpa catatan tertulis.';
                
                let recHtml = '';
                if (item.recommendation) {
                    recHtml = `
                        <div style="font-size:11.5px;color:#0f766e;background:#f0fdfa;border-left:3px solid #0f766e;padding:7px 10px;margin-top:8px;border-radius:4px;display:flex;justify-content:space-between;align-items:center;gap:8px;">
                            <div><strong>💡 Refleksi Klinis:</strong> ${escapeHtml(item.recommendation)}</div>
                            ${item.relevant_link ? `<a href="${escapeHtml(item.relevant_link)}" style="color:#0f766e;font-weight:700;text-decoration:none;white-space:nowrap;font-size:11px;">Buka ></a>` : ''}
                        </div>
                    `;
                }

                return `
                    <div class="mood-entry-item" style="padding:14px 16px;background:#ffffff;border:1px solid var(--slate-200);border-radius:var(--radius-lg);margin-bottom:10px;">
                        <div class="mood-entry-head" style="margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;">
                            <div class="mood-entry-emotion" style="font-size:13px;display:flex;align-items:center;gap:6px;">
                                <span>${icon}</span>
                                <strong style="color:var(--slate-800);">${escapeHtml(item.mood_label)}</strong>
                                <span style="font-size:10.5px;color:${color};background:${color}15;padding:1px 6px;border-radius:var(--radius-full);font-weight:700;">
                                    ${scoreScaled}
                                </span>
                            </div>
                            <div style="font-size:11.5px;color:var(--slate-400);">
                                ${timeStr}
                            </div>
                        </div>
                        <div class="mood-entry-quote" style="font-size:12px;padding:8px 12px;background:var(--slate-50);border-radius:var(--radius-sm);color:var(--slate-700);line-height:1.5;">
                            "${noteText}"
                        </div>
                        ${recHtml}
                    </div>
                `;
            }).join('');
        }

        // Load All Real Data from API
        function loadMoodTrackerData() {
            fetch('api_mood.php?action=get')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        allJournalData = data.all_history || [];

                        // Real streak / total
                        const streakEl = document.getElementById('moodTrackerStreakText');
                        if (streakEl) {
                            streakEl.innerText = `${data.total_entries || 0} Catatan Tersimpan`;
                        }

                        // Real today pill
                        const todayPill = document.getElementById('moodTrackerTodayPill');
                        if (todayPill && data.date_info) {
                            todayPill.innerText = data.date_info.formatted;
                        }

                        renderWeeklyCurve(data);
                        renderCalendar(data.date_info, data.calendar_days);
                        renderJournalEntries(allJournalData);
                    }
                })
                .catch(err => console.error('Gagal memuat data mood tracker:', err));
        }

        // Submit Mood Journal
        const submitBtn = document.getElementById('btnSubmitMoodJournal');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                const selectedBtn = document.querySelector('.mood-spectrum-btn.selected');
                const rawMood = selectedBtn ? selectedBtn.getAttribute('data-mood') : 'tenang';
                
                const moodMap = {
                    'bahagia': 'sangat-baik',
                    'tenang': 'baik-tenang',
                    'netral': 'biasa-saja',
                    'lelah': 'cemas-lelah',
                    'sedih': 'kewalahan'
                };
                const moodType = moodMap[rawMood] || 'baik-tenang';
                const noteVal = (document.getElementById('moodNoteInput') ? document.getElementById('moodNoteInput').value : '').trim();

                // Collect active tags
                const activeTags = [];
                document.querySelectorAll('#moodTagsContainer .mood-tag-pill.active').forEach(p => {
                    activeTags.push(p.innerText.trim());
                });

                // Collect active triggers
                const activeTriggers = [];
                document.querySelectorAll('#moodTriggersContainer .mood-trigger-box.active').forEach(t => {
                    activeTriggers.push(t.innerText.trim());
                });

                submitBtn.disabled = true;
                submitBtn.innerText = 'Menyimpan...';

                fetch('api_mood.php?action=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        mood_type: moodType,
                        note: noteVal,
                        tags: activeTags,
                        triggers: activeTriggers
                    })
                })
                .then(r => r.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Jurnal Tersimpan!';
                    setTimeout(() => {
                        submitBtn.innerText = 'Simpan Jurnal';
                    }, 2200);

                    // Clear form
                    if (document.getElementById('moodNoteInput')) {
                        document.getElementById('moodNoteInput').value = '';
                    }
                    document.querySelectorAll('#moodTagsContainer .mood-tag-pill').forEach(p => p.classList.remove('active'));
                    document.querySelectorAll('#moodTriggersContainer .mood-trigger-box').forEach(t => t.classList.remove('active'));

                    // Instantly refresh all widgets in realtime!
                    loadMoodTrackerData();
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Simpan Jurnal';
                    console.error('Error saving mood journal:', err);
                    alert('Terjadi kendala saat menyimpan jurnal.');
                });
            });
        }

        // Initial Load
        loadMoodTrackerData();
    });
    </script>
</body>
</html>
