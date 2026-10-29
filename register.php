<?php
session_start();
require_once 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = trim($_POST['role'] ?? 'Mahasiswa');

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Alamat email ini sudah terdaftar. Silakan masuk.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (fullname, alias, email, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$fullname, $fullname, $email, $hashed_password, $role]);
            } catch (Exception $e) {
                $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$fullname, $email, $hashed_password]);
            }

            $userId = $pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit;
        }
    } else {
        $error = "Mohon lengkapi seluruh kolom yang diwajibkan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - MindCare</title>
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
            <li><a href="login.php">Sign In</a></li>
            <li><a href="register.php" class="active">Register</a></li>
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
            
            <!-- LEFT COLUMN: Deep Teal Value Proposition Rectangle (Matches Login Card) -->
            <section class="login-hero-card">
                <div class="pill-accredited">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                    <span>RUANG PULIH TERAKREDITASI</span>
                </div>

                <div class="reflection-tag">M U L A I &nbsp; P E R J A L A N A N M U</div>

                <h1 class="reflection-quote">
                    “Temukan Ketenangan Jiwa &amp; Ruang Bebas Penghakiman.”
                </h1>

                <p class="reflection-body">
                    Masuki ruang digital terenkripsi yang menghargai privasi penuhmu dan siap mendampingi setiap langkah kecil pemulihan.
                </p>

                <!-- 3 Key Clinical Value Points (Glassmorphic) -->
                <div style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:var(--radius-xl); padding:18px 20px; display:flex; flex-direction:column; gap:14px; backdrop-filter:blur(8px);">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:34px; height:34px; border-radius:50%; background:rgba(94,234,212,0.2); color:#5eead4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                        </div>
                        <div>
                            <div style="font-size:13.5px; font-weight:700; color:#ffffff;">Ruang Bebas Penghakiman</div>
                            <div style="font-size:12px; color:#ccfbf1; margin-top:2px; line-height:1.4;">Komunitas anonim &amp; aman. Ekspresikan dirimu tanpa takut dinilai.</div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:34px; height:34px; border-radius:50%; background:rgba(94,234,212,0.2); color:#5eead4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <div style="font-size:13.5px; font-weight:700; color:#ffffff;">Konseling Psikolog Ber-STR</div>
                            <div style="font-size:12px; color:#ccfbf1; margin-top:2px; line-height:1.4;">Terhubung langsung dengan psikolog klinis berlisensi HIMPSI &amp; Kemenkes.</div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:34px; height:34px; border-radius:50%; background:rgba(94,234,212,0.2); color:#5eead4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                        <div>
                            <div style="font-size:13.5px; font-weight:700; color:#ffffff;">Privasi Medis Tingkat Tinggi</div>
                            <div style="font-size:12px; color:#ccfbf1; margin-top:2px; line-height:1.4;">Data dan catatan terenkripsi end-to-end berstandar UU PDP No. 27/2022.</div>
                        </div>
                    </div>
                </div>



                <!-- Trust and Compliance Badge (Identical to Login) -->
                <div class="login-trust-footer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5eead4" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    <span>Terakreditasi &amp; Standar Privasi UU PDP No. 27/2022</span>
                </div>
            </section>

            <!-- RIGHT COLUMN: Registration Card -->
            <section class="auth-panel-card">
                <div class="card-prehead">
                    <span class="card-prehead-label">MULAI PERJALANANMU</span>
                </div>

                <h2 class="card-main-heading">Buat Akun MindCare Baru</h2>

                <!-- Tabs: Masuk ke Akun | Daftar Akun Baru -->
                <div class="auth-tabs-bar">
                    <a href="login.php" class="auth-tab-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <span>Masuk ke Akun</span>
                    </a>
                    <a href="register.php" class="auth-tab-link active">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>Daftar Akun Baru</span>
                    </a>
                </div>

                <!-- Social Sign Up Buttons -->
                <div class="social-buttons-grid">
                    <button type="button" class="btn-social" onclick="alert('Pendaftaran dengan Google siap digunakan.');">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Google</span>
                    </button>
                    <button type="button" class="btn-social" onclick="alert('Pendaftaran dengan SSO Lembaga siap digunakan.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        <span>SSO Lembaga</span>
                    </button>
                </div>

                <div class="horizontal-divider-text">ATAU DAFTAR DENGAN EMAIL</div>

                <?php if (!empty($error)): ?>
                    <div style="background-color:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;border:1px solid #fecaca;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" id="registerForm">
                    <!-- Field: Nama Panggilan / Alias -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="fullname" class="field-label">Nama Panggilan / Alias</label>
                            <span class="field-hint-right">Bisa berupa nama samaran santai</span>
                        </div>
                        <div class="input-with-icon-wrapper">
                            <span class="input-icon-left">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <input type="text" id="fullname" name="fullname" class="custom-input" placeholder="Contoh: Rian, KawanTeduh, LenteraJiwa" required>
                        </div>
                    </div>

                    <!-- Field: Alamat Email -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="email" class="field-label">Alamat Email</label>
                            <span class="field-hint-right">Email aktif untuk verifikasi akun</span>
                        </div>
                        <div class="input-with-icon-wrapper">
                            <span class="input-icon-left">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                            </span>
                            <input type="email" id="email" name="email" class="custom-input" placeholder="nama@email.com atau akun pribadimu" required>
                        </div>
                    </div>

                    <!-- Field: Peran / Status Pengguna Saat Ini -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label class="field-label">Peran / Status Pengguna Saat Ini</label>
                        </div>
                        <div class="role-options-row">
                            <label class="role-pill-option selected" id="role_mhs">
                                <input type="radio" name="role" value="Mahasiswa" checked>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                                <span>Mahasiswa</span>
                            </label>
                            <label class="role-pill-option" id="role_karyawan">
                                <input type="radio" name="role" value="Karyawan">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                <span>Karyawan</span>
                            </label>
                            <label class="role-pill-option" id="role_umum">
                                <input type="radio" name="role" value="Umum">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span>Umum</span>
                            </label>
                        </div>
                    </div>

                    <!-- Field: Kata Sandi Baru -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="reg_password" class="field-label">Kata Sandi Baru</label>
                            <span class="badge-pwd-strength" id="pwd_strength_badge">Sangat Kuat</span>
                        </div>
                        <div class="input-with-icon-wrapper">
                            <span class="input-icon-left">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input type="password" id="reg_password" name="password" class="custom-input" placeholder="SereneSoul#2025!" required value="SereneSoul#2025!">
                            <button type="button" class="input-action-right toggle-password-visibility" data-target="reg_password" aria-label="Tampilkan sandi">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>

                        <!-- Strength meter bar -->
                        <div class="strength-meter-bar">
                            <div class="strength-segment active-green"></div>
                            <div class="strength-segment active-green"></div>
                            <div class="strength-segment active-green"></div>
                            <div class="strength-segment active-green"></div>
                        </div>

                        <!-- 4 Criteria checklist -->
                        <div class="strength-criteria-grid">
                            <div class="criteria-item checked" id="chk_length" data-text="Minimal 8 karakter">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Minimal 8 karakter
                            </div>
                            <div class="criteria-item checked" id="chk_symbols" data-text="Kombinasi simbol (!@#$)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Kombinasi simbol (!@#$)
                            </div>
                            <div class="criteria-item checked" id="chk_num_upper" data-text="Angka & huruf kapital">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Angka & huruf kapital
                            </div>
                            <div class="criteria-item checked" id="chk_personal" data-text="Bebas informasi pribadi">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Bebas informasi pribadi
                            </div>
                        </div>
                    </div>

                    <!-- Field: Ulangi Kata Sandi -->
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="confirm_password" class="field-label">Ulangi Kata Sandi</label>
                        </div>
                        <div class="input-with-icon-wrapper">
                            <span class="input-icon-left">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input type="password" id="confirm_password" name="confirm_password" class="custom-input" placeholder="SereneSoul#2025!" required value="SereneSoul#2025!">
                            <button type="button" class="input-action-right toggle-password-visibility" data-target="confirm_password" aria-label="Tampilkan sandi">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Terms agreement checkbox -->
                    <label class="checkbox-agreement-label">
                        <input type="checkbox" name="terms" required checked>
                        <span>
                            Saya menyetujui <a href="#terms">Ketentuan Layanan</a> & <a href="#privacy">Kebijakan Privasi</a>. Data bersifat rahasia.
                        </span>
                    </label>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-teal">
                        <span>Daftar Akun Baru</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>

                <div class="card-footer-links">
                    <div>Sudah punya akun? <a href="login.php">Masuk</a></div>
                    <div><a href="#partner">Kemitraan Psikolog Mitra →</a></div>
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
