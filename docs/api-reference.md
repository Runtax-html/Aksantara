# Referensi API: Aksantara

Dokumen ini berisi spesifikasi teknis antarmuka pemrograman aplikasi (API) yang digunakan oleh antarmuka frontend Aksantara untuk berinteraksi dengan server backend.

---

## 1. Protokol & Konvensi Umum

- **Base URL**: `/api`
- **Format Pertukaran Data**: `application/json` (Request & Response).
- **Mekanisme Autentikasi**: Laravel Session Cookie + Header CSRF Token (`X-XSRF-TOKEN` otomatis via Axios / Inertia).
- **Status Kode HTTP**:
  - `200 OK`: Permintaan berhasil diproses.
  - `400 Bad Request`: Parameter masukan tidak valid atau saldo koin tidak mencukupi.
  - `401 Unauthorized`: Sesi pengguna belum login atau telah kedaluwarsa.
  - `500 Internal Server Error`: Kegagalan tak terduga pada server.

---

## 2. Katalog Endpoint Gamifikasi (`/api/gamification`)

### 2.1. Ambil State Gamifikasi Lengkap
Mengambil data agregasi profil pengguna, status level 1–6, misi harian, tracker 7 hari, dan leaderboard Jawara Aksara.

- **URL**: `/api/gamification/state`
- **Method**: `GET`
- **Auth**: Wajib
- **Sample Response (200 OK)**:
```json
{
  "user": {
    "id": 1,
    "name": "Asep Sunandar",
    "email": "asep@example.com",
    "xp": 450,
    "level": 5,
    "coins": 65,
    "streak": 3,
    "max_streak": 7,
    "current_level_xp": 50,
    "xp_to_next_level": 50,
    "xp_percent": 50,
    "completed_stories": [1]
  },
  "levels": [
    {
      "id": 1,
      "title": "Level 1: Swara Aksara Sunda",
      "subtitle": "a, i, u, é, o, e, eu (Pilihan & Tulis)",
      "status": "completed",
      "stars": 3,
      "score": 100,
      "icon": "⭐",
      "color": "from-emerald-400 to-green-500"
    },
    {
      "id": 2,
      "title": "Level 2: Ngalagena Dasar 1",
      "subtitle": "ka, ga, nga, ca, ja, nya",
      "status": "active",
      "stars": 0,
      "score": 0,
      "icon": "🚀",
      "color": "from-[#FF4D30] to-orange-500"
    }
  ],
  "missions": [
    { "id": 1, "key": "complete_level", "title": "Selesaikan 1 Level Petualangan", "reward": 20, "completed": true },
    { "id": 2, "key": "practice_writing", "title": "Latihan Menulis di Canvas", "reward": 15, "completed": false },
    { "id": 3, "key": "read_story", "title": "Baca 1 Cerita Dongeng Sunda", "reward": 25, "completed": false }
  ],
  "allMissionsCompleted": false,
  "bonusClaimed": false,
  "unlockedStoryIds": [1, 2],
  "completedStoryIds": [1],
  "hasCheckedInToday": true,
  "weeklyTracker": [
    { "date": "2026-10-09", "dayName": "Jum", "dayNumber": 9, "isToday": false, "isCheckedIn": true },
    { "date": "2026-10-15", "dayName": "Kam", "dayNumber": 15, "isToday": true, "isCheckedIn": true }
  ],
  "leaderboard": {
    "top3": [
      { "id": 1, "name": "Asep Sunandar", "streak": 3, "xp": 450, "rank": 1, "badge": "🥇 Jawara 1", "is_current_user": true }
    ],
    "currentUserRank": 1,
    "currentUserEntry": { "id": 1, "name": "Asep Sunandar", "streak": 3, "xp": 450, "rank": 1 }
  }
}
```

---

### 2.2. Selesaikan Level Petualangan
Mencatat penyelesaian kuis per level, memberikan hadiah XP & Koin, membuka level berikutnya, dan memicu auto-checkin serta misi harian.

- **URL**: `/api/gamification/complete-level`
- **Method**: `POST`
- **Request Body**:
```json
{
  "level_id": 1,
  "score": 100,
  "stars": 3,
  "total_questions": 10
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Level 1 berhasil diselesaikan!",
  "xpEarned": 40,
  "coinsEarned": 15,
  "gamification": { "...state..." }
}
```

---

