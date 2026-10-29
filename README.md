# 🌿 MindCare — Ruang Pulih & Kesehatan Mental Digital

<p align="center">
  <img src="logo-horizontal.png" alt="MindCare Logo" width="320">
</p>

<p align="center">
  <strong>Platform digital komprehensif untuk merawat kesehatan mental, refleksi diri terpadu, dan terhubung dengan dukungan psikologis profesional.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Google%20Gemini-AI-4285F4?style=for-the-badge&logo=google&logoColor=white" alt="Google Gemini">
  <img src="https://img.shields.io/badge/Frontend-Vanilla%20JS%20%7C%20CSS3-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="Vanilla JS">
  <img src="https://img.shields.io/badge/SDG%203-Good%20Health%20%26%20Well--Being-4C9F38?style=for-the-badge" alt="SDG 3">
  <img src="https://img.shields.io/badge/Status-Active%20Development-success?style=for-the-badge" alt="Status">
</p>

---

## 📌 Daftar Isi

1. [Tentang MindCare](#-tentang-mindcare)
2. [Fitur-Fitur Utama](#-fitur-fitur-utama)
3. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
4. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
5. [Skema Basis Data](#-skema-basis-data)
6. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
7. [Konfigurasi API AI Companion](#-konfigurasi-api-ai-companion)
8. [Keamanan & Standar Privasi](#-keamanan--standar-privasi)
9. [Tampilan Antarmuka](#-tampilan-antarmuka)
10. [Kontribusi & Lisensi](#-kontribusi--lisensi)

---

## 📖 Tentang MindCare

**MindCare** adalah aplikasi berbasis web yang didedikasikan untuk mendukung kesejahteraan mental masyarakat Indonesia, khususnya kalangan remaja, mahasiswa, dan profesional muda. Berakar dari komitmen terhadap **SDG 3 (*Good Health and Well-Being*)**, MindCare menghadirkan ekosistem pertolongan mandiri (*self-care*), pendampingan berbasis kecerdasan buatan (*AI Counseling Companion*), hingga akses cepat ke penanganan krisis dan tenaga psikolog profesional.

MindCare dirancang dengan antarmuka yang ramah, hangat, dan menenangkan serta menjamin privasi pengguna 100%.

---

## ✨ Fitur-Fitur Utama

### 🤖 1. AI Chatbot Mental Health Companion (`chatbot.php` & `api_chat.php`)
- Asisten refleksi berbasis **Google Gemini AI** yang merespons dengan gaya bahasa hangat, empatik, tenang, dan tidak menghakimi.
- Deteksi otomatis kata kunci krisis dengan saran nomor darurat 119 Ext 8.
- Mendukung integrasi multi-model fallback yang responsif.

### 📝 2. Self Check: Skrining Kesejahteraan Mandiri (`self-check.php`)
- Kuesioner skrining mandiri terukur untuk mendeteksi tanda-tanda awal stres, kecemasan, atau kelelahan emosional.
- Dilengkapi indikator skor interaktif, progress bar dinamis, dan hasil evaluasi yang disertai rekomendasi tindak lanjut.

### 📊 3. Mood Tracker & Jurnal Emosi (`mood-tracker.php`)
- Pelacakan spektrum suasana hati harian (Bahagia, Tenang, Netral, Lelah, Cemas, Sedih).
- Fitur *Mood Streak Counter* untuk membangun kebiasaan merawat diri secara konsisten.
- Tag pemicu emosi (akademik, pekerjaan, relasi, kesehatan fisik, keuangan) dan catatan reflektif singkat.

### 🧘 4. Meditasi & Latihan Pernapasan Interaktif (`meditasi.php`)
- Visualisator pernapasan terpandu (*Breathing Visualizer*) berbasis teknik teruji (misalnya *Box Breathing* dan metode 4-7-8).
- Sesi meditasi bertema: Relaksasi Cepat, Pelepasan Stres Kerja/Kuliah, Kualitas Tidur, dan *Mindful Walking*.

### 👩‍⚕️ 5. Konsultasi Psikolog Profesional (`konsultasi.php`)
- Direktori psikolog klinis tersertifikasi lengkap dengan profil keahlian, tarif, ulasan, dan penjadwalan sesi konseling.
- Pencatatan riwayat sesi dan topik konsultasi ke basis data.

### 🚨 6. Bantuan Darurat & Hotline Krisis (`emergency.php`)
- Tombol darurat akses cepat ke **Hotline Kemenkes 119 Ext 8 (Sejiwa)** dan layanan tanggap darurat nasional.
- Pemutar audio relaksasi darurat instan untuk meredakan serangan panik (*panic attack*).
- Panduan rencana keselamatan darurat (*Safety Plan Protocol*).

### 👥 7. Forum Komunitas Suportif (`forum-komunitas.php`)
- Ruang berbagi cerita dan saling menguatkan antarpengguna dengan opsi anonimitas terjamin.
- Kategori topik diskusi yang terstruktur untuk pengalaman komunitas yang aman dan positif.

### 📚 8. Artikel Edukasi Kesehatan Mental (`artikel.php`)
- Ensiklopedia bacaan edukatif berbasis riset ilmiah mengenai manajemen stres, *burnout*, kecemasan sosial, hingga *work-life balance*.

### 👤 9. Profil Pengguna & Keamanan Akun (`profile.php`, `login.php`, `register.php`)
- Autentikasi aman menggunakan algoritma hash sandi `password_hash()` BCRYPT.
- *Live Password Strength Meter* dengan checklist indikator kekuatan sandi saat pendaftaran.
- Pengelolaan kontak darurat (*emergency contact*), institusi, dan riwayat kesehatan diri.

---

## 🛠 Teknologi yang Digunakan

| Komponen | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Backend** | PHP 8.x | Native PHP terstruktur dengan sesi aman & sanitasi input |
| **Database** | MySQL / MariaDB | Penyimpanan relasional via driver `PDO` (aman dari SQL Injection) |
| **Frontend** | HTML5, Modern CSS3 | Custom Design System, Flexbox & CSS Grid, Glassmorphism, Micro-animations |
| **Scripting** | Vanilla JavaScript (ES6+) | Interaksi UI tanpa dependensi berat (zero library overhead) |
| **AI Integration** | RESTful JSON API | Google Gemini API (`gemini-3-flash-preview`, `gemini-2.5-flash`) |
| **Web Server** | Apache (XAMPP / Laragon) | Server lokal untuk proses pengembangan dan pengujian |

---

## 📂 Struktur Direktori Proyek

```text
mindcare_project/
├── .vscode/                     # Konfigurasi workspace editor
├── Logo MindCare...png          # Asset grafis logo resmi
├── logo-*.png                   # Varian logo (horizontal, icon, white, mark)
├── favicon.ico & favicon.png    # Icon situs web
│
├── index.php                    # Landing page publik (Hero, Nilai Unggulan, SDG 3)
├── login.php                    # Halaman masuk sistem (Autentikasi akun)
├── register.php                 # Halaman pendaftaran anggota baru (Validasi sandi interaktif)
├── logout.php                   # Penghancuran sesi & pengalihan keluar
│
├── dashboard.php                # Dashboard utama pengguna (Pusat aktivitas harian)
├── self-check.php               # Modul kuesioner skrining mandiri
├── mood-tracker.php             # Modul pelacak suasana hati & catatan jurnal
├── meditasi.php                 # Modul meditasi terpandu & breathing visualizer
├── chatbot.php                  # Antarmuka ruang percakapan AI Companion
├── api_chat.php                 # Endpoint API komunikasi dengan Google Gemini AI
├── artikel.php                  # Modul katalog artikel edukasi kesehatan mental
├── forum-komunitas.php          # Modul forum diskusi komunitas
├── konsultasi.php               # Modul pemesanan & katalog psikolog klinis
├── event-wellness.php           # Modul lokakarya, webinar, & kegiatan wellness
├── emergency.php                # Modul pusat bantuan krisis & hotline 119
├── profile.php                  # Pengaturan akun, profil pribadi, & kontak darurat
│
├── database.sql                 # Skema DDL tabel database MySQL
├── db_connect.php               # Koneksi database aman menggunakan PDO
├── style.css                    # Seluruh stylesheet terpadu (Design tokens & layout)
├── script.js                    # Logika interaktif DOM, visualizer, audio, & AJAX chat
└── README.md                    # Dokumentasi lengkap proyek
```

---

## 🗄 Skema Basis Data

Sistem menggunakan database bernama `mindcare_db` dengan dua tabel utama:

### 1. Tabel `users`
Menyimpan identitas dan kredensial pengguna yang terdaftar.

| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | `INT AUTO_INCREMENT PRIMARY KEY` | ID unik pengguna |
| `fullname` | `VARCHAR(100)` | Nama lengkap pengguna |
| `alias` | `VARCHAR(100) NULL` | Nama panggilan / anonim |
| `email` | `VARCHAR(100) UNIQUE` | Alamat surel aktif |
| `password` | `VARCHAR(255)` | Kata sandi terenkripsi (BCRYPT) |
| `role` | `VARCHAR(50)` | Peran pengguna (contoh: Mahasiswa, Member Aktif) |
| `phone` | `VARCHAR(50)` | Nomor kontak telepon |
| `birthdate` | `VARCHAR(50)` | Tanggal lahir |
| `institution` | `VARCHAR(150)` | Asal kampus / institusi / tempat kerja |
| `emergency_contact` | `VARCHAR(255)` | Nama dan nomor kontak darurat |
| `bio` | `TEXT NULL` | Catatan bio singkat pengguna |
| `created_at` | `TIMESTAMP` | Waktu akun pertama kali didaftarkan |

### 2. Tabel `consultations`
Menyimpan data janji temu atau catatan konsultasi dengan tenaga profesional.

| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | `INT AUTO_INCREMENT PRIMARY KEY` | ID unik sesi konsultasi |
| `user_id` | `INT` | Relasi ke `users.id` (*Foreign Key ON DELETE CASCADE*) |
| `topic` | `VARCHAR(255)` | Topik permasalahan konseling |
| `notes` | `TEXT` | Catatan refleksi / anjuran sesi |
| `consultation_date` | `DATETIME` | Jadwal pelaksanaan konsultasi |

---

## 🚀 Panduan Instalasi & Menjalankan

Ikuti langkah-langkah berikut untuk menjalankan MindCare di lingkungan lokal komputer Anda (menggunakan **XAMPP**):

### Prasyarat
- **XAMPP** dengan PHP versi **8.0 atau yang lebih baru**.
- Web Browser modern (Google Chrome, Mozilla Firefox, Microsoft Edge, Safari).

### Langkah 1: Pindahkan Berkas Proyek
Pastikan folder proyek berada di direktori `htdocs` XAMPP Anda:
```text
C:\xampp\htdocs\mindcare_project\
```

### Langkah 2: Nyalakan Layanan XAMPP
1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **Apache**.
3. Klik tombol **Start** pada modul **MySQL**.

### Langkah 3: Impor Basis Data
1. Buka browser dan buka alamat: `http://localhost/phpmyadmin/`.
2. Buat database baru bernama `mindcare_db`.
3. Pilih database `mindcare_db`, klik tab **Import**.
4. Klik tombol **Choose File**, pilih file `database.sql` yang ada di dalam folder proyek ini.
5. Klik tombol **Import / Kirim** di bagian bawah untuk mengeksekusi tabel.

> **Atau via MySQL CLI:**
> ```bash
> mysql -u root -p < "C:\xampp\htdocs\mindcare_project\database.sql"
> ```

### Langkah 4: Sesuaikan Konfigurasi Database (Jika Diperlukan)
Buka file `db_connect.php` dan pastikan kredensial sesuai dengan setelan MySQL Anda:
```php
$host = '127.0.0.1';
$dbname = 'mindcare_db';
$username = 'root';
$password = ''; // Isi jika MySQL lokal Anda menggunakan kata sandi
```

### Langkah 5: Buka Aplikasi di Browser
Akses URL berikut pada peramban Anda:
```text
http://localhost/mindcare_project/
```
- Anda dapat mendaftar akun baru melalui menu **Daftar Akun** (`register.php`) atau masuk dengan akun yang sudah ada.

---

## 🔑 Konfigurasi API AI Companion

MindCare dilengkapi dengan chatbot bertenaga kecerdasan buatan melalui file `api_chat.php`.

1. Secara bawaan, MindCare sudah menyematkan token Google Gemini API siap pakai.
2. Jika Anda ingin menggunakan API Key milik pribadi:
   - Dapatkan API Key gratis di [Google AI Studio](https://aistudio.google.com/).
   - Masukkan kunci API Anda ke dalam pengaturan antarmuka chat atau sesuaikan konstanta pada file [api_chat.php](file:///c:/xampp/htdocs/mindcare_project/api_chat.php):
     ```php
     define('DEFAULT_GEMINI_KEY', 'MASUKKAN_GEMINI_API_KEY_ANDA_DI_SINI');
     ```

---

## 🛡 Keamanan & Standar Privasi

- **Penyimpanan Kredensial**: Menggunakan `password_hash()` BCRYPT terverifikasi dan `password_verify()`.
- **Perlindungan SQL Injection**: Menggunakan *Prepared Statements* dengan parameter binding pada objek `PDO`.
- **Sanitasi Data Output**: Mencegah serangan *Cross-Site Scripting (XSS)* dengan membungkus output dinamis melalui `htmlspecialchars()`.
- **Keamanan Sesi**: Validasi status login berbasis `$_SESSION['user_id']` pada halaman tertutup dengan pengalihan otomatis (*auto-redirect*).

---

## 🎨 Desain & Pengalaman Pengguna (UI/UX)

- **Palet Warna Terapeutik**: Kombinasi warna *Deep Forest Teal* (`#0f766e`), *Fresh Sage Green*, dan *Soft Slate* memberikan rasa tenang, aman, dan memulihkan.
- **Glassmorphism & Micro-Interactions**: Transisi halus saat kursor melayang (*hover*), indikator kekuatan kata sandi dinamis, dan visualisasi napas memusatkan fokus pengguna.
- **Mobile Responsive**: Tata letak fleksibel yang nyaman diakses melalui perangkat ponsel (*smartphone*), tablet, maupun layar desktop lebar.

---

## 🤝 Kontribusi & Lisensi

Proyek ini dikembangkan sebagai inisiatif terbuka untuk mendukung kesehatan mental generasi muda Indonesia.

- **Dibuat dengan rasa peduli untuk setiap jiwa yang sedang berjuang.**
- *Mulai Peduli, Mulai dari Dirimu.*

---

<p align="center">
  © 2026 <strong>MindCare Indonesia</strong>. Ruang Pulih & Kesejahteraan Mental Digital.
</p>
