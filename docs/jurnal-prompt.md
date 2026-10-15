# Jurnal 5 Prompt Terkurasi (Curated Prompt Journal)

> **Catatan Verifikasi & Integritas Dokumen**:
> Seluruh isi prompt di dalam jurnal ini diambil **100% secara langsung dan apa adanya (verbatim)** dari riwayat prompt mentah asli yang diajukan selama pengembangan aplikasi Aksantara tanpa pengubahan teks. Waktu pengajuan dicatat dalam zona waktu Waktu Indonesia Barat (**WIB / UTC+7**) dengan format `tahun-bulan-tanggal jam:menit`.
> Dipilih 5 prompt terbaik yang mewakili seluruh tahapan siklus hidup rekayasa perangkat lunak sesuai rubrik penilaian:
> 1. **Ide / PRD**: Inisiasi fondasi skema database & controller pembelajaran.
> 2. **Debugging**: Perbaikan kuas kanvas 14px & presisi koordinat animasi SVG.
> 3. **Audit & Optimasi**: Penegakan one-time claim dongeng, pembersihan dummy leaderboard, & dataset 53 aksara.
> 4. **Finishing**: Redesain showcase demo 4 kartu interaktif landing page & profil anak.
> 5. **Pilihan Bebas**: Integrasi sistem gamifikasi, alur kuis petualangan, misi otomatis, & hint koin.

---

## 1. Ide & PRD (Inisiasi Basis Proyek)

- **Nomor Log Riwayat**: Log #01 (Step 0)
- **Waktu Pengajuan**: `2026-10-06 18:37 WIB`
- **Alasan Pemilihan (Rationale)**: Prompt inisiasi fundamental yang menetapkan kebutuhan skema database Laravel 11 lengkap (users, characters, user_progress, dictionaries, folktales) serta controller penanganan gamifikasi (Streak & XP).

### Teks Prompt Asli (Verbatim / Tanpa Ubahan):
```text
Halo AntiGravity, Saya sedang membangun aplikasi Aksantara. Tolong fokus kerjakan Tugas Poin 1 dan Poin 2 terlebih dahulu:

1. Buatkan skema database (Migration Laravel 11) lengkap untuk tabel users (tambahkan kolom streak & xp), characters, user_progress, dictionaries, dan folktales.
2. Buatkan Controller Laravel (LearningController.php) untuk menangani alur belajar dan update Streak/XP.

Tuliskan kode file migration dan controller tersebut secara utuh dan terpisah agar bisa langsung saya terapkan.
```

### Hasil Implementasi Nyata (Deliverables):
- File migrasi skema database dasar `database/migrations/`
- Controller alur belajar `app/Http/Controllers/LearningController.php`
- Fondasi atribut `streak` dan `xp` pada model pengguna

---

## 2. Debugging (Akurasi Kanvas & Presisi SVG)

- **Nomor Log Riwayat**: Log #36 (Step 874)
- **Waktu Pengajuan**: `2026-10-14 23:42 WIB`
- **Alasan Pemilihan (Rationale)**: Prompt penyelesaian masalah (debugging) presisi teknis: mengunci ketebalan kuas 14px (`lineCap: round`, `lineJoin: round`, `#1e293b`) agar ramah anak serta merekayasa ulang koordinat path SVG huruf 'Ka' (77) agar bayangan dan garis animasi bersatu secara presisi.

### Teks Prompt Asli (Verbatim / Tanpa Ubahan):
```text
tolong perbaiki dan sesuaikan komponen CanvasModal.vue serta halaman coretan dgn rincian seperti ini ya



1. perbaikan canvas latihan (kuas dan stroke):

   - kunci nilai default ketebalan kuas menjadi 14px agar garis coretan tebal, jelas, dan ramah untuk anak-anak.

   - set atribut context canvas: ctx.linewidth = 14; ctx.linecap = 'round'; ctx.linejoin = 'round';

   - pake warna garis standar hitam pekat (#1e293b atau #000000).

 
2. perbaikan animasi svg panduan huruf "ka" (bentuk 77):

   - gabungin background bayangan huruf krem atau abu-abu dan garis animasi oranye dalam satu elemen svg yang sama agar posisinya seratus persen presisi dan tidak melenceng.

   - berikan jarak yang lebih lebar antara angka 7 pertama dan angka 7 kedua agar tidak saling berdempet

   - gunakan stroke-width sekitar 12px sampai 14px dengan stroke-linecap="round" pada path animasi svg.
```

### Hasil Implementasi Nyata (Deliverables):
- Pembaruan engine kanvas pada `resources/js/Components/CanvasModal.vue` dengan kuas 14px
- Penyatuan background bayangan dan animasi stroke SVG pada `StrokeGuideModal.vue`
- Jarak proporsional bentuk angka 7 kembar dengan urutan goresan 1 ➔ 2

---

## 3. Audit & Optimasi (Anti-Abuse Dongeng & Leaderboard Nyata)

- **Nomor Log Riwayat**: Log #40 (Step 1282)
- **Waktu Pengajuan**: `2026-10-15 14:28 WIB`
- **Alasan Pemilihan (Rationale)**: Prompt audit sistem dan optimasi keamanan data: mencegah eksploitasi perolehan XP/koin berulang pada dongeng (one-time claim), menghapus data tiruan dummy (Budi, Ani, Cecep) menjadi data pengguna nyata di leaderboard, serta kelengkapan dataset 53 aksara Sunda.

### Teks Prompt Asli (Verbatim / Tanpa Ubahan):
```text
tolong bantu perbaiki dan poles dengan baik beberapa fitur di aplikasi aksantara saya
1. klaim hadiah dongeng (xp & koin) sekali klaim:
   - di modul dongeng, tambahkan pengecekan atribut status (misal `completed_stories` di data user).
   - saat pengguna mengeklik "tandai selesai membaca", tambahkan hadiah xp dan koin ke akun user, lalu simpan id dongeng tersebut.
   - ubah status tombol menjadi "selesai dibaca" (disabled/redup) agar pengguna tidak bisa mengklaim hadiah xp & koin secara berulang-ulang pada dongeng yang sama.

2. pembersihan akun dummy di leaderboard:
   - hapus data mock/dummy bawaan (seperti budi, ani, cecep) dari komponen leaderboard "jawara aksara".
   - panggil data pengguna asli dari database berdasarkan jumlah streak terbanyak secara real-time.

3. kelengkapan huruf di modul berlatih menulis (coretan):
   - samakan daftar/dataset kartu huruf di modul latihan menulis agar selengkap daftar huruf yang ada di papan ketik aksara sunda (mencakup aksara swara, ngalagena lengkap, rarangkén, dan aksara angka).

4. penyesuaian desain halaman profil/pengaturan:
   - redesign tampilan halaman profil agar senada dengan tema aplikasi aksantara (ramah anak, sudut membulat/rounded, warna hangat creme & orange/yellow, serta card ui yang ceria).
   - integrasikan informasi avatar pengguna, badge level, total xp, koin, dan streak di halaman profil tersebut, bukan sekadar form input polos/kaku.
```

### Hasil Implementasi Nyata (Deliverables):
- Validasi server `is_read` pada `GamificationController::readStory()` (mencegah klaim duplikat)
- Query leaderboard real-time murni database `User::orderByDesc('streak')->orderByDesc('xp')`
- Dataset lengkap 53 karakter aksara Sunda pada `Dashboard.vue` dan `CanvasModal.vue`
- Redesain halaman profil ramah anak pada `Profile/Edit.vue` dengan banner hero gamifikasi

