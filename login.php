<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'Mahasiswa';
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Email atau kata sandi tidak cocok. Mohon periksa kembali.";
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - MindCare</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>

    <!-- NAVBAR -->
    <header class="public-navbar">
        <a href="index.php" class="brand-logo" title="MindCare - Ruang Pulih & Kesehatan Mental">
            <img src="logo-horizontal.png" alt="MindCare - Kesehatan Mental" class="brand-logo-img logo-desktop" style="height:42px;width:auto;max-width:200px;object-fit:contain;">
            <img src="logo-icon.png" alt="MindCare Logo" class="brand-logo-icon logo-mobile" style="height:38px;width:auto;object-fit:contain;">
        </a>

        <ul class="public-nav-links">
            <li><a href="login.php" class="active">Sign In</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>

        <div class="public-nav-right">
            <div class="encrypt-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>256-BIT ENCRYPTED</span>
            </div>
            <a href="login.php" class="user-nav-btn" title="Masuk Akun">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        </div>
    </header>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <main class="public-page-wrapper">
        <div class="auth-split-grid login-variant">
            
            <!-- LEFT COLUMN: Deep Teal Reflection & Breathing Visualizer -->
            <section class="login-hero-card">
                <div class="pill-accredited">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    <span>RUANG PULIH TERAKREDITASI</span>
                </div>

                <div class="reflection-tag">R E F L E K S I &nbsp; H A R I &nbsp; I N I</div>

                <h1 class="reflection-quote">
                    “Merawat pikiran adalah bentuk cinta tertinggi pada dirimu sendiri.”
                </h1>

                <p class="reflection-body">
                    Ambil jeda, tarik napas dalam-dalam, dan ijinkan dirimu untuk merasa aman di ruang ini.
                </p>

                <!-- Breathing Widget -->
                <div class="breathing-box">
                    <div class="breathing-header">
                        <div class="breathing-title">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.04Z"></path><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.04Z"></path></svg>
                            <span>Ritme Napas Tenang</span>
                        </div>
                        <span class="breathing-mode-pill">4-7-8 Stabil</span>
                    </div>

                    <!-- Animated Canvas Wave -->
                    <canvas id="breathingWaveCanvas" class="breathing-wave-canvas"></canvas>

                    <div class="breathing-phases">
                        <span>Inhalasi Perlahan</span>
                        <span>Hening Penuh</span>
                        <span>Ekshalasi Rileks</span>
                    </div>
                </div>

                <!-- 3 Stats Counter -->
                <div class="login-stats-grid">
                    <div class="login-stat-col">
                    </div>
                    <div class="login-stat-col">
                    </div>
                    <div class="login-stat-col">
                    </div>
                </div>

                <!-- Trust and Compliance Badge -->
                <div class="login-trust-footer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5eead4" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    <span>Standar Privasi UU PDP No. 27/2022</span>
                </div>
            </section>

            <!-- RIGHT COLUMN: Login Card -->
            <section class="auth-panel-card">
                <div class="card-prehead">
                    <h2 class="card-main-heading">Selamat Datang Kembali</h2>
                    <span class="privacy-status-pill">
                        <span class="dot"></span>
                        <span>Aman & Privat</span>
                    </span>
                </div>

                <p class="card-subtext">
                    Lanjutkan perjalanan pemulihanmu.
                </p>

                <!-- Tabs: Masuk ke Akun | Daftar Akun Baru -->
                <div class="auth-tabs-bar">
                    <a href="login.php" class="auth-tab-link active">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <span>Masuk ke Akun</span>
                    </a>
                    <a href="register.php" class="auth-tab-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>Daftar Akun Baru</span>
                    </a>
                </div>

                <!-- Social Sign In Buttons -->
                <div class="social-buttons-grid">
                    <button type="button" class="btn-social" onclick="alert('Masuk dengan Google.');">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Google</span>
                    </button>
                    <button type="button" class="btn-social" onclick="alert('Masuk dengan SSO Lembaga siap digunakan.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        <span>SSO Lembaga</span>
                    </button>
                </div>

                <div class="horizontal-divider-text">ATAU GUNAKAN EMAIL</div>

                <?php if (!empty($error)): ?>
                    <div style="background-color:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;border:1px solid #fecaca;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" id="loginForm">
                    <!-- Field: Alamat Email -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="login_email" class="field-label">Alamat Email</label>
                            <span class="badge-email-registered">Email Terdaftar</span>
                        </div>
                        <div class="input-with-icon-wrapper">
                            <span class="input-icon-left">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                            </span>
                            <input type="email" id="login_email" name="email" class="custom-input" placeholder="nama@email.com atau email pribadimu" required value="nadia.pratiwi@campus.ac.id">
                        </div>
                    </div>

                    <!-- Field: Kata Sandi -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="login_password" class="field-label">Kata Sandi</label>
                            <span class="field-hint-right"><a href="#forgot" onclick="alert('Instruksi pemulihan kata sandi telah dikirim ke email terdaftar Anda.'); return false;">Lupa Kata Sandi?</a></span>
                        </div>
                        <div class="input-with-icon-wrapper">
                            <span class="input-icon-left">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input type="password" id="login_password" name="password" class="custom-input" placeholder="Ketik kata sandi akunmu" required value="SereneSoul#2025!">
                            <button type="button" class="input-action-right toggle-password-visibility" data-target="login_password" aria-label="Tampilkan sandi">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me checkbox -->
                    <label class="checkbox-agreement-label">
                        <input type="checkbox" name="remember" checked>
                        <span>Ingat perangkat aman ini selama 30 hari</span>
                    </label>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-teal">
                        <span>Masuk ke MindCare</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>

                <div class="card-footer-links">
                    <div><a href="#partner-login" onclick="alert('Portal Mitra Konselor.'); return false;">Portal Psikolog Mitra →</a></div>
                </div>

                <!-- Bottom Privacy Notice Box -->
                <div class="login-privacy-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span><strong>Ruang Privat Tanpa Jejak:</strong> Data emosi dan obrolan konseling tidak pernah dijual ke pihak ketiga.</span>
                </div>
            </section>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="public-footer">
        <div class="public-footer-left">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <span>Ruang aman terenkripsi • Standar Privasi Klinis Terpadu</span>
        </div>
        <div class="public-footer-right">
            <a href="#privacy">Privasi & Ketentuan</a>
            <span>•</span>
            <a href="emergency.php">Bantuan Darurat</a>
            <span>•</span>
            <span>© 2025 MindCare Health</span>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
