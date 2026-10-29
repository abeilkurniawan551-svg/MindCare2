<?php
session_start();

// Sesuai alur: jika user sudah login, langsung diarahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindCare - Ruang Pulih & Kesehatan Mental</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body class="landing-wrapper">


    <!-- PUBLIC HEADER & NAVIGATION -->
    <header class="public-navbar">
        <a href="index.php" class="brand-logo" title="MindCare - Ruang Pulih & Kesehatan Mental">
            <img src="logo-horizontal.png" alt="MindCare - Kesehatan Mental" class="brand-logo-img logo-desktop" style="height:42px;width:auto;max-width:200px;object-fit:contain;">
            <img src="logo-icon.png" alt="MindCare Logo" class="brand-logo-icon logo-mobile" style="height:38px;width:auto;object-fit:contain;">
        </a>

        <ul class="public-nav-links">
            <li><a href="index.php" class="active">Beranda</a></li>
            <li><a href="#layanan-klinis">Layanan Klinis</a></li>
        </ul>

        <div class="landing-nav-actions">
            <a href="login.php" class="btn-login-nav">
                <span>Masuk</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            </a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="landing-hero-section">
        <!-- Left Hero Presentation -->
        <div class="hero-left-content">
            <div class="hero-sdg-pill">
                <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;"></span>
                <span>SDG 3 • 100% Rahasia</span>
            </div>

            <h1 class="hero-landing-title">
                Mulai Peduli,<br>
                Mulai dari Dirimu.
            </h1>

            <p class="hero-landing-desc">
                Ruang aman digital untuk merawat kesehatan mentalmu dan terhubung dengan dukungan profesional.
            </p>

            <div class="hero-cta-group">
                <a href="register.php" class="btn-hero-primary">
                    <span>Mulai Sekarang</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="#fitur" class="btn-hero-outline">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <span>Jelajahi Fitur</span>
                </a>
            </div>
        </div>

        <!-- Right Interactive Emotion Check Card -->
        <div class="hero-right-card">
            <div class="hero-card-greeting">Halo, Kawan Cerita</div>
            <h2 class="hero-card-question">Bagaimana perasaanmu?</h2>
            <div class="hero-checkin-label">Check-In Emosi Cepat</div>

            <div class="hero-mood-pills-row">
                <div class="hero-mood-pill" onclick="selectMood(this, 'Cemas')">
                    😟
                    <span>Cemas</span>
                </div>
                <div class="hero-mood-pill" onclick="selectMood(this, 'Lelah')">
                    🥱
                    <span>Lelah</span>
                </div>
                <div class="hero-mood-pill" onclick="selectMood(this, 'Netral')">
                    😐
                    <span>Netral</span>
                </div>
                <div class="hero-mood-pill active" onclick="selectMood(this, 'Tenang & Damai')">
                    🌿
                    <span>Tenang</span>
                </div>
                <div class="hero-mood-pill" onclick="selectMood(this, 'Syukur')">
                    ✨
                    <span>Syukur</span>
                </div>
            </div>

            <div class="hero-stability-box">
                <div>
                    <div class="hero-stability-text">Stabilitas Emosi Minggu Ini</div>
                    <div style="font-size:11px;color:#16a34a;font-weight:700;">Keseimbangan naik +18%</div>
                </div>
                <div class="hero-stability-badge">
                    🔥 7 Hari Aktif
                </div>
            </div>

            <!-- Doctor availability mini card -->
            <div class="hero-psychologist-mini">
                <div class="hero-psychologist-left">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%230f766e'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%23134e4a'/></svg>" alt="dr. Nadya Paramita" class="hero-doc-avatar">
                    <div class="hero-doc-info">
                        <div class="name">dr. Nadya Paramita, M.Psi.</div>
                        <div class="slot">Slot terdekat: Hari ini 16:00 WIB</div>
                    </div>
                </div>
                <a href="login.php" class="btn-mini-book" style="text-decoration:none;">Pesan</a>
            </div>

            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--slate-500);justify-content:center;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Kerahasiaan Medis Terlindungi</span>
            </div>
        </div>
    </section>

    <!-- SECTION 2: ALUR PEMULIHAN TERPADU -->
    <section class="landing-section" style="background-color:#f8fafc;border-top:1px solid var(--slate-100);border-bottom:1px solid var(--slate-100);">
        <div class="landing-section-header">
            <span class="landing-section-prehead">ALUR PEMULIHAN TERPADU</span>
            <h2 class="landing-section-title">Perjalanan Kesejahteraanmu</h2>
            <p class="landing-section-sub">
                Langkah fleksibel dan personal untuk memulihkan keseimbangan mentalmu.
            </p>
        </div>

        <div class="process-steps-grid">
            <!-- Step 01 -->
            <div class="process-step-card">
                <div class="step-card-number">01</div>
                <h3 class="step-card-title">Kenali Dirimu</h3>
                <p class="step-card-desc">
                    Skrining mandiri 3 menit (PHQ-9 & GAD-7) dan jurnal mood harian.
                </p>
                <div class="step-card-pill">Akurat & Terstandar Klinis</div>
            </div>

            <!-- Step 02 -->
            <div class="process-step-card">
                <div class="step-card-number">02</div>
                <h3 class="step-card-title">Rawat Mental Harian</h3>
                <p class="step-card-desc">
                    Modul audio mindfulness, panduan pernapasan 4-7-8, dan artikel psikoedukasi.
                </p>
                <div class="step-card-pill">Latihan 5–15 Menit/Hari</div>
            </div>

            <!-- Step 03 -->
            <div class="process-step-card">
                <div class="step-card-number">03</div>
                <h3 class="step-card-title">Dapatkan Dukungan</h3>
                <p class="step-card-desc">
                    Curhat 24/7 bersama AI companion atau telekonsultasi dengan psikolog HIMPSI.
                </p>
                <div class="step-card-pill">Privasi Penuh Dijamin</div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: MOMEN HENING SEJENAK - PERNAPASAN RELAKSASI 4-7-8 -->
    <section class="landing-section">
        <div class="breathing-moment-box">
            <div class="breathing-left-content">
                <span class="landing-section-prehead" style="color:var(--teal-800);">M O M E N &nbsp; H E N I N G &nbsp; S E J E N A K</span>
                <h2 style="font-size:1.85rem;font-weight:800;color:var(--teal-950);letter-spacing:-0.02em;">Pernapasan Relaksasi 4–7–8</h2>
                <p style="font-size:13.5px;color:var(--teal-900);line-height:1.6;">
                    Tarik napas 4 hitungan, tahan 7, hembuskan 8.
                </p>
                <div style="display:flex;align-items:center;gap:14px;margin-top:6px;">
                    <button type="button" class="btn-hero-primary" style="padding:10px 22px;" onclick="startBreathingModal()">
                        <span>Mulai Latihan</span>
                    </button>
                    <span style="font-size:12px;color:var(--teal-800);font-weight:600;">Status: Santai</span>
                </div>
            </div>

            <div class="breathing-bubble-visual" id="breathingBubble">
                Napas Santai
            </div>
        </div>
    </section>

    <!-- SECTION 4: 7 FITUR UNTUK MENJAGA KESEIMBANGAN JIWA -->
    <section class="landing-section" id="fitur">
        <div class="landing-section-header">
            <span class="landing-section-prehead">LAYANAN LENGKAP TERPADU</span>
            <h2 class="landing-section-title">7 Fitur untuk Menjaga Keseimbangan Jiwa</h2>
            <p class="landing-section-sub">
                Pendekatan klinis dan teknologi mutakhir untuk kesehatan mentalmu.
            </p>
        </div>

        <div class="features-seven-grid">
            
            <!-- Fitur 1 -->
            <div class="feature-box-card">
                <h3 class="feature-box-title">Self Check Klinis</h3>
                <p class="feature-box-desc">
                    Deteksi dini stres dan kecemasan dengan instrumen tervalidasi (PHQ-9, GAD-7).
                </p>
                <a href="login.php" class="feature-box-link">Skrining Anonim 3 Menit →</a>
            </div>

            <!-- Fitur 2 -->
            <div class="feature-box-card">
                <h3 class="feature-box-title">Mood Tracker & Jurnal</h3>
                <p class="feature-box-desc">
                    Visualisasikan dinamika emosi dan pahami pola suasana hatimu.
                </p>
                <a href="login.php" class="feature-box-link">Grafik Tren Emosi →</a>
            </div>

            <!-- Fitur 3 -->
            <div class="feature-box-card">
                <h3 class="feature-box-title">Meditasi & Sleep Stories</h3>
                <p class="feature-box-desc">
                    Audio pemandu tidur, suara alam, dan panduan mindfulness.
                </p>
                <a href="login.php" class="feature-box-link">120+ Rekaman Audio →</a>
            </div>

            <!-- Fitur 4 (Large Card: AI Companion) -->
            <div class="feature-box-card span-two">
                <span class="step-card-pill" style="margin-bottom:4px;">Selalu Siaga 24 Jam</span>
                <h3 class="feature-box-title">MindCare AI Companion</h3>
                <p class="feature-box-desc">
                    Ruang katarsis AI yang hangat dan empatik, siap menemanimu kapan pun.
                </p>
                
                <div style="background-color:#ffffff;border:1px solid #ccfbf1;border-radius:var(--radius-lg);padding:14px;margin:8px 0;font-size:12.5px;color:#134e4a;font-style:italic;">
                    <strong>MindCare AI:</strong> "Aku mendengar perasaan lelahmu hari ini. Tarik napas sejenak, kamu sudah berusaha sebaik mungkin..."
                </div>

                <div style="display:flex;align-items:center;gap:12px;margin-top:auto;">
                    <a href="chatbot.php" class="btn-hero-primary" style="padding:10px 20px;font-size:13px;">
                        <span>Coba Ruang Cerita AI</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                    <span style="font-size:11.5px;color:var(--slate-500);">Gratis & Terjaga Privasinya</span>
                </div>
            </div>

            <!-- Fitur 5 -->
            <div class="feature-box-card">
                <h3 class="feature-box-title">Artikel Terverifikasi</h3>
                <p class="feature-box-desc">
                    Wawasan psikoedukasi berbasis sains dari praktisi berwenang.
                </p>
                <a href="#edukasi" class="feature-box-link">Baca Pustaka Mental →</a>
            </div>

            <!-- Fitur 6 -->
            <div class="feature-box-card">
                <h3 class="feature-box-title">Forum Komunitas Anonim</h3>
                <p class="feature-box-desc">
                    Berbagi pengalaman anonim dengan moderasi psikolog.
                </p>
                <a href="login.php" class="feature-box-link">Masuk Lingkar Dukungan →</a>
            </div>

            <!-- Fitur 7 (Double width / price highlight) -->
            <div class="feature-box-card span-two">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <h3 class="feature-box-title">Konsultasi Telepsikologi Berlisensi</h3>
                    <span style="font-size:12px;font-weight:700;color:var(--teal-800);">Mulai Rp 99.000 /sesi</span>
                </div>
                <p class="feature-box-desc">
                    Jadwal fleksibel, tarif ramah mahasiswa, pembayaran QRIS/GoPay/OVO.
                </p>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:auto;">
                    <a href="login.php" class="feature-box-link">Pilih Psikolog Tersedia →</a>
                    <span style="font-size:11px;color:var(--slate-500);">Durasi privat 60 menit</span>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION 5: TENAGA AHLI BERLISENSI - KONSELING PRIVAT PSIKOLOG HIMPSI -->
    <section class="landing-section" style="background-color:#f8fafc;border-top:1px solid var(--slate-100);border-bottom:1px solid var(--slate-100);" id="layanan-klinis">
        <div class="landing-section-header">
            <span class="landing-section-prehead">TENAGA AHLI BERLISENSI</span>
            <h2 class="landing-section-title">Konseling Privat Bersama Psikolog HIMPSI</h2>
            <p class="landing-section-sub">
                Konselor ber-STR aktif dengan pengalaman klinis terpercaya.
            </p>

            <div style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-full);padding:6px 18px;font-size:11.5px;color:#991b1b;margin-top:10px;display:flex;align-items:center;gap:8px;">
                <span><strong>Bukan Pengganti Diagnosa Medis Kritis:</strong> Untuk pendampingan psikologis dan intervensi awal. Kondisi darurat, hubungi faskes terdekat.</span>
                <span class="step-card-pill" style="background:#ffffff;color:#15803d;margin:0;">Terakreditasi HIMPSI</span>
            </div>
        </div>

        <div class="psychologists-team-grid">
            
            <!-- Psikolog 1 -->
            <div class="psychologist-team-card">
                <div class="psychologist-card-top">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%230f766e'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%23134e4a'/></svg>" alt="Arya Daniswara" class="psychologist-portrait-lg">
                    <div>
                        <div class="psy-rating-pill">★ 4.9 <span style="color:var(--slate-400);font-weight:normal;">(240+ ulasan)</span></div>
                        <div class="psy-info-name">Arya Daniswara, M.Psi.</div>
                        <div class="psy-info-sipp">Psikolog Klinis Dewasa • SIPP: 2021-0891-KL</div>
                    </div>
                </div>
                <div class="psy-focus-box">
                    <strong>Fokus:</strong> Burnout karier, kecemasan, quarter-life crisis, regulasi stres kerja.
                </div>
                <div class="psy-pricing-row">
                    <div>
                        <div class="psy-price-label">Tarif Konseling</div>
                        <div class="psy-price-value">Rp 129.000 <span style="font-size:11px;color:var(--slate-400);font-weight:normal;">/ 60 mnt</span></div>
                    </div>
                    <a href="login.php" class="btn-hero-primary" style="padding:8px 16px;font-size:12px;">Pilih Jadwal</a>
                </div>
            </div>

            <!-- Psikolog 2 -->
            <div class="psychologist-team-card">
                <div class="psychologist-card-top">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%23ea580c'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%230f766e'/></svg>" alt="Citra Larasati" class="psychologist-portrait-lg">
                    <div>
                        <div class="psy-rating-pill">★ 5.0 <span style="color:var(--slate-400);font-weight:normal;">(310+ ulasan)</span></div>
                        <div class="psy-info-name">Citra Larasati, M.Psi.</div>
                        <div class="psy-info-sipp">Psikolog Klinis & Hubungan • SIPP: 2019-1142-KL</div>
                    </div>
                </div>
                <div class="psy-focus-box">
                    <strong>Fokus:</strong> Hubungan relasional, trauma interpersonal, self-esteem, kecemasan.
                </div>
                <div class="psy-pricing-row">
                    <div>
                        <div class="psy-price-label">Tarif Konseling</div>
                        <div class="psy-price-value">Rp 119.000 <span style="font-size:11px;color:var(--slate-400);font-weight:normal;">/ 60 mnt</span></div>
                    </div>
                    <a href="login.php" class="btn-hero-primary" style="padding:8px 16px;font-size:12px;">Pilih Jadwal</a>
                </div>
            </div>

            <!-- Psikolog 3 -->
            <div class="psychologist-team-card">
                <div class="psychologist-card-top">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23fed7aa'/><circle cx='32' cy='24' r='12' fill='%232563eb'/><path d='M12 56c0-11 9-16 20-16s20 5 20 16' fill='%231e293b'/></svg>" alt="Fauzan Anshori" class="psychologist-portrait-lg">
                    <div>
                        <div class="psy-rating-pill">★ 4.9 <span style="color:var(--slate-400);font-weight:normal;">(185+ ulasan)</span></div>
                        <div class="psy-info-name">Fauzan Anshori, M.Psi.</div>
                        <div class="psy-info-sipp">Psikolog Klinis & Mindfulness • SIPP: 2018-0533-KL</div>
                    </div>
                </div>
                <div class="psy-focus-box">
                    <strong>Fokus:</strong> Gangguan tidur (insomnia), manajemen amarah, duka cita, depresi ringan.
                </div>
                <div class="psy-pricing-row">
                    <div>
                        <div class="psy-price-label">Tarif Konseling</div>
                        <div class="psy-price-value">Rp 139.000 <span style="font-size:11px;color:var(--slate-400);font-weight:normal;">/ 60 mnt</span></div>
                    </div>
                    <a href="login.php" class="btn-hero-primary" style="padding:8px 16px;font-size:12px;">Pilih Jadwal</a>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION 6: EDUKASI & SUARA KOMUNITAS -->
    <section class="landing-section" id="edukasi">
        <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:40px;">
            
            <!-- Left: Pustaka Pemulihan -->
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <div>
                        <span class="landing-section-prehead">EDUKASI BERBASIS BUKTI</span>
                        <h2 style="font-size:1.6rem;font-weight:800;color:var(--slate-900);">Pustaka Pemulihan</h2>
                    </div>
                    <a href="login.php" style="font-size:12.5px;color:var(--teal-700);font-weight:700;text-decoration:none;">Lihat Semua &gt;</a>
                </div>

                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="background:#ffffff;border:1px solid var(--slate-200);border-radius:var(--radius-lg);padding:18px;">
                        <span class="step-card-pill" style="font-size:10px;margin-bottom:6px;">Burnout & Karier • Bacaan 5 mnt</span>
                        <h3 style="font-size:14.5px;font-weight:800;color:var(--slate-900);margin-bottom:4px;">Mengenali Tanda Burnout di Tempat Kerja Sebelum Terlambat</h3>
                        <p style="font-size:12.5px;color:var(--slate-600);line-height:1.5;">
                            Kenali beda kelelahan biasa dengan sindrom burnout klinis.
                        </p>
                    </div>

                    <div style="background:#ffffff;border:1px solid var(--slate-200);border-radius:var(--radius-lg);padding:18px;">
                        <span class="step-card-pill" style="font-size:10px;margin-bottom:6px;">Mindfulness • Bacaan 4 mnt</span>
                        <h3 style="font-size:14.5px;font-weight:800;color:var(--slate-900);margin-bottom:4px;">Overthinking di Malam Hari? Lakukan Grounding 5–4–3–2–1</h3>
                        <p style="font-size:12.5px;color:var(--slate-600);line-height:1.5;">
                            Teknik sensoris sederhana untuk menenangkan pikiran yang berkecamuk.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: Suara Komunitas -->
            <div id="komunitas">
                <div style="margin-bottom:20px;">
                    <span class="landing-section-prehead">KISAH PEMULIHAN NYATA</span>
                    <h2 style="font-size:1.6rem;font-weight:800;color:var(--slate-900);">Suara Komunitas</h2>
                </div>

                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div class="testimonial-bubble-card">
                        <p class="testimonial-quote-text">
                            "Sesi konseling di MindCare membuka sudut pandang baru. Psikolognya sangat mendengarkan tanpa menghakimi."
                        </p>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:12.5px;font-weight:700;color:var(--slate-900);">Raka A. (22 th)</div>
                                <div style="font-size:11px;color:var(--slate-500);">Mahasiswa, Yogyakarta</div>
                            </div>
                            <span class="step-card-pill" style="background:#ecfdf5;color:#065f46;">Terverifikasi</span>
                        </div>
                    </div>

                    <div class="testimonial-bubble-card">
                        <p class="testimonial-quote-text">
                            "Audio Meditasi sebelum tidur menyelamatkan insomnia saya. Kualitas tidur meningkat drastis."
                        </p>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:12.5px;font-weight:700;color:var(--slate-900);">Sari D. (27 th)</div>
                                <div style="font-size:11px;color:var(--slate-500);">Product Designer, Jakarta</div>
                            </div>
                            <span class="step-card-pill" style="background:#ecfdf5;color:#065f46;">Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION 7: PRIVASI TANPA KOMPROMI -->
    <section class="landing-section">
        <div class="landing-section-header">
            <span class="landing-section-prehead">STANDAR KEAMANAN TERTINGGI</span>
            <h2 class="landing-section-title">Privasi Tanpa Kompromi</h2>
            <p class="landing-section-sub">
                MindCare menerapkan standar proteksi sekelas industri perbankan.
            </p>
        </div>

        <div class="security-three-grid">
            <div class="security-feature-card">
                <div style="width:40px;height:40px;border-radius:10px;background:var(--teal-50);display:flex;align-items:center;justify-content:center;color:var(--teal-700);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h3 class="security-feature-title">Enkripsi End-to-End</h3>
                <p class="security-feature-desc">
                    Percakapan dienkripsi penuh AES-256. Hanya kamu dan psikologmu yang memiliki kuncinya.
                </p>
            </div>

            <div class="security-feature-card">
                <div style="width:40px;height:40px;border-radius:10px;background:var(--teal-50);display:flex;align-items:center;justify-content:center;color:var(--teal-700);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                </div>
                <h3 class="security-feature-title">Anonim Penuh di Forum</h3>
                <p class="security-feature-desc">
                    Nama samaran otomatis. Identitas asli tidak pernah dipublikasikan.
                </p>
            </div>

            <div class="security-feature-card">
                <div style="width:40px;height:40px;border-radius:10px;background:var(--teal-50);display:flex;align-items:center;justify-content:center;color:var(--teal-700);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h3 class="security-feature-title">Tanpa Penjualan Data</h3>
                <p class="security-feature-desc">
                    Data jurnal dan asesmen tidak pernah dijual ke pihak ketiga.
                </p>
            </div>
        </div>

        <!-- CTA Big Banner -->
        <div class="landing-cta-banner">
            <span class="cta-banner-prehead">AMBIL LANGKAH KECIL PERTAMAMU HARI INI</span>
            <h2 class="cta-banner-title">Kamu Tidak Perlu Menghadapi Segalanya Sendirian.</h2>
            <p class="cta-banner-desc">
                Bergabung bersama ribuan orang yang telah menemukan ketenangan jiwa.
            </p>
            <div class="cta-banner-buttons">
                <a href="register.php" class="btn-screening-white" style="border-radius:var(--radius-full);padding:13px 28px;">
                    <span>Mulai Skrining Gratis</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="tel:119" class="btn-hero-outline" style="border-radius:var(--radius-full);background:rgba(255,255,255,0.1);color:#ffffff;border-color:rgba(255,255,255,0.3);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <span>Bantuan Krisis 119</span>
                </a>
            </div>
            <div class="cta-banner-subtext">
                Tanpa biaya awal • Terkoneksi HIMPSI
            </div>
        </div>
    </section>

    <!-- LANDING GLOBAL FOOTER -->
    <footer class="landing-global-footer">
        <div class="footer-four-cols-grid">
            <!-- Col 1 -->
            <div>
                <a href="index.php" class="brand-logo" title="MindCare - Ruang Pulih & Kesehatan Mental" style="margin-bottom:12px;">
                    <img src="logo-horizontal.png" alt="MindCare - Kesehatan Mental" class="brand-logo-img" style="height:36px;">
                </a>
                <p class="footer-col-desc">
                    Platform pemulihan psikologis dan konseling klinis berlisensi di Indonesia.
                </p>
            </div>

            <!-- Col 2 -->
            <div>
                <div class="footer-col-title">Akses Cepat</div>
                <ul class="footer-col-links">
                    <li><a href="login.php">Skrining Dini (PHQ-9 & GAD-7)</a></li>
                    <li><a href="#fitur">Mindfulness & Pernapasan</a></li>
                    <li><a href="#layanan-klinis">Direktori Psikolog Klinis</a></li>
                </ul>
            </div>

            <!-- Col 3 -->
            <div>
                <div class="footer-col-title">SDG 3: Kesehatan Sejahtera</div>
                <p class="footer-col-desc">
                    Mendukung akses layanan kesehatan mental bagi semua.
                </p>
            </div>

            <!-- Col 4 -->
            <div>
                <div class="footer-col-title">Saluran Krisis 24/7</div>
                <div style="font-size:12.5px;color:var(--slate-700);line-height:1.6;">
                    <strong>Hotline Kemenkes RI:</strong> <a href="tel:119" style="color:#e11d48;font-weight:700;">119 ext 8</a><br>
                    Layanan Telekonseling Gratis Halo Kemenkes
                </div>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div>© 2025 MindCare Indonesia. Hak cipta dilindungi undang-undang.</div>
            <div style="display:flex;gap:18px;">
                <a href="#kebijakan" style="color:inherit;text-decoration:none;">Kebijakan Privasi Rekam Medis</a>
                <span>•</span>
                <a href="#etik" style="color:inherit;text-decoration:none;">Standar Etik HIMPSI</a>
            </div>
        </div>
    </footer>

    <script>
        function selectMood(el, moodName) {
            document.querySelectorAll('.hero-mood-pill').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
        }

        function startBreathingModal() {
            alert('Latihan Pernapasan 4-7-8 dimulai:\n\n1. Tarik napas perlahan melalui hidung (4 detik)...\n2. Tahan napas (7 detik)...\n3. Hembuskan perlahan lewat mulut (8 detik)...');
        }
    </script>
</body>
</html>