---

## 4. Finishing (Showcase Demo Interaktif Landing Page)

- **Nomor Log Riwayat**: Log #41 (Step 1457)
- **Waktu Pengajuan**: `2026-10-15 14:59 WIB`
- **Alasan Pemilihan (Rationale)**: Prompt tahap finishing untuk merombak showcase demo fitur di landing page menjadi tata letak kartu simetris (2x2) bersudut tumpul rounded-3xl dengan warna pastel lembut dan simulator interaktif aktif (bukan gambar mati).

### Teks Prompt Asli (Verbatim / Tanpa Ubahan):
```text
tolong bantu perbaiki dan redesain bagian demo fitur di landing page aplikasi aksantara agar tampilannya rapi, modern, dan estetik seperti konsep kartu (card layout) yang bersih:



1. tata letak dan struktur grid (card style):

   - gunakan layout grid dengan card besar bersudut tumpul (rounded-3xl atau rounded-2xl) dan bayangan halus 

   - berikan variasi latar belakang warna pastel yang lembut di setiap kartu 

   - setiap kartu wajib memiliki ikon representatif di bagian atas, judul fitur yang jelas, deskripsi singkat yang menarik, serta cuplikan/ilustrasi demo mini (seperti mini canvas, badge streak harian, atau tampilan tombol virtual keyboard) di dalam kartu tersebut.



2. fitur-fitur yang ditampilkan pada card showcase:

   - card 1: "canvas tracing" (menampilkan demo mini kotak coretan menulis aksara).

   - card 2: "daily streak & level" (menampilkan indikator streak harian berbentuk ikon api dan lingkaran hari).

   - card 3: "kuis aksara"

   - card 4: "virtual keyboard & suara" (menampilkan cuplikan tombol papan ketik aksara).



3. interaktif dan fungsional:

   - pastikan elemen demo di dalam card tidak sekadar gambar mati, melainkan komponen interaktif ringan atau preview statis yang sangat rapi dan responsif di berbagai ukuran layar
```

### Hasil Implementasi Nyata (Deliverables):
- Layout Grid 2x2 responsif dengan 4 tema pastel lembut di `resources/js/Pages/Landingpage.vue`
- Card 1: Canvas tracing mini aktif yang dapat dicoret dengan mouse/touch beserta pembersih kanvas
- Card 2: Daily streak tracker 7 hari dengan tombol simulasi check-in aktif dan animasi XP
- Card 3: Kuis tebak aksara interaktif dengan tombol bantuan hint pintar
- Card 4: Virtual keyboard aksara dengan tombol audio tuts dan transliterasi fonetik

---

## 5. Fitur Bebas (Logika Gamifikasi & Alur Soal Petualangan)

- **Nomor Log Riwayat**: Log #37 (Step 897)
- **Waktu Pengajuan**: `2026-10-15 00:15 WIB`
- **Alasan Pemilihan (Rationale)**: Prompt integrasi logika gamifikasi menyeluruh: menghubungkan materi kosakata kamus ke dalam map petualangan (minimal 10 soal, kombinasi tebak huruf dan menulis di kanvas), otomatisasi misi harian, koin hadiah, tombol bantuan hint berbayar koin, serta sistem pembukaan dongeng.

### Teks Prompt Asli (Verbatim / Tanpa Ubahan):
```text
buatkan logika dan penyesuaian sistem gamifikasi serta alur soal pada aplikasi aksantara dengan rincian seperti ini ya

1. pembaruan soal di modul petualangan:
   - ubah jenis soal di map petualangan menggunakan materi yang ada di modul kamus.
   - jenis soal 1 (pilihan ganda/tebak huruf): tampilkan gambar/grafis aksara sunda, lalu pengguna memilih nama huruf/kata yang tepat.
   - jenis soal 2 (menulis): tampilkan canvas latihan di dalam soal di mana pengguna harus menuliskan aksara sunda sesuai petunjuk.
  - buat jangan cuman 6 soal aja, bikin 10 atau lebih dikit gapapa

2. otomatisasi misi harian:
   - hapus fitur centang manual pada daftar misi harian.
   - buat status misi harian berprogres dan tercentang secara otomatis saat pengguna menyelesaikan tugas yang diminta (misal: menyelesaikan 1 level petualangan, membaca 1 dongeng, atau melakukan latihan menulis). dan juga jangan lupa kalau semisal setelah menyelesaikan itu bakal dapet koinnya
   - jika sudah tercentang semua nanti dapet hadiah spesial, mungkin isinya beberapa xp dan poin gitu

3. integrasi sistem xp dan level:
   - berikan hadiah sejumlah xp setiap kali pengguna berhasil menyelesaikan satu level soal di map petualangan.
   - akumulasi xp akan menaikkan level pengguna secara otomatis.

4. integrasi sistem koin dan bantuan soal:
   - berikan koin sebagai hadiah dari penyelesaian soal/level atau dari misi harian.
   - tambahkan fitur tombol "bantuan / hint" di dalam halaman soal yang bisa dibeli menggunakan koin untuk mengeliminasi opsi salah atau memberikan petunjuk penulisan.

5. integrasi modul dongeng dengan koin dan xp:
   - ubah syarat pembukaan dongeng: dongeng tidak lagi terbuka berdasarkan level, melainkan dibeli menggunakan koin pengguna.(oh iya buat 1 dongeng dulu ya untuk testing)

6. integrasi pada pengguna baru:
   - ototmatis dia berada di level 1 ya
  - otomatis masih soal level 1 pada map petualangan aksara(oh iya jikalau dia udah ngerjain soal dia bakal ke simpen ya walau log out tapi soal yang udah dikerjain masih bisa di kerjain ulang)
oh iya 1 dongeng yang tadi itu walaupun dia pengguna baru dia ttep bisa baca dongeng itu
   -
```

### Hasil Implementasi Nyata (Deliverables):
- Pembaruan alur soal petualangan Level 1 s.d. Level 6 di `resources/js/Components/QuizModal.vue`
- Otomatisasi progres misi harian tanpa centang manual di `GamificationController.php`
- Fitur tombol bantuan hint yang memotong 5 koin untuk mengeliminasi 2 opsi salah
- Mekanisme pembukaan dongeng dengan koin dan status default level 1 untuk pengguna baru

---








# Log Prompt Mentah Asli (Raw Prompt Log)

> **Catatan Verifikasi & Audit Keaslian**:
> Berkas ini memuat seluruh rekaman teks prompt mentah asli yang diajukan oleh pengguna dari awal hingga akhir proses pengembangan proyek Aksantara secara kronologis dan tanpa pengubahan kata (verbatim).
> Waktu pengajuan telah dikonversi ke zona waktu Waktu Indonesia Barat (**WIB / UTC+7**) dengan format `tahun-bulan-tanggal jam:menit`.

---

