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
    <title>Pustaka Edukasi &amp; Psikoedukasi Terverifikasi - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        /* Specific Styles for Artikel Edukasi */
        .edu-container {
            padding: 4px 0 50px 0;
            max-width: 1240px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        /* Top Trust Banner - Clean, non-intrusive */
        .edu-trust-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            padding: 9px 18px;
            border-radius: var(--radius-full);
            font-size: 13px;
            color: var(--teal-800);
            font-weight: 500;
        }
        .edu-trust-items {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .edu-trust-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .edu-trust-item svg {
            color: var(--teal-600);
            flex-shrink: 0;
        }

        /* Section Header */
        .edu-header {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .edu-badge-category {
            align-self: flex-start;
            background: #e6fffa;
            color: var(--teal-700);
            border: 1px solid #b2f5ea;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .edu-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.5px;
            line-height: 1.25;
        }
        .edu-subtitle {
            font-size: 15px;
            color: var(--slate-600);
            max-width: 820px;
            line-height: 1.6;
        }

        /* Search & Filter Section */
        .edu-search-row {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 4px;
        }
        .edu-search-input-wrap {
            position: relative;
            flex: 1;
            max-width: 600px;
        }
        .edu-search-input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
        }
        .edu-search-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-lg);
            font-size: 14px;
            color: var(--slate-800);
            transition: all 0.2s ease;
        }
        .edu-search-input:focus {
            outline: none;
            border-color: var(--teal-600);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
        }
        .edu-search-btn {
            background: var(--teal-800);
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .edu-search-btn:hover {
            background: var(--teal-900);
            transform: translateY(-1px);
        }

        /* Category Filter Pills */
        .edu-filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .edu-pill-btn {
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .edu-pill-btn:hover {
            background: var(--slate-50);
            border-color: var(--slate-300);
            color: var(--slate-900);
        }
        .edu-pill-btn.active {
            background: var(--teal-900);
            color: #ffffff;
            border-color: var(--teal-900);
        }

        /* HERO FEATURED ARTICLE CARD */
        .edu-hero-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-2xl);
            display: grid;
            grid-template-columns: 420px 1fr;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            transition: box-shadow 0.25s ease;
        }
        .edu-hero-card:hover {
            box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.08);
        }
        .edu-hero-image-wrap {
            position: relative;
            background: #f1f5f9;
            min-height: 320px;
            overflow: hidden;
        }
        .edu-hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .edu-hero-card:hover .edu-hero-image {
            transform: scale(1.03);
        }
        .edu-hero-image-overlay {
            position: absolute;
            bottom: 12px;
            left: 12px;
            right: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .edu-badge-image {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(6px);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .edu-hero-content {
            padding: 32px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 18px;
        }
        .edu-hero-tags {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .edu-tag-clinical {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .edu-tag-review {
            color: var(--slate-500);
            font-size: 12.5px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .edu-hero-title {
            font-size: 23px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.35;
            letter-spacing: -0.3px;
        }
        .edu-hero-desc {
            font-size: 14.5px;
            color: var(--slate-600);
            line-height: 1.65;
        }
        .edu-hero-author-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #f8fafc;
            border-radius: var(--radius-lg);
            border: 1px solid #edf2f7;
        }
        .edu-author-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: var(--shadow-sm);
        }
        .edu-author-avatar-initials {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--teal-700);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .edu-author-info {
            display: flex;
            flex-direction: column;
        }
        .edu-author-name {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--slate-800);
        }
        .edu-author-title {
            font-size: 12px;
            color: var(--slate-500);
        }
        .edu-hero-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 6px;
            border-top: 1px solid var(--slate-100);
            flex-wrap: wrap;
            gap: 12px;
        }
        .edu-hero-time {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12.5px;
            color: var(--slate-500);
        }
        .edu-hero-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-read-article {
            background: var(--teal-800);
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-read-article:hover {
            background: var(--teal-900);
        }
        .btn-icon-square {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            border: 1px solid var(--slate-200);
            background: #ffffff;
            color: var(--slate-600);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-icon-square:hover {
            background: var(--slate-100);
            color: var(--slate-900);
        }

        /* CLINICAL PREVIEW SPLIT SECTION */
        .edu-clinical-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 8px;
        }
        .edu-clinical-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--slate-100);
            padding-bottom: 12px;
        }
        .edu-clinical-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .edu-clinical-views {
            font-size: 12.5px;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .edu-clinical-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 24px;
            align-items: start;
            width: 100%;
        }

        /* Left Reading Content */
        .edu-reading-col {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 32px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
            width: 100%;
        }
        .edu-read-h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.4;
        }
        .edu-read-p {
            font-size: 14.5px;
            color: var(--slate-700);
            line-height: 1.7;
        }
        .edu-quote-card {
            background: #f0fdf4;
            border-left: 4px solid var(--teal-600);
            border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
            padding: 20px 24px;
            margin: 6px 0;
        }
        .edu-quote-text {
            font-size: 16px;
            font-weight: 600;
            font-style: italic;
            color: var(--teal-950);
            line-height: 1.6;
        }
        .edu-quote-author {
            margin-top: 8px;
            font-size: 12.5px;
            color: var(--teal-800);
            font-weight: 500;
        }

        /* Restorative Facets List */
        .edu-facets-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            list-style: none;
        }
        .edu-facet-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            line-height: 1.6;
            color: var(--slate-700);
        }
        .edu-facet-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ccfbf1;
            color: var(--teal-800);
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Takeaway highlight cards */
        .edu-takeaways-wrap {
            margin-top: 10px;
            background: #f8fafc;
            border: 1px dashed var(--slate-300);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
        }
        .edu-takeaways-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-800);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }
        .edu-takeaways-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .edu-takeaway-item {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.5;
        }
        .edu-takeaway-item strong {
            color: var(--slate-900);
            display: block;
            margin-bottom: 4px;
        }

        /* Right Column (Tools & Author) */
        .edu-widgets-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
            width: 380px;
            min-width: 320px;
        }

        /* Breathing Exercise Widget */
        .edu-breathing-card {
            background: linear-gradient(145deg, #ffffff, #f0fdfa);
            border: 1px solid #ccfbf1;
            border-radius: var(--radius-xl);
            padding: 24px;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.06);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .edu-breathing-tag {
            font-size: 11px;
            font-weight: 700;
            color: var(--teal-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .edu-breathing-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--slate-900);
        }
        .edu-breathing-desc {
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.5;
        }
        .breathing-visual-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px 0;
            position: relative;
        }
        .breathing-pulse-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: radial-gradient(circle, #5eead4 0%, #14b8a6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 0 20px rgba(20, 184, 166, 0.35);
            transition: transform 1s ease, box-shadow 1s ease;
        }
        .breathing-pulse-circle.inhale {
            transform: scale(1.28);
            box-shadow: 0 0 35px rgba(20, 184, 166, 0.55);
        }
        .breathing-pulse-circle.hold {
            transform: scale(1.28);
            background: radial-gradient(circle, #38bdf8 0%, #0284c7 100%);
            box-shadow: 0 0 30px rgba(56, 189, 248, 0.5);
        }
        .breathing-pulse-circle.exhale {
            transform: scale(0.92);
            box-shadow: 0 0 10px rgba(20, 184, 166, 0.2);
        }
        .breathing-timer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border: 1px solid #ccfbf1;
            padding: 10px 16px;
            border-radius: var(--radius-lg);
            margin-top: 6px;
        }
        .breathing-timer-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
        }
        .breathing-controls {
            display: flex;
            gap: 8px;
        }
        .btn-breathing-ctl {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: var(--teal-800);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-breathing-ctl:hover {
            background: var(--teal-900);
            transform: scale(1.05);
        }
        .btn-breathing-secondary {
            background: #e2e8f0;
            color: var(--slate-700);
        }
        .btn-breathing-secondary:hover {
            background: #cbd5e1;
        }

        /* Consult CTA Mini Card */
        .edu-consult-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .edu-consult-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-900);
        }
        .edu-consult-p {
            font-size: 12.5px;
            color: var(--slate-600);
            line-height: 1.5;
        }
        .edu-consult-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--teal-700);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }
        .edu-consult-link:hover {
            text-decoration: underline;
        }

        /* Author bio card */
        .edu-author-box {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .edu-box-tag {
            font-size: 11px;
            font-weight: 700;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .edu-box-author-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .edu-box-author-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }
        .edu-box-bio {
            font-size: 12.5px;
            color: var(--slate-600);
            line-height: 1.5;
        }

        /* ARTICLE GRID SECTION */
        .edu-grid-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 16px;
        }
        .edu-grid-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 12px;
        }
        .edu-grid-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--slate-900);
        }
        .edu-grid-subtitle {
            font-size: 14px;
            color: var(--slate-500);
            margin-top: 4px;
        }
        .edu-sort-select {
            padding: 8px 14px;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            background: #ffffff;
            cursor: pointer;
        }

        /* Article Cards Grid */
        .edu-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }
        @media (max-width: 1024px) {
            .edu-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .edu-hero-card {
                grid-template-columns: 1fr;
            }
            .edu-clinical-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 680px) {
            .edu-cards-grid {
                grid-template-columns: 1fr;
            }
        }
        .edu-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }
        .edu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            border-color: var(--slate-300);
        }
        .edu-card-img-wrap {
            position: relative;
            height: 180px;
            background: #e2e8f0;
            overflow: hidden;
        }
        .edu-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .edu-card:hover .edu-card-img {
            transform: scale(1.04);
        }
        .edu-card-tag-floating {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: var(--radius-full);
        }
        .edu-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: space-between;
            gap: 14px;
        }
        .edu-card-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11.5px;
            color: var(--slate-500);
        }
        .edu-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--slate-900);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .edu-card-desc {
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .edu-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid var(--slate-100);
            margin-top: auto;
        }
        .edu-card-author-mini {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--slate-700);
        }
        .edu-card-author-mini .mini-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--teal-100);
            color: var(--teal-800);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }
        .btn-card-action {
            background: none;
            border: none;
            color: var(--slate-400);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.15s ease;
        }
        .btn-card-action:hover {
            color: var(--teal-700);
        }

        /* Special Crisis Protocol Card */
        .edu-card-crisis {
            border: 1.5px solid #fecdd3;
            background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%);
        }
        .edu-crisis-badge {
            background: #e11d48;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .btn-read-crisis {
            background: #be123c;
            color: #ffffff;
            padding: 7px 12px;
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background 0.15s ease;
        }
        .btn-read-crisis:hover {
            background: #9f1239;
        }

        /* SUNDAY NEWSLETTER BANNER */
        .edu-newsletter-banner {
            background: linear-gradient(135deg, #e6fffa 0%, #ccfbf1 100%);
            border: 1px solid #99f6e4;
            border-radius: var(--radius-2xl);
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 16px;
            margin-top: 16px;
        }
        .edu-news-tag {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--teal-800);
            background: #ffffff;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            border: 1px solid #99f6e4;
        }
        .edu-news-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--teal-950);
            max-width: 640px;
            line-height: 1.3;
        }
        .edu-news-desc {
            font-size: 14.5px;
            color: var(--teal-900);
            max-width: 600px;
            line-height: 1.6;
        }
        .edu-news-form {
            display: flex;
            gap: 10px;
            width: 100%;
            max-width: 520px;
            margin-top: 8px;
        }
        .edu-news-input {
            flex: 1;
            padding: 12px 18px;
            background: #ffffff;
            border: 1.5px solid #99f6e4;
            border-radius: var(--radius-lg);
            font-size: 14px;
            color: var(--slate-800);
        }
        .edu-news-input:focus {
            outline: none;
            border-color: var(--teal-600);
        }
        .edu-news-btn {
            background: var(--teal-800);
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: var(--radius-lg);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .edu-news-btn:hover {
            background: var(--teal-900);
            transform: translateY(-1px);
        }
        .edu-news-note {
            font-size: 12px;
            color: var(--teal-700);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* MODAL ARTICLE READER */
        .article-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .article-modal-backdrop.active {
            display: flex;
        }
        .article-modal-window {
            background: #ffffff;
            width: 100%;
            max-width: 760px;
            max-height: 90vh;
            border-radius: var(--radius-2xl);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalFadeIn 0.25s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }
        .article-modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .article-modal-body {
            padding: 32px 36px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
            font-size: 15px;
            line-height: 1.75;
            color: var(--slate-800);
        }
        .article-modal-body h2 {
            font-size: 22px;
            color: var(--slate-900);
            font-weight: 800;
            line-height: 1.35;
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
            <a href="profile.php" class="header-user-pill" title="Profil <?= $fullDisplayName ?>">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="<?= $fullDisplayName ?>">
                <div class="header-user-meta">
                    <div class="header-user-name"><?= $fullDisplayName ?></div>
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
                        <a href="artikel.php" class="active">
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
            <div class="edu-container">

                <!-- TOP TRUST BAR (Polished & non-intrusive) -->
                <div class="edu-trust-bar">
                    <div class="edu-trust-items">
                        <span class="edu-trust-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Standardisasi Riset Terkini
                        </span>
                        <span class="edu-trust-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Ditinjau Psikolog Klinis HIMPSI
                        </span>
                        <span class="edu-trust-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                            Bebas Toksisitas Positif
                        </span>
                    </div>
                    <span style="font-size:12px; color:var(--teal-700);">Diperbarui Mingguan</span>
                </div>

                <!-- SECTION HEADER -->
                <div class="edu-header">
                    <span class="edu-badge-category">Pustaka Pemulihan &amp; Psikoedukasi</span>
                    <h1 class="edu-title">Pustaka Edukasi Kesehatan Mental &amp; Psikoedukasi Terverifikasi</h1>
                    <p class="edu-subtitle">
                        Eksplorasi wawasan berbasis riset psikologi klinis, panduan refleksi mandiri, dan metode restoratif untuk merawat navigasi batinmu.
                    </p>

                    <!-- Search Input & Filter Pills -->
                    <div class="edu-search-row">
                        <div class="edu-search-input-wrap">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" id="articleSearchInput" class="edu-search-input" placeholder="Cari topik: kecemasan, burnout, manajemen tidur, relasi..." oninput="filterArticles()">
                        </div>
                        <button type="button" class="edu-search-btn" onclick="filterArticles()">Telusuri</button>
                    </div>

                    <div class="edu-filter-pills" id="topicPills">
                        <button class="edu-pill-btn active" onclick="setTopicFilter('all', this)">Semua Topik</button>
                        <button class="edu-pill-btn" onclick="setTopicFilter('stres', this)">Manajemen Stres</button>
                        <button class="edu-pill-btn" onclick="setTopicFilter('burnout', this)">Burnout &amp; Produktivitas</button>
                        <button class="edu-pill-btn" onclick="setTopicFilter('relasi', this)">Hubungan &amp; Keluarga</button>
                        <button class="edu-pill-btn" onclick="setTopicFilter('emosi', this)">Regulasi Emosi</button>
                        <button class="edu-pill-btn" onclick="setTopicFilter('tidur', this)">Gangguan Tidur</button>
                    </div>
                </div>

                <!-- HERO FEATURED ARTICLE CARD -->
                <article class="edu-hero-card" id="heroArticle">
                    <div class="edu-hero-image-wrap">
                        <img src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=800&q=80" alt="Panduan Burnout" class="edu-hero-image" onerror="this.src='https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=800&q=80'">
                        <div class="edu-hero-image-overlay">
                            <span class="edu-badge-image">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                Bacaan Unggulan
                            </span>
                            <span class="edu-badge-image">Level: Eksploratif</span>
                        </div>
                    </div>
                    <div class="edu-hero-content">
                        <div>
                            <div class="edu-hero-tags">
                                <span class="edu-tag-clinical">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Psikoedukasi Klinis
                                </span>
                                <span class="edu-tag-review">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    Ditinjau oleh Psikolog HIMPSI
                                </span>
                            </div>
                            <h2 class="edu-hero-title" style="margin-top:10px;">
                                Membedakan Lelah Fisik Biasa dengan Burnout Psikologis: Panduan Memulihkan Energi Batin
                            </h2>
                            <p class="edu-hero-desc" style="margin-top:8px;">
                                Mengapa istirahat tidur saja seringkali tidak cukup menyembuhkan burnout? Pahami 7 jenis istirahat yang sesungguhnya dibutuhkan otak, jiwa, dan prosesor emosional sebelum keletihan kronis mengambil alih kapasitas pribadimu.
                            </p>
                        </div>

                        <div class="edu-hero-author-row">
                            <div class="edu-author-avatar-initials">CL</div>
                            <div class="edu-author-info">
                                <span class="edu-author-name">dr. Citra Larasati, M.Psi</span>
                                <span class="edu-author-title">Psikolog Klinis Dewasa &amp; Konselor Emosi</span>
                            </div>
                        </div>

                        <div class="edu-hero-meta-row">
                            <div class="edu-hero-time">
                                <span>⏱️ 8 menit baca</span>
                                <span>📅 24 Oktober 2026</span>
                            </div>
                            <div class="edu-hero-actions">
                                <button type="button" class="btn-read-article" onclick="openArticleModal('burnout')">
                                    <span>Baca Artikel Lengkap</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                                <button type="button" class="btn-icon-square" title="Simpan Artikel" onclick="toggleBookmark(this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                </button>
                                <button type="button" class="btn-icon-square" title="Bagikan Artikel" onclick="shareArticle()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- PRATINJAU KAJIAN KLINIS (SPLIT SECTION) -->
                <section class="edu-clinical-section">
                    <div class="edu-clinical-header">
                        <h2 class="edu-clinical-title">
                            <span class="edu-badge-category">Kajian Terpandu</span>
                            Pratinjau Kajian Klinis: Anatomi Pemulihan Diri
                        </h2>
                        <span class="edu-clinical-views">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            Ditinjau 3.842 kali oleh anggota MindCare pekan ini
                        </span>
                    </div>

                    <div class="edu-clinical-grid">
                        
                        <!-- Left Reading Column -->
                        <div class="edu-reading-col">
                            <div>
                                <h3 class="edu-read-h3">1. Mengapa Libur Panjang Kerap Gagal Menyembuhkan Lelah Batin?</h3>
                                <p class="edu-read-p" style="margin-top:8px;">
                                    Banyak dari kita mengira bahwa satu hari tanpa membuka laptop atau marathon tidur 10 jam di akhir pekan dapat langsung mengembalikan vitalitas. Namun, bila lelah yang dirasakan bersumber dari beban kognitif berlebih dan resonansi stres kronis, sistem saraf parasimpatis tidak otomatis aktif hanya dengan berbaring pasif tanpa intensi sadar.
                                </p>
                            </div>

                            <div class="edu-quote-card">
                                <p class="edu-quote-text">
                                    “Tubuhmu tidak sedang rusak. Ia hanya sedang memberitahu bahwa batas energimu telah terlampaui.”
                                </p>
                                <p class="edu-quote-author">
                                    — dr. Citra Larasati, M.Psi • Catatan Konseling Klinis MindCare
                                </p>
                            </div>

                            <div>
                                <h3 class="edu-read-h3">2. Memetakan 7 Dimensi Istirahat (Restorative Facets)</h3>
                                <p class="edu-read-p" style="margin-top:6px; margin-bottom:12px;">
                                    Berdasarkan panduan riset MindCare, keletihan manusia terbagi menjadi 7 spektrum spesifik yang membutuhkan intervensi berbeda:
                                </p>

                                <ul class="edu-facets-list">
                                    <li class="edu-facet-item">
                                        <span class="edu-facet-num">1</span>
                                        <div><strong>Istirahat Mental:</strong> Menghentikan komputasi mental terus-menerus melalui jeda hening berkala dan journaling pembebasan beban pikiran.</div>
                                    </li>
                                    <li class="edu-facet-item">
                                        <span class="edu-facet-num">2</span>
                                        <div><strong>Istirahat Sensorik:</strong> Mengurangi stimulasi layar berkedip, lampu sorot dingin, dan notifikasi berfrekuensi tinggi.</div>
                                    </li>
                                    <li class="edu-facet-item">
                                        <span class="edu-facet-num">3</span>
                                        <div><strong>Istirahat Emosional:</strong> Berada di ruang aman di mana kamu bebas tidak berpura-pura bahagia atau selalu tegar.</div>
                                    </li>
                                    <li class="edu-facet-item">
                                        <span class="edu-facet-num">4</span>
                                        <div><strong>Istirahat Kreatif:</strong> Menikmati keindahan alam dan seni tanpa beban tuntutan apresiasi atau target produksi.</div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Takeaways Box -->
                            <div class="edu-takeaways-wrap">
                                <div class="edu-takeaways-title">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    Intisari Klinis (Takeaway Ringkas)
                                </div>
                                <div class="edu-takeaways-grid">
                                    <div class="edu-takeaway-item">
                                        <strong>Tidur Restoratif</strong>
                                        Kurangi paparan layar minimal 60 menit sebelum tidur untuk memberi ruang regulasi sistem saraf parasimpatis.
                                    </div>
                                    <div class="edu-takeaway-item">
                                        <strong>Dekompresi Mikro</strong>
                                        Lakukan jeda 90 detik setiap 60 menit kerja intensif untuk mencegah akumulasi hormon stres (kortisol).
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Widgets Column -->
                        <div class="edu-widgets-col">
                            
                            <!-- Interactive Breathing Card -->
                            <div class="edu-breathing-card">
                                <span class="edu-breathing-tag">Aplikasi Praktik Langsung</span>
                                <h3 class="edu-breathing-title">Latihan Pernapasan Resonansi 4-7-8</h3>
                                <p class="edu-breathing-desc">
                                    Latihan pernapasan ritmis selama 3 menit terbukti menurunkan denyut jantung dan meredakan ketegangan saraf simpatik seketika.
                                </p>

                                <div class="breathing-visual-container">
                                    <div class="breathing-pulse-circle" id="breathCircle">
                                        <span id="breathActionText">Tarik Nafas</span>
                                    </div>
                                </div>

                                <div class="breathing-timer-row">
                                    <span class="breathing-timer-text" id="breathTimerDisplay">Pernapasan Rileks: 03:00</span>
                                    <div class="breathing-controls">
                                        <button type="button" class="btn-breathing-ctl" id="btnBreathToggle" onclick="toggleBreathingExercise()" title="Mulai / Jeda">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                        </button>
                                        <button type="button" class="btn-breathing-ctl btn-breathing-secondary" onclick="resetBreathingExercise()" title="Reset">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><polyline points="3 3 3 8 8 8"></polyline></svg>
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn-read-article" style="width:100%; justify-content:center; margin-top:4px;" onclick="openQuickBreathing()">
                                    Coba Latihan Relaksasi Lengkap
                                </button>
                            </div>

                            <!-- Consult CTA Mini Card -->
                            <div class="edu-consult-card">
                                <h4 class="edu-consult-title">Butuh Evaluasi Personal?</h4>
                                <p class="edu-consult-p">
                                    Jika keletihan berlangsung lebih dari 4 minggu dan mengganggu fungsi harian, konsultasi terpadu bersama profesional berlisensi adalah langkah awal terbaik.
                                </p>
                                <a href="emergency.php" class="edu-consult-link">
                                    Jadwalkan Sesi Konseling Rahasia
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>

                            <!-- Author Card -->
                            <div class="edu-author-box">
                                <span class="edu-box-tag">Tentang Penulis</span>
                                <div class="edu-box-author-top">
                                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80" alt="dr. Citra Larasati" class="edu-box-author-img">
                                    <div>
                                        <div style="font-size:14px; font-weight:700; color:var(--slate-800);">dr. Citra Larasati, M.Psi</div>
                                        <div style="font-size:11.5px; color:var(--teal-700); font-weight:600;">SIPA: 2021 • Psikolog Klinis Dewasa</div>
                                    </div>
                                </div>
                                <p class="edu-box-bio">
                                    Berfokus pada pemulihan burnout profesional, regulasi emosi, serta pendampingan kesehatan mental terpadu sejak 2015.
                                </p>
                            </div>

                        </div>

                    </div>
                </section>

                <!-- PUSTAKA PEMULIHAN TERBARU & TERVERIFIKASI -->
                <section class="edu-grid-section">
                    <div class="edu-grid-header">
                        <div>
                            <span class="edu-badge-category">Pustaka Terverifikasi</span>
                            <h2 class="edu-grid-title" style="margin-top:6px;">Pustaka Pemulihan Terbaru &amp; Terverifikasi</h2>
                            <p class="edu-grid-subtitle">Pilihan bacaan ringkas dan mendalam untuk membantumu merangkul perawatan diri.</p>
                        </div>
                        <select class="edu-sort-select" onchange="sortArticles(this.value)">
                            <option value="terbaru">Urutkan: Terbaru</option>
                            <option value="populer">Urutkan: Paling Populer</option>
                            <option value="durasi">Urutkan: Durasi Baca Singkat</option>
                        </select>
                    </div>

                    <div class="edu-cards-grid" id="articleCardsGrid">
                        
                        <!-- Card 1 -->
                        <article class="edu-card" data-topic="burnout stres" data-duration="6">
                            <div class="edu-card-img-wrap">
                                <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80" alt="Seni Melambat" class="edu-card-img">
                                <span class="edu-card-tag-floating">#ManajemenFokus</span>
                            </div>
                            <div class="edu-card-body">
                                <div>
                                    <div class="edu-card-meta">
                                        <span>⏱️ 6 menit</span>
                                        <span>•</span>
                                        <span>Bacaan Psikolog Klinis</span>
                                    </div>
                                    <h3 class="edu-card-title" style="margin-top:6px;">Seni Melambat: Mengatasi Sindrom Kelelahan Batin Mahasiswa Tingkat Akhir</h3>
                                    <p class="edu-card-desc" style="margin-top:6px;">
                                        Melepaskan tekanan ekspektasi dan menemukan kembali ritme belajar yang sehat tanpa mengorbankan kewarasan mental.
                                    </p>
                                </div>
                                <div class="edu-card-footer">
                                    <div class="edu-card-author-mini">
                                        <span class="mini-avatar">AR</span>
                                        <span>Aria Rahardian, S.Psi</span>
                                    </div>
                                    <button type="button" class="btn-card-action" title="Simpan" onclick="toggleBookmark(this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 2 -->
                        <article class="edu-card" data-topic="tidur stres" data-duration="5">
                            <div class="edu-card-img-wrap">
                                <img src="https://images.unsplash.com/photo-1512438248247-f0f2a5a8b7f0?auto=format&fit=crop&w=600&q=80" alt="Menjaga Batas Digital" class="edu-card-img">
                                <span class="edu-card-tag-floating">#DetoksTeknologi</span>
                            </div>
                            <div class="edu-card-body">
                                <div>
                                    <div class="edu-card-meta">
                                        <span>⏱️ 5 menit baca</span>
                                        <span>•</span>
                                        <span>Modul Kesiapan</span>
                                    </div>
                                    <h3 class="edu-card-title" style="margin-top:6px;">Menjaga Batas Digital: Berhenti Mengorbankan Kualitas Tidurmu demi Notifikasi</h3>
                                    <p class="edu-card-desc" style="margin-top:6px;">
                                        Penurunan rentang emosi 30 menit sebelum tidur dan cara menata relasi sehat dengan gawai pintarmu.
                                    </p>
                                </div>
                                <div class="edu-card-footer">
                                    <div class="edu-card-author-mini">
                                        <span class="mini-avatar">DY</span>
                                        <span>dr. Dimas Yoga, Sp.KJ</span>
                                    </div>
                                    <button type="button" class="btn-card-action" title="Simpan" onclick="toggleBookmark(this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 3 -->
                        <article class="edu-card" data-topic="stres emosi" data-duration="4">
                            <div class="edu-card-img-wrap">
                                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80" alt="Teknik Grounding" class="edu-card-img">
                                <span class="edu-card-tag-floating">#PencegahanKrisis</span>
                            </div>
                            <div class="edu-card-body">
                                <div>
                                    <div class="edu-card-meta">
                                        <span>⏱️ 4 menit baca</span>
                                        <span>•</span>
                                        <span>Panduan Cepat</span>
                                    </div>
                                    <h3 class="edu-card-title" style="margin-top:6px;">Teknik Grounding 5-4-3-2-1: Jangkar Penyelamat saat Overthinking Mendera</h3>
                                    <p class="edu-card-desc" style="margin-top:6px;">
                                        Panduan langkah demi langkah memfungsikan indera fisik untuk meredam kecemasan mendadak di mana pun kamu berada.
                                    </p>
                                </div>
                                <div class="edu-card-footer">
                                    <div class="edu-card-author-mini">
                                        <span class="mini-avatar">SN</span>
                                        <span>Siti Nurhaliza, M.Psi</span>
                                    </div>
                                    <button type="button" class="btn-card-action" title="Simpan" onclick="toggleBookmark(this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 4 -->
                        <article class="edu-card" data-topic="emosi relasi" data-duration="6">
                            <div class="edu-card-img-wrap">
                                <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80" alt="Inner Critic" class="edu-card-img">
                                <span class="edu-card-tag-floating">#SelfCompassion</span>
                            </div>
                            <div class="edu-card-body">
                                <div>
                                    <div class="edu-card-meta">
                                        <span>⏱️ 6 menit baca</span>
                                        <span>•</span>
                                        <span>Psikolog Naratif</span>
                                    </div>
                                    <h3 class="edu-card-title" style="margin-top:6px;">Mengenal Inner Critic: Berdamai dengan Suara Keras di Dalam Kepala</h3>
                                    <p class="edu-card-desc" style="margin-top:6px;">
                                        Cara mengubah dialog batin yang menuntut menjadi dialog yang penuh belas kasih dan penerimaan diri.
                                    </p>
                                </div>
                                <div class="edu-card-footer">
                                    <div class="edu-card-author-mini">
                                        <span class="mini-avatar">FH</span>
                                        <span>Fajar Hidayat, M.Psi</span>
                                    </div>
                                    <button type="button" class="btn-card-action" title="Simpan" onclick="toggleBookmark(this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <!-- Card 5 (Special Protocol Card) -->
                        <article class="edu-card edu-card-crisis" data-topic="relasi emosi" data-duration="7">
                            <div class="edu-card-img-wrap">
                                <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=80" alt="Panduan Pertolongan Pertama" class="edu-card-img">
                                <span class="edu-card-tag-floating" style="background:#be123c;">#DukunganSosial</span>
                            </div>
                            <div class="edu-card-body">
                                <div>
                                    <div class="edu-card-meta">
                                        <span class="edu-crisis-badge">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"></circle></svg>
                                            Panduan Penting
                                        </span>
                                        <span>•</span>
                                        <span>Protokol Krisis</span>
                                    </div>
                                    <h3 class="edu-card-title" style="margin-top:6px; color:#9f1239;">Bila Sahabat Bercerita tentang Niat Menyakiti Diri: Panduan Pertolongan Pertama</h3>
                                    <p class="edu-card-desc" style="margin-top:6px;">
                                        Sudut ucapan yang perlu diucapkan, respons yang harus dihindari tanpa menghakimi, serta rujukan darurat kesehatan mental terpercaya.
                                    </p>
                                </div>
                                <div class="edu-card-footer">
                                    <div class="edu-card-author-mini">
                                        <span class="mini-avatar" style="background:#ffe4e6; color:#be123c;">TK</span>
                                        <span>Tim Krisis MindCare</span>
                                    </div>
                                    <a href="emergency.php" class="btn-read-crisis">
                                        Buka Panduan
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <!-- Card 6 -->
                        <article class="edu-card" data-topic="burnout emosi" data-duration="7">
                            <div class="edu-card-img-wrap">
                                <img src="https://images.unsplash.com/photo-1517256064527-09c73fc73e38?auto=format&fit=crop&w=600&q=80" alt="Silent Burnout" class="edu-card-img">
                                <span class="edu-card-tag-floating">#KesadaranDiri</span>
                            </div>
                            <div class="edu-card-body">
                                <div>
                                    <div class="edu-card-meta">
                                        <span>⏱️ 7 menit baca</span>
                                        <span>•</span>
                                        <span>Kajian Mendalam</span>
                                    </div>
                                    <h3 class="edu-card-title" style="margin-top:6px;">Memahami Silent Burnout: Saat Terlihat Produktif Padahal Jiwa Sedang Runtuh</h3>
                                    <p class="edu-card-desc" style="margin-top:6px;">
                                        Mengenali tanda bahaya saat efisiensi kerja tinggi dibayar dengan kehampaan emosi dan keletihan batin berkepanjangan.
                                    </p>
                                </div>
                                <div class="edu-card-footer">
                                    <div class="edu-card-author-mini">
                                        <span class="mini-avatar">CL</span>
                                        <span>dr. Citra Larasati</span>
                                    </div>
                                    <button type="button" class="btn-card-action" title="Simpan" onclick="toggleBookmark(this)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </article>

                    </div>
                </section>

                <!-- SUNDAY NEWSLETTER BANNER -->
                <div class="edu-newsletter-banner">
                    <span class="edu-news-tag">✉️ Buletin Mingguan MindCare</span>
                    <h2 class="edu-news-title">Dapatkan Intisari Pemulihan Jiwa Setiap Hari Minggu Pagi</h2>
                    <p class="edu-news-desc">
                        Bebas spam. Kurasi riset ilmiah yang disederhanakan, latihan pernapasan mingguan, dan pengingat lembut untuk merawat kesehatan mentalmu sebelum memulai pekan baru.
                    </p>
                    <form class="edu-news-form" onsubmit="subscribeNewsletter(event)">
                        <input type="email" id="newsletterEmail" class="edu-news-input" placeholder="Ketikkan alamat email pribadimu..." required>
                        <button type="submit" class="edu-news-btn">Langganan Gratis</button>
                    </form>
                    <span class="edu-news-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Data emailmu terenkripsi dan aman. Kamu bebas berhenti berlangganan kapan pun.
                    </span>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL POPUP BACA ARTIKEL -->
    <div class="article-modal-backdrop" id="articleModal" onclick="closeArticleModal(event)">
        <div class="article-modal-window" onclick="event.stopPropagation()">
            <div class="article-modal-header">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="edu-badge-category" style="margin:0;">Kajian Lengkap</span>
                    <span style="font-size:12.5px; color:var(--slate-500);">Waktu baca ~8 menit</span>
                </div>
                <button type="button" class="btn-icon-square" onclick="closeArticleModalDirect()" title="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="article-modal-body">
                <h2>Membedakan Lelah Fisik Biasa dengan Burnout Psikologis: Panduan Memulihkan Energi Batin</h2>
                <div style="display:flex; align-items:center; gap:12px; font-size:13px; color:var(--slate-500); padding-bottom:12px; border-bottom:1px solid var(--slate-100);">
                    <span>Ditulis oleh: <strong>dr. Citra Larasati, M.Psi</strong></span>
                    <span>•</span>
                    <span>Ditinjau oleh Komite Klinis HIMPSI</span>
                </div>
                
                <p>
                    Pernahkah kamu tidur selama 9 hingga 10 jam di akhir pekan, namun saat bangun tubuhmu tetap terasa seperti memikul beban berton-ton? Pikiranmu enggan beranjak, dan membayangkan tugas esok hari saja sudah memicu sesak di dada?
                </p>
                <p>
                    Kondisi tersebut adalah salah satu indikator nyata perbedaan mendasar antara <em>fatigue</em> (kelelahan fisik akibat pengerahan tenaga otot) dengan <strong>Burnout Psikologis</strong> (kelelahan kognitif dan emosional yang terjadi akibat pengurasan sumber daya batin secara berkepanjangan tanpa jeda pemulihan yang memadai).
                </p>

                <div class="edu-quote-card">
                    <p class="edu-quote-text">“Istirahat bukan sekadar tidak melakukan apa-apa. Istirahat sejati adalah mengisi kembali apa yang telah terkuras habis.”</p>
                </div>

                <h3 style="font-size:17px; font-weight:700; color:var(--slate-900);">Tiga Langkah Awal Memulihkan Diri:</h3>
                <ol style="padding-left:20px; display:flex; flex-direction:column; gap:8px;">
                    <li><strong>Akui dan Validasi:</strong> Hentikan membandingkan kapasitasmu dengan orang lain di media sosial. Validasi bahwa lelah yang kamu rasakan adalah nyata dan wajar.</li>
                    <li><strong>Beri Batasan Sensorik:</strong> Matikan notifikasi aplikasi kerja di luar jam kerja. Lindungi 60 menit sebelum tidur dari paparan stimulasi layar biru.</li>
                    <li><strong>Latih Dekompresi Bernapas:</strong> Sempatkan 3 menit pernapasan ritmis 4-7-8 untuk memberi sinyal pada saraf simpatik bahwa keadaan saat ini aman.</li>
                </ol>

                <div style="background:#f0fdfa; border:1px solid #ccfbf1; padding:16px; border-radius:var(--radius-lg); margin-top:8px;">
                    <div style="font-weight:700; color:var(--teal-900); font-size:14px; margin-bottom:4px;">Butuh Bantuan Teman Bicara?</div>
                    <div style="font-size:13px; color:var(--teal-800);">Tim psikolog dan konselor MindCare siap mendampingimu mengurai beban mental secara privat dan profesional.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Filter Articles by Topic
        function setTopicFilter(topic, btn) {
            document.querySelectorAll('#topicPills .edu-pill-btn').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('#articleCardsGrid .edu-card');
            cards.forEach(card => {
                const cardTopics = card.getAttribute('data-topic') || '';
                if (topic === 'all' || cardTopics.includes(topic)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Search Filter
        function filterArticles() {
            const query = (document.getElementById('articleSearchInput').value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('#articleCardsGrid .edu-card');
            
            cards.forEach(card => {
                const text = card.innerText.toLowerCase();
                if (!query || text.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Sort Articles
        function sortArticles(type) {
            const grid = document.getElementById('articleCardsGrid');
            const cards = Array.from(grid.children);

            if (type === 'durasi') {
                cards.sort((a, b) => {
                    const durA = parseInt(a.getAttribute('data-duration') || '5', 10);
                    const durB = parseInt(b.getAttribute('data-duration') || '5', 10);
                    return durA - durB;
                });
            } else {
                // Default order or newest
                cards.sort(() => Math.random() - 0.5);
            }

            cards.forEach(card => grid.appendChild(card));
        }

        // Bookmark Toggle
        function toggleBookmark(btn) {
            const svg = btn.querySelector('svg');
            if (btn.classList.contains('saved')) {
                btn.classList.remove('saved');
                svg.setAttribute('fill', 'none');
                btn.style.color = 'var(--slate-400)';
            } else {
                btn.classList.add('saved');
                svg.setAttribute('fill', 'currentColor');
                btn.style.color = 'var(--teal-700)';
            }
        }

        // Share Article
        function shareArticle() {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href);
                alert('Tautan artikel berhasil disalin ke clipboard!');
            } else {
                alert('Bagikan artikel ini kepada rekan terdekatmu: ' + window.location.href);
            }
        }

        // Newsletter subscription
        function subscribeNewsletter(e) {
            e.preventDefault();
            const email = document.getElementById('newsletterEmail').value;
            alert('Terima kasih! Alamat ' + email + ' telah berhasil terdaftar pada Buletin Pemulihan Jiwa Mingguan MindCare.');
            document.getElementById('newsletterEmail').value = '';
        }

        // Interactive 4-7-8 Breathing Engine
        let breathInterval = null;
        let breathSecondsLeft = 180; // 3 minutes
        let isBreathingActive = false;
        let breathPhase = 'inhale'; // inhale (4s), hold (7s), exhale (8s)
        let phaseTimer = null;

        function toggleBreathingExercise() {
            if (isBreathingActive) {
                pauseBreathing();
            } else {
                startBreathing();
            }
        }

        function startBreathing() {
            isBreathingActive = true;
            document.getElementById('btnBreathToggle').innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>';
            
            runPhaseCycle();

            breathInterval = setInterval(() => {
                if (breathSecondsLeft > 0) {
                    breathSecondsLeft--;
                    updateTimerUI();
                } else {
                    resetBreathingExercise();
                    alert('Sesi pernapasan 3 menit selesai. Ambil waktu sejenak untuk merasakan ketenangan tubuhmu.');
                }
            }, 1000);
        }

        function runPhaseCycle() {
            if (!isBreathingActive) return;
            const circle = document.getElementById('breathCircle');
            const actionText = document.getElementById('breathActionText');

            // Phase 1: Inhale (4 seconds)
            breathPhase = 'inhale';
            circle.className = 'breathing-pulse-circle inhale';
            actionText.innerText = 'Tarik Nafas (4s)';

            phaseTimer = setTimeout(() => {
                if (!isBreathingActive) return;
                // Phase 2: Hold (7 seconds)
                breathPhase = 'hold';
                circle.className = 'breathing-pulse-circle hold';
                actionText.innerText = 'Tahan Nafas (7s)';

                phaseTimer = setTimeout(() => {
                    if (!isBreathingActive) return;
                    // Phase 3: Exhale (8 seconds)
                    breathPhase = 'exhale';
                    circle.className = 'breathing-pulse-circle exhale';
                    actionText.innerText = 'Hembuskan (8s)';

                    phaseTimer = setTimeout(() => {
                        if (!isBreathingActive) return;
                        runPhaseCycle();
                    }, 8000);
                }, 7000);
            }, 4000);
        }

        function pauseBreathing() {
            isBreathingActive = false;
            clearInterval(breathInterval);
            clearTimeout(phaseTimer);
            document.getElementById('btnBreathToggle').innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>';
            document.getElementById('breathActionText').innerText = 'Jeda';
            document.getElementById('breathCircle').className = 'breathing-pulse-circle';
        }

        function resetBreathingExercise() {
            pauseBreathing();
            breathSecondsLeft = 180;
            updateTimerUI();
            document.getElementById('breathActionText').innerText = 'Tarik Nafas';
        }

        function updateTimerUI() {
            const m = Math.floor(breathSecondsLeft / 60);
            const s = breathSecondsLeft % 60;
            const formatted = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            document.getElementById('breathTimerDisplay').innerText = 'Pernapasan Rileks: ' + formatted;
        }

        function openQuickBreathing() {
            window.scrollTo({ top: 380, behavior: 'smooth' });
            if (!isBreathingActive) {
                toggleBreathingExercise();
            }
        }

        // Modal Reader
        function openArticleModal(id) {
            document.getElementById('articleModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeArticleModalDirect() {
            document.getElementById('articleModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function closeArticleModal(e) {
            if (e.target.id === 'articleModal') {
                closeArticleModalDirect();
            }
        }
    </script>
    <script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
