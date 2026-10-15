# Product Requirements Document (PRD): Aksantara

Aksantara adalah platform web edukasi aksara tradisional Nusantara yang mengadopsi mekanisme gamifikasi terstruktur untuk segmen anak usia sekolah dasar (7–12 tahun). Fokus materi utama saat ini diimplementasikan pada **Aksara Sunda**, dengan fondasi arsitektur modular yang dapat diperluas ke aksara daerah lain (Jawa, Bali, Batak).

---

## 1. Sasaran Pengguna & Prinsip Desain UX

| Parameter | Spesifikasi |
| :--- | :--- |
| **Target Pengguna** | Siswa Sekolah Dasar usia 7–12 tahun, guru muatan lokal, dan pemula. |
| **Bahasa Antarmuka** | Bahasa Indonesia (ramah anak, instruktif, tanpa terminologi teknis yang membingungkan). |
| **Gaya Visual** | Hangat, bersahabat, kontras tinggi. Latar krem lembut (`#FFFDF5`), aksen oranye cerah (`#FF4D30`), amber, dan hijau zamrud. |
| **Ukuran Elemen UI** | Tombol sentuh minimal 48px, sudut membulat lebar (`rounded-2xl` s.d. `rounded-3xl`), font sans-serif tebal dan mudah terbaca. |
| **Interaksi Input** | Mendukung ganda: *mouse pointer* (desktop/laptop) dan *touch event* (layar sentuh HP/tablet). |

---

## 2. Arsitektur Modul Pembelajaran

Aksantara memiliki 5 pilar modul fungsional di halaman utama dashboard:

```
                  ┌─────────────────────────────────────────┐
                  │          Dashboard Aksantara            │
                  └────────────────────┬────────────────────┘
                                       │
     ┌─────────────────┬───────────────┼───────────────┬─────────────────┐
     ▼                 ▼               ▼               ▼                 ▼
[Petualangan]     [Coretan]     [Papan Ketik]       [Kamus]          [Dongeng]
(Peta 6 Level)   (53 Huruf)     (Virtual KB)     (Transliterasi)   (Cerita Rakyat)
  - Tebak Huruf   - Swara         - 53 Aksara      - Kamus Sunda     - Pembelian Koin
  - Menulis       - Ngalagena     - Audio Lafal    - Real-time Type  - Baca Aksara
  - Hint Koin     - Rarangkén     - Salin Teks     - Suara Pelafalan - Klaim Sekali
                  - Angka
```

### 2.1. Modul Petualangan (Map Soal Progresif)
- Peta bertingkat berisi **Level 1 sampai Level 6**:
  - Level 1: Aksara Swara (*a, i, u, é, o, e, eu*).
  - Level 2: Aksara Ngalagena Dasar Bagian 1 (*ka, ga, nga, ca, ja, nya*).
  - Level 3: Aksara Ngalagena Dasar Bagian 2 (*ta, da, na, pa, ba, ma*).
  - Level 4: Kosakata Alam & Satwa (*ucing, hayam, maung, tangkal*).
  - Level 5: Kosakata Anggota Tubuh & Makanan (*panon, leungeun, cau, sangu*).
  - Level 6: Jawara Aksara Sunda (Kombinasi Tebak Huruf & Menulis).
- Setiap level berisi minimal 10 soal acak yang terdiri dari:
  1. **Soal Pilihan Ganda / Tebak Huruf**: Menampilkan grafis/karakter aksara, memilih nama/bunyi huruf yang tepat.
  2. **Soal Menulis (Canvas Interactive)**: Menampilkan panduan tulisan, pengguna mencoret langsung di kanvas untuk menuntaskan soal.
- **Sistem Kunci Level**: Pengguna baru otomatis mulai di Level 1. Level berikutnya hanya terbuka jika level sebelumnya telah berstatus `completed`. Soal yang telah diselesaikan tetap dapat dimainkan ulang tanpa batasan.
- **Fitur Hint (Bantuan Soal)**: Memotong 5 koin untuk mengeliminasi 2 opsi salah atau menampilkan petunjuk penulisan.