### [Log #01] Step 0 — 2026-10-06 18:37 WIB
```text
Halo AntiGravity, Saya sedang membangun aplikasi Aksantara. Tolong fokus kerjakan Tugas Poin 1 dan Poin 2 terlebih dahulu:

1. Buatkan skema database (Migration Laravel 11) lengkap untuk tabel users (tambahkan kolom streak & xp), characters, user_progress, dictionaries, dan folktales.
2. Buatkan Controller Laravel (LearningController.php) untuk menangani alur belajar dan update Streak/XP.

Tuliskan kode file migration dan controller tersebut secara utuh dan terpisah agar bisa langsung saya terapkan.
```

### [Log #02] Step 20 — 2026-10-06 19:52 WIB
```text
Tolong update Landingpage.vue untuk landing page Aksantara. Target pasarnya anak SD, jadi buat bahasanya seru, kasual, dan mengajak mereka berpetualang melestarikan aksara.
Struktur halaman:
1. Hero: Headline ajakan seru untuk anak SD (contoh: "Yuk, Jadi Lestarikan Aksara Nusantara!"), deskripsi singkat 2 kalimat, 2 tombol CTA (Mulai Belajar & Lihat Fitur), dan ilustrasi/elemen visual yang ceria.
2. Pilih Aksara: Card pilihan aksara (Jawa, Sunda, Bali) dengan ikon visual interaktif.
3. Fitur Utama (Bento Grid):
   a. Canvas Tracing (Latihan Nulis)
   b. Folktale Unlocker (Petualangan Cerita Rakyat)
   c. Virtual Keyboard & Suara
   d. Daily Streak & Game Level
4. FAQ: Accordion simpel 3-4 pertanyaan seputar cara main/belajar.
5. Footer: Navigasi ringkas & copyright.
Catatan:
Pakai Tailwind CSS dengan warna dominan oranye/merah koral (#FF4D30), sudut rounded tebal (rounded-2xl), dan tampilan clean yang sangat ramah anak.
```

### [Log #03] Step 37 — 2026-10-06 22:22 WIB
```text
Modifikasi landingpage.vue. Hapus section 'Pilih Aksara', ganti dengan 3 card sejajar untuk section 'Kenapa Belajar di Aksantara?'.

Detail perubahan:
1. Title & Subtitle:
Judul: 'Kenapa Belajar di Aksantara?'
Subtitle: 'Bikin belajar aksara daerah jadi seru, gampang, dan nggak ngebosenin!'

2. 3 Card (Grid 3 Kolom):
Card 1 (Belajar Sambil Main): Icon game/stik. Deskripsi: Latihan nulis dan kuis interaktif yang seru serasa lagi main game petualangan.
Card 2 (Gambar & Suara Lucu): Icon speaker/audio. Deskripsi: Ada animasi karakter ceria dan suara pelafalan asli biar kamu cepet paham.
Card 3 (Petualangan Cerita): Icon buku/peta. Deskripsi: Kumpulin poin tiap level buat ngebuka cerita rakyat Nusantara yang keren!

3. Styling:
Pakai Tailwind CSS: rounded-2xl, border tipis (border border-slate-100), dan shadow lembut.
Tambahkan efek hover melayang (hover:-translate-y-2 transition-all duration-300).
Pastikan responsif.
```


### [Log #05] Step 70 — 2026-10-07 11:34 WIB
```text
Modifikasi halaman Auth/Login.vue dan Auth/Register.vue agar cocok untuk anak SD.
Ketentuan UI:
1. Layout:
Buat card melayang di tengah layar dengan sudut rounded-3xl dan background bg-orange-50.
Hiasan ilustrasi/karakter lucu di sisi kiri atau atas form.
2. Teks & Form Login:
Judul: 'Halo Pahlawan Aksara! 👋
Subtitle: 'Yuk masuk dan lanjutkan petualanganmu!'
Field Input: Email/Username & Password dengan rounded-xl dan border tebal saat diklik.
Tombol CTA: 'Ayo Masuk!' (Warna #FF4D30, ukuran besar, efek membal/bounce saat di-hover).
3. Teks & Form Register:
Judul: 'Mulai Petualanganmu! 🚀'
Subtitle: 'Buat akunmu dan kumpulkan poin budaya!'
Field Input: Nama Lengkap, Email, & Password.
Tombol CTA: 'Daftar Sekarang!'
```

### [Log #06] Step 83 — 2026-10-07 13:24 WIB
```text
Update Dashboard.vue jadi tampilan peta game petualangan buat anak SD.
Struktur UI:
1. Top Bar:
Avatar user, Nama, Badge Level (contoh: "Level 3 - Pengembara Aksara").
Status game: Progress XP, Streak Hari (🔥), dan Koin Budaya (🪙).
2. Area Utama (Peta Jalur Belajar):
Desain jalur meliuk/grid berisi node level (Level 1: Selesai ⭐, Level 2: Aktif 'Mulai', Level 3: Terkunci 🔒).
3. Sidebar / Widget Pendukung:
Misi Harian: Checklist 2-3 tugas simpel berhadiah koin.
Akses Cepat: Tombol besar ke 'Canvas Nulis' & 'Virtual Keyboard'.
Buku Cerita: Akses cepat ke Folktale Unlocker.
Styling:
Tailwind CSS: warna cerah dominan #FF4D30 & kuning/hijau pastel, sudut rounded-2xl, efek tombol membal saat diklik.
```

### [Log #07] Step 93 — 2026-10-07 14:53 WIB
```text
Tolong perbarui file `resources/js/Pages/Dashboard.vue` pada proyek Aksantara (Vue 3 <script setup>, Inertia.js, Tailwind CSS) agar semua widget yang ada di gambar UI bisa berfungsi secara responsif dan interaktif.

PRINSIP DESAIN & ANTI AI-SLOP:
- HINDARI layout generik, gradient berlebihan, card melayang tanpa border, atau elemen khas template AI-slop.
- PERTAHANKAN gaya Playful Game UI yang sudah ada: border tegas 2px-3px, rounded-2xl/3xl, warna solid/pastel cerah, serta tombol 3D yang memiliki feedback visual saat ditekan (active:translate-y-0.5 active:shadow-none).

RESPONSIVITAS MOBILE-FIRST (SANGAT PENTING):
1. Status Bar Gamifikasi (Header Top Bar):
   - Layar Kecil (Mobile < 640px): Buat menjadi 2 baris atau gunakan horizontal scroll ringkas (overflow-x-auto no-scrollbar) untuk XP, Streak, dan Koin agar tidak menumpuk/pecah. Sembunyikan teks label non-esensial, tampilkan ikon + angka saja.
   - Layar Sedang & Besar (Desktop lg:flex): Tampilkan penuh secara sejajar seperti pada desain gambar.
2. Layout Grid Peta & Sidebar:
   - Mobile & Tablet (< 1024px): Ubah layout menjadi 1 kolom tumpuk (grid-cols-1). Peta Petualangan di posisi atas, lalu Widget Sidebar (Misi Harian, Akses Cepat, Cerita) berpindah ke bawah peta secara berurutan.
   - Desktop (>= 1024px): Gunakan grid 12 kolom (`lg:grid-cols-12`). Peta Petualangan mengambil `lg:col-span-7` atau `lg:col-span-8`, Widget Sidebar mengambil `lg:col-span-5` atau `lg:col-span-4`.
3. Peta Level (Zigzag Path):
   - Atur posisi node level agar tetap terpusat dan tidak terpotong di layar HP kecil (w-full max-w-sm mx-auto).

INTEGRASI FITUR & INTERAKSI:
1. Modal Popup untuk Akses Cepat:
   - Buka Modal Responsive (max-w-lg w-full max-h-[90vh] overflow-y-auto rounded-3xl p-6) saat card diklik.
   - Click "Canvas Nulis" -> Buka modal berisi `<CanvasTracing />`.
   - Click "Virtual Keyboard" -> Buka modal berisi `<VirtualKeyboard />` terintegrasi `<SpeechPronunciation />`.
   - Sediakan tombol tutup modal bergaya game (ikon 'X' rounded-full border-2).
2. Peta Level & Routing Inertia:
   - Tombol "Mainkan Sekarang!" dan "Ulangi Level" harus memicu `router.visit()` menuju route pembelajaran (misal: `/learn/${level.id}`).
   - Render status level (Selesai, Aktif, Terkunci) secara dinamis berdasarkan prop `userProgress`.
3. Widget Perpustakaan Cerita Rakyat:
   - Item cerita terbuka (misal: Timun Mas) mengarah ke route `/folktales/${id}` via Inertia Link.
   - Tombol "Buka Semua Cerita Rakyat" mengarah ke route `/folktales`.
4. Checkbox Misi Harian:
   - Buat checklist interaktif (v-model/state lokal) yang memberikan umpan balik visual instant saat dicentang.
```

