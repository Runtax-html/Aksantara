<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Dictionary;
use App\Models\Folktale;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningController extends Controller
{
    // ================================================================
    // KONFIGURASI GAMIFIKASI
    // ================================================================

    /** XP bonus saat streak berlanjut */
    private const STREAK_BONUS_XP = 5;

    /** XP dasar per jawaban benar (jika karakter tidak punya xp_reward) */
    private const DEFAULT_CORRECT_XP = 10;

    /** Persentase correct_count agar dianggap mastered */
    private const MASTERY_THRESHOLD = 80;

    /** Minimal total latihan sebelum mastery bisa tercapai */
    private const MIN_ATTEMPTS_FOR_MASTERY = 5;

    /** XP yang dibutuhkan per level-up */
    private const XP_PER_LEVEL = 100;

    // ================================================================
    // LESSON & CHARACTER ENDPOINTS
    // ================================================================

    /**
     * Ambil daftar karakter berdasarkan jenis aksara & kategori.
     *
     * GET /api/learn/characters?script_type=jawa&category=aksara_dasar
     */
    public function getCharacters(Request $request): JsonResponse
    {
        $request->validate([
            'script_type' => 'required|string|max:50',
            'category'    => 'nullable|string|max:50',
        ]);

        $query = Character::where('script_type', $request->script_type)
            ->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $characters = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $characters,
        ]);
    }

    /**
     * Ambil detail satu karakter beserta progress user (jika login).
     *
     * GET /api/learn/characters/{character}
     */
    public function showCharacter(Character $character): JsonResponse
    {
        $progress = null;

        if (Auth::check()) {
            $progress = UserProgress::where('user_id', Auth::id())
                ->where('character_id', $character->id)
                ->first();
        }

        return response()->json([
            'success'  => true,
            'data'     => $character,
            'progress' => $progress,
        ]);
    }

    /**
     * Tandai karakter sebagai "sudah dipelajari" dan berikan XP awal.
     *
     * POST /api/learn/characters/{character}/learn
     */
    public function learnCharacter(Character $character): JsonResponse
    {
        $user = Auth::user();

        $progress = UserProgress::firstOrCreate(
            [
                'user_id'      => $user->id,
                'character_id' => $character->id,
            ],
            [
                'is_learned'        => true,
                'last_practiced_at' => now(),
            ]
        );

        // Jika belum pernah dipelajari, berikan XP
        if (!$progress->wasRecentlyCreated && !$progress->is_learned) {
            $progress->update(['is_learned' => true, 'last_practiced_at' => now()]);
        }

        if ($progress->wasRecentlyCreated) {
            $this->addXp($user, $character->xp_reward ?? self::DEFAULT_CORRECT_XP);
            $this->updateStreak($user);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Karakter berhasil dipelajari!',
            'progress' => $progress->fresh(),
            'user'     => $this->userStats($user),
        ]);
    }

    // ================================================================
    // QUIZ / SUBMIT ANSWER
    // ================================================================

    /**
     * Submit jawaban kuis untuk satu karakter.
     *
     * POST /api/learn/quiz/submit
     * Body: { character_id: int, answer: string }
     */
    public function submitAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'character_id' => 'required|exists:characters,id',
            'answer'       => 'required|string|max:100',
        ]);

        $user      = Auth::user();
        $character = Character::findOrFail($request->character_id);
        $isCorrect = mb_strtolower(trim($request->answer)) === mb_strtolower(trim($character->latin));

        // Ambil atau buat progress
        $progress = UserProgress::firstOrCreate(
            [
                'user_id'      => $user->id,
                'character_id' => $character->id,
            ],
            [
                'is_learned' => true,
            ]
        );

        // Update statistik
        DB::transaction(function () use ($progress, $isCorrect, $user, $character) {
            if ($isCorrect) {
                $progress->increment('correct_count');
                $this->addXp($user, $character->xp_reward ?? self::DEFAULT_CORRECT_XP);
            } else {
                $progress->increment('incorrect_count');
            }

            // Refresh untuk mendapatkan nilai terbaru setelah increment
            $progress->refresh();

            // Hitung mastery percentage
            $total = $progress->correct_count + $progress->incorrect_count;
            $masteryPct = $total > 0
                ? (int) round(($progress->correct_count / $total) * 100)
                : 0;

            $isMastered = $masteryPct >= self::MASTERY_THRESHOLD
                          && $total >= self::MIN_ATTEMPTS_FOR_MASTERY;

            $progress->update([
                'mastery_percentage' => $masteryPct,
                'is_mastered'        => $isMastered,
                'is_learned'         => true,
                'last_practiced_at'  => now(),
            ]);

            $this->updateStreak($user);
        });

        return response()->json([
            'success'        => true,
            'is_correct'     => $isCorrect,
            'correct_answer' => $character->latin,
            'progress'       => $progress->fresh(),
            'user'           => $this->userStats($user->fresh()),
        ]);
    }

    // ================================================================
    // PROGRESS & DASHBOARD
    // ================================================================

    /**
     * Ambil ringkasan progress user untuk satu jenis aksara.
     *
     * GET /api/learn/progress?script_type=jawa
     */
    public function getProgress(Request $request): JsonResponse
    {
        $request->validate([
            'script_type' => 'required|string|max:50',
        ]);

        $user = Auth::user();

        $totalCharacters = Character::where('script_type', $request->script_type)->count();

        $progress = UserProgress::where('user_id', $user->id)
            ->whereHas('character', fn ($q) => $q->where('script_type', $request->script_type))
            ->get();

        $learned  = $progress->where('is_learned', true)->count();
        $mastered = $progress->where('is_mastered', true)->count();

        $totalCorrect   = $progress->sum('correct_count');
        $totalIncorrect = $progress->sum('incorrect_count');
        $totalAttempts   = $totalCorrect + $totalIncorrect;

        return response()->json([
            'success' => true,
            'data'    => [
                'script_type'      => $request->script_type,
                'total_characters'  => $totalCharacters,
                'learned'          => $learned,
                'mastered'         => $mastered,
                'completion_pct'   => $totalCharacters > 0
                    ? round(($learned / $totalCharacters) * 100, 1)
                    : 0,
                'mastery_pct'      => $totalCharacters > 0
                    ? round(($mastered / $totalCharacters) * 100, 1)
                    : 0,
                'total_attempts'   => $totalAttempts,
                'accuracy_pct'     => $totalAttempts > 0
                    ? round(($totalCorrect / $totalAttempts) * 100, 1)
                    : 0,
            ],
            'user'    => $this->userStats($user),
        ]);
    }

    /**
     * Ambil progress detail per karakter (untuk halaman review).
     *
     * GET /api/learn/progress/details?script_type=jawa
     */
    public function getProgressDetails(Request $request): JsonResponse
    {
        $request->validate([
            'script_type' => 'required|string|max:50',
        ]);

        $details = UserProgress::with('character')
            ->where('user_id', Auth::id())
            ->whereHas('character', fn ($q) => $q->where('script_type', $request->script_type))
            ->orderByDesc('last_practiced_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $details,
        ]);
    }

    // ================================================================
    // DICTIONARY
    // ================================================================

    /**
     * Cari kata di kamus.
     *
     * GET /api/learn/dictionary?script_type=jawa&q=hana
     */
    public function searchDictionary(Request $request): JsonResponse
    {
        $request->validate([
            'script_type' => 'required|string|max:50',
            'q'           => 'nullable|string|max:255',
        ]);

        $query = Dictionary::where('script_type', $request->script_type);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('word_latin', 'LIKE', "%{$search}%")
                  ->orWhere('word_aksara', 'LIKE', "%{$search}%")
                  ->orWhere('meaning', 'LIKE', "%{$search}%");
            });
        }

        $results = $query->orderBy('word_latin')->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    // ================================================================
    // FOLKTALES (CERITA RAKYAT)
    // ================================================================

    /**
     * Ambil daftar cerita rakyat.
     *
     * GET /api/learn/folktales?script_type=jawa&difficulty=pemula
     */
    public function getFolktales(Request $request): JsonResponse
    {
        $request->validate([
            'script_type' => 'nullable|string|max:50',
            'difficulty'  => 'nullable|in:pemula,menengah,mahir',
        ]);

        $query = Folktale::query()
            ->select([
                'id', 'title', 'script_type', 'synopsis', 'difficulty',
                'region', 'cover_image_url', 'read_time_minutes', 'xp_reward',
            ]);

        if ($request->filled('script_type')) {
            $query->where('script_type', $request->script_type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $folktales = $query->orderBy('difficulty')
            ->orderBy('title')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $folktales,
        ]);
    }

    /**
     * Ambil detail satu cerita rakyat (konten lengkap).
     *
     * GET /api/learn/folktales/{folktale}
     */
    public function showFolktale(Folktale $folktale): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $folktale,
        ]);
    }

    /**
     * Tandai cerita rakyat selesai dibaca & berikan XP.
     *
     * POST /api/learn/folktales/{folktale}/complete
     */
    public function completeFolktale(Folktale $folktale): JsonResponse
    {
        $user = Auth::user();

        // Gunakan cache sederhana di session agar tidak double-claim XP
        $cacheKey = "folktale_completed_{$user->id}_{$folktale->id}";

        if (cache()->has($cacheKey)) {
            return response()->json([
                'success' => true,
                'message' => 'Cerita ini sudah pernah diselesaikan sebelumnya.',
                'user'    => $this->userStats($user),
            ]);
        }

        $this->addXp($user, $folktale->xp_reward);
        $this->updateStreak($user);

        // Tandai selesai selama 24 jam (bisa dibaca ulang besok untuk XP lagi)
        cache()->put($cacheKey, true, now()->addHours(24));

        return response()->json([
            'success' => true,
            'message' => "Selamat! Kamu mendapat {$folktale->xp_reward} XP dari membaca cerita ini.",
            'user'    => $this->userStats($user->fresh()),
        ]);
    }

    // ================================================================
    // PRIVATE HELPERS — STREAK & XP
    // ================================================================

    /**
     * Update streak harian user.
     *
     * Logika:
     * - Jika belum pernah aktif → mulai streak = 1
     * - Jika aktivitas terakhir = kemarin → lanjut streak (+1)
     * - Jika aktivitas terakhir = hari ini → tidak berubah
     * - Jika > 1 hari lalu → reset streak ke 1
     */
    private function updateStreak($user): void
    {
        $today         = Carbon::today();
        $lastActivity  = $user->last_activity_at
            ? Carbon::parse($user->last_activity_at)->startOfDay()
            : null;

        if (is_null($lastActivity)) {
            // Pertama kali belajar
            $user->streak = 1;
        } elseif ($lastActivity->equalTo($today)) {
            // Sudah belajar hari ini — streak tetap
            return;
        } elseif ($lastActivity->equalTo($today->copy()->subDay())) {
            // Belajar kemarin — lanjutkan streak
            $user->streak += 1;

            // Bonus XP karena streak berlanjut
            $this->addXp($user, self::STREAK_BONUS_XP);
        } else {
            // Lebih dari 1 hari absen — reset
            $user->streak = 1;
        }

        $user->last_activity_at = $today;
        $user->save();
    }

    /**
     * Tambah XP dan hitung level-up otomatis.
     */
    private function addXp($user, int $amount): void
    {
        $user->xp += $amount;
        $user->level = (int) floor($user->xp / self::XP_PER_LEVEL) + 1;
        $user->save();
    }

    /**
     * Format statistik user untuk response JSON.
     */
    private function userStats($user): array
    {
        return [
            'id'               => $user->id,
            'name'             => $user->name,
            'xp'               => $user->xp,
            'level'            => $user->level,
            'streak'           => $user->streak,
            'last_activity_at' => $user->last_activity_at,
            'xp_to_next_level' => self::XP_PER_LEVEL - ($user->xp % self::XP_PER_LEVEL),
        ];
    }
}