### 2.2. Modul Coretan (Canvas Tracing & Latihan Menulis)
- Katalog lengkap 53 karakter Aksara Sunda berstandar Unicode:
  - 7 Aksara Swara: ᮃ, ᮄ, ᮅ, ᮈ, ᮇ, ᮉ, ᮆ.
  - 23 Aksara Ngalagena: ᮊ, ᮌ, ᮍ, ᮎ, ᮏ, ᮑ, ᮒ, ᮓ, ᮔ, ᮕ, ᮘ, ᮙ, ᮚ, ᮛ, ᮜ, ᮝ, ᮞ, ᮠ, serta konsonan serapan (ᮋ, ᮐ, ᮖ, ᮗ, ᮟ, ᮮ).
  - 13 Rarangkén (Vokalisasi & Tanda Bunyi): ◌ᮤ, ◌ᮥ, ◌ᮦ, ◌ᮧ, ◌ᮨ, ◌ᮩ, ◌᮪, ◌ᮀ, ◌ᮁ, ◌ᮂ, ◌ᮡ, ◌ᮢ, ◌ᮣ.
  - 10 Aksara Angka: ᮰ (0) s.d. ᮹ (9).
- Fitur Kanvas:
  - Ketebalan kuas dikunci pada 14px (`lineCap: 'round'`, `lineJoin: 'round'`).
  - Warna tinta standar pekat (`#1e293b`).
  - Watermark bayangan huruf dan garis panduan pembantu.
  - Modal panduan animasi SVG langkah goresan (Stroke Guide 1 ➔ 2).

### 2.3. Modul Papan Ketik Virtual (Virtual Keyboard)
- Papan ketik interaktif di browser berisi 53 karakter aksara Sunda.
- Tombol audio pelafalan untuk mendengarkan bunyi setiap tombol yang ditekan.
- Bidang output teks dengan fitur salin cepat (*copy to clipboard*).

### 2.4. Modul Kamus & Transliterasi
- Pencarian kata dua arah: Bahasa Indonesia ↔ Aksara Sunda.
- Konversi teks Latin langsung menjadi glif aksara Sunda secara *real-time*.
- Fitur sintesis suara pelafalan per kata berbasis Edge-TTS.

### 2.5. Modul Dongeng Sunda (Cerita Rakyat Nusantara)
- Kumpulan cerita rakyat khas Sunda bergambar (contoh: *Sangkuriang*, *Lutung Kasarung*, *Si Kabayan*, dll.).
- **Mekanisme Pembukaan**: Dongeng pertama terbuka otomatis untuk pengujian/user baru. Dongeng berikutnya dibuka menggunakan saldo koin (30 koin per cerita).
- **Klaim Hadiah Sekali (One-Time Claim)**: Tombol "Tandai Selesai Membaca" memberikan hadiah +50 XP dan +10 Koin hanya pada pembacaan pertama. Setelah itu tombol terkunci berstatus "✓ Selesai Dibaca" agar tidak terjadi manipulasi farming XP.

---

## 3. Spesifikasi Sistem Gamifikasi

1. **Runtutan Belajar (Streak)**:
   - Bertambah +1 hari setiap kali pengguna melakukan aktivitas belajar (kuis, coretan, dongeng, atau check-in manual).
   - Direset ke 0 jika tidak ada aktivitas belajar selama 1 hari kalender penuh.
   - Rekor `max_streak` tersimpan permanen di profil pengguna.
2. **Kalkulasi Level & XP**:
   $$\text{Level} = \max\left(1, \left\lfloor \frac{\text{XP}}{100} \right\rfloor + 1\right)$$
   - Kenaikan level otomatis terjadi di backend setiap kali XP bertambah.
3. **Ekonomi Koin**:
   - Saldo awal user baru: 50 koin.
   - Pemasukan: Penyelesaian level (+10–25 koin), Misi Harian (+15–25 koin), Bonus Box (+30 koin), Dongeng (+10 koin).
   - Pengeluaran: Hint kuis (-5 koin), Membuka dongeng baru (-30 koin).
4. **Misi Harian Otomatis**:
   - 3 target harian otomatis berbasis tanggal: Selesaikan 1 level, Latihan menulis, dan Baca 1 dongeng.
   - Bonus Box terbuka otomatis jika seluruh misi hari tersebut tercapai (+50 XP & +30 Koin).
5. **Leaderboard Jawara Aksara**:
   - Peringkat murni berbasis data database riil diurutkan berdasarkan `streak DESC`, kemudian `xp DESC`.
   - Tidak menggunakan data tiruan (dummy mock).

---

## 4. Kriteria Keberhasilan Teknis (Acceptance Criteria)

- Seluruh endpoint API gamifikasi mengembalikan response JSON seragam dengan payload state mutakhir.
- Kanvas coretan bekerja mulus tanpa latensi pada perangkat mobile (touch event tidak memicu scrolling halaman).
- Audio pelafalan kata tersimpan di server (*file-based MD5 cache*) untuk menghemat kuota dan memangkas latensi jaringan.
- Seluruh aset Vue terkompilasi bersih via Vite (`npm run build`) dengan *zero syntax / lint errors*.