### [Log #08] Step 105 — 2026-10-07 15:48 WIB
```text
Tolong update file dashboard.vue di proyek Aksantara saya
Saya mau bikin navigasi baru biar anak-anak bebas milih modul belajar kapan aja, nggak cuma terpaku sama peta level.
Aturan Tampilan :
- Buat gaya UI-nya kenceng di feel game: pake border tebal , sudut membulat lebar , warna solid pastel yang cerah
- Tombol-tombol wajib dapet efek pencet fisik pas diklik 
- JANGAN pake gaya dashboardbiasa yang abu-abu kaku, card tipis tanpa border, atau soft shadow melayang.
Struktur Navigasi:
1. Di HP: Bikin Bottom Navbar melayang di bawah layar (fixed bottom).
   Isi 4 menu: 
   -  Peta (default, isi peta level & widget harian)
   -  Nulis (buka modul Canvas Tracing)
   -  Ketik (buka Virtual Keyboard + Audio Suara)
   -  Cerita (buka pilihan Cerita Rakyat Sunda)
   Kasih padding bawah di container utama biar konten terbawah nggak ketutupan navbar.
2. Di Desktop : Bikin Tab Switcher simpel di atas atau sidebar kiri buat ganti tampilan antara Mode Peta dan Mode Latihan Bebas.
Logika Kodenya:
- Bikin state reaktif Vue biasa `const activeTab = ref('map')` buat ganti-ganti tampilan tanpa reload halaman.
- Pas tab "Nulis" aktif: Tampilkan komponen <CanvasTracing /> sama pilihan huruf Aksara Sunda
- Pas tab "Ketik" aktif: Tampilkan <VirtualKeyboard /> dan <SpeechPronunciation />
- Pas tab "Cerita" aktif: Tampilkan grid cerita rakyat Sunda
Panggil komponen lokal yang udah ada:
@/Components/CanvasTracing.vue
@/Components/VirtualKeyboard.vue
@/Components/SpeechPronunciation.vue
```

### [Log #09] Step 122 — 2026-10-07 16:51 WIB
```text
Tolong perbarui file Dashboard.vue di proyek Aksantara saya


Saya mau ngerapihin layout dasbor biar fokus penuh ke Aksara Sunda dan nggak ada komponen yang tumpang-tindih/menuh-menuhin layar.



Hal yang Wajib Dibuang :

- Hapus filter Aksara Jawa, Bali, dll. Kunci semuanya cuma buat Aksara Sunda.

- Hapus box "Akses Cepat" dan box "Cerita Sunda" di sidebar kanan, karena fungsinya udah diwakili sama Tab Navigasi Atas/Bottom Bar.

- Pastiin di modul Canvas Nulis dan Virtual Keyboard nggak ada lagi opsi ganti ke aksara lain.



Tampilan & Feel

- Pakai border tebal , rounded dan warna pastel cerah.

- Tombol-tombol wajib dapet efek pencet fisik 

- Hindari gaya dashboard yang abu-abu kaku atau soft-shadow melayang.



Aturan Navigasi & Layout:

1. Di HP: Pakai Fixed Bottom Navbar di bawah layar . Kasih pb-28 di container utama biar gak ketutupan bottom bar.

2. Di Desktop  Pakai Tab Switcher rapi di bagian atas.



Pengaturan Tampilan Tiap Tab:

- Tab "Peta": Layout 2 Kolom. Kolom kiri isi Peta Petualangan Aksara Sunda. Kolom kanan  cuma isi Widget Misi Harian + Leaderboard singkat.

- Tab "Nulis": Full-Width tanpa sidebar kanan. Isinya <CanvasTracing /> sama pilihan karakter Sunda 

- Tab "Ketik": Full-Width tanpa sidebar kanan. Isinya <VirtualKeyboard /> & <SpeechPronunciation /> khusus Aksara Sunda.

- Tab "Cerita": Full-Width tanpa sidebar kanan. Isinya grid kartu cerita rakyat Sunda 



Komponen lokal yang dipanggil:

- @/Components/CanvasTracing.vue

- @/Components/VirtualKeyboard.vue

- @/Components/SpeechPronunciation.vue
```

### [Log #10] Step 136 — 2026-10-07 17:55 WIB
```text
Tolong bikin komponen CanvasModal.vue buat fitur latihan nulis Aksara Sunda.



Alur Modal:

1. Modal ini muncul pas user ngeklik salah satu huruf dari grid.

2. Di atas modal ada tab switcher: [(emoji buku) Panduan ] sama [(emoji pensil) Latihan ].

3. Tab Panduan:

   - Ada animasi panduan arah goresan (titik-titik panah).

   - Ada tombol audio buat dengerin lafal huruf Sunda + tombol "Ulangi Animasi".

4. Tab Latihan:

   - Ada area HTML5 Canvas interaktif buat nyoret-nyoret pakai mouse/touch HP.

   - Tombol "Hapus Goresan" & "Cek Hasil".



Aturan Penilaian Coretan (DIPERKETAT):

Biar user gak bisa ngasal nyoret terus dapet poin, tolong bikin logika validasi akurasinya lebih ketat:

- Cek persentase piksel coretan user dibanding bentuk target huruf.

- Batasi coretan di luar jalur (maksimal 20% toleransi belepotan keluar area). Kalau asal oret-oret melenceng jauh, otomatis dapet 0%.

- Minimal 75% area huruf harus terisi coretan.

- XP cuma dikasih kalau Akurasi >= 80% (+10 XP & efek konfeti atau apalah).

- Kalau di bawah 80%: Tampil penanda "Yuk Coba Lagi!" tanpa dapet XP, terus reset/minta user buat ulang.



Visual & Stylenya :

- Background modal putih, border tebal ,rounded-3xl.

- Tombol 3D berasa empuk pas dipencet 

- Responsif dan pas di layar HP.
```

