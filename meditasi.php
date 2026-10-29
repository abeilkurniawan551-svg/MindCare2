<?php
session_start();
require_once __DIR__ . '/db_connect.php';

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
$roleName = $_SESSION['role'] ?? 'Mahasiswa & Anggota MindCare';
$isLoggedIn = isset($_SESSION['user_id']);
$userId = (int)($_SESSION['user_id'] ?? 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditasi, Pernapasan & Audio Relaksasi - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        /* Specific Styles for Meditasi & Pernapasan */
        .med-container {
            padding: 4px 0 100px 0; /* extra bottom padding for floating player */
            max-width: 1240px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        /* Top Header */
        .med-badge-category {
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

        .med-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            flex-wrap: wrap;
        }

        .med-header-text {
            max-width: 740px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .med-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.5px;
            line-height: 1.25;
        }

        .med-subtitle {
            font-size: 14.5px;
            color: var(--slate-600);
            line-height: 1.6;
        }

        /* Right Stat Card */
        .med-stat-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .med-stat-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e6fffa;
            color: var(--teal-700);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .med-stat-group {
            display: flex;
            flex-direction: column;
        }

        .med-stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .med-stat-val {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.2;
            margin-top: 2px;
        }

        .med-stat-val span {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--slate-400);
        }

        .med-stat-divider {
            width: 1px;
            height: 36px;
            background: var(--slate-200);
        }

        /* ========================================================
           HERO: SOMATIC BOX BREATHING WIDGET
           ======================================================== */
        .breath-hero-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            padding: 36px 40px;
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 40px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 900px) {
            .breath-hero-card {
                grid-template-columns: 1fr;
                padding: 24px;
                gap: 28px;
            }
        }

        /* Left Breathing Visualizer */
        .breath-visual-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .breath-dial-wrapper {
            position: relative;
            width: 250px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Dial Compass Labels */
        .breath-marker {
            position: absolute;
            font-size: 11px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.3s ease;
        }
        .breath-marker.active {
            color: var(--teal-700);
            font-weight: 800;
        }
        .marker-top { top: 0; left: 50%; transform: translateX(-50%); }
        .marker-right { right: 0; top: 50%; transform: translateY(-50%); }
        .marker-bottom { bottom: 0; left: 50%; transform: translateX(-50%); }
        .marker-left { left: 0; top: 50%; transform: translateY(-50%); }

        /* Outer Glow Ring */
        .breath-ring-track {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 8px solid #f1f5f9;
        }

        .breath-ring-fill {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 8px solid transparent;
            border-top-color: var(--teal-500);
            transform: rotate(-45deg);
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Inner Pulsing Circle */
        .breath-core-circle {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #0d9488, #115e59);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 10px 25px rgba(13, 148, 136, 0.35);
            transition: transform 1s ease-in-out, background 0.5s ease;
            cursor: pointer;
            user-select: none;
            padding: 12px;
            z-index: 2;
        }

        .breath-core-circle.inhale {
            transform: scale(1.15);
            background: radial-gradient(circle at 35% 35%, #14b8a6, #0f766e);
            box-shadow: 0 15px 35px rgba(20, 184, 166, 0.45);
        }

        .breath-core-circle.hold {
            transform: scale(1.15);
            background: radial-gradient(circle at 35% 35%, #0f766e, #134e4a);
            box-shadow: 0 12px 30px rgba(15, 118, 110, 0.4);
        }

        .breath-core-circle.exhale {
            transform: scale(0.9);
            background: radial-gradient(circle at 35% 35%, #0d9488, #115e59);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.25);
        }

        .breath-core-circle.pause {
            transform: scale(0.9);
            background: radial-gradient(circle at 35% 35%, #334155, #1e293b);
            box-shadow: 0 6px 18px rgba(30, 41, 59, 0.25);
        }

        .breath-status-label {
            font-size: 13.5px;
            font-weight: 700;
            color: #ccfbf1;
            letter-spacing: 0.3px;
        }

        .breath-counter-num {
            font-size: 40px;
            font-weight: 800;
            line-height: 1;
            margin: 4px 0;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        .breath-instruction-text {
            font-size: 10px;
            font-weight: 600;
            color: #a7f3d0;
            max-width: 120px;
            line-height: 1.25;
        }

        .breath-cycle-indicator {
            font-size: 12px;
            font-weight: 600;
            color: var(--slate-500);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .breath-cycle-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--teal-600);
            display: inline-block;
        }

        /* Right Breathing Details & Rhythm Selection */
        .breath-detail-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .breath-tags-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tag-somatic-active {
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tag-vagus-desc {
            font-size: 12px;
            color: var(--slate-500);
            font-weight: 600;
        }

        .breath-exercise-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.3;
        }

        .breath-exercise-desc {
            font-size: 14px;
            color: var(--slate-600);
            line-height: 1.6;
        }

        .breath-section-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 4px;
        }

        /* 3 Rhythm Options */
        .rhythm-options-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        @media (max-width: 600px) {
            .rhythm-options-grid {
                grid-template-columns: 1fr;
            }
        }

        .rhythm-card-btn {
            background: #f8fafc;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
        }

        .rhythm-card-btn:hover {
            border-color: var(--teal-400);
            background: #f0fdfa;
        }

        .rhythm-card-btn.active {
            background: #e6fffa;
            border-color: var(--teal-600);
            box-shadow: 0 0 0 1px var(--teal-600);
        }

        .rhythm-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--slate-900);
        }

        .rhythm-card-btn.active .rhythm-title {
            color: var(--teal-900);
        }

        .rhythm-subtitle {
            font-size: 11px;
            color: var(--slate-500);
            font-weight: 500;
        }

        .rhythm-card-btn.active .rhythm-subtitle {
            color: var(--teal-700);
        }

        /* Action Buttons Row */
        .breath-actions-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .btn-start-breath {
            background: var(--teal-800);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-lg);
            padding: 11px 22px;
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 2px 8px rgba(15, 118, 110, 0.25);
        }

        .btn-start-breath:hover {
            background: var(--teal-900);
            transform: translateY(-1px);
        }

        .btn-reset-breath {
            background: #ffffff;
            border: 1.5px solid var(--slate-300);
            color: var(--slate-700);
            border-radius: var(--radius-lg);
            padding: 11px 18px;
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-reset-breath:hover {
            background: #f1f5f9;
            color: var(--slate-900);
        }

        .breath-toggle-zen {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-600);
            cursor: pointer;
            margin-left: 6px;
            user-select: none;
        }

        .breath-toggle-zen input[type="checkbox"] {
            accent-color: var(--teal-700);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* ========================================================
           MIDDLE SECTION: TERAKHIR DIDENGARKAN & KOLEKSI FAVORIT
           ======================================================== */
        .med-middle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 900px) {
            .med-middle-grid {
                grid-template-columns: 1fr;
            }
        }

        .med-subcol {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .med-subcol-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .med-subcol-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-900);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .med-subcol-link {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--teal-700);
            text-decoration: none;
            transition: color 0.15s;
        }

        .med-subcol-link:hover {
            color: var(--teal-900);
            text-decoration: underline;
        }

        .med-subcol-count {
            font-size: 12px;
            color: var(--slate-500);
            font-weight: 600;
        }

        /* Recent Cards */
        .recent-audio-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.02);
            transition: all 0.15s ease;
        }

        .recent-audio-card:hover {
            border-color: var(--teal-400);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        }

        .recent-audio-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .recent-thumb {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .recent-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .recent-category {
            font-size: 10px;
            font-weight: 700;
            color: var(--teal-700);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .recent-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .recent-meta {
            font-size: 11.5px;
            color: var(--slate-500);
        }

        .recent-audio-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .recent-badge-progress {
            background: #f1f5f9;
            color: var(--slate-600);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: var(--radius-full);
        }

        .btn-mini-play {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            color: var(--teal-800);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-mini-play:hover {
            background: var(--teal-800);
            border-color: var(--teal-800);
            color: #ffffff;
            transform: scale(1.05);
        }

        /* Favorite Items */
        .favorite-audio-item {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: all 0.15s ease;
        }

        .favorite-audio-item:hover {
            border-color: var(--teal-400);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        }

        .fav-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .fav-icon-box {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fav-icon-nature { background: #dcfce7; color: #15803d; }
        .fav-icon-moon { background: #e0f2fe; color: #0369a1; }
        .fav-icon-energy { background: #f3e8ff; color: #7e22ce; }

        .fav-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .fav-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fav-meta {
            font-size: 11.5px;
            color: var(--slate-500);
        }

        /* ========================================================
           CATALOG SECTION: AUDIO CARDS
           ======================================================== */
        .med-catalog-section {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .catalog-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .catalog-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--slate-900);
        }

        .catalog-count-badge {
            font-size: 12.5px;
            color: var(--slate-500);
            font-weight: 600;
        }

        /* Filter Pills */
        .catalog-pills-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .catalog-pill {
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-full);
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .catalog-pill:hover {
            background: #f1f5f9;
            color: var(--slate-900);
        }

        .catalog-pill.active {
            background: var(--teal-900);
            border-color: var(--teal-900);
            color: #ffffff;
        }

        /* 3-Column Catalog Grid */
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .catalog-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .catalog-grid {
                grid-template-columns: 1fr;
            }
        }

        .med-audio-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .med-audio-card:hover {
            transform: translateY(-3px);
            border-color: var(--teal-400);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        .card-cover-wrap {
            position: relative;
            width: 100%;
            height: 180px;
            background: #0f172a;
            overflow: hidden;
        }

        .card-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .med-audio-card:hover .card-cover-img {
            transform: scale(1.05);
        }

        .card-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.65) 100%);
        }

        .card-top-badges {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge-card-cat {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            letter-spacing: 0.3px;
        }

        .btn-card-bookmark {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            border: none;
            color: var(--slate-700);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-card-bookmark:hover {
            background: #ffffff;
            color: var(--teal-700);
            transform: scale(1.1);
        }

        .btn-card-bookmark.bookmarked {
            background: var(--teal-600);
            color: #ffffff;
        }

        .card-bottom-stats {
            position: absolute;
            bottom: 10px;
            left: 12px;
            font-size: 11.5px;
            color: #ffffff;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.7);
        }

        .card-body {
            padding: 18px 20px 20px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1;
        }

        .card-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.35;
        }

        .card-desc {
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-footer {
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .author-box {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .author-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--slate-700);
            font-size: 10.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .author-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-card-listen {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: var(--teal-800);
            border-radius: var(--radius-md);
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .btn-card-listen:hover {
            background: var(--teal-800);
            color: #ffffff;
            border-color: var(--teal-800);
        }

        /* ========================================================
           FLOATING STICKY PLAYER DOCK (BOTTOM)
           ======================================================== */
        .med-player-dock {
            position: fixed;
            bottom: 20px;
            left: calc(260px + (100% - 260px) / 2);
            transform: translateX(-50%);
            width: min(940px, calc(100% - 300px));
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            padding: 12px 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            z-index: 100;
            transition: all 0.25s ease;
        }

        @media (max-width: 1024px) {
            .med-player-dock {
                left: 50%;
                width: calc(100% - 40px);
            }
        }

        @media (max-width: 768px) {
            .med-player-dock {
                flex-direction: column;
                gap: 10px;
                padding: 12px 16px;
                bottom: 12px;
            }
        }

        /* Player Left Info */
        .player-track-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 220px;
            max-width: 260px;
        }

        .player-track-art {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--teal-800);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(15, 118, 110, 0.3);
        }

        .player-text-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .player-track-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--slate-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-track-sub {
            font-size: 11px;
            color: var(--slate-500);
            font-weight: 500;
        }

        /* Player Center Controls & Progress */
        .player-center-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex: 1;
            max-width: 440px;
        }

        .player-controls-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-player-skip {
            background: none;
            border: none;
            color: var(--slate-600);
            cursor: pointer;
            padding: 4px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 2px;
            transition: color 0.15s;
        }

        .btn-player-skip:hover {
            color: var(--teal-800);
        }

        .btn-player-main-play {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--teal-800);
            border: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(15, 118, 110, 0.3);
            transition: all 0.15s ease;
        }

        .btn-player-main-play:hover {
            background: var(--teal-900);
            transform: scale(1.08);
        }

        .player-scrubber-row {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .player-time {
            font-size: 11px;
            font-weight: 700;
            color: var(--slate-500);
            font-variant-numeric: tabular-nums;
            min-width: 34px;
        }

        .player-slider {
            flex: 1;
            height: 5px;
            border-radius: 5px;
            accent-color: var(--teal-700);
            cursor: pointer;
            background: var(--slate-200);
        }

        /* Player Right Ambient & Volume */
        .player-right-block {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .btn-ambient-toggle {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: var(--teal-800);
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: var(--radius-full);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
        }

        .btn-ambient-toggle.active {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .btn-speed-toggle {
            background: #f1f5f9;
            border: none;
            color: var(--slate-700);
            font-size: 11.5px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-speed-toggle:hover {
            background: #e2e8f0;
        }

        .volume-control-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .volume-slider {
            width: 70px;
            height: 4px;
            accent-color: var(--teal-700);
            cursor: pointer;
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
            <input type="text" class="app-search-input" placeholder="Cari materi meditasi, nama psikolog, artikel..." aria-label="Cari fitur atau artikel">
        </div>

        <div class="app-header-actions">
            <!-- Crisis Button -->
            <a href="emergency.php" class="btn-crisis-header" title="Bantuan Krisis 119">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span class="crisis-label-full">Bantuan Krisis 119</span>
                <span class="crisis-label-short">119</span>
            </a>

            <!-- Notification Bell -->
            <button class="header-icon-btn" title="Notifikasi" onclick="alert('Belum ada notifikasi baru.')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span class="badge-dot"></span>
            </button>

            <!-- User Menu Profile -->
            <a href="profile.php" class="header-user-pill" title="Profil <?= $fullDisplayName ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= $fullDisplayName ?></div>
                    <div class="header-user-role">Member Aktif</div>
                </div>
            </a>
        </div>
    </header>

    <!-- MAIN BODY LAYOUT -->
    <div class="app-body-layout">
        
        <!-- SIDEBAR NAVIGATION -->
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

            <div class="sidebar-top-status">
                <span class="status-label">Status Kehadiran</span>
                <span class="status-online"><span class="status-dot"></span> Online</span>
            </div>

            <div class="sidebar-nav-scroll">
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item">
                        <a href="dashboard.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="self-check.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            <span>Self Check</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="mood-tracker.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                            <span>Mood Tracker</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item active">
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
                        <a href="profile.php">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Profil &amp; Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bottom user summary card -->
            <div class="sidebar-user-card">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name"><?= $fullDisplayName ?></span>
                    <span class="sidebar-user-sub">Mahasiswi &amp; Anggota MindCare</span>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="dashboard-content-scroll">
            <div class="med-container">

                <!-- TOP HEADER -->
                <div class="med-header-row">
                    <div class="med-header-text">
                        <span class="med-badge-category">
                            🌿 Pusat Ketenangan &amp; Pemulihan Jiwa
                        </span>
                        <h1 class="med-title">Meditasi, Pernapasan &amp; Audio Relaksasi</h1>
                        <p class="med-subtitle">
                            Luangkan waktu sejenak untuk melatih kesadaran penuh, menenangkan sistem saraf parasimpatis, dan menemukan hening batin di tengah padatnya rutinitas harianmu.
                        </p>
                    </div>

                    <!-- Weekly Stat Card -->
                    <div class="med-stat-card">
                        <div class="med-stat-icon-wrap" title="Meditasi">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="2.5"></circle><path d="M12 11c-2.5 0-5 1.5-5 4v2h10v-2c0-2.5-2.5-4-5-4z"></path><path d="M5 19c2 1 4 1.5 7 1.5s5-.5 7-1.5"></path></svg>
                        </div>
                        <div class="med-stat-group">
                            <span class="med-stat-label">Rutinitas Mingguan</span>
                            <span class="med-stat-val">4 Sesi <span>/ Target 5</span></span>
                        </div>
                        <div class="med-stat-divider"></div>
                        <div class="med-stat-group">
                            <span class="med-stat-label">Total Relaksasi</span>
                            <span class="med-stat-val">72 <span>Menit</span></span>
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     HERO: LATIHAN SOMATIK & BOX BREATHING WIDGET
                     ======================================================== -->
                <section class="breath-hero-card" id="breathingSection">
                    
                    <!-- Left: Breathing Circle Guide -->
                    <div class="breath-visual-col">
                        <div class="breath-dial-wrapper">
                            <span class="breath-marker marker-top" id="markerInhale">Tarik</span>
                            <span class="breath-marker marker-right" id="markerHold1">Tahan</span>
                            <span class="breath-marker marker-bottom" id="markerExhale">Hembus</span>
                            <span class="breath-marker marker-left" id="markerHold2">Jeda</span>

                            <!-- Outer Track & Glow Ring -->
                            <div class="breath-ring-track"></div>
                            <div class="breath-ring-fill" id="breathRingFill"></div>

                            <!-- Animated Pulsing Center -->
                            <div class="breath-core-circle" id="breathCircleCore" onclick="toggleBreathing()">
                                <span class="breath-status-label" id="breathPhaseLabel">Siap Mulai</span>
                                <span class="breath-counter-num" id="breathCountdown">4</span>
                                <span class="breath-instruction-text" id="breathSubLabel">Tarik Napas Perlahan</span>
                            </div>
                        </div>

                        <!-- Cycle Tracker -->
                        <div class="breath-cycle-indicator">
                            <span class="breath-cycle-dot"></span>
                            <span id="cycleCounterText">Siklus: 0 / 4 Selesai</span>
                        </div>
                    </div>

                    <!-- Right: Info & Controls -->
                    <div class="breath-detail-col">
                        <div class="breath-tags-row">
                            <span class="tag-somatic-active">Latihan Somatik Aktif</span>
                            <span class="tag-vagus-desc">Metode Relaksasi Teruji • Regulasi Saraf Vagus</span>
                        </div>

                        <h2 class="breath-exercise-title" id="exerciseTitle">Latihan Pernapasan Kotak (Box Breathing 4–4–4–4)</h2>
                        <p class="breath-exercise-desc" id="exerciseDesc">
                            Menenangkan ritme detak jantung dan mengembalikan kejernihan fokus dalam hitungan menit lewat panduan napas yang stabil dan berirama seimbang.
                        </p>

                        <!-- Rhythm Options -->
                        <span class="breath-section-label">Pilih Pola Ritme</span>
                        <div class="rhythm-options-grid">
                            <button type="button" class="rhythm-card-btn active" id="btnRhythmBox" onclick="selectRhythm('box')">
                                <span class="rhythm-title">Box 4–4–4–4</span>
                                <span class="rhythm-subtitle">Fokus &amp; Regulasi Stres</span>
                            </button>
                            <button type="button" class="rhythm-card-btn" id="btnRhythmSleep" onclick="selectRhythm('sleep')">
                                <span class="rhythm-title">Pola 4–7–8</span>
                                <span class="rhythm-subtitle">Pelepas Tidur Alami</span>
                            </button>
                            <button type="button" class="rhythm-card-btn" id="btnRhythmPanic" onclick="selectRhythm('panic')">
                                <span class="rhythm-title">Pursed-Lip 2–4</span>
                                <span class="rhythm-subtitle">Pelega Sesak Panik</span>
                            </button>
                        </div>

                        <!-- Action Controls -->
                        <div class="breath-actions-row">
                            <button type="button" class="btn-start-breath" id="btnToggleBreath" onclick="toggleBreathing()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                <span id="btnToggleBreathText">Mulai Latihan</span>
                            </button>

                            <button type="button" class="btn-reset-breath" onclick="resetBreathing()">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><polyline points="3 3 3 8 8 8"></polyline></svg>
                                <span>Reset</span>
                            </button>

                            <label class="breath-toggle-zen">
                                <input type="checkbox" id="zenChimeToggle" checked>
                                <span>Panduan Getar &amp; Lonceng Zen</span>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- ========================================================
                     MIDDLE ROW: TERAKHIR DIDENGARKAN & KOLEKSI FAVORIT
                     ======================================================== -->
                <div class="med-middle-grid">
                    
                    <!-- Left: Terakhir Didengarkan -->
                    <div class="med-subcol">
                        <div class="med-subcol-header">
                            <span class="med-subcol-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Terakhir Didengarkan
                            </span>
                            <a href="javascript:void(0)" class="med-subcol-link" onclick="alert('Riwayat pemutaran tersimpan 30 hari terakhir.')">Lihat Riwayat</a>
                        </div>

                        <!-- Card 1 -->
                        <div class="recent-audio-card">
                            <div class="recent-audio-left">
                                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=200&q=80" alt="Pelepas Beban Pikiran" class="recent-thumb">
                                <div class="recent-info">
                                    <span class="recent-category">Meditasi Tidur Terpandu</span>
                                    <span class="recent-title" title="Pelepas Beban Pikiran & Ketenangan Jiwa">Pelepas Beban Pikiran &amp; Ketenangan...</span>
                                    <span class="recent-meta">Rian Ardiansyah • Terakhir 12:45 / 20:00</span>
                                </div>
                            </div>
                            <div class="recent-audio-right">
                                <span class="recent-badge-progress">65% Selesai</span>
                                <button type="button" class="btn-mini-play" title="Lanjutkan Mendengarkan" onclick="loadTrack('Pelepas Beban Pikiran & Ketenangan Jiwa', 'Praktik Mindful • 20 Menit', 765, 1200)">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="recent-audio-card">
                            <div class="recent-audio-left">
                                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=200&q=80" alt="Mengurai Burnout Akademik" class="recent-thumb">
                                <div class="recent-info">
                                    <span class="recent-category">Latihan Fokus &amp; Burnout</span>
                                    <span class="recent-title" title="Mengurai Burnout Akademik & Kerja">Mengurai Burnout Akademik &amp; Kerja</span>
                                    <span class="recent-meta">Dra. Indira Wardhani, M.Psi • Terakhir 05:10 / 15:00</span>
                                </div>
                            </div>
                            <div class="recent-audio-right">
                                <span class="recent-badge-progress">35% Selesai</span>
                                <button type="button" class="btn-mini-play" title="Lanjutkan Mendengarkan" onclick="loadTrack('Mengurai Burnout Akademik & Kerja', 'Latihan Fokus • 15 Menit', 310, 900)">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Koleksi Favorit -->
                    <div class="med-subcol">
                        <div class="med-subcol-header">
                            <span class="med-subcol-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                Koleksi Favorit
                            </span>
                            <span class="med-subcol-count">4 Tersimpan</span>
                        </div>

                        <!-- Fav 1: Pine Rain -->
                        <div class="favorite-audio-item">
                            <div class="fav-left">
                                <div class="fav-icon-box fav-icon-nature">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 19h8a4 4 0 0 0 3.8-2.8 4 4 0 0 0-1.6-4.5A5 5 0 0 0 6 10a5 5 0 0 0 .5 2.1A4 4 0 0 0 8 19z"></path><path d="M12 19v3"></path></svg>
                                </div>
                                <div class="fav-info">
                                    <span class="fav-title">Suara Hujan Hutan Pinus Cibodas</span>
                                    <span class="fav-meta">45 Menit • Soundscape Alam</span>
                                </div>
                            </div>
                            <button type="button" class="btn-mini-play" title="Putar Sekarang" onclick="loadTrack('Suara Hujan Hutan Pinus Cibodas', 'Soundscape Alam • 45 Menit', 0, 2700); toggleAmbientSound(true);">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </button>
                        </div>

                        <!-- Fav 2: Hening Menjelang Tidur -->
                        <div class="favorite-audio-item">
                            <div class="fav-left">
                                <div class="fav-icon-box fav-icon-moon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                                </div>
                                <div class="fav-info">
                                    <span class="fav-title">Hening Menjelang Tidur Nusantara</span>
                                    <span class="fav-meta">25 Menit • Sleep Story</span>
                                </div>
                            </div>
                            <button type="button" class="btn-mini-play" title="Putar Sekarang" onclick="loadTrack('Hening Menjelang Tidur Nusantara', 'Sleep Story • 25 Menit', 0, 1500)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </button>
                        </div>

                        <!-- Fav 3: Grounding 5-4-3-2-1 -->
                        <div class="favorite-audio-item">
                            <div class="fav-left">
                                <div class="fav-icon-box fav-icon-energy">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                </div>
                                <div class="fav-info">
                                    <span class="fav-title">Grounding 5–4–3–2–1 Meredam Panik</span>
                                    <span class="fav-meta">6 Menit • Respons Cepat</span>
                                </div>
                            </div>
                            <button type="button" class="btn-mini-play" title="Putar Sekarang" onclick="loadTrack('Grounding 5–4–3–2–1 untuk Meredam Panik', 'Respons Cepat • 6 Menit', 0, 360)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     KATALOG SESI RELAKSASI (AUDIO MODULES)
                     ======================================================== -->
                <section class="med-catalog-section">
                    <div class="catalog-header-row">
                        <h2 class="catalog-title">Katalog Sesi Relaksasi</h2>
                        <span class="catalog-count-badge" id="catalogCountText">Menampilkan 6 Modul Panduan</span>
                    </div>

                    <!-- Category Pills -->
                    <div class="catalog-pills-row">
                        <button type="button" class="catalog-pill active" onclick="filterCatalog('all', this)">Semua</button>
                        <button type="button" class="catalog-pill" onclick="filterCatalog('pernapasan', this)">Latihan Pernapasan</button>
                        <button type="button" class="catalog-pill" onclick="filterCatalog('mandiri', this)">Relaksasi Mandiri</button>
                        <button type="button" class="catalog-pill" onclick="filterCatalog('tidur', this)">Meditasi Tidur</button>
                        <button type="button" class="catalog-pill" onclick="filterCatalog('stres', this)">Redakan Stres &amp; Kecemasan</button>
                        <button type="button" class="catalog-pill" onclick="filterCatalog('nature', this)">Musik Ambient &amp; Bunyi Alam</button>
                    </div>

                    <!-- Grid of 6 Cards -->
                    <div class="catalog-grid" id="catalogGrid">
                        
                        <!-- Card 1: Hening Menjelang Tidur -->
                        <article class="med-audio-card" data-cat="tidur mandiri">
                            <div class="card-cover-wrap">
                                <img src="https://images.unsplash.com/photo-1518495973542-4542c06a5843?auto=format&fit=crop&w=600&q=80" alt="Hening Menjelang Tidur" class="card-cover-img">
                                <div class="card-cover-overlay"></div>
                                <div class="card-top-badges">
                                    <span class="badge-card-cat">Audio Terpandu</span>
                                    <button type="button" class="btn-card-bookmark" title="Simpan ke Favorit" onclick="toggleCardBookmark(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="card-bottom-stats">
                                    <span>⏱ 25 Menit</span>
                                    <span>•</span>
                                    <span>👥 18.4k Didengar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">Hening Menjelang Tidur (Sleep Story Nusantara)</h3>
                                <p class="card-desc">
                                    Kisah narasi lembut menyusuri ketenangan lembah pegunungan untuk melepaskan beban pikiran dan menghantarkan tidur lelap.
                                </p>
                                <div class="card-footer">
                                    <div class="author-box">
                                        <div class="author-avatar">RA</div>
                                        <span class="author-name">Rian Ardiansyah</span>
                                    </div>
                                    <button type="button" class="btn-card-listen" onclick="loadTrack('Hening Menjelang Tidur (Sleep Story Nusantara)', 'Audio Terpandu • 25 Menit', 0, 1500)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                                        <span>Mulai Dengar</span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 2: Mengurai Burnout Akademik & Kerja -->
                        <article class="med-audio-card" data-cat="stres mandiri">
                            <div class="card-cover-wrap">
                                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=600&q=80" alt="Mengurai Burnout" class="card-cover-img">
                                <div class="card-cover-overlay"></div>
                                <div class="card-top-badges">
                                    <span class="badge-card-cat">Latihan Fokus</span>
                                    <button type="button" class="btn-card-bookmark" title="Simpan ke Favorit" onclick="toggleCardBookmark(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="card-bottom-stats">
                                    <span>⏱ 12 Menit</span>
                                    <span>•</span>
                                    <span>👥 12.2k Didengar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">Mengurai Burnout Akademik &amp; Kerja</h3>
                                <p class="card-desc">
                                    Lepaskan tekanan deadline yang menumpuk. Pelajari teknik pelepasan beban mental terstruktur untuk mengembalikan vitalitas.
                                </p>
                                <div class="card-footer">
                                    <div class="author-box">
                                        <div class="author-avatar" style="background:#fce7f3; color:#9d174d;">IW</div>
                                        <span class="author-name">Dra. Indira W., M.Psi</span>
                                    </div>
                                    <button type="button" class="btn-card-listen" onclick="loadTrack('Mengurai Burnout Akademik & Kerja', 'Latihan Fokus • 12 Menit', 0, 720)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                                        <span>Mulai Dengar</span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 3: Grounding 5-4-3-2-1 -->
                        <article class="med-audio-card" data-cat="stres pernapasan">
                            <div class="card-cover-wrap">
                                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80" alt="Grounding 5-4-3-2-1" class="card-cover-img">
                                <div class="card-cover-overlay"></div>
                                <div class="card-top-badges">
                                    <span class="badge-card-cat" style="background:rgba(225, 29, 72, 0.75);">Respons Cepat</span>
                                    <button type="button" class="btn-card-bookmark" title="Simpan ke Favorit" onclick="toggleCardBookmark(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="card-bottom-stats">
                                    <span>⏱ 6 Menit</span>
                                    <span>•</span>
                                    <span>👥 31.9k Didengar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">Grounding 5–4–3–2–1 untuk Meredam Panik</h3>
                                <p class="card-desc">
                                    Protokol darurat sensorik saat serangan kecemasan melanda untuk menenangkan denyut nadi dan kembali berpijak pada saat ini.
                                </p>
                                <div class="card-footer">
                                    <div class="author-box">
                                        <div class="author-avatar" style="background:#fee2e2; color:#b91c1c;">KP</div>
                                        <span class="author-name">Klinik MindCare</span>
                                    </div>
                                    <button type="button" class="btn-card-listen" onclick="loadTrack('Grounding 5–4–3–2–1 untuk Meredam Panik', 'Respons Cepat • 6 Menit', 0, 360)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                                        <span>Mulai Dengar</span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 4: Suara Hujan Hutan Pinus Cibodas -->
                        <article class="med-audio-card" data-cat="nature tidur">
                            <div class="card-cover-wrap">
                                <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=600&q=80" alt="Suara Hujan Hutan Pinus" class="card-cover-img">
                                <div class="card-cover-overlay"></div>
                                <div class="card-top-badges">
                                    <span class="badge-card-cat" style="background:rgba(13, 148, 136, 0.75);">Soundscape Alam</span>
                                    <button type="button" class="btn-card-bookmark" title="Simpan ke Favorit" onclick="toggleCardBookmark(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="card-bottom-stats">
                                    <span>⏱ 45 Menit</span>
                                    <span>•</span>
                                    <span>👥 42.1k Didengar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">Suara Hujan Tropis di Hutan Pinus Cibodas</h3>
                                <p class="card-desc">
                                    Rekaman audio binaural 3D rintik air hujan membasahi pucuk pohon pinus yang menenangkan detak jantung dan riuh kepala.
                                </p>
                                <div class="card-footer">
                                    <div class="author-box">
                                        <div class="author-avatar" style="background:#ecfccb; color:#4d7c0f;">NL</div>
                                        <span class="author-name">Nature Lab ID</span>
                                    </div>
                                    <button type="button" class="btn-card-listen" onclick="loadTrack('Suara Hujan Tropis di Hutan Pinus Cibodas', 'Soundscape Alam • 45 Menit', 0, 2700); toggleAmbientSound(true);">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                                        <span>Mulai Dengar</span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 5: Mindful Eating & Menikmati Hari -->
                        <article class="med-audio-card" data-cat="mandiri">
                            <div class="card-cover-wrap">
                                <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=600&q=80" alt="Mindful Eating" class="card-cover-img">
                                <div class="card-cover-overlay"></div>
                                <div class="card-top-badges">
                                    <span class="badge-card-cat">Praktik Harian</span>
                                    <button type="button" class="btn-card-bookmark" title="Simpan ke Favorit" onclick="toggleCardBookmark(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="card-bottom-stats">
                                    <span>⏱ 10 Menit</span>
                                    <span>•</span>
                                    <span>👥 9.5k Didengar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">Mindful Eating &amp; Menikmati Hari</h3>
                                <p class="card-desc">
                                    Panduan makan dengan kesadaran penuh, merasakan setiap tekstur, aroma, dan rasa secara utuh tanpa distraksi layar.
                                </p>
                                <div class="card-footer">
                                    <div class="author-box">
                                        <div class="author-avatar" style="background:#e0e7ff; color:#4338ca;">SM</div>
                                        <span class="author-name">Sari Mutia, C.Ht</span>
                                    </div>
                                    <button type="button" class="btn-card-listen" onclick="loadTrack('Mindful Eating & Menikmati Hari', 'Praktik Harian • 10 Menit', 0, 600)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                                        <span>Mulai Dengar</span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 6: Body Scan Relaksasi Ketegangan Otot -->
                        <article class="med-audio-card" data-cat="pernapasan mandiri tidur">
                            <div class="card-cover-wrap">
                                <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80" alt="Body Scan Relaksasi" class="card-cover-img">
                                <div class="card-cover-overlay"></div>
                                <div class="card-top-badges">
                                    <span class="badge-card-cat" style="background:rgba(99, 102, 241, 0.75);">Somatik Tubuh</span>
                                    <button type="button" class="btn-card-bookmark" title="Simpan ke Favorit" onclick="toggleCardBookmark(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="card-bottom-stats">
                                    <span>⏱ 15 Menit</span>
                                    <span>•</span>
                                    <span>👥 27.6k Didengar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">Body Scan Relaksasi Ketegangan Otot</h3>
                                <p class="card-desc">
                                    Mengenali dan melepaskan titik-titik ketegangan kronis pada leher, bahu, dan punggung secara bertahap dengan napas sadar.
                                </p>
                                <div class="card-footer">
                                    <div class="author-box">
                                        <div class="author-avatar">RA</div>
                                        <span class="author-name">Rian Ardiansyah</span>
                                    </div>
                                    <button type="button" class="btn-card-listen" onclick="loadTrack('Body Scan Relaksasi Ketegangan Otot', 'Somatik Tubuh • 15 Menit', 0, 900)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                                        <span>Mulai Dengar</span>
                                    </button>
                                </div>
                            </div>
                        </article>

                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- ========================================================
         STICKY BOTTOM AUDIO PLAYER BAR
         ======================================================== -->
    <div class="med-player-dock" id="medPlayerDock">
        
        <!-- Track Info -->
        <div class="player-track-info">
            <div class="player-track-art">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
            </div>
            <div class="player-text-details">
                <span class="player-track-name" id="playerTrackTitle">Pelepas Beban Pikiran &amp; Ketenangan Jiwa</span>
                <span class="player-track-sub" id="playerTrackSub">Praktik Mindful • 20 Menit</span>
            </div>
        </div>

        <!-- Center Controls & Scrubber -->
        <div class="player-center-block">
            <div class="player-controls-row">
                <button type="button" class="btn-player-skip" title="Mundur 15 Detik" onclick="seekRelative(-15)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    <span>15</span>
                </button>

                <button type="button" class="btn-player-main-play" id="btnMainPlayPause" title="Putar / Jeda" onclick="togglePlayerPlayback()">
                    <svg id="mainPlayIcon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <svg id="mainPauseIcon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="display:none;"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                </button>

                <button type="button" class="btn-player-skip" title="Maju 15 Detik" onclick="seekRelative(15)">
                    <span>15</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                </button>
            </div>

            <div class="player-scrubber-row">
                <span class="player-time" id="currentTimeLabel">12:45</span>
                <input type="range" class="player-slider" id="playerTimeline" min="0" max="1200" value="765" oninput="onTimelineScrub(this.value)">
                <span class="player-time" id="totalTimeLabel">20:00</span>
            </div>
        </div>

        <!-- Right Controls: Ambient Rain, Speed, Volume -->
        <div class="player-right-block">
            <button type="button" class="btn-ambient-toggle active" id="btnAmbientRain" onclick="toggleAmbientSound()">
                <span>🌧️ Suara Hujan: Aktif</span>
            </button>

            <button type="button" class="btn-speed-toggle" id="btnPlaybackSpeed" onclick="cyclePlaybackSpeed()">1.0x</button>

            <div class="volume-control-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                <input type="range" class="volume-slider" id="playerVolume" min="0" max="1" step="0.05" value="0.8" oninput="setPlayerVolume(this.value)">
            </div>
        </div>
    </div>

    <script src="script.js?v=<?= time() ?>"></script>
    <!-- JAVASCRIPT LOGIC -->
    <script>
        /* ========================================================
           1. WEB AUDIO SYNTHESIZER (Zen Chimes & Ambient Rain)
           ======================================================== */
        let audioCtx = null;
        let rainSource = null;
        let rainGain = null;
        let isRainPlaying = true;
        let ambientGainVal = 0.35;

        function getAudioContext() {
            if (!audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                audioCtx = new AudioContext();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        // Gentle Zen Sing Bowl Chime
        function playZenChime(freq = 528) {
            const zenEnabled = document.getElementById('zenChimeToggle')?.checked;
            if (!zenEnabled) return;
            try {
                const ctx = getAudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                // slight overtone
                osc.frequency.exponentialRampToValueAtTime(freq * 0.99, ctx.currentTime + 2.5);

                gain.gain.setValueAtTime(0.001, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.35, ctx.currentTime + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 3.0);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 3.0);
            } catch (e) {
                console.log('Audio init on user gesture:', e);
            }
        }

        // Ambient Rain Synthesizer (White noise with lowpass filters)
        function startRainSynth() {
            try {
                const ctx = getAudioContext();
                if (rainSource) return;

                const bufferSize = ctx.sampleRate * 2;
                const noiseBuffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
                const output = noiseBuffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) {
                    output[i] = Math.random() * 2 - 1;
                }

                const whiteNoise = ctx.createBufferSource();
                whiteNoise.buffer = noiseBuffer;
                whiteNoise.loop = true;

                // Low-pass filter to sound like soft rainfall
                const filter = ctx.createBiquadFilter();
                filter.type = 'lowpass';
                filter.frequency.setValueAtTime(800, ctx.currentTime);

                rainGain = ctx.createGain();
                rainGain.gain.setValueAtTime(ambientGainVal, ctx.currentTime);

                whiteNoise.connect(filter);
                filter.connect(rainGain);
                rainGain.connect(ctx.destination);

                whiteNoise.start();
                rainSource = whiteNoise;
            } catch (e) {
                console.log('Rain synth error:', e);
            }
        }

        function stopRainSynth() {
            if (rainSource) {
                try {
                    rainSource.stop();
                    rainSource.disconnect();
                } catch(e) {}
                rainSource = null;
            }
        }

        function toggleAmbientSound(forceState = null) {
            if (forceState !== null) {
                isRainPlaying = forceState;
            } else {
                isRainPlaying = !isRainPlaying;
            }

            const btn = document.getElementById('btnAmbientRain');
            if (isRainPlaying) {
                btn.classList.add('active');
                btn.innerHTML = '<span>🌧️ Suara Hujan: Aktif</span>';
                startRainSynth();
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '<span>🌧️ Suara Hujan: Senyap</span>';
                stopRainSynth();
            }
        }

        /* ========================================================
           2. INTERACTIVE BREATHING ENGINE (Box, 4-7-8, Pursed-Lip)
           ======================================================== */
        const RHYTHMS = {
            box: {
                name: 'Latihan Pernapasan Kotak (Box Breathing 4–4–4–4)',
                desc: 'Menenangkan ritme detak jantung dan mengembalikan fokus pikiran dalam hitungan menit lewat panduan napas yang stabil dan berirama seimbang.',
                phases: [
                    { name: 'Tarik Napas', sub: 'Tarik napas perlahan lewat hidung', duration: 4, type: 'inhale', marker: 'markerInhale' },
                    { name: 'Tahan Napas', sub: 'Tahan dengan paru-paru penuh', duration: 4, type: 'hold', marker: 'markerHold1' },
                    { name: 'Hembuskan', sub: 'Keluarkan napas perlahan lewat mulut', duration: 4, type: 'exhale', marker: 'markerExhale' },
                    { name: 'Jeda Tenang', sub: 'Istirahatkan dada dalam keadaan rileks', duration: 4, type: 'pause', marker: 'markerHold2' }
                ]
            },
            sleep: {
                name: 'Pernapasan Pengantar Tidur (Pola 4–7–8)',
                desc: 'Menurunkan frekuensi gelombang otak dan memicu relaksasi saraf parasimpatis terdalam untuk mengantarmu ke tidur pulas.',
                phases: [
                    { name: 'Tarik Napas', sub: 'Tarik lembut melalui hidung', duration: 4, type: 'inhale', marker: 'markerInhale' },
                    { name: 'Tahan Napas', sub: 'Tahan napas dengan tenang', duration: 7, type: 'hold', marker: 'markerHold1' },
                    { name: 'Hembuskan', sub: 'Hembuskan tuntas lewat bibir', duration: 8, type: 'exhale', marker: 'markerExhale' }
                ]
            },
            panic: {
                name: 'Pereda Panik Cepat (Pursed-Lip 2–4)',
                desc: 'Membuka saluran pernapasan saat sesak, menurunkan laju respirasi dan menghentikan respon panik hiperventilasi secara instan.',
                phases: [
                    { name: 'Tarik Napas', sub: 'Tarik napas santai 2 hitungan', duration: 2, type: 'inhale', marker: 'markerInhale' },
                    { name: 'Hembuskan', sub: 'Tiup napas panjang lewat bibir mengerucut', duration: 4, type: 'exhale', marker: 'markerExhale' }
                ]
            }
        };

        let currentRhythmKey = 'box';
        let breathInterval = null;
        let isBreathingActive = false;
        let currentPhaseIdx = 0;
        let currentSecondsLeft = 4;
        let completedCycles = 0;

        function selectRhythm(key) {
            if (isBreathingActive) {
                resetBreathing();
            }
            currentRhythmKey = key;
            const r = RHYTHMS[key];

            document.getElementById('exerciseTitle').textContent = r.name;
            document.getElementById('exerciseDesc').textContent = r.desc;

            document.getElementById('btnRhythmBox').classList.toggle('active', key === 'box');
            document.getElementById('btnRhythmSleep').classList.toggle('active', key === 'sleep');
            document.getElementById('btnRhythmPanic').classList.toggle('active', key === 'panic');

            // Hide/Show pause markers based on rhythm
            document.getElementById('markerHold2').style.display = key === 'box' ? 'block' : 'none';
            document.getElementById('markerHold1').style.display = (key === 'box' || key === 'sleep') ? 'block' : 'none';

            resetBreathing();
        }

        function toggleBreathing() {
            if (isBreathingActive) {
                pauseBreathing();
            } else {
                startBreathing();
            }
        }

        function startBreathing() {
            getAudioContext();
            isBreathingActive = true;
            document.getElementById('btnToggleBreathText').textContent = 'Jeda Latihan';
            document.getElementById('btnToggleBreath').style.background = '#0f766e';

            // Start soundscape if user likes
            if (isRainPlaying && !rainSource) {
                startRainSynth();
            }

            if (currentSecondsLeft <= 0) {
                advancePhase();
            } else {
                applyPhaseUI();
            }

            if (!breathInterval) {
                breathInterval = setInterval(tickBreathing, 1000);
            }
        }

        function pauseBreathing() {
            isBreathingActive = false;
            clearInterval(breathInterval);
            breathInterval = null;
            document.getElementById('btnToggleBreathText').textContent = 'Lanjutkan Latihan';
            document.getElementById('btnToggleBreath').style.background = 'var(--teal-800)';
            document.getElementById('breathPhaseLabel').textContent = 'Dijeda';
        }

        function resetBreathing() {
            pauseBreathing();
            currentPhaseIdx = 0;
            completedCycles = 0;
            const phases = RHYTHMS[currentRhythmKey].phases;
            currentSecondsLeft = phases[0].duration;

            document.getElementById('btnToggleBreathText').textContent = 'Mulai Latihan';
            document.getElementById('breathPhaseLabel').textContent = 'Siap Mulai';
            document.getElementById('breathCountdown').textContent = currentSecondsLeft;
            document.getElementById('breathSubLabel').textContent = phases[0].sub;
            document.getElementById('cycleCounterText').textContent = 'Siklus: 0 / 4 Selesai';

            const core = document.getElementById('breathCircleCore');
            core.className = 'breath-core-circle';

            document.querySelectorAll('.breath-marker').forEach(m => m.classList.remove('active'));
            document.getElementById('breathRingFill').style.transform = 'rotate(-45deg)';
        }

        function tickBreathing() {
            if (currentSecondsLeft > 1) {
                currentSecondsLeft--;
                document.getElementById('breathCountdown').textContent = currentSecondsLeft;
            } else {
                advancePhase();
            }
        }

        function advancePhase() {
            const phases = RHYTHMS[currentRhythmKey].phases;
            currentPhaseIdx = (currentPhaseIdx + 1) % phases.length;

            if (currentPhaseIdx === 0) {
                completedCycles++;
                document.getElementById('cycleCounterText').textContent = `Siklus: ${completedCycles} / 4 Selesai`;
                if (completedCycles === 4) {
                    playZenChime(660); // High chime upon completing target
                }
            }

            currentSecondsLeft = phases[currentPhaseIdx].duration;
            applyPhaseUI();
        }

        function applyPhaseUI() {
            const phase = RHYTHMS[currentRhythmKey].phases[currentPhaseIdx];
            const core = document.getElementById('breathCircleCore');

            core.className = `breath-core-circle ${phase.type}`;
            document.getElementById('breathPhaseLabel').textContent = phase.name;
            document.getElementById('breathCountdown').textContent = currentSecondsLeft;
            document.getElementById('breathSubLabel').textContent = phase.sub;

            // Highlight markers
            document.querySelectorAll('.breath-marker').forEach(m => m.classList.remove('active'));
            if (phase.marker) {
                document.getElementById(phase.marker)?.classList.add('active');
            }

            // Rotate ring indicator
            const rotations = {
                markerInhale: 'rotate(0deg)',
                markerHold1: 'rotate(90deg)',
                markerExhale: 'rotate(180deg)',
                markerHold2: 'rotate(270deg)'
            };
            if (phase.marker && rotations[phase.marker]) {
                document.getElementById('breathRingFill').style.transform = rotations[phase.marker];
            }

            // Play pleasant chime on phase changes
            if (phase.type === 'inhale') {
                playZenChime(432); // Grounding 432Hz
            } else if (phase.type === 'exhale') {
                playZenChime(396); // Releasing 396Hz
            }
        }

        /* ========================================================
           3. AUDIO PLAYER DOCK CONTROLLER
           ======================================================== */
        let isPlayerPlaying = false;
        let playerTimer = null;
        let currentTrackCurrentSec = 765;
        let currentTrackTotalSec = 1200;
        let playbackSpeeds = [1.0, 1.25, 1.5, 0.75];
        let currentSpeedIdx = 0;

        function formatSeconds(s) {
            const m = Math.floor(s / 60);
            const sec = Math.floor(s % 60);
            return `${m < 10 ? '0' : ''}${m}:${sec < 10 ? '0' : ''}${sec}`;
        }

        function loadTrack(title, subtitle, curSec = 0, totalSec = 1200) {
            document.getElementById('playerTrackTitle').textContent = title;
            document.getElementById('playerTrackSub').textContent = subtitle;

            currentTrackCurrentSec = curSec;
            currentTrackTotalSec = totalSec;

            document.getElementById('playerTimeline').max = totalSec;
            document.getElementById('playerTimeline').value = curSec;

            document.getElementById('currentTimeLabel').textContent = formatSeconds(curSec);
            document.getElementById('totalTimeLabel').textContent = formatSeconds(totalSec);

            startPlayerPlayback();
        }

        function togglePlayerPlayback() {
            if (isPlayerPlaying) {
                pausePlayerPlayback();
            } else {
                startPlayerPlayback();
            }
        }

        function startPlayerPlayback() {
            getAudioContext();
            isPlayerPlaying = true;
            document.getElementById('mainPlayIcon').style.display = 'none';
            document.getElementById('mainPauseIcon').style.display = 'block';

            if (isRainPlaying && !rainSource) {
                startRainSynth();
            }

            if (!playerTimer) {
                playerTimer = setInterval(() => {
                    if (currentTrackCurrentSec < currentTrackTotalSec) {
                        currentTrackCurrentSec += 1;
                        document.getElementById('playerTimeline').value = currentTrackCurrentSec;
                        document.getElementById('currentTimeLabel').textContent = formatSeconds(currentTrackCurrentSec);
                    } else {
                        pausePlayerPlayback();
                    }
                }, 1000 / playbackSpeeds[currentSpeedIdx]);
            }
        }

        function pausePlayerPlayback() {
            isPlayerPlaying = false;
            document.getElementById('mainPlayIcon').style.display = 'block';
            document.getElementById('mainPauseIcon').style.display = 'none';
            clearInterval(playerTimer);
            playerTimer = null;
        }

        function seekRelative(deltaSec) {
            currentTrackCurrentSec = Math.max(0, Math.min(currentTrackTotalSec, currentTrackCurrentSec + deltaSec));
            document.getElementById('playerTimeline').value = currentTrackCurrentSec;
            document.getElementById('currentTimeLabel').textContent = formatSeconds(currentTrackCurrentSec);
        }

        function onTimelineScrub(val) {
            currentTrackCurrentSec = parseInt(val, 10);
            document.getElementById('currentTimeLabel').textContent = formatSeconds(currentTrackCurrentSec);
        }

        function cyclePlaybackSpeed() {
            currentSpeedIdx = (currentSpeedIdx + 1) % playbackSpeeds.length;
            const spd = playbackSpeeds[currentSpeedIdx];
            document.getElementById('btnPlaybackSpeed').textContent = `${spd}x`;
            if (isPlayerPlaying) {
                pausePlayerPlayback();
                startPlayerPlayback();
            }
        }

        function setPlayerVolume(val) {
            ambientGainVal = parseFloat(val) * 0.4;
            if (rainGain && audioCtx) {
                rainGain.gain.setValueAtTime(ambientGainVal, audioCtx.currentTime);
            }
        }

        /* ========================================================
           4. CATALOG FILTERING & BOOKMARKS
           ======================================================== */
        function filterCatalog(category, btn) {
            document.querySelectorAll('.catalog-pill').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('#catalogGrid .med-audio-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cats = card.getAttribute('data-cat') || '';
                if (category === 'all' || cats.includes(category)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('catalogCountText').textContent = `Menampilkan ${visibleCount} Modul Panduan`;
        }

        function toggleCardBookmark(btn) {
            btn.classList.toggle('bookmarked');
            if (btn.classList.contains('bookmarked')) {
                btn.style.color = '#ffffff';
                alert('Audio berhasil disimpan ke Koleksi Favorit!');
            } else {
                btn.style.color = 'var(--slate-700)';
            }
        }

        // Auto-initialize background ambient audio upon first user gesture
        document.addEventListener('click', function initAudioOnce() {
            getAudioContext();
            document.removeEventListener('click', initAudioOnce);
        }, { once: true });
    </script>
</body>
</html>
