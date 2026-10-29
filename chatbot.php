<?php
session_start();
require_once __DIR__ . '/db_connect.php';

// Validasi session login user, jika belum ada ambil user pertama dari DB atau arahkan ke login
if (!isset($_SESSION['user_id'])) {
    try {
        $stmtUser = $pdo->query("SELECT id, fullname, role, email FROM users ORDER BY id ASC LIMIT 1");
        $firstUser = $stmtUser->fetch();
        if ($firstUser) {
            $_SESSION['user_id'] = $firstUser['id'];
            $_SESSION['fullname'] = $firstUser['fullname'];
            $_SESSION['role'] = $firstUser['role'] ?? 'Mahasiswa Aktif';
        } else {
            header("Location: login.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['user_id'] = 1;
        $_SESSION['fullname'] = 'Sobat MindCare';
        $_SESSION['role'] = 'Member Aktif';
    }
}

$userId = (int)$_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'Sobat MindCare';
$partsName = explode(' ', trim($fullname));
$firstDisplayName = $partsName[0];
$userRole = $_SESSION['role'] ?? 'Member Aktif';

// Tangani Request: Mulai Percakapan Baru (?new_session=1)
if (isset($_GET['new_session'])) {
    try {
        $stmtNew = $pdo->prepare("INSERT INTO chat_sessions (user_id, session_title, total_messages, status) VALUES (:uid, 'Sesi Refleksi Baru', 0, 'active')");
        $stmtNew->execute([':uid' => $userId]);
        $newSid = (int)$pdo->lastInsertId();
        header("Location: chatbot.php?session_id=" . $newSid);
        exit;
    } catch (Exception $e) {
        header("Location: chatbot.php");
        exit;
    }
}

// Tangani Request: Bersihkan Jejak Sesi (?clear_session=ID)
if (isset($_GET['clear_session'])) {
    $clearSid = (int)$_GET['clear_session'];
    try {
        $stmtDel = $pdo->prepare("DELETE FROM chat_sessions WHERE id = :sid AND user_id = :uid");
        $stmtDel->execute([':sid' => $clearSid, ':uid' => $userId]);
    } catch (Exception $e) {}
    header("Location: chatbot.php");
    exit;
}

// Ambil Arsip Sesi Cerita Nyata untuk User ini
$stmtSessions = $pdo->prepare("SELECT * FROM chat_sessions WHERE user_id = :uid ORDER BY updated_at DESC");
$stmtSessions->execute([':uid' => $userId]);
$sessions = $stmtSessions->fetchAll(PDO::FETCH_ASSOC);

// Jika user belum punya sesi sama sekali, buatkan 1 sesi awal yang nyata
if (empty($sessions)) {
    try {
        $stmtCreateFirst = $pdo->prepare("INSERT INTO chat_sessions (user_id, session_title, total_messages, status) VALUES (:uid, 'Sesi Refleksi Baru', 0, 'active')");
        $stmtCreateFirst->execute([':uid' => $userId]);
        $firstSid = (int)$pdo->lastInsertId();
        header("Location: chatbot.php?session_id=" . $firstSid);
        exit;
    } catch (Exception $e) {
        $sessions = [];
    }
}

// Tentukan Sesi Aktif
$activeSessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : (!empty($sessions) ? (int)$sessions[0]['id'] : 1);
$activeSession = null;
foreach ($sessions as $s) {
    if ((int)$s['id'] === $activeSessionId) {
        $activeSession = $s;
        break;
    }
}
if (!$activeSession && !empty($sessions)) {
    $activeSession = $sessions[0];
    $activeSessionId = (int)$activeSession['id'];
}

// Ambil Pesan Asli dari Sesi Aktif
$chatMessages = [];
if ($activeSessionId > 0) {
    try {
        $stmtMsgs = $pdo->prepare("SELECT * FROM chat_messages WHERE session_id = :sid ORDER BY created_at ASC");
        $stmtMsgs->execute([':sid' => $activeSessionId]);
        $chatMessages = $stmtMsgs->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $chatMessages = [];
    }
}

$currentTimeRealtime = date('H:i') . ' WIB';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot Companion - MindCare</title>
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

            <!-- User Pill Realtime -->
            <a href="profile.php" class="header-user-pill" title="Profil <?= htmlspecialchars($fullname) ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= htmlspecialchars($fullname) ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= htmlspecialchars($fullname) ?></div>
                    <div class="header-user-role"><?= htmlspecialchars($userRole) ?></div>
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
                        <a href="chatbot.php" class="active">
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
                        <a href="profile.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Profil & Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bottom user summary card -->
            <div class="sidebar-user-card">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= htmlspecialchars($fullname) ?>">
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name"><?= htmlspecialchars($fullname) ?></span>
                    <span class="sidebar-user-sub"><?= htmlspecialchars($userRole) ?></span>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- CHATBOT THREE COLUMNS -->
        <main class="chatbot-three-cols">
            
            <!-- COLUMN 1: ARSIP SESI CERITA REALTIME ASLI -->
            <section class="chat-sessions-sidebar">
                <a href="chatbot.php?new_session=1" class="btn-new-chat" style="text-decoration:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Mulai Percakapan Baru</span>
                </a>

                <div class="session-archive-head">
                    <span>ARSIP SESI CERITA</span>
                    <span class="badge" id="sessionCountBadge"><?= count($sessions) ?> Sesi</span>
                </div>

                <div class="session-items-list" id="sessionArchiveList">
                    <?php if (empty($sessions)): ?>
                        <div style="font-size:12px; color:var(--slate-400); text-align:center; padding:20px 0;">Belum ada arsip sesi cerita.</div>
                    <?php else: ?>
                        <?php foreach ($sessions as $s): ?>
                            <?php 
                                $isActive = ((int)$s['id'] === $activeSessionId); 
                                $msgCount = (int)$s['total_messages'];
                                $rawTime = strtotime($s['updated_at'] ?? $s['created_at']);
                                $isToday = (date('Y-m-d', $rawTime) === date('Y-m-d'));
                                $timeStr = $isToday ? ('Hari ini, ' . date('H:i', $rawTime)) : date('d M, H:i', $rawTime);
                                $statusLabel = $isActive ? 'Berlangsung' : ($msgCount . ' pesan');
                            ?>
                            <a href="chatbot.php?session_id=<?= $s['id'] ?>" class="session-item-card <?= $isActive ? 'active' : '' ?>" style="text-decoration:none; color:inherit;">
                                <div class="session-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="session-item-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($s['session_title']) ?></div>
                                    <div class="session-item-time"><?= $timeStr ?> • <?= $statusLabel ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Session privacy notice at bottom -->
                <div class="session-privacy-card">
                    <div class="session-privacy-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>Privasi Tanpa Jejak</span>
                    </div>
                    <p class="session-privacy-desc">
                        Percakapan dienkripsi end-to-end. Hapus riwayat sesi kapan pun dengan aman.
                    </p>
                    <button type="button" class="session-privacy-btn" id="clearSessionBtn" onclick="if(confirm('Apakah Anda yakin ingin membersihkan seluruh jejak pesan pada sesi ini?')) { window.location.href='chatbot.php?clear_session=<?= $activeSessionId ?>'; }">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <span>Bersihkan jejak sesi ini</span>
                    </button>
                </div>
            </section>

            <!-- COLUMN 2: MAIN CHAT STREAM -->
            <section class="chat-main-stream-col">
                
                <!-- Chat Header -->
                <div class="chat-channel-header">
                    <div class="chat-bot-identity">
                        <div class="bot-avatar-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
                        </div>
                        <div>
                            <div class="bot-title">MindCare AI Companion</div>
                            <div class="bot-status-line">
                                <span class="dot"></span>
                                <span>Siaga Mendengarkan 24/7 • Sesi Aman & Terenkripsi</span>
                            </div>
                        </div>
                    </div>

                    <div class="chat-header-actions">
                        <a href="mood-tracker.php" class="header-icon-btn" style="text-decoration:none; padding:6px 12px; width:auto; border-radius:var(--radius-full); font-size:12px; font-weight:700; color:var(--teal-700); background-color:var(--teal-50); border:1px solid var(--teal-200); display:flex; align-items:center; gap:6px;" title="Buka Jurnal Refleksi">
                            <span>📝 Jurnal Refleksi</span>
                        </a>
                        <button type="button" class="chat-head-icon-btn" title="Refresh Sesi" onclick="window.location.reload();">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Scroll Stream -->
                <div class="chat-conversation-area" id="chatConversationArea">
                    <div class="chat-date-divider" id="chatDateDivider">Hari ini • <?= $currentTimeRealtime ?></div>

                    <!-- Single Bot Welcome Message: Bersih & Realtime -->
                    <div class="chat-msg-row bot">
                        <div class="bot-avatar-box" style="width:34px;height:34px;border-radius:50%;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
                        </div>
                        <div class="chat-msg-body">
                            <div class="chat-msg-meta">MindCare AI • <?= $currentTimeRealtime ?></div>
                            <div class="chat-bubble">
                                Halo <?= htmlspecialchars($firstDisplayName) ?>. Aku di sini untuk mendengarkan tanpa menghakimi sama sekali. Apapun yang sedang membebani pundakmu hari ini, luapkan saja pelan-pelan tanpa terburu-buru.<br><br>
                                <strong>Apa yang paling terasa dominan di hati atau pikiranmu saat ini?</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Pesan Nyata dari Database jika sesi sudah ada dialog sebelumnya -->
                    <?php if (!empty($chatMessages)): ?>
                        <?php foreach ($chatMessages as $msg): ?>
                            <?php 
                                $msgTime = date('H:i', strtotime($msg['created_at'])) . ' WIB';
                                $isUser = ($msg['sender'] === 'user');
                            ?>
                            <?php if ($isUser): ?>
                                <div class="chat-msg-row user">
                                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='36' height='36' viewBox='0 0 36 36'><circle cx='18' cy='18' r='18' fill='%230f766e'/><circle cx='18' cy='14' r='6' fill='%23ffffff'/><path d='M8 30c0-5.5 4.5-8 10-8s10 2.5 10 8' fill='%23ffffff'/></svg>" alt="<?= htmlspecialchars($firstDisplayName) ?>" class="chat-msg-avatar">
                                    <div class="chat-msg-body">
                                        <div class="chat-msg-meta"><?= htmlspecialchars($firstDisplayName) ?> • <?= $msgTime ?></div>
                                        <div class="chat-bubble"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="chat-msg-row bot">
                                    <div class="bot-avatar-box" style="width:34px;height:34px;border-radius:50%;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
                                    </div>
                                    <div class="chat-msg-body">
                                        <div class="chat-msg-meta">MindCare AI • <?= $msgTime ?></div>
                                        <div class="chat-bubble">
                                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                            <?php 
                                                $actionList = !empty($msg['action_data']) ? json_decode($msg['action_data'], true) : [];
                                            ?>
                                            <?php if (!empty($actionList) && is_array($actionList)): ?>
                                                <div class="bot-action-cards-grid">
                                                    <?php foreach ($actionList as $act): ?>
                                                        <div class="bot-action-card">
                                                            <div class="bot-action-card-title"><?= htmlspecialchars($act['title'] ?? '') ?></div>
                                                            <div class="bot-action-card-desc"><?= htmlspecialchars($act['desc'] ?? '') ?></div>
                                                            <button type="button" class="btn-card-action" data-action="<?= htmlspecialchars($act['action'] ?? '') ?>" data-url="<?= htmlspecialchars($act['url'] ?? '') ?>"><?= htmlspecialchars($act['btnText'] ?? 'Buka') ?></button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Quick Prompt Chips Row -->
                    <div class="quick-prompt-chips-bar" id="quickChipsBar">
                        <button type="button" class="prompt-chip" data-prompt="Aku merasa cemas berlebih dan overthinking hari ini">Aku sedang merasa cemas berlebih</button>
                        <button type="button" class="prompt-chip" data-prompt="Bantu aku membedah masalah yang sedang menumpuk satu persatu">Bantu bedah masalah satu persatu</button>
                        <button type="button" class="prompt-chip" data-prompt="Aku sangat lelah secara emosional dan merasa burnout">Aku lelah & burnout</button>
                        <button type="button" class="prompt-chip" data-prompt="Bagaimana cara terbaik menuangkan perasaanku ke Jurnal Refleksi?">Tulis ke Jurnal Refleksi</button>
                        <button type="button" class="prompt-chip" data-prompt="Beri aku afirmasi positif penenang batin">Beri afirmasi penenang</button>
                    </div>
                </div>

                <!-- Chat Input Controls -->
                <div class="chat-input-container">
                    <form id="chatForm" class="chat-input-box">
                        <button type="button" class="chat-input-btn" title="Gunakan Audio Voice" onclick="alert('Fitur input suara privat aktif.');">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                        </button>
                        <input type="text" id="chatInput" placeholder="Ceritakan apa yang kamu rasakan, <?= htmlspecialchars($firstDisplayName) ?>..." autocomplete="off">
                        <button type="button" class="chat-input-btn" title="Emosi / Mood" onclick="window.location.href='mood-tracker.php';">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                        </button>
                        <button type="submit" class="btn-send-message" title="Kirim Pesan">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>

                    <div class="chat-input-disclaimer">
                        <span>🔒 Sesi privat & terlindungi untuk <?= htmlspecialchars($firstDisplayName) ?></span>
                        <span>Tekan Enter ↵ untuk mengirim</span>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Pass Realtime User Context to Javascript -->
    <script>
        window.MINDCARE_USER = {
            id: <?= json_encode($userId) ?>,
            fullname: <?= json_encode($fullname) ?>,
            firstName: <?= json_encode($firstDisplayName) ?>
        };
        window.MINDCARE_SESSION_ID = <?= json_encode($activeSessionId) ?>;
    </script>
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