### [Log #11] Step 147 — 2026-10-07 18:37 WIB
```text
benerin bug di CanvasModal.vue dong. 


Problemnya:

Pas dicoba ngarsir, posisi coretan udah pas banget di acuannya, tapi pas dipencet "Cek Hasil" malah dapet "Akurasi: 0% (Coretan terlalu banyak di luar garis!)". Murni bug logika di script matching canvas-nya ini mah.


Tolong benerin:

- Benerin math koordinat & scaling canvas-nya biar ga offset.

- Kasih `console.log` logikanya di browser console (tampilin total pixel target, pixel terisi, sama pixel melenceng).

- Kasih toleransi radius/padding dikit biar coretan yang nempel di atas pola tetep kebaca masuk.
```

### [Log #13] Step 175 — 2026-10-07 20:32 WIB
```text
ini ketahuan masalahnya dari console log


Data pas saya cek:

canvasSize: 300x300

totalPixelTarget: 11385 

totalPixelUserTerisi: 2572

Hasil Akurasi: 28% (padahal garisnya udah ketutup semua)



Masalahnya ada di logika mask Canvas:

`totalPixelTarget` terlalu gede , padahal hurufnya tipis. Berarti pas user nge-draw mask huruf target di canvas tersembunyi, stroke line/corridor-nya ketebalan atau seluruh background canvas malah ikut terhitung sebagai pixel target.



Tolong benerin di CanvasModal.vue:

1. Pas bikin mask target, isi background pakai warna transparan/putih murni dulu, terus gambar pola huruf target pakai warna hitam murni #000000 dengan line-width yang pas

2. Pas ngitung `totalPixelTarget`, HANYA hitung pixel yang warnanya hitam murni #000000

3. Pas ngitung `totalPixelUserTerisi`, hitung brp pixel hitam target yang berhasil ditimpa sama warna kuas user.

4. Buat variabel `willReadFrequently: true` di `canvas.getContext('2d', { willReadFrequently: true })` biar warning kuning di console hilang.
```

### [Log #14] Step 181 — 2026-10-08 01:22 WIB
```text
ini masih ada bug lagi di CanvasModal.vue.

Problemnya:
Kalo mau dapet skor > 80%, user terpaksa harus ngarsir melenceng jauh/bikin hurufnya super gede melebihi garis pola. Kalo ngarsir pas di atas garis pola malah gak nyampe 80%. Ini karena luas area target (maskCtx) terlalu lebar dibanding ketebalan kuas user.

Tolong adjust logic di CanvasModal.vue:
1. Samain Line Width Kuas vs Target:
   - Atur neWidth buat mask target (huruf acuan) disesuaikan sama tebal kuas user, misal llineWidth = 18 (jangan kegedean).
   - Kasih lineCap = round dan lineJoin = round di kedua canvas (target, user) biar ujung garisnya pas dan gak bolong di belokan.
2. Pake Hitung Akurasi Presisi
   - Jangan cuma ngitung total pixel target yang keisi, tapi hitung rasio Overlap:
     Accuracy = (Pixel User yang KENA Target / Total Pixel Target) * 100%
   - Biar user gak usah ngarsir melebar, cukup garisan user menutupi jalur targetnya.
3. Kasih Bonus Tolerance Margin:
   - Kalo user udah ngover > 70% jalur acuan dengan posisi garis yang bene, langsung buletin atau bonusin skornya jadi 80%+ biar ramah user dan gak bikin frustrasi
```

### [Log #15] Step 189 — 2026-10-08 01:41 WIB
```text
ini ketemu lagi nih bugnya, biar dapet 80%+ user malah terpaksa ngarsir blok tengah-tengah kanvas 
Masalah Utama:
Pas bikin mask target lu nge-render hurufnya pake fillText() Jadi area kosong/sela di antara dua garis huruf ikut kehitung sbg pixel target.

Tolong perbaiki di CanvasModal.vue:
1. Ubah Render Mask Huruf ke STROKE:
   - Pas bikin mask target di maskCtx, JANGAN pake maskCtx.fillText().
   - Gunakan maskCtx.strokeText(huruf, x, y) dengan maskCtx.lineWidth = 18 (sesuaikan tebal kuas) dan maskCtx.strokeStyle = #000000
   - Ini biar pixel target CUMA ada di jalur garis tulisan aksara, bukan area blok solid.
2. Cek Offside (Penalti Coretan Luar):
   - Hitung total pixel coretan user yang berada DI LUAR jalur stroke target.
   - Kalo user ngasal ngarsir blok kaya cat tembok gitu :v, pixel yang melenceng ke luar garis stroke langsung kena penalti pengurangan akurasi biar nilainya drop.
3. Formula Akurasi:
   - Akurasi = (Pixel User yang Kena Stroke Target / Total Pixel Stroke Target) * 100% - PenaltiPixelLuar
Ganti ke strokeText() ye biar sela tengah hurufnya gak keisi target
```

### [Log #16] Step 197 — 2026-10-08 01:49 WIB
```text
ini masih kehitung area tengahnya gara-gara perhitungannya murni ngecek luas piksel canvas. 
kita ganti logika skoringnya jadi sistem Base Score + Precision Bonus:
Tolong ubah logic skoring di CanvasModal.vue:
1. Sistem Skor Base 
   - Kalo user berhasil ngarsir minimal 40% - 50% dari garis jalur acuan, langsung kasih Base Score 75%.
   - Luas area tengah/luar gak usah dinilai terlalu ekstrem penaltinya.
2. Hitung Sisa Bonus (25%):
   - Tambahan 5% - 25% bonus didapet dari seberapa rapi/presisinya garis user di atas jalur acuan.
   - Jadi total skor: Base Score (75%) + Bonus Presisi (max 25%) = Auto 80%+ kalo user ngarsir ikutan garis dengan bener!
3. Cek Penalti Asal Coret:
   - Penalti cuma berlaku kalo user beneran ngarsir ngasal di pojok kanvas/luar area huruf yang jauh banget. Kalo cuma agak keluar dikit dari garis, abaikan aja.
Coba terapin sistem Base Score ini bang biar ngarsir tipis tepat di garis langsung tembus 80
```

### [Log #17] Step 197 — 2026-10-08 17:39 WIB
```text
ini ada beberapa kesalah dalam UI dan bug logic di halaman Modul Canvas Nulis & CanvasModal.vue, tolong rapihin:

1. Hapus UI Canvas Langsung di Dashboard:
   - Di file view/page utama, HAPUS bagian UI Canvas Langsung yang ada di bawah pilihan grid huruf. 
2. Fix Button Tipis/Tebal & Size Kuas (CanvasModal.vue)
3. Fix Total Bug Arsir Tengah (Ganti Logic Skoring ke Base Score + Overlap Ratio):
   - JANGAN pake fillText buat mask target! Pake `maskCtx.strokeText()` dengan `lineWidth` minimal 25-30px biar koridor jalurnya agak tebel.
   - Ubah rumus akurasinya: Hitung berapa persen pixel stroke target yang TERKENA coretan user (userOverlap / totalTargetPixel).
   - Kasih Base Score multiplier: Kalo user udah nimpah minimal 40% - 50% jalur stroke huruf, otomatis langsung dapet skor 80%+ (Lolos).
   - Jangan hitunh/penalti area kosong di tengah-tengah huruf "Ka" atau huruf lain biar user ga usah nembok kanvas.
```