### 2.3. Log Latihan Menulis (Coretan Canvas)
Dipanggil saat anak mencoretkan kuas pada modul Coretan. Memajukan misi `practice_writing` dan check-in hari ini secara otomatis.

- **URL**: `/api/gamification/practice-writing`
- **Method**: `POST`
- **Request Body**:
```json
{
  "character": "ᮊ"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Latihan menulis aksara ᮊ berhasil dicatat!",
  "coinsEarned": 15,
  "gamification": { "...state..." }
}
```

---

### 2.4. Tandai Selesai Membaca Dongeng (One-Time Claim)
Mencatat dongeng telah selesai dibaca dan memberikan hadiah pembacaan pertama kali (+50 XP, +10 Koin). Jika sudah pernah diklaim, hadiah tidak diduplikasi.

- **URL**: `/api/gamification/read-story`
- **Method**: `POST`
- **Request Body**:
```json
{
  "story_id": 1
}
```
- **Response (200 OK — Pembacaan Pertama)**:
```json
{
  "success": true,
  "alreadyClaimed": false,
  "message": "Selamat! Kamu menyelesaikan cerita dan mendapatkan 50 XP & 10 Koin!",
  "xpEarned": 50,
  "coinsEarned": 10,
  "gamification": { "...state..." }
}
```
- **Response (200 OK — Sudah Pernah Diklaim Sebelumnya)**:
```json
{
  "success": true,
  "alreadyClaimed": true,
  "message": "Dongeng ini sudah pernah selesai dibaca. Hadiah sudah diambil sebelumnya.",
  "xpEarned": 0,
  "coinsEarned": 0,
  "gamification": { "...state..." }
}
```

---

### 2.5. Buka Dongeng Baru Menggunakan Koin
Membuka dongeng yang terkunci dengan memotong saldo koin pengguna (default: 30 koin).

- **URL**: `/api/gamification/unlock-story`
- **Method**: `POST`
- **Request Body**:
```json
{
  "story_id": 2
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Cerita berhasil dibuka!",
  "coinsSpent": 30,
  "gamification": { "...state..." }
}
```
- **Response (400 Bad Request — Koin Kurang)**:
```json
{
  "success": false,
  "message": "Koin tidak cukup! Butuh 30 koin untuk membuka cerita ini."
}
```

---

### 2.6. Beli Bantuan Hint Soal Kuis
Memotong 5 koin saat pengguna menekan tombol bantuan di kuis petualangan untuk mengeliminasi 2 opsi salah.

- **URL**: `/api/gamification/use-hint`
- **Method**: `POST`
- **Request Body**: `{}`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Bantuan digunakan (-5 Koin)",
  "coinsSpent": 5,
  "gamification": { "...state..." }
}
```

---

### 2.7. Klaim Hadiah Spesial Bonus Box Misi Harian
Mengklaim hadiah peti bonus harian (+50 XP, +30 Koin) saat ketiga misi harian telah berstatus `is_completed: true`.

- **URL**: `/api/gamification/claim-mission-bonus`
- **Method**: `POST`
- **Request Body**: `{}`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Hadiah spesial berhasil diklaim! (+50 XP, +30 Koin)",
  "xpEarned": 50,
  "coinsEarned": 30,
  "gamification": { "...state..." }
}
```

---

### 2.8. Check-in Runtutan Belajar Manual
Mencatat presensi hari ini secara manual dari modal streak jika belum ada aktivitas otomatis.

- **URL**: `/api/gamification/check-in`
- **Method**: `POST`
- **Request Body**: `{}`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Check-in harian berhasil!",
  "gamification": { "...state..." }
}
```

---

## 3. Endpoint Sintesis Suara (`/api/tts`)

Menghasilkan audio pelafalan kata bahasa Sunda berbasis Microsoft Edge-TTS (`su-ID-JajangNeural`).

- **URL**: `/api/tts`
- **Method**: `GET` atau `POST`
- **Auth**: Publik / Opsional
- **Parameters**:
  - `text` (*string*, required, max 300 char): Kata atau kalimat yang akan disuarakan.
  - `voice` (*string*, optional, default: `'su-ID-JajangNeural'`).
- **Sample Request**:
  `POST /api/tts` dengan body `{"text": "Wilujeng Sumping"}`
- **Response (200 OK)**:
```json
{
  "url": "/audio/sunda/cache/8b9f1d07c08b29c0f994df7bc61e13ab.mp3",
  "cached": true,
  "text": "Wilujeng Sumping"
}
```
