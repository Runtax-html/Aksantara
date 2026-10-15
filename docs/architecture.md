# Arsitektur Sistem & Panduan Pengembangan: Aksantara

Dokumen ini menjelaskan arsitektur perangkat lunak, tumpukan teknologi (*tech stack*), alur siklus hidup *request-response*, struktur direktori, serta panduan pengoperasian di lingkungan lokal.

---

## 1. Stack Teknologi & Diagram Layer

Aksantara dibangun menggunakan pendekatan monolitik modern berbasis **Inertia.js** yang menggabungkan kemudahan backend Laravel dengan responsivitas Single Page Application (SPA) Vue 3:

```
+-------------------------------------------------------------+
|                      Client Layer (SPA)                     |
|  Vue 3 (Composition API, <script setup>) + Tailwind CSS v3  |
|  Vite Bundler + HTML5 Canvas API + Web Speech / Web Audio   |
+------------------------------+------------------------------+
                               | Inertia Protocol (JSON/XHR)
+------------------------------v------------------------------+
|                     Application Layer (Monolith)            |
|  Laravel 11.x (PHP 8.2+)                                    |
|  - Routing & Auth Middleware (Session/Cookie)               |
|  - Inertia.js Server Adapter                                |
|  - GamificationController & ProfileController               |
|  - TtsController + Symfony Process (Python Runtime)         |
+------------------------------+------------------------------+
                               | Eloquent ORM
+------------------------------v------------------------------+
|                     Data & Storage Layer                    |
|  MySQL / MariaDB Database                                   |
|  File System: Cached MP3 Audio (/public/audio/sunda/cache/)  |
+-------------------------------------------------------------+
```

### Rincian Komponen:
1. **Backend**: Laravel 11 (PHP 8.2+), Eloquent ORM, Laravel Breeze (Inertia Stack) untuk autentikasi berbasis session dan cookie CSRF.
2. **Frontend**: Vue.js 3 (`<script setup>`), Tailwind CSS v3 dengan palet warna khusus ramah anak, Vite 8 sebagai aset bundler.
3. **Mesin Suara (Audio Engine)**:
   - **Primary**: Microsoft Edge-TTS (`su-ID-JajangNeural`) via Python runtime di `app/Http/Controllers/TtsController.php`.
   - **File-Based Cache**: Hasil audio disimpan di `public/audio/sunda/cache/{md5_hash}.mp3`. Panggilan berulang untuk teks yang sama langsung disajikan dari cache tanpa eksekusi ulang Python.
   - **Fallback**: `window.speechSynthesis` bawaan peramban (`id-ID`) dan Web Audio API untuk efek suara tuts.

---

## 2. Alur Request & Hidrasi Data (Request Lifecycle)

### 2.1. Alur Akses Dashboard (`/dashboard`)
1. Pengguna membuka URL `/dashboard` dengan sesi terotentikasi.
2. `routes/web.php` mengeksekusi `GamificationController::getGamificationState($request->user())`.
3. Server memproses:
   - Validasi level otomatis berdasarkan akumulasi XP saat ini.
   - Pengecekan status streak (reset ke 0 jika `last_activity_at < today - 1 hari`).
   - Query progres level 1–6 (`user_quiz_progress`), misi harian (`user_daily_missions`), dan dongeng terbuka (`user_unlocked_stories`).
   - Kalkulasi peringkat leaderboard dari tabel `users` (`ORDER BY streak DESC, xp DESC`).
4. Data dihidrasi langsung ke props Vue via `Inertia::render('Dashboard', ['gamification' => $gamification])`.

### 2.2. Alur Aksi Asinkron Gamifikasi (AJAX)
1. Pengguna menyelesaikan kuis, menekan check-in, atau membaca dongeng.
2. Frontend mengirimkan request HTTP `POST` ke endpoint `/api/gamification/*`.
3. Server memperbarui status pada model `User` dan mencatat riwayat transaksi (`UserCheckin`, `UserDailyMission`, dll.).
4. Controller merespons dengan JSON berisi payload `gamification` terbaru, kemudian frontend memperbarui state reaktif secara lokal via `updateGamificationState(data)`.

---

## 3. Peta Struktur Direktori Proyek

```
aksantara/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/                     # Controller autentikasi Laravel Breeze
│   │   ├── GamificationController.php # Core engine kalkulasi kuis, streak, koin, level
│   │   ├── ProfileController.php      # Manajemen profil & statistik user
│   │   └── TtsController.php          # Edge-TTS bridge & audio caching
│   └── Models/
│       ├── User.php                  # Model user (XP, koin, streak, level)
│       ├── UserCheckin.php           # Riwayat check-in harian
│       ├── UserDailyMission.php      # Progres misi harian per tanggal
│       ├── UserQuizProgress.php      # Progres per level petualangan
│       └── UserUnlockedStory.php     # Pembukaan & status klaim hadiah dongeng
├── database/migrations/              # Skema tabel database modular
├── docs/                             # 5 Dokumen referensi teknis acuan AI Agent
├── public/audio/sunda/cache/         # Berkas MP3 hasil sintesis suara Edge-TTS
├── resources/js/
│   ├── Components/                   # Modal dan widget interaktif (Canvas, Quiz, Streak, dll.)
│   ├── Pages/                        # Halaman Inertia (Dashboard.vue, Landingpage.vue, Edit.vue)
│   └── app.js                        # Inisialisasi frontend Inertia & Vue
├── routes/
│   ├── auth.php                      # Rute autentikasi
│   └── web.php                       # Rute aplikasi & API gamifikasi
└── scripts/
    └── generate_audio.py             # Script CLI generator audio aksara
```

---

## 4. Panduan Pengoperasian Lokal (Local Setup Runbook)

### 4.1. Prasyarat Sistem
- **PHP**: Versi 8.2+ dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`.
- **Composer**: Versi 2.x.
- **Node.js**: Versi 18.x atau 20.x LTS & npm.
- **Python**: Versi 3.9+ dengan pustaka `edge-tts`.
- **Database**: MySQL 8.0+ / MariaDB 10.4+ / SQLite.

### 4.2. Langkah Instalasi Cepat
```bash
# 1. Masuk direktori & salin konfigurasi environment
cd aksantara
copy .env.example .env

# 2. Instal dependensi backend & frontend
composer install
npm install

# 3. Instal pustaka Python untuk sintesis audio
pip install edge-tts

# 4. Generate App Key & Jalankan Migrasi Database
php artisan key:generate
php artisan migrate
```

### 4.3. Menjalankan Server Lokal
Jalankan 2 perintah berikut di terminal terpisah:
```bash
# Terminal 1: Backend PHP Server (http://localhost:8000)
php artisan serve

# Terminal 2: Frontend Vite HMR Dev Server (http://localhost:5173)
npm run dev
```

### 4.4. Kompilasi Produksi & Pemecahan Masalah
- **Build Aset**: Jalankan `npm run build` sebelum rilis produksi.
- **Isu Suara Edge-TTS**: Jika audio tidak bersuara, pastikan perintah `python --version` dan `pip list | findstr edge-tts` berjalan tanpa kendala di sistem operasi host.
- **Pembersihan Cache Laravel**: `php artisan config:clear && php artisan route:clear && php artisan view:clear`.