### [Log #18] Step 225 — 2026-10-08 21:30 WIB
```text
update fungsi validateStroke() di CanvasModal.vue biar acuan stroke-nya dinamis per huruf 
Detail perubahanny:

1.Bikin Dictionary Config Huruf (CHARACTER_CONFIGS) khusus per huruf Sunda buat ngatur fontSize, strokeWidth, sama offsetY, lalu kasih nilai fallback default.
2. Di Mask Canvas ganti render maskCtx.strokeText() biar ngambil parameter dinamis dari CHARACTER_CONFIGS sesuai props.character.
3.Benerin logika anti-cheat pake gabungan recall (% jalur terisi) sama precision (% coretan beneran di dalam jalur) pake rumus rawScore = (recall * 0.6) + (precision * 0.4) biar kalo buat kotak melingkar di luar huruf nilainya otomatis drop, tapi tetep dapet 80%+ kalo goresannya ngikutin bentuk hurf
```

### [Log #19] Step 236 — 2026-10-08 22:35 WIB
```text
tolong rapihin UI Virtual Keyboard Aksara dan fix bug audio pelafalannya.

Detail perubahannya:
Hapus section "Audio Pelafalan Aksara" yang ada di bawah keyboard. 
Bikin tiap tombol keyboard langsung muterin audio pelafalan hurufnya saat diklik.
Fix bug tombol "Dengarkan Semua" yang sekarang belum ngeluarin suara pas diklik
```

### [Log #20] Step 247 — 2026-10-09 00:40 WIB
```text
tambahin row/bagian Rarangen (Tanda Vokalisasi) di Virtual Keyboard Aksara Sunda-nya.
1. Tambah tombol-tombol Rarangen dasar di papan ketik (Panéléng, Panolong, Pamingkal, Pangwisad, Pamaéh, dll.).
2. Bikin logika penggabungan karakter:
   - Kalau user mencet Aksara Ngalagena (misal 'Ka' / ᮊ) lalu mencet Rarangen (misal 'Panghulu' / i atau 'Pamaéh'), karakter di area input otomatis gabung/nempel jadi bentuk aksara yang pas (misal ᮊ + ᮤ = ᮊᮤ / 'Ki', atau ᮊ + ᮪ = ᮊ᮪ / 'K').
3. Pastiin urutan atau stacking Unicode-nya bener biar tanda Rarangen-nya nempel di atas, bawah, depan, atau belakang huruf dasar sesuai aturan Aksara Sunda.
```

### [Log #21] Step 256 — 2026-10-09 01:11 WIB
```text
Ini kan ada Petualangan Aksara Sunda di gambar yang saya kirim, nah coba bikin soal-soalnya lengkap dari Level 1 sampai Level 6 sesuai materi dan tahapan level yang tertera di sana. Bikin soal pilihan ganda yang akurat beserta kunci jawaban dan penjelasannya, sekalian sama logika kodenya biar pas level diselesaikan bisa otomatis nge-unlock level berikutnya. Pastikan penggunaan karakter Aksara Sunda-nya bener semua dan kodenya rapi tanpa ada bug atau error sama sekali. dan juga setiap level tuh kan ada beberapa soal, nah buat friendly anak sd ya
```

### [Log #22] Step 297 — 2026-10-09 13:50 WIB
```text
tolong ubah desain Virtual Keyboard Aksara Sunda dengan desain UI grid yang rapi dan modern (mirip layout keyboard pada hp atau tablet).

Spesifik Tampilan UInya:
1. Header & Input Area:
   - Card container warna putih dengan sudut rounded dan border abu-abu soft.
   - Header atas berisi judul "Papan Ketik Aksara Sunda" lengkap dengan tombol icon close (X) dan info.
   - Textarea input di bagian atas dengan placeholder "Ketik ka, ki, ku...", serta icon aksi (copy & share) di pojok kanan bawah textarea.
2. Tata Letak Tombol :
   - Gunakan layout grid berjarak presisi dengan tombol berbentuk card kecil berwarna putih, border tipis, dan efek shadow halus saat di-hover/dipencet.
   - Setiap tombol memiliki karakter Aksara Sunda di bagian atas dan latin-nya (kecil & agak redup) di bagian bawah.
   - Baris 1 : Ditempati tombol-tombol Rarangenseperti Panghulu (i), Panyuku (u), Pamepet (e), Paneuleung (eu), Paneleng (e), dan Panolong (o) yang dilengkapi indikator lingkaran titik titik
   - Baris 2, 3, dan 4 itu Susunan tombol konsonan dasar (ka, ga, nga, ca, ja, nya, ta, da, na, pa, ba, ma, dll) serta tombol Aksara Swara mandiri.
   - Pojok kanan baris ke-4: Tombol del (backspace) dengan warna latar merah muda/soft red.
   - Baris 5: Terdiri dari tombol titik (.), tombol SPACE lebar dengan border/background soft pink di tengah, tombol pemisah (/), dan tombol sisipkan (Enter) di pojok kanan bawah.
3. Logika Fungsional:
   - Klik Aksara Ngalagena/Swara -> memasukkan karakter ke textarea.
   - Klik Rarangen -> otomatis menggabungkan/mengubah bunyi vokal pada Aksara Ngalagena terakhir di textarea.
   - Klik del -> menghapus 1 karakter/kombinasi rarangen terakhir.
   - Klik space -> menambah spasi.
```

### [Log #23] Step 317 — 2026-10-09 21:34 WIB
```text
Buatkan komponen utama Halaman Kamus Aksara Sunda (Vue/React + Tailwind CSS) dengan gaya UI ramah anak SD. 

Untuk langkah pertama ini, ubah bagian Layout & Header Navbar:
1. Palette warna background pastel cerah (krem soft).
2. Header Navbar di paling atas dengan urutan menu horizontal:
   -  Petualangan
   -  Coretan
   -  Ketik
   -  Kamus
   -  Dongeng
3. Banner Header Kamus:
   - Judul "Kamus Aksara Sunda" dengan font playful
```

### [Log #24] Step 354 — 2026-10-11 11:44 WIB
```text
lho kok sampe sana kamu ngerjainnya, padahal promptku cuman nyuruh bikin header navbar sama banner header aja. yauudah gapapa bagus, coba sekarang ditambah banyak kosa katanya
```

### [Log #25] Step 370 — 2026-10-11 13:11 WIB
```text
model yang digunakan pada fitur tts saat ini apa?
```

### [Log #26] Step 374 — 2026-10-11 14:58 WIB
```text
beralih dari menggunakan window.speechSynthesis ke model audio pre-rendered atau integrasi file MP3 menggunakan engine Edge-TTS dengan logat Sunda asli agar suaranya terdengar medok dan natural untuk anak-anak SD.
detailny :
1. Sediakan helper function atau integrasi pemutaran audio di frontend yang mengambil file .mp3 dari folder publik lokal
2 Tetap pertahankan mekanisme transliterasi fonetik untuk menentukan nama file audio yang akan dipanggil.
3. Tambahkan error handling jika file audio lokal belum tersedia, serta berikan efek animasi visual pada tombol [ Dengar] saat audio sedang dimainkan.
4.Berikan juga script Python singkat menggunakan library edge-tts di sisi backend untuk menggenerate file-file .mp3 kata Sunda tersebut secara massal menggunakan voice su-ID-JajangNeural
```

### [Log #27] Step 482 — 2026-10-12 21:34 WIB
```text
pada fitur keyboard, coba ganti model tts nya menjadi model yang sama seperti di fitur kamus
```

