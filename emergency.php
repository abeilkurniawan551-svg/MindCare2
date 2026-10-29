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
    <title>Pusat Bantuan Darurat &amp; Krisis 24 Jam - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- Cache-busting link ke style.css -->
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    
    <!-- EMBEDDED STYLES: Memastikan CSS selalu ter-load 100% tanpa terhalang cache browser -->
    <style>
        /* Base page layout */
        .emergency-page-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* 1. Hero Emergency Banner */
        .emergency-hero-banner {
            background: linear-gradient(135deg, #fff1f2 0%, #fff7f7 100%) !important;
            border: 1px solid #fecdd3 !important;
            border-radius: 18px !important;
            padding: 24px 30px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 24px !important;
            box-shadow: 0 4px 20px -2px rgba(225, 29, 72, 0.08) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .emergency-hero-banner::after {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(244, 63, 94, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .emergency-hero-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 780px;
        }

        .emergency-tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #fee2e2;
            color: #be123c;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 9999px;
            align-self: flex-start;
        }

        .emergency-tag-pill .icon-asterisk {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            background-color: #e11d48;
            color: #ffffff;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 900;
        }

        .emergency-hero-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .emergency-hero-subtitle {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.5;
            margin: 0;
        }

        .emergency-btn-red {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 10px !important;
            background-color: #e11d48 !important;
            color: #ffffff !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            padding: 13px 24px !important;
            border-radius: 12px !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35) !important;
            transition: all 0.2s ease !important;
            flex-shrink: 0 !important;
        }

        .emergency-btn-red:hover {
            background-color: #be123c !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(225, 29, 72, 0.45) !important;
        }

        .emergency-btn-red svg {
            animation: pulse-phone 2s infinite ease-in-out;
        }

        @keyframes pulse-phone {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        /* 2. Three Hotlines Grid */
        .emergency-hotlines-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .hotline-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .hotline-card:hover {
            border-color: #5eead4;
            box-shadow: 0 6px 16px -2px rgba(13, 148, 136, 0.1);
            transform: translateY(-2px);
        }

        .hotline-badges-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .hotline-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .hotline-badge.teal {
            background-color: #f0fdfa;
            color: #115e59;
            border: 1px solid #99f6e4;
        }

        .hotline-badge.green {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .hotline-badge.rose {
            background-color: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .hotline-badge.purple {
            background-color: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .hotline-badge.amber {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .hotline-badge.slate {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .hotline-card-head {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 14px;
        }

        .hotline-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hotline-icon-box.teal {
            background-color: #f0fdfa;
            color: #0f766e;
            border: 1px solid #ccfbf1;
        }

        .hotline-icon-box.rose {
            background-color: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
        }

        .hotline-icon-box.emerald {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .hotline-card-info {
            display: flex;
            flex-direction: column;
        }

        .hotline-card-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 0;
        }

        .hotline-card-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Number Box */
        .hotline-number-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .hotline-number-meta {
            display: flex;
            flex-direction: column;
        }

        .hotline-number-lbl {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
        }

        .hotline-number-val {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin-top: 2px;
        }

        .hotline-copy-btn {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .hotline-copy-btn:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .hotline-desc-text {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.5;
            min-height: 38px;
            margin: 0;
        }

        .hotline-actions-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
        }

        .btn-hotline-primary {
            flex: 1;
            background-color: #0f766e;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
            border: none;
            cursor: pointer;
        }

        .btn-hotline-primary:hover {
            background-color: #115e59;
        }

        .btn-hotline-secondary {
            flex: 1;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .btn-hotline-secondary:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-hotline-wa {
            flex: 1;
            background-color: #22c55e;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .btn-hotline-wa:hover {
            background-color: #16a34a;
        }

        .btn-hotline-map {
            flex: 1;
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-hotline-map:hover {
            background-color: #f8fafc;
            border-color: #2dd4bf;
            color: #0f766e;
        }

        /* 3. Middle Section: Breathing & Panic Protocol */
        .emergency-two-cols {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            gap: 20px;
        }

        .emergency-action-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .section-pretitle {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0f766e;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-pretitle.rose {
            color: #e11d48;
        }

        .section-main-heading {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin: 4px 0 0 0;
        }

        .section-desc-light {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.5;
            margin: -6px 0 0 0;
        }

        /* Breathing Circle */
        .breathing-visual-box {
            background: linear-gradient(180deg, #f0fdfa 0%, #e6fffa 100%);
            border: 1px solid #ccfbf1;
            border-radius: 16px;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            text-align: center;
            min-height: 270px;
            position: relative;
            overflow: hidden;
        }

        .breathing-circle-outer {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(45, 212, 191, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: transform 0.6s ease-in-out;
        }

        .breathing-circle-inner {
            width: 104px;
            height: 104px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d9488, #0f766e);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(13, 148, 136, 0.35);
            transition: transform 1s ease-in-out;
        }

        .breathing-phase-text {
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .breathing-phase-count {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            margin-top: 2px;
        }

        .breathing-status-instruction {
            font-size: 13px;
            font-weight: 600;
            color: #042f2e;
            min-height: 20px;
        }

        .btn-breathing-control {
            background-color: #0f766e;
            color: #ffffff;
            border: none;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.25);
        }

        .btn-breathing-control:hover {
            background-color: #115e59;
            transform: translateY(-1px);
        }

        /* Grounding Steps List */
        .grounding-steps-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .grounding-step-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease;
        }

        .grounding-step-item:hover {
            background-color: #ffffff;
            border-color: #5eead4;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .grounding-step-num {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #0f766e;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .grounding-step-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .grounding-step-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .grounding-step-desc {
            font-size: 12px;
            color: #475569;
            line-height: 1.45;
        }

        /* 4. Peer Guide Section */
        .peer-guide-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .peer-two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .peer-box {
            border-radius: 12px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .peer-box.dos {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .peer-box.donts {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
        }

        .peer-box-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 800;
            margin: 0;
        }

        .peer-box.dos .peer-box-title {
            color: #15803d;
        }

        .peer-box.donts .peer-box-title {
            color: #be123c;
        }

        .peer-items-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .peer-item-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            line-height: 1.45;
        }

        .peer-item-row.do-item {
            color: #14532d;
        }

        .peer-item-row.dont-item {
            color: #881337;
        }

        .peer-icon-mark {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .peer-item-strong {
            font-weight: 700;
        }

        /* 5. Privacy Footer & Quick Exit */
        .emergency-privacy-footer {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .privacy-left-block {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .privacy-icon-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #f0fdfa;
            color: #0f766e;
            border: 1px solid #99f6e4;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .privacy-text-block {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .privacy-title-bold {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .privacy-desc-para {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        .btn-quick-exit {
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #334155;
            font-size: 12.5px;
            font-weight: 700;
            padding: 9px 16px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .btn-quick-exit:hover {
            background-color: #fee2e2;
            border-color: #fca5a5;
            color: #b91c1c;
        }

        .btn-quick-exit kbd {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 1px 5px;
            font-size: 10px;
            color: #475569;
        }

        /* Floating Copy Toast */
        .emergency-copy-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background-color: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25);
            z-index: 9999;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .emergency-copy-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .emergency-hotlines-grid,
            .emergency-two-cols,
            .peer-two-cols {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .emergency-hero-banner {
                flex-direction: column;
                align-items: flex-start;
            }
            .emergency-btn-red {
                width: 100%;
            }
            .emergency-privacy-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-quick-exit {
                width: 100%;
                justify-content: center;
            }
        }

        /* RS Psikiatri & Google Maps Radar Styling */
        .gps-pulse-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
        }
        .gps-pulse-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse-gps 1.5s infinite;
        }
        @keyframes pulse-gps {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .rs-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(6px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .rs-modal-overlay.active {
            display: flex;
            opacity: 1;
        }
        .rs-modal-container {
            background: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 860px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--slate-200);
            animation: modalSlideUp 0.3s ease-out;
        }
        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .rs-modal-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #042f2e 0%, #115e59 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .rs-modal-close-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #ffffff;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .rs-modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        .rs-modal-radar-banner {
            background: #fff1f2;
            border-bottom: 1px solid #fecdd3;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-live-gmaps-radar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e11d48;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(225, 29, 72, 0.25);
            transition: all 0.2s;
        }
        .btn-live-gmaps-radar:hover {
            background: #be123c;
            transform: translateY(-1px);
        }
        .rs-modal-controls {
            padding: 16px 24px;
            background: #f8fafc;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-gps-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0f766e;
            color: #ffffff;
            border: none;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-gps-trigger:hover {
            background: #115e59;
        }
        .rs-region-select {
            padding: 8px 14px;
            background: #ffffff;
            border: 1.5px solid var(--slate-300);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            outline: none;
            cursor: pointer;
        }
        .rs-modal-list {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: #f8fafc;
        }
        .rs-item-card {
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            transition: all 0.2s ease;
        }
        .rs-item-card:hover {
            border-color: #0d9488;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.1);
            transform: translateY(-2px);
        }
        .rs-item-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }
        .rs-item-header-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .rs-item-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-900);
        }
        .rs-distance-badge {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .rs-item-address {
            font-size: 12.5px;
            color: var(--slate-600);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .rs-item-phone {
            font-size: 12px;
            color: var(--slate-500);
        }
        .rs-item-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-rs-map {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0f766e;
            color: #ffffff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-rs-map:hover {
            background: #115e59;
            transform: translateY(-1px);
        }
        .btn-rs-call {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fee2e2;
            color: #be123c;
            border: 1px solid #fca5a5;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-rs-call:hover {
            background: #fecdd3;
        }
        @media (max-width: 640px) {
            .rs-item-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .rs-item-actions {
                width: 100%;
                justify-content: flex-start;
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
            <input type="text" class="app-search-input" placeholder="Cari materi meditasi, nama psikolog, artikel...">
        </div>

        <!-- Header Actions -->
        <div class="app-header-actions">
            <a href="emergency.php" class="btn-crisis-header" style="background:#fee2e2;border-color:#fca5a5;" title="Bantuan Krisis 119">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span class="crisis-label-full">Bantuan Krisis 119</span>
                <span class="crisis-label-short">119</span>
            </a>

            <button type="button" class="header-icon-btn" title="Notifikasi" onclick="alert('Tidak ada notifikasi darurat baru.');">
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
                        <a href="emergency.php" class="active">
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
                        <a href="login.php" style="color:#0f766e;font-weight:700;">
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
                    <span class="sidebar-user-sub"><?= htmlspecialchars($roleName) ?></span>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN CONTENT AREA -->
        <main class="dashboard-content-scroll">
            <div class="emergency-page-container">

                <!-- 1. HERO EMERGENCY BANNER -->
                <section class="emergency-hero-banner">
                    <div class="emergency-hero-content">
                        <div class="emergency-tag-pill">
                            <span class="icon-asterisk">★</span>
                            <span>SIAGA DARURAT 24 JAM • RAHASIA &amp; TERLINDUNGI</span>
                        </div>
                        <h1 class="emergency-hero-title">
                            Kamu Tidak Sendirian. Bantuan Segera Selalu Ada Untukmu.
                        </h1>
                        <p class="emergency-hero-subtitle">
                            Jika kamu atau orang terdekat sedang mengalami krisis emosional, tekanan hebat, atau kondisi darurat—segera hubungi layanan bebas pulsa di bawah. Siaga 24 jam.
                        </p>
                    </div>

                    <a href="tel:119,8" class="emergency-btn-red" id="heroCall119Btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <span>Panggil 119 Ext 8</span>
                    </a>
                </section>

                <!-- 2. THREE HOTLINES ROW -->
                <section class="emergency-hotlines-grid">
                    
                    <!-- Card 1: Layanan SEJIWA -->
                    <div class="hotline-card">
                        <div>
                            <div class="hotline-badges-row">
                                <span class="hotline-badge teal">Kementerian Kesehatan RI</span>
                                <span class="hotline-badge green">24 Jam Bebas Pulsa</span>
                            </div>

                            <div class="hotline-card-head">
                                <div class="hotline-icon-box teal">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </div>
                                <div class="hotline-card-info">
                                    <h2 class="hotline-card-title">Layanan SEJIWA</h2>
                                    <span class="hotline-card-subtitle">Kemenkes Republik Indonesia</span>
                                </div>
                            </div>
                        </div>

                        <div class="hotline-number-box">
                            <div class="hotline-number-meta">
                                <span class="hotline-number-lbl">Hotline Resmi Darurat</span>
                                <span class="hotline-number-val">119 Ext 8</span>
                            </div>
                            <button type="button" class="hotline-copy-btn" data-copy="119" title="Salin nomor hotline">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </div>

                        <p class="hotline-desc-text">
                            Layanan konseling darurat psikologis resmi Kemenkes RI terhubung langsung dengan psikolog klinis siaga tanggap.
                        </p>

                        <div class="hotline-actions-row">
                            <a href="tel:119,8" class="btn-hotline-primary">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span>Hubungi 119 Ext 8 Sekarang</span>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Hotline LISA -->
                    <div class="hotline-card">
                        <div>
                            <div class="hotline-badges-row">
                                <span class="hotline-badge rose">Pencegahan Krisis Bunuh Diri</span>
                                <span class="hotline-badge purple">ID &amp; EN Support</span>
                            </div>

                            <div class="hotline-card-head">
                                <div class="hotline-icon-box rose">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                </div>
                                <div class="hotline-card-info">
                                    <h2 class="hotline-card-title">Hotline LISA</h2>
                                    <span class="hotline-card-subtitle">Love Inside Suicide Awareness</span>
                                </div>
                            </div>
                        </div>

                        <div class="hotline-number-box">
                            <div class="hotline-number-meta">
                                <span class="hotline-number-lbl">Saluran Panggilan &amp; WA</span>
                                <span class="hotline-number-val">0811-3815-472</span>
                            </div>
                            <button type="button" class="hotline-copy-btn" data-copy="08113815472" title="Salin nomor Hotline LISA">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </div>

                        <p class="hotline-desc-text">
                            Dukungan pencegahan krisis dan intervensi darurat dalam Bahasa Indonesia &amp; Inggris, bebas stigma dan penuh empati.
                        </p>

                        <div class="hotline-actions-row">
                            <a href="tel:08113815472" class="btn-hotline-secondary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span>Telepon</span>
                            </a>
                            <a href="https://wa.me/628113815472?text=Halo%20Hotline%20LISA,%20saya%20sedang%20mengalami%20krisis%20dan%20membutuhkan%20bantuan%20segera" target="_blank" rel="noopener" class="btn-hotline-wa">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3: IGD Psikiatri Terdekat (Terintegrasi Google Maps) -->
                    <div class="hotline-card" id="cardIGDPsikiatri" style="border: 2px solid #0d9488; background: linear-gradient(180deg, #ffffff 0%, #f0fdfa 100%);">
                        <div>
                            <div class="hotline-badges-row">
                                <span class="hotline-badge amber">Tindakan Medis Darurat</span>
                                <span class="gps-pulse-badge" id="cardGpsBadge">
                                    <span class="gps-pulse-dot"></span>
                                    <span id="cardGpsText">GPS Google Maps Aktif</span>
                                </span>
                            </div>

                            <div class="hotline-card-head">
                                <div class="hotline-icon-box emerald" style="background: #0f766e; color: #ffffff;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"></path><path d="M2 12h20"></path><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
                                </div>
                                <div class="hotline-card-info">
                                    <h2 class="hotline-card-title">IGD Psikiatri Terdekat</h2>
                                    <span class="hotline-card-subtitle">RSJ &amp; RSUD Rujukan 24 Jam • Google Maps</span>
                                </div>
                            </div>
                        </div>

                        <div class="hotline-number-box" style="background:#ffffff; border:1px solid #ccfbf1;">
                            <div class="hotline-number-meta" style="overflow:hidden;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span class="hotline-number-lbl" id="cardHospitalLabel">RS Jiwa Terdekat Terdeteksi</span>
                                    <span class="rs-distance-badge" id="cardHospitalDistance" style="font-size:10.5px;">📍 ~2.4 km</span>
                                </div>
                                <span id="cardHospitalName" style="font-size:13px;font-weight:800;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                                    RS Jiwa Dr. Soeharto Heerdjan
                                </span>
                                <span id="cardHospitalAddress" style="font-size:11px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                                    Jl. Prof. Dr. Latumeten No. 1, Grogol, Jakarta Barat
                                </span>
                            </div>
                            <button type="button" class="hotline-copy-btn" id="btnCopyAddress" title="Salin Alamat RS">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </div>

                        <p class="hotline-desc-text">
                            Pencari RS Jiwa dan IGD Psikiatri terdekat dari lokasimu saat ini. Terhubung otomatis dengan Google Maps rute tercepat dan hotline ambulans medis siaga 24 jam.
                        </p>

                        <div class="hotline-actions-row" style="display:flex; flex-direction:column; gap:8px;">
                            <button type="button" class="btn-hotline-primary" onclick="openHospitalFinderModal()" style="background:#0f766e; justify-content:center; width:100%; border:none; cursor:pointer;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                                <span>Cari RS Psikiatri Terdekat (GPS)</span>
                            </button>
                            <a href="https://www.google.com/maps/search/rumah+sakit+jiwa+psikiatri+terdekat/" target="_blank" rel="noopener" id="cardDirectMapsBtn" class="btn-hotline-map" style="justify-content:center; width:100%;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                <span>Buka Radar Google Maps Langsung</span>
                            </a>
                        </div>
                    </div>

                </section>

                <!-- 3. MIDDLE SECTION: RITME NAPAS & PROTOKOL PANIK -->
                <div class="emergency-two-cols">
                    
                    <!-- LEFT: Guided 4-7-8 Breathing Technique -->
                    <section class="emergency-action-card">
                        <div>
                            <span class="section-pretitle">Latihan Mandiri Cepat</span>
                            <h2 class="section-main-heading">Ritme Napas 4-7-8</h2>
                        </div>
                        <p class="section-desc-light">
                            Teknik napas darurat untuk mengaktifkan sistem saraf parasimpatis dan menurunkan lonjakan detak jantung seketika.
                        </p>

                        <!-- Breathing Visualizer -->
                        <div class="breathing-visual-box">
                            <div class="breathing-circle-outer" id="breathingCircleOuter">
                                <div class="breathing-circle-inner" id="breathingCircleInner">
                                    <span class="breathing-phase-text" id="breathingPhaseText">Siap</span>
                                    <span class="breathing-phase-count" id="breathingCount">4</span>
                                </div>
                            </div>

                            <div class="breathing-status-instruction" id="breathingInstruction">
                                Tekan tombol di bawah untuk memulai panduan relaksasi.
                            </div>

                            <button type="button" class="btn-breathing-control" id="toggleBreathingBtn">
                                <svg id="breathingPlayIcon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                                <span id="breathingBtnText">Mulai Panduan Napas</span>
                            </button>
                        </div>
                    </section>

                    <!-- RIGHT: Panic Attack Protocol (4 Grounding Steps) -->
                    <section class="emergency-action-card">
                        <div>
                            <span class="section-pretitle rose">PROTOKOL STABILISASI EMOSI</span>
                            <h2 class="section-main-heading">Pertolongan Pertama Saat Serangan Panik</h2>
                        </div>
                        <p class="section-desc-light">
                            Saat cemas berlebih atau tubuh gemetar hebat, lakukan 4 langkah grounding fisik ini secara berurutan:
                        </p>

                        <div class="grounding-steps-list">
                            <!-- Step 1 -->
                            <div class="grounding-step-item">
                                <div class="grounding-step-num">1</div>
                                <div class="grounding-step-content">
                                    <div class="grounding-step-title">Pijakkan Kaki Kuat-Kuat ke Lantai</div>
                                    <div class="grounding-step-desc">
                                        Lepaskan alas kaki jika memungkinkan. Rasakan gravitasi menopang tubuhmu. Kamu ada di tempat yang aman saat ini.
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="grounding-step-item">
                                <div class="grounding-step-num">2</div>
                                <div class="grounding-step-content">
                                    <div class="grounding-step-title">Sentuh 3 Benda Bertekstur Berbeda</div>
                                    <div class="grounding-step-desc">
                                        Pegang ujung pakaian, meja dingin, atau kunci logam. Alihkan fokus pikiran pada suhu dan tekstur untuk meredakan disosiasi.
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="grounding-step-item">
                                <div class="grounding-step-num">3</div>
                                <div class="grounding-step-content">
                                    <div class="grounding-step-title">Teguk Air Dingin Pelan-Pelan</div>
                                    <div class="grounding-step-desc">
                                        Perhatikan sensasi dingin di tenggorokan saat menelan. Refleks menelan secara biologis menstabilkan laju napas yang terengah.
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="grounding-step-item">
                                <div class="grounding-step-num">4</div>
                                <div class="grounding-step-content">
                                    <div class="grounding-step-title">Hubungi Hotline / Satu Orang Tepercaya</div>
                                    <div class="grounding-step-desc">
                                        Kirim pesan sederhana: <em>"Aku sedang panik, bisakah temani aku sebentar?"</em> atau langsung tekan tombol panggilan 119 di atas.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- 4. PANDUAN REKAN & PENDAMPING (DO'S & DON'TS) -->
                <section class="peer-guide-card">
                    <div class="peer-guide-header">
                        <span class="section-pretitle">PANDUAN REKAN &amp; PENDAMPING</span>
                        <h2 class="section-main-heading">Menghadapi Teman yang Sedang Mengalami Krisis</h2>
                        <p class="section-desc-light">
                            Ketika seseorang mempercayaimu dalam titik terendahnya, kehadiran yang tenang jauh lebih berharga daripada solusi yang terburu-buru.
                        </p>
                    </div>

                    <div class="peer-two-cols">
                        <!-- DO'S -->
                        <div class="peer-box dos">
                            <div class="peer-box-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Hal yang Sebaiknya Dilakukan (Do's)</span>
                            </div>

                            <ul class="peer-items-list">
                                <li class="peer-item-row do-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <div>
                                        <span class="peer-item-strong">Dengarkan tanpa menghakimi:</span> Biarkan mereka meluapkan rasa sakit tanpa memotong atau berusaha menceramahi.
                                    </div>
                                </li>
                                <li class="peer-item-row do-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <div>
                                        <span class="peer-item-strong">Validasi perasaan mereka:</span> Ucapkan kalimat seperti <em>"Aku paham ini sangat berat untukmu, terima kasih sudah mau bertahan."</em>
                                    </div>
                                </li>
                                <li class="peer-item-row do-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <div>
                                        <span class="peer-item-strong">Amankan lingkungan sekitar:</span> Jauhkan benda berbahaya, obat-obatan berlebih, atau situasi berisiko secara halus dan tenang.
                                    </div>
                                </li>
                                <li class="peer-item-row do-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <div>
                                        <span class="peer-item-strong">Temani saat menelepon bantuan:</span> Tawarkan diri untuk mendampingi ketika mereka menelepon 119 Ext 8 atau LISA hotline.
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- DON'TS -->
                        <div class="peer-box donts">
                            <div class="peer-box-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span>Hal yang Harus Dihindari (Don'ts)</span>
                            </div>

                            <ul class="peer-items-list">
                                <li class="peer-item-row dont-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    <div>
                                        <span class="peer-item-strong">Jangan membandingkan penderitaan:</span> Hindari kalimat <em>"Masih banyak orang yang lebih susah dari kamu"</em> yang memperparah rasa bersalah.
                                    </div>
                                </li>
                                <li class="peer-item-row dont-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    <div>
                                        <span class="peer-item-strong">Jangan menganggap sepele atau drama:</span> Tanggapi setiap ungkapan ingin menyerah sebagai panggilan bantuan yang nyata.
                                    </div>
                                </li>
                                <li class="peer-item-row dont-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    <div>
                                        <span class="peer-item-strong">Jangan tinggalkan sendirian:</span> Jika dalam kondisi agitasi tinggi atau ada ancaman menyakiti diri, tetaplah bersama mereka.
                                    </div>
                                </li>
                                <li class="peer-item-row dont-item">
                                    <svg class="peer-icon-mark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    <div>
                                        <span class="peer-item-strong">Jangan berjanji merahasiakan bahaya:</span> Keselamatan nyawa berada di atas janji menjaga rahasia. Segera libatkan bantuan medis.
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- 5. PRIVACY GUARANTEE & QUICK EXIT -->
                <footer class="emergency-privacy-footer">
                    <div class="privacy-left-block">
                        <div class="privacy-icon-circle">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                        <div class="privacy-text-block">
                            <span class="privacy-title-bold">Jaminan Kerahasiaan &amp; Keamanan Privasi</span>
                            <span class="privacy-desc-para">
                                Panggilan atau akses hotline krisis pada halaman ini terenkripsi dan tidak disimpan dalam riwayat aktivitas aplikasi MindCare demi privasi penuh tanpa jejak.
                            </span>
                        </div>
                    </div>

                    <button type="button" class="btn-quick-exit" id="quickExitBtn" title="Tutup segera halaman ini dan alihkan ke pencarian Google">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span>Keluar Cepat (Quick Exit)</span>
                        <kbd>Esc</kbd>
                    </button>
                </footer>

            </div>
        </main>
    </div>

    <!-- MODAL INTERAKTIF: PENCARI RS PSIKIATRI & IGD TERDEKAT GOOGLE MAPS -->
    <div class="rs-modal-overlay" id="hospitalFinderModal" onclick="closeHospitalFinderModal(event)">
        <div class="rs-modal-container" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="rs-modal-header">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <span class="gps-pulse-badge" style="background:rgba(255,255,255,0.2); color:#ffffff; border-color:rgba(255,255,255,0.3);">
                            <span class="gps-pulse-dot" style="background:#4ade80;"></span>
                            Live Google Maps Locator
                        </span>
                        <span style="font-size:12px; opacity:0.85;">• Siaga Krisis 24 Jam</span>
                    </div>
                    <h2 style="font-size:20px; font-weight:800; margin:0; letter-spacing:-0.02em;">
                        Cari RS Jiwa &amp; IGD Psikiatri Terdekat
                    </h2>
                    <p style="font-size:12.5px; opacity:0.9; margin:4px 0 0 0;">
                        Sistem mendeteksi jarak rumah sakit terdekat dari posisimu dan menghubungkannya langsung ke rute Google Maps.
                    </p>
                </div>
                <button type="button" class="rs-modal-close-btn" onclick="closeHospitalFinderModalDirect()" title="Tutup Modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Google Maps Radar Direct Banner -->
            <div class="rs-modal-radar-banner">
                <div style="display:flex; align-items:center; gap:10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <div style="font-size:12.5px; color:#881337;">
                        <strong>Buka Langsung di Google Maps:</strong> Telusuri seluruh unit IGD psikiatri &amp; RSJ terverifikasi di sekitarmu.
                    </div>
                </div>
                <a href="https://www.google.com/maps/search/rumah+sakit+jiwa+psikiatri+terdekat/" id="btnLiveRadarMaps" target="_blank" rel="noopener" class="btn-live-gmaps-radar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                    <span>Buka Radar Maps Sekitar</span>
                </a>
            </div>

            <!-- Controls: GPS trigger & Region Selector & Search -->
            <div class="rs-modal-controls">
                <button type="button" class="btn-gps-trigger" id="btnTriggerGps" onclick="getUserGpsLocation()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                    <span id="btnGpsLabel">Deteksi Lokasi GPS Saya</span>
                </button>

                <select class="rs-region-select" id="selectRegionFilter" onchange="filterHospitalsByRegion()">
                    <option value="all">Semua Wilayah Indonesia (Urut Jarak)</option>
                    <option value="jabodetabek" selected>Jabodetabek (Jakarta, Bogor, Depok, Tangerang, Bekasi)</option>
                    <option value="jabar">Jawa Barat (Bandung, Cisarua, Cirebon)</option>
                    <option value="jateng_diy">Jawa Tengah &amp; DIY (Semarang, Solo, Magelang, Jogja)</option>
                    <option value="jatim">Jawa Timur (Surabaya, Malang, Lawang)</option>
                    <option value="sumatera">Sumatera (Medan, Padang, Palembang)</option>
                    <option value="bali_timur">Bali &amp; Indonesia Timur (Bangli, Makassar, Manado)</option>
                </select>

                <input type="text" id="inputSearchHospital" class="event-search-input" style="flex:1; min-width:180px; padding:7px 12px; font-size:12.5px; border-radius:8px; border:1.5px solid #cbd5e1;" placeholder="Cari nama RS atau kota..." oninput="filterHospitalsBySearch()">
            </div>

            <!-- Live GPS Status Line -->
            <div id="gpsStatusLine" style="padding: 7px 24px; background:#f1f5f9; font-size:12px; color:#475569; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span id="gpsLocationText">📍 Lokasi aktif: <strong>Jakarta Pusat (Default Presisi)</strong></span>
                </div>
                <span id="gpsCoordsText" style="color:#64748b; font-size:11px;">Koordinat: -6.1751, 106.8650</span>
            </div>

            <!-- List of Hospitals -->
            <div class="rs-modal-list" id="hospitalListContainer">
                <!-- Dynamically rendered via JavaScript -->
            </div>
        </div>
    </div>

    <!-- FLOATING TOAST FEEDBACK -->
    <div class="emergency-copy-toast" id="copyToast">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span id="copyToastMsg">Nomor hotline berhasil disalin!</span>
    </div>

    <!-- INTERACTIVE JAVASCRIPT LOGIC -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Copy Hotline Buttons
        const copyBtns = document.querySelectorAll('.hotline-copy-btn');
        const toast = document.getElementById('copyToast');
        const toastMsg = document.getElementById('copyToastMsg');
        let toastTimeout;

        function showToast(msg) {
            if (toast) {
                toastMsg.textContent = msg;
                toast.classList.add('show');
                clearTimeout(toastTimeout);
                toastTimeout = setTimeout(() => {
                    toast.classList.remove('show');
                }, 2400);
            }
        }

        copyBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                const textToCopy = this.getAttribute('data-copy');
                if (textToCopy) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        showToast(`Nomor ${textToCopy} berhasil disalin ke papan klip!`);
                    }).catch(() => {
                        showToast(`Nomor disalin: ${textToCopy}`);
                    });
                }
            });
        });

        // 2. Quick Exit (Safety Escape Feature)
        const quickExitBtn = document.getElementById('quickExitBtn');
        function executeQuickExit() {
            window.location.replace('https://www.google.com');
        }

        if (quickExitBtn) {
            quickExitBtn.addEventListener('click', executeQuickExit);
        }

        // Listen for ESC key for emergency exit
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                executeQuickExit();
            }
        });

        // 3. Functional 4-7-8 Breathing Technique
        const toggleBreathingBtn = document.getElementById('toggleBreathingBtn');
        const breathingPlayIcon = document.getElementById('breathingPlayIcon');
        const breathingBtnText = document.getElementById('breathingBtnText');
        const outerCircle = document.getElementById('breathingCircleOuter');
        const innerCircle = document.getElementById('breathingCircleInner');
        const phaseText = document.getElementById('breathingPhaseText');
        const countText = document.getElementById('breathingCount');
        const instructionText = document.getElementById('breathingInstruction');

        let isBreathingActive = false;
        let breathingTimer = null;
        let countdownTimer = null;
        let currentSecond = 4;

        // Gentle Web Audio tone for breathing cues
        let audioCtx = null;
        function playGentleChime(freq = 440) {
            try {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.8);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.8);
            } catch(e) {}
        }

        function runBreathingCycle() {
            if (!isBreathingActive) return;

            // Phase 1: INHALE (4 seconds)
            phaseText.textContent = 'Tarik';
            instructionText.textContent = 'Tarik napas perlahan lewat hidung...';
            currentSecond = 4;
            countText.textContent = currentSecond;
            playGentleChime(440);

            // Expand circles
            if (outerCircle) outerCircle.style.transform = 'scale(1.35)';
            if (innerCircle) {
                innerCircle.style.transform = 'scale(1.25)';
                innerCircle.style.background = 'linear-gradient(135deg, #0d9488, #0f766e)';
            }

            let inhaleCounter = setInterval(() => {
                if (!isBreathingActive) { clearInterval(inhaleCounter); return; }
                currentSecond--;
                if (currentSecond > 0) {
                    countText.textContent = currentSecond;
                } else {
                    clearInterval(inhaleCounter);
                }
            }, 1000);

            // Phase 2: HOLD (7 seconds)
            breathingTimer = setTimeout(() => {
                if (!isBreathingActive) return;
                phaseText.textContent = 'Tahan';
                instructionText.textContent = 'Tahan napas dengan tenang dan rileks...';
                currentSecond = 7;
                countText.textContent = currentSecond;
                playGentleChime(523.25);

                if (innerCircle) {
                    innerCircle.style.background = 'linear-gradient(135deg, #0284c7, #0369a1)';
                }

                let holdCounter = setInterval(() => {
                    if (!isBreathingActive) { clearInterval(holdCounter); return; }
                    currentSecond--;
                    if (currentSecond > 0) {
                        countText.textContent = currentSecond;
                    } else {
                        clearInterval(holdCounter);
                    }
                }, 1000);

                // Phase 3: EXHALE (8 seconds)
                breathingTimer = setTimeout(() => {
                    if (!isBreathingActive) return;
                    phaseText.textContent = 'Hembuskan';
                    instructionText.textContent = 'Hembuskan perlahan lewat mulut sampai habis...';
                    currentSecond = 8;
                    countText.textContent = currentSecond;
                    playGentleChime(349.23);

                    // Shrink circles
                    if (outerCircle) outerCircle.style.transform = 'scale(1)';
                    if (innerCircle) {
                        innerCircle.style.transform = 'scale(1)';
                        innerCircle.style.background = 'linear-gradient(135deg, #0d9488, #0f766e)';
                    }

                    let exhaleCounter = setInterval(() => {
                        if (!isBreathingActive) { clearInterval(exhaleCounter); return; }
                        currentSecond--;
                        if (currentSecond > 0) {
                            countText.textContent = currentSecond;
                        } else {
                            clearInterval(exhaleCounter);
                        }
                    }, 1000);

                    // Next cycle
                    breathingTimer = setTimeout(() => {
                        if (isBreathingActive) {
                            runBreathingCycle();
                        }
                    }, 8000);

                }, 7000);

            }, 4000);
        }

        function stopBreathingCycle() {
            isBreathingActive = false;
            clearTimeout(breathingTimer);
            if (outerCircle) outerCircle.style.transform = 'scale(1)';
            if (innerCircle) {
                innerCircle.style.transform = 'scale(1)';
                innerCircle.style.background = 'linear-gradient(135deg, #0d9488, #0f766e)';
            }
            phaseText.textContent = 'Siap';
            countText.textContent = '4';
            instructionText.textContent = 'Tekan tombol di bawah untuk memulai panduan relaksasi.';
            breathingBtnText.textContent = 'Mulai Panduan Napas';
            breathingPlayIcon.innerHTML = '<polygon points="5 3 19 12 5 21 5 3"></polygon>';
            toggleBreathingBtn.style.backgroundColor = '#0f766e';
        }

        if (toggleBreathingBtn) {
            toggleBreathingBtn.addEventListener('click', function() {
                if (!isBreathingActive) {
                    isBreathingActive = true;
                    breathingBtnText.textContent = 'Jeda Panduan';
                    breathingPlayIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
                    toggleBreathingBtn.style.backgroundColor = '#475569';
                    runBreathingCycle();
                } else {
                    stopBreathingCycle();
                }
            });
        }

        // 4. RS Psikiatri & IGD Terdekat Terintegrasi Google Maps
        const hospitalDatabase = [
            {
                id: 1,
                name: "RS Jiwa Dr. Soeharto Heerdjan (Kemenkes RI)",
                type: "RSJ Kelas A & Pusat Rujukan Nasional",
                region: "jabodetabek",
                city: "Jakarta Barat",
                address: "Jl. Prof. Dr. Latumeten No. 1, Jelambar, Grogol Petamburan, Jakarta Barat",
                lat: -6.1624,
                lng: 106.7915,
                phone: "(021) 5682841",
                igdPhone: "08111956828",
                services: ["IGD Psikiatri 24 Jam", "Kedaruratan Jiwa Anak & Remaja", "BPJS Kesehatan"]
            },
            {
                id: 2,
                name: "RS Jiwa Islam Klender",
                type: "RS Khusus Jiwa Swasta Terakreditasi",
                region: "jabodetabek",
                city: "Jakarta Timur",
                address: "Jl. Bunga Rampai X No. 8, Malaka Jaya, Duren Sawit, Jakarta Timur",
                lat: -6.2238,
                lng: 106.9038,
                phone: "(021) 8614138",
                igdPhone: "081298007700",
                services: ["IGD 24 Jam", "Detoksifikasi & Adiksi", "Rawat Inap Krisis"]
            },
            {
                id: 3,
                name: "IGD Psikiatri RSUPN Dr. Cipto Mangunkusumo (RSCM)",
                type: "RS Umum Pusat Nasional / Rujukan Tertinggi",
                region: "jabodetabek",
                city: "Jakarta Pusat",
                address: "Jl. Diponegoro No. 71, Kenari, Senen, Jakarta Pusat",
                lat: -6.1969,
                lng: 106.8488,
                phone: "1500135",
                igdPhone: "(021) 1500135",
                services: ["IGD Medis & Psikiatri 24 Jam", "Klinik Jiwa Dewasa & Anak", "BPJS Kesehatan"]
            },
            {
                id: 4,
                name: "RS Jiwa Dr. H. Marzoeki Mahdi (PKJN)",
                type: "Pusat Kesehatan Jiwa Nasional (PKJN)",
                region: "jabodetabek",
                city: "Bogor",
                address: "Jl. Dr. Sumeru No. 114, Menteng, Kota Bogor, Jawa Barat",
                lat: -6.5815,
                lng: 106.7725,
                phone: "(0251) 8320467",
                igdPhone: "081380008320",
                services: ["IGD Psikiatri 24 Jam", "Rehabilitasi Psikososial", "Ambulans Krisis 24 Jam"]
            },
            {
                id: 5,
                name: "RS Jiwa Provinsi Jawa Barat (RSJ Cisarua)",
                type: "RS Jiwa Pemprov Jabar",
                region: "jabar",
                city: "Bandung Barat",
                address: "Jl. Kolonel Masturi KM. 7, Jambudipa, Cisarua, Kabupaten Bandung Barat",
                lat: -6.8174,
                lng: 107.5583,
                phone: "(022) 2700307",
                igdPhone: "0811223307",
                services: ["IGD 24 Jam", "Klinik Psikoterapi & Konseling", "BPJS Kesehatan"]
            },
            {
                id: 6,
                name: "IGD Psikiatri RSUP Dr. Hasan Sadikin (RSHS)",
                type: "RS Umum Pusat Rujukan Jawa Barat",
                region: "jabar",
                city: "Bandung",
                address: "Jl. Pasteur No. 38, Sukajadi, Kota Bandung, Jawa Barat",
                lat: -6.8974,
                lng: 107.5986,
                phone: "(022) 2034953",
                igdPhone: "08112034953",
                services: ["IGD Medis & Psikiatri Terpadu", "Subspesialis Jiwa", "BPJS Kesehatan"]
            },
            {
                id: 7,
                name: "RS Jiwa Daerah Dr. Amino Gondohutomo",
                type: "RS Jiwa Daerah Provinsi Jawa Tengah",
                region: "jateng_diy",
                city: "Semarang",
                address: "Jl. Brigjen Sudiarto No. 347, Gemah, Pedurungan, Kota Semarang",
                lat: -7.0097,
                lng: 110.4632,
                phone: "(024) 6722565",
                igdPhone: "08112700672",
                services: ["IGD Psikiatri 24 Jam", "Klinik Tumbuh Kembang", "Layanan Konseling Krisis"]
            },
            {
                id: 8,
                name: "RS Jiwa Grhasia DIY",
                type: "RS Khusus Jiwa Daerah Istimewa Yogyakarta",
                region: "jateng_diy",
                city: "Sleman / Yogyakarta",
                address: "Jl. Kaliurang KM. 17, Pakembinangun, Pakem, Sleman, DI Yogyakarta",
                lat: -7.6631,
                lng: 110.4194,
                phone: "(0274) 895143",
                igdPhone: "08112895143",
                services: ["IGD Psikiatri 24 Jam", "Rehabilitasi Ketergantungan Obat", "BPJS Kesehatan"]
            },
            {
                id: 9,
                name: "RS Jiwa Daerah Surakarta (RSJD Solo)",
                type: "RS Jiwa Daerah Jawa Tengah",
                region: "jateng_diy",
                city: "Surakarta / Solo",
                address: "Jl. Ki Hajar Dewantara No. 80, Jebres, Kota Surakarta, Jawa Tengah",
                lat: -7.5502,
                lng: 110.8524,
                phone: "(0271) 641442",
                igdPhone: "08112641442",
                services: ["IGD 24 Jam", "Konsultasi Neuropsikiatri", "BPJS Kesehatan"]
            },
            {
                id: 10,
                name: "RS Jiwa Menur Surabaya",
                type: "RS Jiwa Pemprov Jawa Timur",
                region: "jatim",
                city: "Surabaya",
                address: "Jl. Menur No. 120, Kertajaya, Gubeng, Kota Surabaya, Jawa Timur",
                lat: -7.2831,
                lng: 112.7712,
                phone: "(031) 5021635",
                igdPhone: "081132201635",
                services: ["IGD Psikiatri 24 Jam", "Unit Terapi Adiksi", "BPJS Kesehatan"]
            },
            {
                id: 11,
                name: "RS Jiwa Dr. Radjiman Wediodiningrat (RSJ Lawang)",
                type: "Pusat Rujukan Jiwa Kemenkes RI Jawa Timur",
                region: "jatim",
                city: "Malang",
                address: "Jl. Jenderal A. Yani, Lawang, Kabupaten Malang, Jawa Timur",
                lat: -7.8385,
                lng: 112.6975,
                phone: "(0341) 426015",
                igdPhone: "08113426015",
                services: ["IGD Psikiatri 24 Jam", "Psikiatri Forensik & Komunitas", "BPJS Kesehatan"]
            },
            {
                id: 12,
                name: "RS Jiwa Provinsi Bali (RSJ Bangli)",
                type: "RS Jiwa Rujukan Wilayah Bali & Nusa Tenggara",
                region: "bali_timur",
                city: "Bangli / Denpasar",
                address: "Jl. Kusumayuda No. 29, Kawan, Kec. Bangli, Kabupaten Bangli, Bali",
                lat: -8.4552,
                lng: 115.3562,
                phone: "(0366) 91002",
                igdPhone: "08123991002",
                services: ["IGD 24 Jam", "Konseling Krisis & Trauma Healing", "BPJS Kesehatan"]
            },
            {
                id: 13,
                name: "RS Jiwa Prof. Dr. M. Ildrem",
                type: "RS Jiwa Pemprov Sumatera Utara",
                region: "sumatera",
                city: "Medan",
                address: "Jl. Tali Air No. 21, Mangga, Medan Tuntungan, Kota Medan, Sumatera Utara",
                lat: 3.5358,
                lng: 98.6496,
                phone: "(061) 8360051",
                igdPhone: "08126083600",
                services: ["IGD Psikiatri 24 Jam", "Rehabilitasi Napza", "BPJS Kesehatan"]
            },
            {
                id: 14,
                name: "RS Jiwa Prof. HB. Saanin Padang",
                type: "RS Jiwa Pemprov Sumatera Barat",
                region: "sumatera",
                city: "Padang",
                address: "Jl. Raya Ulu Gadut, Limau Manis Sel., Pauh, Kota Padang, Sumatera Barat",
                lat: -0.9271,
                lng: 100.4182,
                phone: "(0751) 72001",
                igdPhone: "08116672001",
                services: ["IGD 24 Jam", "Klinik Jiwa Terpadu", "BPJS Kesehatan"]
            },
            {
                id: 15,
                name: "RS Jiwa Ernaldi Bahar",
                type: "RS Jiwa Pemprov Sumatera Selatan",
                region: "sumatera",
                city: "Palembang",
                address: "Jl. Tembus Terminal KM. 12, Alang-Alang Lebar, Kota Palembang",
                lat: -2.9328,
                lng: 104.7214,
                phone: "(0711) 5646142",
                igdPhone: "08117856461",
                services: ["IGD Psikiatri 24 Jam", "Konseling Adiksi", "BPJS Kesehatan"]
            },
            {
                id: 16,
                name: "RS Khusus Daerah Dadi Provinsi Sulsel",
                type: "Pusat Rujukan Jiwa Indonesia Timur",
                region: "bali_timur",
                city: "Makassar",
                address: "Jl. Lanto Dg. Pasewang No. 34, Maricaya Sel., Mamajang, Kota Makassar",
                lat: -5.1587,
                lng: 119.4218,
                phone: "(0411) 873120",
                igdPhone: "08114187312",
                services: ["IGD Psikiatri 24 Jam", "Rehabilitasi Napza", "BPJS Kesehatan"]
            }
        ];

        let currentUserCoords = {
            lat: -6.1751,
            lng: 106.8650,
            isGps: false,
            label: "Jakarta Pusat (Perkiraan Default)"
        };

        // Haversine distance calculator (km)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Render hospital list
        function renderHospitals() {
            const container = document.getElementById('hospitalListContainer');
            if (!container) return;

            const regionFilter = document.getElementById('selectRegionFilter')?.value || 'all';
            const searchQuery = (document.getElementById('inputSearchHospital')?.value || '').toLowerCase().trim();

            // Calculate distance for all hospitals
            let hospitalsWithDist = hospitalDatabase.map(h => {
                const dist = calculateDistance(currentUserCoords.lat, currentUserCoords.lng, h.lat, h.lng);
                return { ...h, distance: dist };
            });

            // Sort by nearest distance
            hospitalsWithDist.sort((a, b) => a.distance - b.distance);

            // Filter by region and search query
            const filtered = hospitalsWithDist.filter(h => {
                const matchesRegion = (regionFilter === 'all' || h.region === regionFilter);
                const matchesSearch = (!searchQuery || 
                    h.name.toLowerCase().includes(searchQuery) || 
                    h.city.toLowerCase().includes(searchQuery) ||
                    h.address.toLowerCase().includes(searchQuery));
                return matchesRegion && matchesSearch;
            });

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding:40px 20px; color:#64748b;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" style="margin-bottom:10px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <div style="font-weight:700; color:#334155;">Tidak menemukan rumah sakit yang cocok</div>
                        <div style="font-size:12px; margin-top:4px;">Coba gunakan kata kunci lain atau pilih 'Semua Wilayah Indonesia'.</div>
                    </div>
                `;
                return;
            }

            let html = '';
            filtered.forEach((h, idx) => {
                const distFormatted = h.distance < 1 
                    ? `${Math.round(h.distance * 1000)} meter` 
                    : `${h.distance.toFixed(1)} km`;
                const isClosest = (idx === 0);
                const cleanPhone = h.igdPhone.replace(/[^0-9+]/g, '');
                const gmapsDirectionUrl = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(h.name + ' ' + h.address)}`;

                html += `
                    <div class="rs-item-card" style="${isClosest ? 'border-color:#0d9488; background:#f0fdfa;' : ''}">
                        <div class="rs-item-info">
                            <div class="rs-item-header-row">
                                <span class="rs-item-name">${h.name}</span>
                                <span class="rs-distance-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    ${distFormatted} ${isClosest ? '• Terdekat' : ''}
                                </span>
                                <span style="font-size:11px; background:#fee2e2; color:#be123c; padding:2px 7px; border-radius:4px; font-weight:700;">IGD 24 Jam</span>
                            </div>
                            <div class="rs-item-address">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span>${h.address} (${h.city})</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:12px; margin-top:2px;" class="rs-item-phone">
                                <span>📞 Telepon / IGD: <strong>${h.phone}</strong> / ${h.igdPhone}</span>
                                <span style="color:#0f766e; font-weight:600;">• ${h.type}</span>
                            </div>
                        </div>
                        <div class="rs-item-actions">
                            <a href="${gmapsDirectionUrl}" target="_blank" rel="noopener" class="btn-rs-map" title="Buka rute navigasi di Google Maps">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                <span>Rute Maps</span>
                            </a>
                            <a href="tel:${cleanPhone}" class="btn-rs-call" title="Panggil IGD Rumah Sakit">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span>Panggil IGD</span>
                            </a>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Update Card 3 on main emergency page
        function updateCardPreview() {
            let hospitalsWithDist = hospitalDatabase.map(h => {
                const dist = calculateDistance(currentUserCoords.lat, currentUserCoords.lng, h.lat, h.lng);
                return { ...h, distance: dist };
            });
            hospitalsWithDist.sort((a, b) => a.distance - b.distance);
            const nearest = hospitalsWithDist[0];
            if (!nearest) return;

            const nameEl = document.getElementById('cardHospitalName');
            const addrEl = document.getElementById('cardHospitalAddress');
            const distEl = document.getElementById('cardHospitalDistance');
            const mapsBtn = document.getElementById('cardDirectMapsBtn');
            const copyBtn = document.getElementById('btnCopyAddress');

            const distFormatted = nearest.distance < 1 
                ? `${Math.round(nearest.distance * 1000)} m` 
                : `${nearest.distance.toFixed(1)} km`;

            if (nameEl) nameEl.textContent = nearest.name;
            if (addrEl) addrEl.textContent = `${nearest.address} (${nearest.city})`;
            if (distEl) distEl.textContent = `📍 Terdekat ~${distFormatted}`;
            if (mapsBtn) {
                mapsBtn.href = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(nearest.name + ' ' + nearest.address)}`;
            }
            if (copyBtn) {
                copyBtn.setAttribute('data-copy', `${nearest.name}, ${nearest.address}`);
            }
        }

        // Global functions for modal and GPS
        window.openHospitalFinderModal = function() {
            const modal = document.getElementById('hospitalFinderModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                renderHospitals();
            }
        };

        window.closeHospitalFinderModalDirect = function() {
            const modal = document.getElementById('hospitalFinderModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        };

        window.closeHospitalFinderModal = function(e) {
            if (e.target.id === 'hospitalFinderModal') {
                closeHospitalFinderModalDirect();
            }
        };

        window.getUserGpsLocation = function() {
            const lbl = document.getElementById('btnGpsLabel');
            const btn = document.getElementById('btnTriggerGps');
            if (!navigator.geolocation) {
                alert('Peramban Anda belum mendukung sensor geolokasi GPS.');
                return;
            }

            if (lbl) lbl.textContent = 'Mencari Satelit GPS...';
            if (btn) btn.style.opacity = '0.7';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    currentUserCoords.lat = pos.coords.latitude;
                    currentUserCoords.lng = pos.coords.longitude;
                    currentUserCoords.isGps = true;
                    currentUserCoords.label = 'GPS Perangkat Terkini';

                    if (lbl) lbl.textContent = '✓ Lokasi Terkoneksi';
                    if (btn) {
                        btn.style.opacity = '1';
                        btn.style.background = '#059669';
                    }

                    // Update UI status text
                    const locText = document.getElementById('gpsLocationText');
                    const coordsText = document.getElementById('gpsCoordsText');
                    const radarMapsBtn = document.getElementById('btnLiveRadarMaps');

                    if (locText) locText.innerHTML = `📍 Lokasi aktif: <strong>Koordinat GPS Perangkatmu</strong>`;
                    if (coordsText) coordsText.textContent = `Lat: ${pos.coords.latitude.toFixed(4)}, Lng: ${pos.coords.longitude.toFixed(4)}`;
                    
                    // Update radar live maps url with user coordinates
                    if (radarMapsBtn) {
                        radarMapsBtn.href = `https://www.google.com/maps/search/rumah+sakit+jiwa+psikiatri+terdekat/@${pos.coords.latitude},${pos.coords.longitude},14z`;
                    }

                    const cardGpsText = document.getElementById('cardGpsText');
                    if (cardGpsText) cardGpsText.textContent = 'GPS Lokasi Kamu Terhubung';

                    // Re-render hospitals & update preview card
                    renderHospitals();
                    updateCardPreview();
                    showToast('Lokasi GPS berhasil dideteksi! Menampilkan RS terdekat.');
                },
                function(err) {
                    if (lbl) lbl.textContent = 'Deteksi Lokasi GPS Saya';
                    if (btn) btn.style.opacity = '1';
                    alert('Izin akses lokasi belum diberikan atau ditolak. Menggunakan lokasi kota default. Anda tetap dapat memilih wilayah di menu pilihan.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        };

        window.filterHospitalsByRegion = function() {
            const reg = document.getElementById('selectRegionFilter')?.value || 'all';
            // Adjust coords preset if user manually picks region and hasn't used GPS
            if (!currentUserCoords.isGps) {
                if (reg === 'jabar') {
                    currentUserCoords.lat = -6.9175; currentUserCoords.lng = 107.6191; currentUserCoords.label = 'Bandung (Jawa Barat)';
                } else if (reg === 'jateng_diy') {
                    currentUserCoords.lat = -7.0051; currentUserCoords.lng = 110.4381; currentUserCoords.label = 'Semarang / DIY';
                } else if (reg === 'jatim') {
                    currentUserCoords.lat = -7.2575; currentUserCoords.lng = 112.7521; currentUserCoords.label = 'Surabaya (Jawa Timur)';
                } else if (reg === 'sumatera') {
                    currentUserCoords.lat = 3.5952; currentUserCoords.lng = 98.6722; currentUserCoords.label = 'Medan (Sumatera)';
                } else if (reg === 'bali_timur') {
                    currentUserCoords.lat = -8.6705; currentUserCoords.lng = 115.2126; currentUserCoords.label = 'Denpasar (Bali)';
                } else {
                    currentUserCoords.lat = -6.1751; currentUserCoords.lng = 106.8650; currentUserCoords.label = 'Jakarta (Jabodetabek)';
                }
                const locText = document.getElementById('gpsLocationText');
                if (locText) locText.innerHTML = `📍 Wilayah terpilih: <strong>${currentUserCoords.label}</strong>`;
            }
            renderHospitals();
            updateCardPreview();
        };

        window.filterHospitalsBySearch = function() {
            renderHospitals();
        };

        // Initialize preview card on load
        updateCardPreview();

        // Copy address button in Card 3
        const btnCopyAddr = document.getElementById('btnCopyAddress');
        if (btnCopyAddr) {
            btnCopyAddr.addEventListener('click', function() {
                const text = this.getAttribute('data-copy') || 'RS Jiwa Dr. Soeharto Heerdjan, Jl. Prof. Dr. Latumeten No. 1, Grogol, Jakarta Barat';
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Alamat RS Jiwa berhasil disalin!');
                }).catch(() => {
                    showToast('Alamat disalin.');
                });
            });
        }
    });
    </script>
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
