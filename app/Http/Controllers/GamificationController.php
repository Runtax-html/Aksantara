<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserQuizProgress;
use App\Models\UserUnlockedStory;
use App\Models\UserDailyMission;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    private const XP_PER_LEVEL = 100;

    /**
     * Definisi default 6 level di Peta Petualangan
     */
    public static function getDefaultLevels(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Level 1: Swara Aksara Sunda',
                'subtitle' => 'a, i, u, é, o, e, eu (Pilihan & Tulis)',
                'icon' => '🔤',
                'color' => 'from-emerald-400 to-green-500',
            ],
            [
                'id' => 2,
                'title' => 'Level 2: Ngalagena Dasar 1',
                'subtitle' => 'ka, ga, nga, ca, ja, nya',
                'icon' => '🚀',
                'color' => 'from-[#FF4D30] to-orange-500',
            ],
            [
                'id' => 3,
                'title' => 'Level 3: Ngalagena Dasar 2',
                'subtitle' => 'ta, da, na, pa, ba, ma',
                'icon' => '🎯',
                'color' => 'from-blue-400 to-indigo-500',
            ],
            [
                'id' => 4,
                'title' => 'Level 4: Kosakata Alam & Sato',
                'subtitle' => 'Ucing, Hayam, Maung, Tangkal',
                'icon' => '🐱',
                'color' => 'from-purple-400 to-pink-500',
            ],
            [
                'id' => 5,
                'title' => 'Level 5: Awak & Dahareun',
                'subtitle' => 'Panon, Leungeun, Cau, Huis',
                'icon' => '🍚',
                'color' => 'from-amber-400 to-orange-500',
            ],
            [
                'id' => 6,
                'title' => 'Level 6: Jawara Aksara Sunda',
                'subtitle' => 'Tantangan Menulis & Tebak Kata',
                'icon' => '👑',
                'color' => 'from-yellow-400 to-amber-500',
            ],
        ];
    }

    /**
     * Format data gamifikasi lengkap user saat ini
     */
    public static function getGamificationState(User $user): array
    {
        // 1. Hitung level otomatis dari XP
        $level = max(1, (int) floor($user->xp / self::XP_PER_LEVEL) + 1);
        if ($user->level !== $level) {
            $user->level = $level;
            $user->save();
        }

        $currentLevelXp = $user->xp % self::XP_PER_LEVEL;
        $xpToNextLevel = self::XP_PER_LEVEL - $currentLevelXp;
        $xpPercent = min(100, round(($currentLevelXp / self::XP_PER_LEVEL) * 100));

        // 2. Data Level Petualangan
        $quizProgresses = UserQuizProgress::where('user_id', $user->id)->get()->keyBy('level_id');
        $defaultLevels = self::getDefaultLevels();

        $levels = [];
        $hasActive = false;

        foreach ($defaultLevels as $idx => $def) {
            $lid = $def['id'];
            $prog = $quizProgresses->get($lid);

            if ($prog) {
                $status = $prog->status;
                $stars = $prog->stars;
                $score = $prog->score;
            } else {
                // Untuk level 1: jika belum ada progress sama sekali, statusnya 'active' (bukan locked)
                if ($lid === 1) {
                    $status = 'active';
                } else {
                    // Cek apakah level sebelumnya sudah completed
                    $prevProg = $quizProgresses->get($lid - 1);
                    if ($prevProg && $prevProg->status === 'completed') {
                        $status = 'active';
                    } else {
                        $status = 'locked';
                    }
                }
                $stars = 0;
                $score = 0;
            }

            $icon = $status === 'completed' ? '⭐' : ($status === 'active' ? '🚀' : '🔒');
            $color = $status === 'completed'
                ? 'from-emerald-400 to-green-500'
                : ($status === 'active' ? 'from-[#FF4D30] to-orange-500' : 'from-slate-300 to-slate-400');

            $levels[] = array_merge($def, [
                'status' => $status,
                'stars' => $stars,
                'score' => $score,
                'icon' => $icon,
                'color' => $color,
            ]);
        }

        // 3. Misi Harian (Otomatis berdasarkan tanggal hari ini)
        $today = Carbon::today()->toDateString();
        $userMissions = UserDailyMission::where('user_id', $user->id)
            ->where('mission_date', $today)
            ->get()
            ->keyBy('mission_key');

        $missionsDef = [
            [
                'id' => 1,
                'key' => 'complete_level',
                'title' => 'Selesaikan 1 Level Petualangan',
                'reward' => 20,
                'completed' => (bool) ($userMissions->get('complete_level')?->is_completed),
            ],
            [
                'id' => 2,
                'key' => 'practice_writing',
                'title' => 'Latihan Menulis di Canvas',
                'reward' => 15,
                'completed' => (bool) ($userMissions->get('practice_writing')?->is_completed),
            ],
            [
                'id' => 3,
                'key' => 'read_story',
                'title' => 'Baca 1 Cerita Dongeng Sunda',
                'reward' => 25,
                'completed' => (bool) ($userMissions->get('read_story')?->is_completed),
            ],
        ];

        $completedMissionsCount = count(array_filter($missionsDef, fn ($m) => $m['completed']));
        $allMissionsCompleted = $completedMissionsCount === count($missionsDef);
        $bonusClaimed = (bool) ($userMissions->get('bonus_box')?->is_completed);

        // 4. Cerita / Dongeng yang Terbuka
        // Dongeng 1 selalu terbuka gratis secara default
        $unlockedStoryIds = UserUnlockedStory::where('user_id', $user->id)->pluck('story_id')->toArray();
        if (!in_array(1, $unlockedStoryIds)) {
            $unlockedStoryIds[] = 1;
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'coins' => $user->coins ?? 50,
                'xp' => $user->xp ?? 0,
                'level' => $user->level ?? 1,
                'streak' => $user->streak ?? 1,
                'current_level_xp' => $currentLevelXp,
                'xp_to_next_level' => $xpToNextLevel,
                'xp_percent' => $xpPercent,
            ],
            'levels' => $levels,
            'completedLevelsCount' => count(array_filter($levels, fn ($l) => $l['status'] === 'completed')),
            'missions' => $missionsDef,
            'completedMissionsCount' => $completedMissionsCount,
            'allMissionsCompleted' => $allMissionsCompleted,
            'bonusClaimed' => $bonusClaimed,
            'unlockedStoryIds' => $unlockedStoryIds,
        ];
    }

    /**
     * Endpoint API: Ambil status gamifikasi
     */
    public function getState(Request $request): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => self::getGamificationState($user),
        ]);
    }

    /**
     * Endpoint API: Selesaikan Level Quiz Petualangan
     */
    public function completeLevel(Request $request): JsonResponse
    {
        $request->validate([
            'level_id' => 'required|integer|min:1|max:10',
            'stars' => 'required|integer|min:0|max:3',
            'score' => 'required|integer|min:0',
            'total' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $levelId = (int) $request->level_id;
        $stars = (int) $request->stars;
        $score = (int) $request->score;
        $total = (int) $request->total;

        $isPassed = ($score / $total) >= 0.7;

        $xpEarned = 0;
        $coinsEarned = 0;
        $messages = [];

        if ($isPassed) {
            // Reward Level
            $xpEarned = ($levelId * 20) + ($stars * 10);
            $coinsEarned = 15 + ($stars * 5); // misal 3 bintang = 30 koin

            $user->xp += $xpEarned;
            $user->coins += $coinsEarned;

            // Simpan / update progress level
            $progress = UserQuizProgress::firstOrNew([
                'user_id' => $user->id,
                'level_id' => $levelId,
            ]);

            $progress->status = 'completed';
            $progress->stars = max($progress->stars ?? 0, $stars);
            $progress->score = max($progress->score ?? 0, $score);
            $progress->total_questions = $total;
            $progress->completed_at = now();
            $progress->save();

            // Unlock level berikutnya
            $nextLevelId = $levelId + 1;
            if ($nextLevelId <= 6) {
                $nextProgress = UserQuizProgress::firstOrNew([
                    'user_id' => $user->id,
                    'level_id' => $nextLevelId,
                ]);
                if (!$nextProgress->exists || $nextProgress->status === 'locked') {
                    $nextProgress->status = 'active';
                    $nextProgress->save();
                }
            }

            // Otomatisasi Misi Harian 1: Selesaikan 1 Level Petualangan
            $today = Carbon::today()->toDateString();
            $mission = UserDailyMission::firstOrNew([
                'user_id' => $user->id,
                'mission_date' => $today,
                'mission_key' => 'complete_level',
            ]);

            if (!$mission->is_completed) {
                $mission->is_completed = true;
                $mission->reward_claimed = true;
                $mission->save();

                $missionCoinBonus = 20;
                $user->coins += $missionCoinBonus;
                $messages[] = "Misi Harian 'Selesaikan 1 Level' selesai! (+{$missionCoinBonus} 🪙)";
            }
        }

        // Hitung level baru
        $oldLevel = $user->level;
        $newLevel = max(1, (int) floor($user->xp / self::XP_PER_LEVEL) + 1);
        $leveledUp = false;

        if ($newLevel > $oldLevel) {
            $user->level = $newLevel;
            $leveledUp = true;
            $messages[] = "Naik Level! Sekarang kamu Level {$newLevel}! 🎉";
        }

        $user->save();

        return response()->json([
            'success' => true,
            'isPassed' => $isPassed,
            'xpEarned' => $xpEarned,
            'coinsEarned' => $coinsEarned,
            'leveledUp' => $leveledUp,
            'newLevel' => $newLevel,
            'messages' => $messages,
            'state' => self::getGamificationState($user),
        ]);
    }

    /**
     * Endpoint API: Latihan Menulis di Canvas (Menyelesaikan Misi Harian 2)
     */
    public function practiceWriting(Request $request): JsonResponse
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $xpEarned = 15;
        $coinsEarned = 5;
        $user->xp += $xpEarned;
        $user->coins += $coinsEarned;

        $messages = [];

        // Otomatisasi Misi Harian 2: Latihan Menulis di Canvas
        $mission = UserDailyMission::firstOrNew([
            'user_id' => $user->id,
            'mission_date' => $today,
            'mission_key' => 'practice_writing',
        ]);

        if (!$mission->is_completed) {
            $mission->is_completed = true;
            $mission->reward_claimed = true;
            $mission->save();

            $missionCoinBonus = 15;
            $user->coins += $missionCoinBonus;
            $messages[] = "Misi Harian 'Latihan Nulis di Canvas' selesai! (+{$missionCoinBonus} 🪙)";
        }

        // Cek Level Up
        $newLevel = max(1, (int) floor($user->xp / self::XP_PER_LEVEL) + 1);
        if ($newLevel > $user->level) {
            $user->level = $newLevel;
            $messages[] = "Naik Level! Sekarang kamu Level {$newLevel}! 🎉";
        }

        $user->save();

        return response()->json([
            'success' => true,
            'xpEarned' => $xpEarned,
            'coinsEarned' => $coinsEarned,
            'messages' => $messages,
            'state' => self::getGamificationState($user),
        ]);
    }

    /**
     * Endpoint API: Membaca Dongeng (Menyelesaikan Misi Harian 3 & Beri XP + Koin)
     */
    public function readStory(Request $request): JsonResponse
    {
        $request->validate([
            'story_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $storyId = (int) $request->story_id;
        $today = Carbon::today()->toDateString();

        $xpEarned = 50;
        $coinsEarned = 10;
        $user->xp += $xpEarned;
        $user->coins += $coinsEarned;

        $messages = [];

        // Simpan riwayat baca
        $storyRecord = UserUnlockedStory::firstOrNew([
            'user_id' => $user->id,
            'story_id' => $storyId,
        ]);
        $storyRecord->is_read = true;
        $storyRecord->read_at = now();
        $storyRecord->save();

        // Otomatisasi Misi Harian 3: Baca 1 Cerita Dongeng Sunda
        $mission = UserDailyMission::firstOrNew([
            'user_id' => $user->id,
            'mission_date' => $today,
            'mission_key' => 'read_story',
        ]);

        if (!$mission->is_completed) {
            $mission->is_completed = true;
            $mission->reward_claimed = true;
            $mission->save();

            $missionCoinBonus = 25;
            $user->coins += $missionCoinBonus;
            $messages[] = "Misi Harian 'Baca 1 Dongeng Sunda' selesai! (+{$missionCoinBonus} 🪙)";
        }

        // Cek Level Up
        $newLevel = max(1, (int) floor($user->xp / self::XP_PER_LEVEL) + 1);
        if ($newLevel > $user->level) {
            $user->level = $newLevel;
            $messages[] = "Naik Level! Sekarang kamu Level {$newLevel}! 🎉";
        }

        $user->save();

        return response()->json([
            'success' => true,
            'xpEarned' => $xpEarned,
            'coinsEarned' => $coinsEarned,
            'messages' => $messages,
            'state' => self::getGamificationState($user),
        ]);
    }

    /**
     * Endpoint API: Beli / Buka Cerita Dongeng Baru dengan Koin
     */
    public function unlockStory(Request $request): JsonResponse
    {
        $request->validate([
            'story_id' => 'required|integer',
            'cost' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $storyId = (int) $request->story_id;
        $cost = (int) $request->cost;

        if ($cost > 0 && ($user->coins < $cost)) {
            return response()->json([
                'success' => false,
                'message' => 'Koin kamu tidak cukup untuk membuka dongeng ini!',
            ], 400);
        }

        if ($cost > 0) {
            $user->coins -= $cost;
            $user->save();
        }

        $record = UserUnlockedStory::firstOrNew([
            'user_id' => $user->id,
            'story_id' => $storyId,
        ]);
        $record->unlocked_at = now();
        $record->save();

        return response()->json([
            'success' => true,
            'message' => 'Dongeng berhasil dibuka!',
            'state' => self::getGamificationState($user),
        ]);
    }

    /**
     * Endpoint API: Beli Bantuan / Hint dalam Soal
     */
    public function useHint(Request $request): JsonResponse
    {
        $request->validate([
            'cost' => 'nullable|integer|min:1',
        ]);

        $user = Auth::user();
        $cost = (int) ($request->cost ?? 5);

        if ($user->coins < $cost) {
            return response()->json([
                'success' => false,
                'message' => "Koin tidak cukup! Butuh {$cost} koin untuk bantuan.",
            ], 400);
        }

        $user->coins -= $cost;
        $user->save();

        return response()->json([
            'success' => true,
            'remainingCoins' => $user->coins,
            'message' => "Bantuan berhasil digunakan! (-{$cost} 🪙)",
            'state' => self::getGamificationState($user),
        ]);
    }

    /**
     * Endpoint API: Klaim Hadiah Spesial Bonus Semua Misi Selesai
     */
    public function claimMissionBonus(Request $request): JsonResponse
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Pastikan semua 3 misi selesai
        $requiredKeys = ['complete_level', 'practice_writing', 'read_story'];
        $completedCount = UserDailyMission::where('user_id', $user->id)
            ->where('mission_date', $today)
            ->whereIn('mission_key', $requiredKeys)
            ->where('is_completed', true)
            ->count();

        if ($completedCount < count($requiredKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Selesaikan semua misi harian terlebih dahulu!',
            ], 400);
        }

        // Cek apakah bonus box sudah diklaim
        $bonusRecord = UserDailyMission::firstOrNew([
            'user_id' => $user->id,
            'mission_date' => $today,
            'mission_key' => 'bonus_box',
        ]);

        if ($bonusRecord->is_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Hadiah spesial semua misi hari ini sudah kamu klaim!',
            ], 400);
        }

        $bonusRecord->is_completed = true;
        $bonusRecord->reward_claimed = true;
        $bonusRecord->save();

        $bonusXp = 50;
        $bonusCoins = 30;

        $user->xp += $bonusXp;
        $user->coins += $bonusCoins;

        // Cek level up
        $newLevel = max(1, (int) floor($user->xp / self::XP_PER_LEVEL) + 1);
        if ($newLevel > $user->level) {
            $user->level = $newLevel;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Selamat! Kamu mendapatkan Hadiah Spesial Box: +{$bonusXp} XP dan +{$bonusCoins} Koin! 🎁🎉",
            'bonusXp' => $bonusXp,
            'bonusCoins' => $bonusCoins,
            'state' => self::getGamificationState($user),
        ]);
    }
}