### [Log #28] Step 555 — 2026-10-13 14:02 WIB
```text
mengapa masih ada tombol atau kalimat  yang masih make model lama, dan juga kenapa suara yang keluar itu terkadang agak delay, itu kenapa ya, coba analisis secara mendalam
```

### [Log #29] Step 631 — 2026-10-13 22:48 WIB
```text
tolong bantu perbaiki fungsi validasi tulisan tangan pada file komponen CanvasModal.vue diproject aksantara ku agar pemeriksaannya jauh lebih akurat dan ramah untuk anak SD.

ini detail detail masalah dan instruksi perubahannya:
1. masalah saat ini:
   validasi sebelumnya hanya mengecek garis tepi doang menggunakan strokeText, jika anak-anak mencoret secara asal di luar bentuk huruf, mereka masih bisa mendapatkan skor tinggi.
   ukuran target validasi di kode berbeda dengan ukuran watermark panduan di tampilan, sehingga kurang singkron.
   validasi yang benar seharusnya mendeteksi area isi huruf penuh , bukan sekadar garis pinggirnya saja.

2. instruksi perubahan yang harus dilakukan:
   - Di bagian objek konfigurasi CHARACTER_CONFIGS 
     Hapus properti `strokeWidth` karena sudah tidak terpakai.
     Tambahkan properti baru bernama paddingStroke dengan nilai
       - vokal sederhana $\rightarrow$ paddingStroke: 10
       - konsonan $\rightarrow$ paddingStroke: 12
       -  berfungsi sebagai zona toleransi jika anak-anak menulis sedikit keluar garis
   - Di dalam fungsi validateStroke
     * ubah cara render mask target: gunakan fillTextterlebih dahulu untuk mengisi penuh bentuk huruf
     * setelah itu, timpa dengan strokeText menggunakan lineWidth = paddingStroke untuk memberikan batas area toleransi
     * Tambahkan console.log informasi paddingStrokeuntuk keperluan debug di konsol browser
   - dibagian template HTML:
     * samakan ukuran font watermark background canvas agar nilainya sinkron secara dinamis dengan properti fontSize yang ada di CHARACTER_CONFIGS
```


### [Log #35] Step 826 — 2026-10-14 22:30 WIB
```text
Tolong bantu ubah dan sesuaikan fitur di halaman Coretan serta modal latihan canvas dengan rincian seperti ini
1 .Perubahan di halaman coretan (menu utama grid huruf):
   - buat satu tombol khusus baru di luar grid huruf (misal: panduan menulis).
   - saat tombol ini diklik, buka modal panduan animasi khusus yang menampilkan gerakan stroke SVG huruf Ka (bentuk 77) dengan animasi css, badge urutan Goresan: 1 ➔ 2, danjuga tombol  Ulangi Animasi.

2. Perubahan di canvas Latihan (Modal Kartu Huruf):
   - hapus tab Panduan dari dalam modal latihan. ssekarang kalokartu huruf diklik, langsung menampilkan canvas latihan.
   -hapus fitur pengatur tebal-tipis arsiran (ukuran kuas).
   - hapus fitur ganti warna coretan (color picker). Kuas dibuat menggunakan satu warna standar saja.
```

### [Log #36] Step 874 — 2026-10-14 23:42 WIB
```text
tolong perbaiki dan sesuaikan komponen CanvasModal.vue serta halaman coretan dgn rincian seperti ini ya



1. perbaikan canvas latihan (kuas dan stroke):

   - kunci nilai default ketebalan kuas menjadi 14px agar garis coretan tebal, jelas, dan ramah untuk anak-anak.

   - set atribut context canvas: ctx.linewidth = 14; ctx.linecap = 'round'; ctx.linejoin = 'round';

   - pake warna garis standar hitam pekat (#1e293b atau #000000).

 
2. perbaikan animasi svg panduan huruf "ka" (bentuk 77):

   - gabungin background bayangan huruf krem atau abu-abu dan garis animasi oranye dalam satu elemen svg yang sama agar posisinya seratus persen presisi dan tidak melenceng.

   - berikan jarak yang lebih lebar antara angka 7 pertama dan angka 7 kedua agar tidak saling berdempet

   - gunakan stroke-width sekitar 12px sampai 14px dengan stroke-linecap="round" pada path animasi svg.
```

### [Log #37] Step 897 — 2026-10-15 00:15 WIB
```text
buatkan logika dan penyesuaian sistem gamifikasi serta alur soal pada aplikasi aksantara dengan rincian seperti ini ya

1. pembaruan soal di modul petualangan:
   - ubah jenis soal di map petualangan menggunakan materi yang ada di modul kamus.
   - jenis soal 1 (pilihan ganda/tebak huruf): tampilkan gambar/grafis aksara sunda, lalu pengguna memilih nama huruf/kata yang tepat.
   - jenis soal 2 (menulis): tampilkan canvas latihan di dalam soal di mana pengguna harus menuliskan aksara sunda sesuai petunjuk.
  - buat jangan cuman 6 soal aja, bikin 10 atau lebih dikit gapapa

2. otomatisasi misi harian:
   - hapus fitur centang manual pada daftar misi harian.
   - buat status misi harian berprogres dan tercentang secara otomatis saat pengguna menyelesaikan tugas yang diminta (misal: menyelesaikan 1 level petualangan, membaca 1 dongeng, atau melakukan latihan menulis). dan juga jangan lupa kalau semisal setelah menyelesaikan itu bakal dapet koinnya
   - jika sudah tercentang semua nanti dapet hadiah spesial, mungkin isinya beberapa xp dan poin gitu

3. integrasi sistem xp dan level:
   - berikan hadiah sejumlah xp setiap kali pengguna berhasil menyelesaikan satu level soal di map petualangan.
   - akumulasi xp akan menaikkan level pengguna secara otomatis.

4. integrasi sistem koin dan bantuan soal:
   - berikan koin sebagai hadiah dari penyelesaian soal/level atau dari misi harian.
   - tambahkan fitur tombol "bantuan / hint" di dalam halaman soal yang bisa dibeli menggunakan koin untuk mengeliminasi opsi salah atau memberikan petunjuk penulisan.

5. integrasi modul dongeng dengan koin dan xp:
   - ubah syarat pembukaan dongeng: dongeng tidak lagi terbuka berdasarkan level, melainkan dibeli menggunakan koin pengguna.(oh iya buat 1 dongeng dulu ya untuk testing)

6. integrasi pada pengguna baru:
   - ototmatis dia berada di level 1 ya
  - otomatis masih soal level 1 pada map petualangan aksara(oh iya jikalau dia udah ngerjain soal dia bakal ke simpen ya walau log out tapi soal yang udah dikerjain masih bisa di kerjain ulang)
oh iya 1 dongeng yang tadi itu walaupun dia pengguna baru dia ttep bisa baca dongeng itu
   -
```

