# Komponen Antarmuka & Frontend: Aksantara

Dokumen ini mendokumentasikan pohon komponen Vue 3, standar desain grafis, aturan interaksi kanvas, dan implementasi Unicode aksara pada sisi klien.

---

## 1. Standar Desain Visual (Design Tokens)

Antarmuka Aksantara dirancang dengan pendekatan *child-friendly* (usia 7–12 tahun) menggunakan Tailwind CSS v3:

| Kategori | Token / Nilai | Penerapan |
| :--- | :--- | :--- |
| **Latar Belakang** | `#FFFDF5` / `bg-orange-50/50` | Latar utama aplikasi untuk mengurangi ketegangan mata anak. |
| **Warna Aksen Utama** | `#FF4D30` (Coral Orange) | Tombol aksi primer, indikator aktif, dan aksen *brand*. |
| **Warna Sekunder** | Amber (`#f59e0b`), Emerald (`#10b981`), Violet (`#8b5cf6`) | Lencana modul, kategori huruf, dan variasi kartu. |
| **Warna Kuas Kanvas** | `#1e293b` (Slate 800) | Tinta standar coretan tebal yang kontras di atas kanvas putih. |
| **Sudut Sudut (Radius)** | `rounded-2xl` (16px), `rounded-3xl` (24px) | Seluruh kartu informasi, modal pop-up, dan tombol sentuh. |
| **Target Sentuh** | Minimal `min-h-[48px]`, `min-w-[48px]` | Menjamin kemudahan menekan tombol pada layar ponsel atau tablet. |

---

## 2. Hirarki & Pohon Komponen

```
App (Inertia Entry)
├── Layouts
│   ├── AuthenticatedLayout.vue  (Navigasi global pengguna masuk)
│   └── GuestLayout.vue          (Tampilan halaman login/register)
├── Pages
│   ├── Landingpage.vue          (Showcase 4 kartu interaktif: Tracing, Streak, Quiz, Keyboard)
│   ├── Dashboard.vue            (Orkestrator utama: 5 tab navigasi + gamifikasi)
│   └── Profile/Edit.vue         (Profil gamifikasi: Avatar hero, XP progress, password form)
└── Components (Modals & Widgets)
    ├── CanvasModal.vue          (Kanvas latihan menulis 53 karakter)
    ├── QuizModal.vue            (Runner soal petualangan: tebak & tulis)
    ├── StrokeGuideModal.vue     (Animasi SVG goresan huruf "Ka" 77)
    ├── StreakModal.vue          (Pop-up runtutan belajar 7 hari)
    ├── StoryReaderModal.vue     (Pembaca dongeng cerita rakyat)
    ├── KamusAksara.vue          (Pencarian kosakata & transliterasi)
    ├── VirtualKeyboard.vue      (Papan ketik digital 53 karakter)
    └── SpeechPronunciation.vue  (Player audio pelafalan aksara)
```

---

## 3. Spesifikasi Komponen Kunci

### 3.1. `Dashboard.vue`
- **Fungsi**: Berperan sebagai wadah pusat aplikasi yang menampung 5 tab:
  1. `Tab 1: 🗺️ Petualangan` (Peta 6 level dan runner `QuizModal.vue`).
  2. `Tab 2: ✍️ Coretan` (Grid 53 karakter dengan filter kategori dan `CanvasModal.vue`).
  3. `Tab 3: ⌨️ Papan Ketik` (Komponen `VirtualKeyboard.vue`).
  4. `Tab 4: 📖 Kamus` (Komponen `KamusAksara.vue`).
  5. `Tab 5: 📜 Dongeng` (Katalog cerita rakyat dan `StoryReaderModal.vue`).
- **State Gamifikasi**: Menyimpan `userStats`, `completedStoryIds`, `weeklyTracker`, dan data `leaderboard` yang disinkronkan saat aksi selesai.

### 3.2. `CanvasModal.vue` (Engine Kanvas Latihan Menulis)
- **Parameter Konteks Kanvas (2D)**:
  - `ctx.lineWidth = 14;` (Tebal, tegas, ramah motorik anak).
  - `ctx.lineCap = 'round';`
  - `ctx.lineJoin = 'round';`
  - `ctx.strokeStyle = '#1e293b';`
- **Penanganan Koordinat Sentuh & Mouse**:
  - Menghitung posisi kursor menggunakan *bounding rect* kanvas:
    $$\text{pos.x} = (e.\text{clientX} - \text{rect.left}) \times \frac{\text{canvas.width}}{\text{rect.width}}$$
    $$\text{pos.y} = (e.\text{clientY} - \text{rect.top}) \times \frac{\text{canvas.height}}{\text{rect.height}}$$
  - Mencegah *event scrolling* bawaan layar ponsel (`@touchmove.prevent`).
