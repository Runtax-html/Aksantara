# PRD: Aksantara

Platform edukasi aksara Nusantara berbasis web dengan gamifikasi ala Duolingo. Dirancang khusus untuk anak SD menggunakan teknologi web modern dan interaktif.

## 1. Target & Karakteristik UX
- **Target:** Anak SD usia 7–12 tahun.
- **Pendekatan UX:** Tampilan ramah anak, tombol besar, warna hangat khas Nusantara, dan navigasi minimalis tanpa istilah rumit.

## 2. Tech Stack
- **Backend:** Laravel 11 + Inertia.js
- **Frontend:** Vue.js 3 (Composition API) + Tailwind CSS
- **Database:** MySQL / PostgreSQL
- **Browser API:** Web Speech API, HTML5 Canvas

## 3. Spesifikasi Fitur Utama

### 1. Canvas Tracing
- Area latihan menggambar aksara menggunakan Canvas HTML5 (dukungan *mouse* & *touch*).
- Logika validasi goresan sederhana untuk mengecek kecocokan pola tulisan anak dengan kunci jawaban.

### 2. Audio & Pelafalan
- Menggunakan `window.speechSynthesis` bawaan browser.
- Tombol pemutar audio untuk mendengar bunyi aksara (contoh: "Ha", "Na", "Ca").

### 3. Pengenalan Suara (Listen & Speak)
- Menggunakan `webkitSpeechRecognition` / `SpeechRecognition` bawaan browser.
- Menangkap input suara anak saat mengucapkan aksara, mengonversinya ke teks, lalu membandingkannya dengan kunci jawaban.

### 4. Virtual Keyboard Aksara
- Komponen keyboard interaktif berisi tombol karakter aksara daerah.
- Input langsung terisi ke `textarea` dengan tombol fitur "Salin ke Clipboard".

### 5. Kamus & Transliterasi Real-time
- Pencarian kata Bahasa Indonesia ↔ Aksara Daerah.
- Input teks Latin otomatis terkonversi (*real-time*) menjadi karakter aksara di kolom sebelah.

### 6. System Gamifikasi
- **Streak:** Menghitung hari belajar berturut-turut. Terlewat 1 hari = reset ke 0.
- **XP:** Poin perolehan tiap kali menyelesaikan kuis/latihan dengan benar.
- **Leaderboard:** Papan peringkat anak berdasarkan akumulasi XP.

### 7. Reward Cerita Rakyat
- Halaman cerita rakyat yang terkunci dan baru terbuka saat XP/level mencukupi.
- Teks cerita dalam aksara daerah dengan fitur terjemahan *pop-up* (saat di-*hover* / di-tap) ke Bahasa Indonesia.

## 4. Skema Tabel Database (Entitas Utama)
- `users` (ditambah kolom `xp`, `streak_count`, `last_learned_at`)
- `characters` (`id`, `region`, `latin_symbol`, `aksara_symbol`, `stroke_data`)
- `user_progress` (`id`, `user_id`, `character_id`, `is_completed`, `score`)
- `dictionaries` (`id`, `region`, `indonesian_word`, `aksara_word`)
- `folktales` (`id`, `title`, `region`, `content_aksara`, `content_indonesian`, `required_xp`)