### [Log #38] Step 900 — 2026-10-15 00:28 WIB
```text
buatkan logika dan penyesuaian sistem gamifikasi serta alur soal pada aplikasi aksantara dengan rincian seperti ini ya



1. pembaruan soal di modul petualangan:

   - ubah jenis soal di map petualangan menggunakan materi yang ada di modul kamus.

   - jenis soal 1 (pilihan ganda/tebak huruf): tampilkan gambar/grafis aksara sunda, lalu pengguna memilih nama huruf/kata yang tepat.

   - jenis soal 2 (menulis): tampilkan canvas latihan di dalam soal di mana pengguna harus menuliskan aksara sunda sesuai petunjuk.

  - buat jangan cuman 6 soal aja, bikin 10 atau lebih dikit gapapa



2. otomatisasi misi harian:

   - hapus fitur centang manual pada daftar misi harian.

   - buat status misi harian berprogres dan tercentang secara otomatis saat pengguna menyelesaikan tugas yang diminta (misal: menyelesaikan 1 level petualangan, membaca 1 dongeng, atau melakukan latihan menulis). dan juga jangan lupa kalau semisal setelah menyelesaikan itu bakal dapet koinnya

   - jika sudah tercentang semua nanti dapet hadiah spesial, mungkin isinya beberapa xp dan poin gitu



3. integrasi sistem xp dan level:

   - berikan hadiah sejumlah xp setiap kali pengguna berhasil menyelesaikan satu level soal di map petualangan.

   - akumulasi xp akan menaikkan level pengguna secara otomatis.



4. integrasi sistem koin dan bantuan soal:

   - berikan koin sebagai hadiah dari penyelesaian soal/level atau dari misi harian.

   - tambahkan fitur tombol "bantuan / hint" di dalam halaman soal yang bisa dibeli menggunakan koin untuk mengeliminasi opsi salah atau memberikan petunjuk penulisan.



5. integrasi modul dongeng dengan koin dan xp:

   - ubah syarat pembukaan dongeng: dongeng tidak lagi terbuka berdasarkan level, melainkan dibeli menggunakan koin pengguna.(oh iya buat 1 dongeng dulu ya untuk testing)



6. integrasi pada pengguna baru:

   - ototmatis dia berada di level 1 ya

  - otomatis masih soal level 1 pada map petualangan aksara(oh iya jikalau dia udah ngerjain soal dia bakal ke simpen ya walau log out tapi soal yang udah dikerjain masih bisa di kerjain ulang)

oh iya 1 dongeng yang tadi itu walaupun dia pengguna baru dia ttep bisa baca dongeng itu

   -
```

### [Log #39] Step 1104 — 2026-10-15 11:01 WIB
```text
segera buatkan dan sesuaikn komponen runtutan belajar (streak) samaleaderboard "jawara aksara" dengan ketentuan seperti ini


1. tampilan card ringkas streak di halaman utama:

   - pasang card atau kotak informasi runtutan belajar di halaman utama (atau di atas menu grid) agar mudah dilihat oleh pengguna.

   - tampilkan informasi singkat di card seperti jumlah hari beruntun (streak), status check-in hari ini


2. modal pop-up detail runtutan belajar:

   - saat card streak diklik, buka modal pop-up di tengah layar.

   - tampilkan judul "runtutan belajar" beserta deskripsi "check-in harian untuk menjaga ritme belajar setiap hari."

   - tampilkan badge streak terlama di bagian atas modal.

   - tampilkan status utama (misal: 3 hari beruntun dan keterangan status check-in hari ini) beserta tombol aksi check-in.

   - tampilkan tracker harian (7 hari: jum, sab, min, sen, sel, rab, kam) dengan lingkaran api aktif untuk hari yang sudah check-in dan redup untuk yang belum.

   - catatan: jangan masukkan fitur leaderboard di dalam modal streak ini karena sudah ada tempatnya ya


3. komponen leaderboard "jawara aksara" berbasis streak terbanyak:

   - ubah kalkulasi peringkat leaderboard berdasarkan jumlah hari streak terbanyak

   - ubah teks detail di bawah nama pengguna menjadi informasi streak harian (misal: " 7 hari beruntun").

   - hapus/ubah badge "minggu ini" di pojok kanan atas judul leaderboard menjadi badge bertuliskan "paling rajin" atau "streak tertinggi".

   - jika pengguna tidak masuk dalam daftar top 3, tampilkan baris khusus di bagian bawah komponen leaderboard yang menampilkan posisi peringkat pengguna, foto profil, nama, dan jumlah streak harian miliknya sendiri


4. otomatisasi check-in:

   - status check-in harian otomatis aktif saat pengguna menyelesaikan minimal satu aktivitas belaja
```

### [Log #40] Step 1282 — 2026-10-15 14:28 WIB
```text
tolong bantu perbaiki dan poles dengan baik beberapa fitur di aplikasi aksantara saya
1. klaim hadiah dongeng (xp & koin) sekali klaim:
   - di modul dongeng, tambahkan pengecekan atribut status (misal `completed_stories` di data user).
   - saat pengguna mengeklik "tandai selesai membaca", tambahkan hadiah xp dan koin ke akun user, lalu simpan id dongeng tersebut.
   - ubah status tombol menjadi "selesai dibaca" (disabled/redup) agar pengguna tidak bisa mengklaim hadiah xp & koin secara berulang-ulang pada dongeng yang sama.

2. pembersihan akun dummy di leaderboard:
   - hapus data mock/dummy bawaan (seperti budi, ani, cecep) dari komponen leaderboard "jawara aksara".
   - panggil data pengguna asli dari database berdasarkan jumlah streak terbanyak secara real-time.

3. kelengkapan huruf di modul berlatih menulis (coretan):
   - samakan daftar/dataset kartu huruf di modul latihan menulis agar selengkap daftar huruf yang ada di papan ketik aksara sunda (mencakup aksara swara, ngalagena lengkap, rarangkén, dan aksara angka).

4. penyesuaian desain halaman profil/pengaturan:
   - redesign tampilan halaman profil agar senada dengan tema aplikasi aksantara (ramah anak, sudut membulat/rounded, warna hangat creme & orange/yellow, serta card ui yang ceria).
   - integrasikan informasi avatar pengguna, badge level, total xp, koin, dan streak di halaman profil tersebut, bukan sekadar form input polos/kaku.
```

### [Log #41] Step 1457 — 2026-10-15 14:59 WIB
```text
tolong bantu perbaiki dan redesain bagian demo fitur di landing page aplikasi aksantara agar tampilannya rapi, modern, dan estetik seperti konsep kartu (card layout) yang bersih:



1. tata letak dan struktur grid (card style):

   - gunakan layout grid dengan card besar bersudut tumpul (rounded-3xl atau rounded-2xl) dan bayangan halus 

   - berikan variasi latar belakang warna pastel yang lembut di setiap kartu 

   - setiap kartu wajib memiliki ikon representatif di bagian atas, judul fitur yang jelas, deskripsi singkat yang menarik, serta cuplikan/ilustrasi demo mini (seperti mini canvas, badge streak harian, atau tampilan tombol virtual keyboard) di dalam kartu tersebut.



2. fitur-fitur yang ditampilkan pada card showcase:

   - card 1: "canvas tracing" (menampilkan demo mini kotak coretan menulis aksara).

   - card 2: "daily streak & level" (menampilkan indikator streak harian berbentuk ikon api dan lingkaran hari).

   - card 3: "kuis aksara"

   - card 4: "virtual keyboard & suara" (menampilkan cuplikan tombol papan ketik aksara).



3. interaktif dan fungsional:

   - pastikan elemen demo di dalam card tidak sekadar gambar mati, melainkan komponen interaktif ringan atau preview statis yang sangat rapi dan responsif di berbagai ukuran layar
```





