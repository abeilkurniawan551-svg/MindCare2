<?php
session_start();
if (!isset($_SESSION['fullname'])) {
    $_SESSION['fullname'] = 'Sobat MindCare';
    $_SESSION['role'] = 'Mahasiswa & Anggota MindCare';
}
$fullname = $_SESSION['fullname'];
$firstDisplayName = explode(' ', $fullname)[0];
$fullDisplayName = htmlspecialchars($fullname);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Check: Skrining Kesejahteraan - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        /* Modern Rounded Box Style for Question Numbers */
        .selfcheck-dots-track {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
            margin: 16px 0 14px 0;
            padding: 4px 2px;
        }

        .selfcheck-dot-btn {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 12px;
            border: 1.5px solid var(--slate-200);
            background: #ffffff;
            color: var(--slate-600);
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            outline: none;
        }

        .selfcheck-dot-btn:hover {
            border-color: var(--teal-500);
            color: var(--teal-800);
            background: var(--teal-50);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(15, 118, 110, 0.15);
        }

        /* Saat pertanyaan telah dijawab */
        .selfcheck-dot-btn.answered {
            background: #f0fdfa;
            border-color: #99f6e4;
            color: var(--teal-800);
            font-weight: 800;
        }

        .selfcheck-dot-btn.answered::after {
            content: '';
            position: absolute;
            top: 4px;
            right: 4px;
            width: 6px;
            height: 6px;
            background: var(--teal-600);
            border-radius: 50%;
        }

        /* Saat pertanyaan sedang aktif dibuka */
        .selfcheck-dot-btn.current {
            background: linear-gradient(135deg, var(--teal-700), #0d9488);
            border-color: var(--teal-700);
            color: #ffffff !important;
            box-shadow: 0 6px 14px rgba(15, 118, 110, 0.35);
            transform: translateY(-2px) scale(1.08);
            font-weight: 800;
        }

        .selfcheck-dot-btn.current::after {
            background: #ffffff;
        }

        /* Responsive track */
        @media (max-width: 640px) {
            .selfcheck-dot-btn {
                width: 32px;
                height: 32px;
                min-width: 32px;
                font-size: 12px;
                border-radius: 9px;
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
                        <a href="self-check.php" class="active">
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
            
            <!-- Page Header (Clean & Concise) -->
            <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:18px;">
                <div>
                    <h1 style="font-size:1.55rem;font-weight:800;color:var(--slate-900);margin:0 0 4px 0;letter-spacing:-0.02em;">
                        Self Check: Evaluasi Kesejahteraan
                    </h1>
                    <p style="color:var(--slate-500);font-size:13px;margin:0;">
                        Kenali kondisi stres dan kecemasanmu melalui skrining mandiri 10 pertanyaan teruji.
                    </p>
                </div>
                <!-- Compact Privacy Pill -->
                <div style="display:inline-flex;align-items:center;gap:6px;font-size:11.5px;color:#0f766e;background:#f0fdf9;border:1px solid #ccfbf1;padding:6px 12px;border-radius:var(--radius-full);">
                    <span>🔒</span>
                    <span>100% Privat & Anonim</span>
                </div>
            </div>

            <!-- Assessment Grid (Questionnaire + Side Widget) -->
            <div class="selfcheck-assessment-grid" id="selfcheckAssessmentGrid">
                
                <!-- Left Card: Questionnaire -->
                <div class="selfcheck-card" id="selfcheckCard">
                    <!-- Progress Bar Header -->
                    <div class="selfcheck-progress-header">
                        <span id="progressText" style="font-weight:700;color:var(--slate-800);">Pertanyaan 1 dari 10</span>
                        <span style="color:var(--teal-700);font-weight:700;" id="progressPercent">10% Selesai</span>
                    </div>
                    <div class="selfcheck-progress-track">
                        <div class="selfcheck-progress-fill" id="progressFill" style="width: 10%;"></div>
                    </div>

                    <!-- 10 Questions Dots Track -->
                    <div class="selfcheck-dots-track" id="dotsTrack" title="Klik nomor pertanyaan untuk berpindah">
                        <!-- Injected by JavaScript -->
                    </div>

                    <!-- Question Category Pill -->
                    <div style="margin-bottom:10px;">
                        <span class="selfcheck-mini-badge teal" id="questionCategory">Kecemasan & Ketegangan</span>
                    </div>

                    <!-- Question Text -->
                    <div class="selfcheck-question-text" id="questionText" style="font-size:1.18rem;font-weight:700;color:var(--slate-800);line-height:1.45;margin:10px 0 20px;min-height:56px;">
                        Dalam 2 minggu terakhir, seberapa sering kamu merasa cemas, gelisah, atau sulit merasa rileks?
                    </div>

                    <!-- 4 Options Grid -->
                    <div class="selfcheck-options-grid" id="optionsContainer">
                        <!-- Dynamically filled with options -->
                    </div>

                    <!-- Validation warning if next clicked without option -->
                    <div id="selectionWarning" style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:9px 14px;border-radius:var(--radius-md);font-size:12.5px;font-weight:600;margin-bottom:16px;">
                        ⚠️ Silakan pilih salah satu jawaban di atas sebelum melanjutkan.
                    </div>

                    <!-- Navigation Row -->
                    <div class="selfcheck-nav-row">
                        <button type="button" class="btn-selfcheck-back" id="btnPrev" style="visibility:hidden;">
                            ← Sebelumnya
                        </button>
                        <button type="button" class="btn-selfcheck-next" id="btnNext">
                            <span>Berikutnya →</span>
                        </button>
                    </div>
                </div>

                <!-- Right Side Widgets -->
                <div class="selfcheck-side-col">
                    
                    <!-- Tarik Napas Widget -->
                    <div class="selfcheck-side-box">
                        <div style="font-size:13px;font-weight:700;color:var(--slate-800);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                            <span>🌿</span> <span>Jeda Bernapas</span>
                        </div>
                        <div class="selfcheck-breathe-hero" style="padding:16px;min-height:76px;display:flex;align-items:center;justify-content:center;">
                            <p id="affirmationQuote" style="font-size:12.5px;margin:0;transition:opacity 0.3s ease;">
                                "Merasakan lelah dan cemas bukan berarti kamu gagal."
                            </p>
                        </div>
                        <div style="font-size:11.5px;color:var(--slate-500);line-height:1.4;margin-bottom:10px;">
                            Jawablah sesuai dengan kondisi yang paling jujur kamu alami belakangan ini.
                        </div>
                        <div class="selfcheck-pills-row">
                            <button type="button" id="btnOpenBreathing" class="selfcheck-mini-badge teal" style="cursor:pointer;border:1px solid var(--teal-300);background:var(--teal-50);">
                                🫁 Latihan Napas 4-4-4
                            </button>
                        </div>
                    </div>

                    <!-- Crisis Help Link -->
                    <div class="selfcheck-side-box" style="background:#f8fafc;">
                        <div style="font-size:13px;font-weight:700;color:var(--slate-800);margin-bottom:6px;">
                            Butuh Teman Bicara?
                        </div>
                        <p style="font-size:12px;color:var(--slate-500);line-height:1.4;margin-bottom:10px;">
                            Konselor siaga 24 jam siap mendengarkan tanpa menghakimi.
                        </p>
                        <a href="emergency.php" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:700;color:var(--teal-700);text-decoration:none;">
                            Hubungi Hotline 119 ↗
                        </a>
                    </div>

                </div>

            </div>

            <!-- RINGKASAN HASIL EVALUASI (DITAMPILKAN SETELAH PERTANYAAN 10) -->
            <div class="selfcheck-summary-card" id="selfcheckSummaryCard" style="display:none;">
                <div class="selfcheck-summary-header">
                    <div>
                        <div style="display:inline-flex;align-items:center;gap:6px;background:var(--teal-50);color:var(--teal-800);font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:var(--radius-full);margin-bottom:6px;">
                            <span>✨ Evaluasi Selesai</span>
                        </div>
                        <h2 style="font-size:1.35rem;font-weight:800;color:var(--slate-900);margin:0 0 4px 0;">
                            Hasil Evaluasi Kesejahteraan
                        </h2>
                        <div style="font-size:12.5px;color:var(--slate-500);">
                            Skor indikatif berdasarkan respons 10 pertanyaan skrining mandiri.
                        </div>
                    </div>
                    <span id="resultTimestamp" style="font-size:11.5px;font-weight:600;color:var(--slate-500);background:var(--slate-100);padding:4px 10px;border-radius:var(--radius-full);">
                        Hari ini
                    </span>
                </div>

                <div class="selfcheck-summary-grid">
                    
                    <!-- Left Score Gauge -->
                    <div class="selfcheck-gauge-box">
                        <div class="selfcheck-gauge-circle">
                            <svg width="140" height="140" viewBox="0 0 140 140">
                                <circle cx="70" cy="70" r="54" stroke="#e2e8f0" stroke-width="10" fill="none" />
                                <circle id="gaugeProgressCircle" cx="70" cy="70" r="54" stroke="#0f766e" stroke-width="10" stroke-dasharray="339.29" stroke-dashoffset="339.29" stroke-linecap="round" fill="none" />
                            </svg>
                            <div class="selfcheck-gauge-text">
                                <span class="selfcheck-gauge-score" id="gaugeScore">0</span>
                                <span class="selfcheck-gauge-max">/ 30</span>
                            </div>
                        </div>

                        <div class="selfcheck-gauge-badge" id="gaugeBadge">Memuat...</div>
                        <p class="selfcheck-gauge-desc" id="gaugeDesc">
                            Menganalisis hasil skrining mandirimu...
                        </p>

                        <!-- Spectrum steps -->
                        <div class="selfcheck-spectrum-track">
                            <div class="selfcheck-spectrum-step" id="stepMinimal">Minimal<br>(0-7)</div>
                            <div class="selfcheck-spectrum-step" id="stepRingan">Ringan<br>(8-14)</div>
                            <div class="selfcheck-spectrum-step" id="stepSedang">Sedang<br>(15-22)</div>
                            <div class="selfcheck-spectrum-step" id="stepBerat">Berat<br>(23+)</div>
                        </div>
                    </div>

                    <!-- Right Advice & Practical Steps -->
                    <div class="selfcheck-advice-box">
                        <div class="selfcheck-advice-msg" id="adviceBox" style="padding:16px 18px;margin-bottom:16px;">
                            <div class="selfcheck-advice-title" style="margin-bottom:6px;">
                                Catatan untuk <?= $firstDisplayName ?>
                            </div>
                            <p class="selfcheck-advice-desc" id="adviceDesc" style="font-size:12.8px;line-height:1.55;">
                                Memuat rekomendasi...
                            </p>
                        </div>

                        <!-- 3 Action Steps Grid -->
                        <div style="font-size:12px;font-weight:700;color:var(--slate-800);margin-bottom:8px;">
                            Langkah Pemulihan yang Disarankan:
                        </div>
                        <div class="selfcheck-steps-grid">
                            
                            <!-- Step 1 -->
                            <div class="selfcheck-step-item">
                                <div>
                                    <div class="selfcheck-step-num">1</div>
                                    <div class="selfcheck-step-title" id="actionTitle1">Box Breathing</div>
                                    <div class="selfcheck-step-desc" id="actionDesc1">Tarik dan hembuskan napas 4 hitungan untuk menenangkan detak jantung.</div>
                                </div>
                                <button type="button" class="selfcheck-step-btn" onclick="openBreathingModal();">
                                    Mulai Sesi →
                                </button>
                            </div>

                            <!-- Step 2 -->
                            <div class="selfcheck-step-item">
                                <div>
                                    <div class="selfcheck-step-num">2</div>
                                    <div class="selfcheck-step-title">Mood Tracker</div>
                                    <div class="selfcheck-step-desc">Catat pemicu emosi harianmu agar pikiran lebih tertata rapi.</div>
                                </div>
                                <a href="mood-tracker.php" class="selfcheck-step-btn" style="text-decoration:none;">
                                    Buka Tracker →
                                </a>
                            </div>

                            <!-- Step 3 -->
                            <div class="selfcheck-step-item">
                                <div>
                                    <div class="selfcheck-step-num">3</div>
                                    <div class="selfcheck-step-title">Konseling</div>
                                    <div class="selfcheck-step-desc">Obrolan terarah bersama Psikolog Klinis bersertifikasi.</div>
                                </div>
                                <a href="konsultasi.php" class="selfcheck-step-btn" style="text-decoration:none;">
                                    Pilih Jadwal →
                                </a>
                            </div>

                        </div>

                        <!-- CTA Row -->
                        <div class="selfcheck-cta-row" style="margin-top:14px;align-items:center;">
                            <button type="button" class="btn-submit-teal" style="padding:10px 20px;font-size:13px;font-weight:600;" onclick="window.location.href='konsultasi.php';">
                                Jadwalkan Konsultasi
                            </button>
                            <button type="button" class="btn-selfcheck-back" id="btnSaveProfile" style="background:#ffffff;border:1px solid var(--slate-300);padding:10px 18px;font-size:13px;">
                                💾 Simpan ke Profil
                            </button>
                            <button type="button" class="btn-selfcheck-back" id="btnRetakeQuiz" style="background:#f1f5f9;border:1px solid var(--slate-300);padding:10px 18px;font-size:13px;">
                                🔄 Ulangi Skrining
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Collapsible Answers Breakdown -->
                <details style="margin-top:24px;border:1px solid var(--slate-200);border-radius:var(--radius-lg);background:#fafafa;padding:14px 18px;">
                    <summary style="font-size:13px;font-weight:700;color:var(--slate-800);cursor:pointer;outline:none;user-select:none;">
                        📋 Lihat Rincian Respons 10 Pertanyaan ▾
                    </summary>
                    <div id="answersReviewList" style="margin-top:14px;display:flex;flex-direction:column;gap:10px;font-size:12.5px;">
                        <!-- Injected by JavaScript -->
                    </div>
                </details>
            </div>

            <!-- Minimalist Footer Note -->
            <div style="text-align:center;font-size:11.5px;color:var(--slate-400);padding:14px 0 24px;">
                Instrumen diadaptasi dari DASS-21 & GAD-7 • Data tersimpan terenkripsi secara aman.
            </div>

        </main>
    </div>

    <!-- Breathing Modal -->
    <div id="breathingModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:99999;align-items:center;justify-content:center;padding:16px;">
        <div style="background:#ffffff;border-radius:var(--radius-xl);max-width:400px;width:100%;padding:28px;text-align:center;box-shadow:0 20px 40px rgba(0,0,0,0.2);position:relative;">
            <button type="button" onclick="closeBreathingModal();" style="position:absolute;top:14px;right:16px;background:none;border:none;font-size:20px;color:var(--slate-400);cursor:pointer;">✕</button>
            <div style="font-size:38px;margin-bottom:8px;">🫁</div>
            <h3 style="font-size:18px;font-weight:800;color:var(--slate-800);margin:0 0 6px;">Box Breathing 4-4-4</h3>
            <p style="font-size:12.5px;color:var(--slate-500);margin-bottom:20px;">
                Ikuti instruksi visual di bawah untuk menenangkan sistem sarafmu.
            </p>
            <div id="breatheCircle" style="width:110px;height:110px;border-radius:50%;background:linear-gradient(135deg, #0f766e, #14b8a6);margin:0 auto 18px;display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:14px;font-weight:700;transition:transform 4s ease-in-out;transform:scale(0.85);box-shadow:0 8px 24px rgba(15,118,110,0.3);">
                Tarik Napas
            </div>
            <div id="breatheTimerText" style="font-size:13px;font-weight:700;color:var(--teal-800);margin-bottom:18px;">
                4 detik
            </div>
            <button type="button" class="btn-submit-teal" onclick="closeBreathingModal();" style="width:100%;padding:10px;font-size:13px;">
                Selesai / Tutup
            </button>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="selfcheckToast" class="selfcheck-toast">
        <span>✅</span>
        <span id="toastMsg">Hasil skrining tersimpan ke profil!</span>
    </div>

    <script src="script.js?v=<?= time() ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 10 psychological screening questions (adapted from DASS-21 / GAD-7)
        const questions = [
            {
                id: 1,
                category: "Kecemasan & Ketegangan",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa cemas, gelisah, atau sulit merasa rileks?"
            },
            {
                id: 2,
                category: "Kelelahan & Energi",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa mudah lelah atau kehabisan energi meski tidak banyak beraktivitas fisik?"
            },
            {
                id: 3,
                category: "Fokus & Konsentrasi",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa sulit berkonsentrasi atau fokus pada pekerjaan maupun aktivitas belajar?"
            },
            {
                id: 4,
                category: "Kualitas Tidur",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu mengalami gangguan tidur (sulit terlelap, sering terbangun, atau tidur berlebihan)?"
            },
            {
                id: 5,
                category: "Beban Pikiran",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa kewalahan dengan beban pikiran, tugas, atau tanggung jawab harian?"
            },
            {
                id: 6,
                category: "Regulasi Emosi",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa mudah kesal, tersinggung, atau cepat marah karena hal-hal kecil?"
            },
            {
                id: 7,
                category: "Minat & Motivasi",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa kehilangan minat atau kepuasan saat melakukan aktivitas yang biasa kamu sukai?"
            },
            {
                id: 8,
                category: "Ketegangan Fisik",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasakan ketegangan fisik akibat stres (otot tegang, sakit kepala, atau detak jantung cepat)?"
            },
            {
                id: 9,
                category: "Kepercayaan Diri",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa ragu pada kemampuan diri sendiri atau dihantui perasaan tidak berdaya?"
            },
            {
                id: 10,
                category: "Optimisme Masa Depan",
                text: "Dalam 2 minggu terakhir, seberapa sering kamu merasa masa depan tampak suram atau sulit merasa optimis terhadap keadaan?"
            }
        ];

        // 4 standardized Likert response options
        const optionsData = [
            {
                score: 0,
                title: "Tidak Pernah",
                desc: "Sama sekali tidak dirasakan dalam 2 pekan terakhir"
            },
            {
                score: 1,
                title: "Beberapa Hari",
                desc: "Dirasakan 1–6 hari dalam 2 pekan terakhir"
            },
            {
                score: 2,
                title: "Lebih dari Separuh Waktu",
                desc: "Dirasakan 7–11 hari, cukup mengganggu aktivitas"
            },
            {
                score: 3,
                title: "Hampir Setiap Hari",
                desc: "Dirasakan 12–14 hari, terjadi hampir sepanjang hari"
            }
        ];

        // Supportive quotes for right hero card
        const affirmations = [
            "Merasakan lelah dan cemas bukan berarti kamu gagal.",
            "Tarik napas dalam-dalam. Kamu berhak mengambil jeda sejenak.",
            "Setiap perasaan yang hadir valid untuk didengar dan dirangkul.",
            "Tubuh dan pikiranmu butuh istirahat, beri dirimu ruang pulih.",
            "Kamu sedang berusaha sebaik mungkin hari ini, itu sudah luar biasa.",
            "Tidak apa-apa jika belum memiliki semua jawaban saat ini.",
            "Perlahan tapi pasti, langkah kecil menuju pulih tetaplah kemajuan.",
            "Lepaskan beban yang bukan kendalimu untuk hari ini.",
            "Kebaikan terbesar terkadang adalah berbelas kasih pada diri sendiri.",
            "Terima kasih sudah meluangkan waktu berharga untuk merawat dirimu."
        ];

        // State
        let currentIndex = 0;
        const totalQuestions = questions.length;
        const userAnswers = new Array(totalQuestions).fill(null);

        // Elements
        const selfcheckAssessmentGrid = document.getElementById('selfcheckAssessmentGrid');
        const selfcheckSummaryCard = document.getElementById('selfcheckSummaryCard');
        const progressText = document.getElementById('progressText');
        const progressPercent = document.getElementById('progressPercent');
        const progressFill = document.getElementById('progressFill');
        const dotsTrack = document.getElementById('dotsTrack');
        const questionCategory = document.getElementById('questionCategory');
        const questionText = document.getElementById('questionText');
        const optionsContainer = document.getElementById('optionsContainer');
        const selectionWarning = document.getElementById('selectionWarning');
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const affirmationQuote = document.getElementById('affirmationQuote');
        const btnRetakeQuiz = document.getElementById('btnRetakeQuiz');
        const btnSaveProfile = document.getElementById('btnSaveProfile');
        const btnOpenBreathing = document.getElementById('btnOpenBreathing');
        const breathingModal = document.getElementById('breathingModal');

        // Initialize question dots (1..10)
        function initDots() {
            dotsTrack.innerHTML = '';
            for (let i = 0; i < totalQuestions; i++) {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'selfcheck-dot-btn';
                dot.textContent = i + 1;
                dot.title = `Pertanyaan ${i + 1}`;
                dot.addEventListener('click', () => {
                    // Only allow jumping if answering or review
                    goToQuestion(i);
                });
                dotsTrack.appendChild(dot);
            }
        }

        // Render current question
        function renderQuestion() {
            const q = questions[currentIndex];
            const percent = Math.round(((currentIndex + 1) / totalQuestions) * 100);

            // Progress text and fill
            progressText.textContent = `Pertanyaan ${currentIndex + 1} dari ${totalQuestions}`;
            progressPercent.textContent = `${percent}% Selesai`;
            progressFill.style.width = `${percent}%`;

            // Category & Question text
            questionCategory.textContent = q.category;
            questionText.textContent = q.text;

            // Affirmation quote
            if (affirmationQuote) {
                affirmationQuote.style.opacity = '0';
                setTimeout(() => {
                    affirmationQuote.textContent = `"${affirmations[currentIndex % affirmations.length]}"`;
                    affirmationQuote.style.opacity = '1';
                }, 200);
            }

            // Update dots
            const allDots = dotsTrack.querySelectorAll('.selfcheck-dot-btn');
            allDots.forEach((dot, idx) => {
                dot.classList.remove('current');
                if (idx === currentIndex) {
                    dot.classList.add('current');
                }
                if (userAnswers[idx] !== null) {
                    dot.classList.add('answered');
                } else {
                    dot.classList.remove('answered');
                }
            });

            // Hide warning
            selectionWarning.style.display = 'none';

            // Prev button visibility
            if (currentIndex === 0) {
                btnPrev.style.visibility = 'hidden';
            } else {
                btnPrev.style.visibility = 'visible';
            }

            // Next button label
            if (currentIndex === totalQuestions - 1) {
                btnNext.innerHTML = `<span>Selesaikan & Lihat Hasil ✨</span>`;
                btnNext.style.background = '#0d9488';
            } else {
                btnNext.innerHTML = `<span>Berikutnya →</span>`;
                btnNext.style.background = 'var(--teal-700)';
            }

            // Render options
            optionsContainer.innerHTML = '';
            optionsData.forEach(opt => {
                const isSelected = userAnswers[currentIndex] === opt.score;
                const box = document.createElement('div');
                box.className = `selfcheck-option-box ${isSelected ? 'selected' : ''}`;
                box.setAttribute('data-score', opt.score);

                box.innerHTML = `
                    <div class="selfcheck-option-top">
                        <span class="selfcheck-option-score">Skor ${opt.score} ${isSelected ? '• Dipilih' : ''}</span>
                        <div class="selfcheck-radio-indicator">
                            ${isSelected ? '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>' : ''}
                        </div>
                    </div>
                    <div class="selfcheck-option-title">${opt.title}</div>
                    <div class="selfcheck-option-desc">${opt.desc}</div>
                `;

                box.addEventListener('click', () => {
                    userAnswers[currentIndex] = opt.score;
                    selectionWarning.style.display = 'none';

                    // Update visually
                    const allBoxes = optionsContainer.querySelectorAll('.selfcheck-option-box');
                    allBoxes.forEach(b => {
                        b.classList.remove('selected');
                        const scoreSpan = b.querySelector('.selfcheck-option-score');
                        const radio = b.querySelector('.selfcheck-radio-indicator');
                        if (scoreSpan) scoreSpan.textContent = `Skor ${b.getAttribute('data-score')}`;
                        if (radio) radio.innerHTML = '';
                    });

                    box.classList.add('selected');
                    const curScoreSpan = box.querySelector('.selfcheck-option-score');
                    const curRadio = box.querySelector('.selfcheck-radio-indicator');
                    if (curScoreSpan) curScoreSpan.textContent = `Skor ${opt.score} • Dipilih`;
                    if (curRadio) curRadio.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>`;

                    // Update current dot to answered
                    const curDot = dotsTrack.children[currentIndex];
                    if (curDot) curDot.classList.add('answered');
                });

                optionsContainer.appendChild(box);
            });
        }

        function goToQuestion(idx) {
            if (idx >= 0 && idx < totalQuestions) {
                currentIndex = idx;
                renderQuestion();
            }
        }

        // Prev Click
        btnPrev.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                renderQuestion();
            }
        });

        // Next Click
        btnNext.addEventListener('click', () => {
            // Check if answered
            if (userAnswers[currentIndex] === null) {
                selectionWarning.style.display = 'block';
                selectionWarning.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }

            if (currentIndex < totalQuestions - 1) {
                currentIndex++;
                renderQuestion();
            } else {
                // Final submission - Show results!
                finishAndShowResults();
            }
        });

        // Calculate and display results
        function finishAndShowResults() {
            // Sum all scores (0 - 30)
            const totalScore = userAnswers.reduce((sum, val) => sum + (val !== null ? val : 0), 0);

            // Determine level
            let levelData = {};
            if (totalScore <= 7) {
                levelData = {
                    key: 'minimal',
                    title: 'Kondisi Mental Prima & Stabil',
                    color: '#0d9488',
                    badgeBg: '#f0fdf4',
                    badgeColor: '#166534',
                    badgeBorder: '#bbf7d0',
                    desc: 'Tingkat stres dan kecemasanmu sangat rendah. Regulasi emosi dan resiliensimu bekerja dengan sangat baik.',
                    advice: 'Luar biasa! Pertahankan ritme hidup seimbang, kualitas istirahat 7–8 jam, serta hobi yang memberi ketenangan jiwa.',
                    stepId: 'stepMinimal'
                };
            } else if (totalScore <= 14) {
                levelData = {
                    key: 'ringan',
                    title: 'Kecemasan & Stres Ringan',
                    color: '#0284c7',
                    badgeBg: '#f0fdf9',
                    badgeColor: '#0f766e',
                    badgeBorder: '#ccfbf1',
                    desc: 'Stres episodik yang wajar dialami akibat rutinitas harian. Kondisi ini dapat diredakan dengan relaksasi dan istirahat berkualitas.',
                    advice: 'Beban pikiranmu saat ini mulai terasa, namun kondisimu masih terkontrol dengan baik. Luangkan waktu jeda bernapas sejenak dan kurangi multitasking agar energi pulih.',
                    stepId: 'stepRingan'
                };
            } else if (totalScore <= 22) {
                levelData = {
                    key: 'sedang',
                    title: 'Stres & Kecemasan Sedang',
                    color: '#d97706',
                    badgeBg: '#fffbeb',
                    badgeColor: '#b45309',
                    badgeBorder: '#fde68a',
                    desc: 'Beban emosional cukup mengganggu fokus, stamina, dan kenyamanan sehari-hari. Sangat dianjurkan mengambil jeda.',
                    advice: 'Wajar bila kamu merasa lelah akhir-akhir ini. Jangan memaksakan diri menanggung semua tanggung jawab sendirian. Luangkan waktu untuk bercerita atau melatih mindfulness.',
                    stepId: 'stepSedang'
                };
            } else {
                levelData = {
                    key: 'berat',
                    title: 'Stres & Ketegangan Tinggi',
                    color: '#e11d48',
                    badgeBg: '#fef2f2',
                    badgeColor: '#b91c1c',
                    badgeBorder: '#fecaca',
                    desc: 'Tingkat tekanan emosional cukup intensif. Sangat disarankan mendapatkan pendampingan dari psikolog profesional.',
                    advice: 'Kami mengerti bahwa saat ini beban terasa sangat berat dan melelahkan. Mencari pertolongan profesional adalah langkah berani dan tepat demi keselamatan serta kesehatan mentalmu.',
                    stepId: 'stepBerat'
                };
            }

            // Update circular gauge
            const gaugeScore = document.getElementById('gaugeScore');
            const gaugeBadge = document.getElementById('gaugeBadge');
            const gaugeDesc = document.getElementById('gaugeDesc');
            const gaugeCircle = document.getElementById('gaugeProgressCircle');
            const adviceDesc = document.getElementById('adviceDesc');
            const resultTimestamp = document.getElementById('resultTimestamp');

            gaugeScore.textContent = totalScore;
            gaugeBadge.textContent = levelData.title;
            gaugeBadge.style.background = levelData.badgeBg;
            gaugeBadge.style.color = levelData.badgeColor;
            gaugeBadge.style.borderColor = levelData.badgeBorder;
            gaugeDesc.textContent = levelData.desc;
            adviceDesc.textContent = levelData.advice;

            // Formatted current time
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            resultTimestamp.textContent = `Hari ini, ${timeStr} WIB`;

            // Gauge SVG circle math (Circumference 339.29 for r=54)
            const circumference = 339.29;
            const progressRatio = Math.min(Math.max(totalScore / 30, 0), 1);
            const offset = circumference * (1 - progressRatio);
            gaugeCircle.style.strokeDashoffset = offset;
            gaugeCircle.style.stroke = levelData.color;

            // Update spectrum track
            const spectrumSteps = document.querySelectorAll('.selfcheck-spectrum-step');
            spectrumSteps.forEach(st => {
                st.classList.remove('active', 'step-minimal', 'step-ringan', 'step-sedang', 'step-berat');
            });
            const activeStepEl = document.getElementById(levelData.stepId);
            if (activeStepEl) {
                activeStepEl.classList.add('active', `step-${levelData.key}`);
            }

            // Populate Breakdown Review List
            const answersReviewList = document.getElementById('answersReviewList');
            answersReviewList.innerHTML = '';
            questions.forEach((q, idx) => {
                const ansScore = userAnswers[idx];
                const optObj = optionsData.find(o => o.score === ansScore) || { title: 'Tidak dijawab' };
                const item = document.createElement('div');
                item.style.padding = '8px 12px';
                item.style.background = '#ffffff';
                item.style.border = '1px solid var(--slate-200)';
                item.style.borderRadius = 'var(--radius-md)';
                item.style.display = 'flex';
                item.style.justifyContent = 'space-between';
                item.style.alignItems = 'center';
                item.style.gap = '12px';
                item.innerHTML = `
                    <div style="flex:1;">
                        <span style="font-weight:700;color:var(--slate-800);">${idx + 1}. ${q.text}</span>
                        <div style="font-size:11.5px;color:var(--slate-500);margin-top:2px;">Kategori: ${q.category}</div>
                    </div>
                    <span style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:var(--radius-full);background:var(--teal-50);color:var(--teal-800);white-space:nowrap;">
                        ${optObj.title} (Skor ${ansScore})
                    </span>
                `;
                answersReviewList.appendChild(item);
            });

            // Transition: Hide questionnaire and show summary
            selfcheckAssessmentGrid.style.display = 'none';
            selfcheckSummaryCard.style.display = 'block';
            selfcheckSummaryCard.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Save to localStorage for persistence
            try {
                localStorage.setItem('MindCare_selfcheck_latest', JSON.stringify({
                    score: totalScore,
                    max: 30,
                    level: levelData.title,
                    date: now.toISOString(),
                    answers: userAnswers
                }));
            } catch (e) {
                console.error(e);
            }
        }

        // Retake Quiz Handler
        btnRetakeQuiz.addEventListener('click', () => {
            // Reset state
            for (let i = 0; i < totalQuestions; i++) {
                userAnswers[i] = null;
            }
            currentIndex = 0;
            selfcheckSummaryCard.style.display = 'none';
            selfcheckAssessmentGrid.style.display = 'grid';
            renderQuestion();
            selfcheckAssessmentGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            showToast('Memulai kembali evaluasi skrining dari nomor 1.');
        });

        // Save to Profile CTA
        btnSaveProfile.addEventListener('click', () => {
            showToast('Hasil evaluasi berhasil disimpan ke profil Anda!');
        });

        // Toast Helper
        function showToast(msg) {
            const toast = document.getElementById('selfcheckToast');
            const toastMsg = document.getElementById('toastMsg');
            toastMsg.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Breathing Modal handlers
        window.openBreathingModal = () => {
            breathingModal.style.display = 'flex';
            startBreatheCycle();
        };

        window.closeBreathingModal = () => {
            breathingModal.style.display = 'none';
            if (window.breatheTimer) clearInterval(window.breatheTimer);
        };

        btnOpenBreathing.addEventListener('click', () => {
            window.openBreathingModal();
        });

        function startBreatheCycle() {
            const circle = document.getElementById('breatheCircle');
            const timerText = document.getElementById('breatheTimerText');
            let phase = 0; // 0: inhale, 1: hold, 2: exhale, 3: hold
            const phases = [
                { text: 'Tarik Napas', scale: 1.25, time: 4 },
                { text: 'Tahan', scale: 1.25, time: 4 },
                { text: 'Hembuskan', scale: 0.85, time: 4 },
                { text: 'Tahan', scale: 0.85, time: 4 }
            ];

            let count = 4;
            function runPhase() {
                const cur = phases[phase];
                circle.textContent = cur.text;
                circle.style.transform = `scale(${cur.scale})`;
                timerText.textContent = `${count} detik`;

                count--;
                if (count < 0) {
                    phase = (phase + 1) % 4;
                    count = 4;
                }
            }

            runPhase();
            if (window.breatheTimer) clearInterval(window.breatheTimer);
            window.breatheTimer = setInterval(runPhase, 1000);
        }

        // Initial setup
        initDots();
        renderQuestion();
    });
    </script>
</body>
</html>