- **Watermark & Karakter Gabungan**:
  - Untuk karakter jenis Rarangkén (yang merupakan *combining mark*), kanvas merender lingkaran putus-putus pembantu (`◌ᮤ`) agar posisi tanda baca tampak jelas.

### 3.3. `StrokeGuideModal.vue` (Panduan Goresan Animasi SVG)
- **Karakter**: Aksara Sunda "Ka" (`ᮊ`) berbentuk khas menyerupai angka 77 kembar.
- **Implementasi SVG**:
  - Menggabungkan bayangan dasar huruf abu-abu krem (`#fef3c7`) dan garis animasi oranye (`#FF4D30`) dalam satu SVG agar posisinya 100% presisi dan tidak bergeser.
  - Lebar garis animasi: `stroke-width="12"`, `stroke-linecap="round"`.
  - Jarak proporsional antara bentuk 7 pertama dan bentuk 7 kedua agar tidak saling menumpuk.
  - Dilengkapi tombol "Ulangi Animasi" dan nomor urutan goresan: **Goresan 1 ➔ Goresan 2**.

### 3.4. `QuizModal.vue` (Runner Kuis Petualangan)
- **Tipe Soal**:
  1. *Multiple Choice* (Pilihan Ganda 4 opsi).
  2. *Writing on Canvas* (Menulis aksara langsung di dalam modal kuis).
- **Fitur Hint**:
  - Mengonsumsi 5 koin untuk mengeliminasi 2 opsi salah atau menampilkan petunjuk bentuk aksara.
- **Evaluasi Hasil**:
  - Menghitung persentase jawaban benar, memberikan bintang (1–3 ⭐), serta mengirim payload ke `/api/gamification/complete-level`.

---

## 4. Dataset Lengkap Aksara Sunda (53 Glif Unicode)

Aksantara mendukung seluruh 53 karakter resmi aksara Sunda sesuai standar Unicode (Blok U+1B80 s.d. U+1BBF):

### 4.1. Aksara Swara (7 Vokal Mandiri)
| Karakter | Unicode | Nama Huruf | Lafal |
| :---: | :---: | :--- | :--- |
| **ᮃ** | `U+1B83` | Swara A | /a/ |
| **ᮄ** | `U+1B84` | Swara I | /i/ |
| **ᮅ** | `U+1B85` | Swara U | /u/ |
| **ᮈ** | `U+1B88` | Swara E (Pamepet) | /ə/ |
| **ᮇ** | `U+1B87` | Swara O | /o/ |
| **ᮉ** | `U+1B89` | Swara Eu | /ɤ/ |
| **ᮆ** | `U+1B86` | Swara É (Panéléng) | /ɛ/ |

### 4.2. Aksara Ngalagena (23 Konsonan Utama & Serapan)
- **Utama (18)**: ᮊ (*Ka*), ᮌ (*Ga*), ᮍ (*Nga*), ᮎ (*Ca*), ᮏ (*Ja*), ᮑ (*Nya*), ᮒ (*Ta*), ᮓ (*Da*), ᮔ (*Na*), ᮕ (*Pa*), ᮘ (*Ba*), ᮙ (*Ma*), ᮚ (*Ya*), ᮛ (*Ra*), ᮜ (*La*), ᮝ (*Wa*), ᮞ (*Sa*), ᮠ (*Ha*).
- **Serapan (5)**: ᮋ (*Qa*), ᮐ (*Za*), ᮖ (*Fa*), ᮗ (*Va*), ᮟ (*Xa*), ᮮ (*Kha*).

### 4.3. Rarangkén (13 Tanda Vokalisasi & Bunyi Akhir)
- **Vokalisasi**: ◌ᮤ (*Panghulu -i*), ◌ᮥ (*Panyuku -u*), ◌ᮦ (*Panéléng -é*), ◌ᮧ (*Panolong -o*), ◌ᮨ (*Pamepet -e*), ◌ᮩ (*Paneuleung -eu*), ◌᮪ (*Pamaéh/Paten*).
- **Penyisip & Akhiran**: ◌ᮀ (*Panyecek -ng*), ◌ᮁ (*Panglayar -r*), ◌ᮂ (*Pangwisad -h*), ◌ᮡ (*Pamingkal -ya*), ◌ᮢ (*Panyakra -ra*), ◌ᮣ (*Panyiku -la*).

### 4.4. Aksara Angka (10 Angka)
- **᮰** (0), **᮱** (1), **᮲** (2), **᮳** (3), **᮴** (4), **᮵** (5), **᮶** (6), **᮷** (7), **᮸** (8), **᮹** (9).
