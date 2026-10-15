<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserQuizProgress;
use App\Models\UserUnlockedStory;
use App\Models\UserDailyMission;
use App\Models\UserCheckin;
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

        // 4. Cerita / Dongeng yang Terbuka & Selesai Diklaim
        // Dongeng 1 selalu terbuka gratis secara default
        $unlockedStoryIds = UserUnlockedStory::where('user_id', $user->id)->pluck('story_id')->toArray();
        if (!in_array(1, $unlockedStoryIds)) {
            $unlockedStoryIds[] = 1;
        }

        // Daftar ID dongeng yang sudah selesai dibaca (klaim sekali seumur hidup)
        $completedStoryIds = UserUnlockedStory::where('user_id', $user->id)
            ->where('is_read', true)
            ->pluck('story_id')
            ->toArray();

        // Reset streak jika kemarin tidak ada aktivitas sama sekali
        if ($user->last_activity_at) {
            $lastAct = Carbon::parse($user->last_activity_at)->startOfDay();
            if ($lastAct->lt(Carbon::today()->subDay())) {
                $user->streak = 0;
                $user->save();
            }
        }

        // Status check-in hari ini
        $hasCheckedInToday = UserCheckin::where('user_id', $user->id)
            ->where('checkin_date', Carbon::today()->toDateString())
            ->exists();

        // Tracker 7 hari harian (Jum, Sab, Min, Sen, Sel, Rab, Kam)
        $pastCheckinDates = UserCheckin::where('user_id', $user->id)
            ->where('checkin_date', '>=', Carbon::today()->subDays(6)->toDateString())
            ->pluck('checkin_date')
            ->toArray();

        $dayNamesIndo = [
            0 => 'Min',
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
        ];

        $weeklyTracker = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dStr = $d->toDateString();
            $weeklyTracker[] = [
                'date' => $dStr,
                'dayName' => $dayNamesIndo[$d->dayOfWeek],
                'dayNumber' => $d->day,
                'isToday' => $i === 0,
                'isCheckedIn' => in_array($dStr, $pastCheckinDates),
            ];
        }

        // Leaderboard Jawara Aksara berbasis STREAK terbanyak (Hanya Data Pengguna Asli Real-time)
        $dbUsers = User::select(['id', 'name', 'streak', 'xp', 'level'])
            ->orderByDesc('streak')
            ->orderByDesc('xp')
            ->get();

        $rankedList = [];
        $currentUserRank = null;
        $currentUserEntry = null;

        foreach ($dbUsers as $idx => $u) {
            $rank = $idx + 1;
            $badge = match ($rank) {
                1 => '🥇 Jawara 1',
                2 => '🥈 Jawara 2',
                3 => '🥉 Jawara 3',
                default => "#{$rank}",
            };

            $isCurrent = $u->id === $user->id;
            $avatar = match ($rank) {
                1 => '👑',
                2 => '🥈',
                3 => '🥉',
                default => ($isCurrent ? '🦸‍♂️' : '⭐'),
            };
            if ($isCurrent) {
                $avatar = '🦸‍♂️';
            }

            $item = [
                'id' => $u->id,
                'name' => $u->name,
                'streak' => (int) $u->streak,
                'xp' => (int) $u->xp,
                'level' => (int) $u->level,
                'avatar' => $avatar,
                'is_current_user' => $isCurrent,
                'rank' => $rank,
                'badge' => $badge,
                'streak_text' => "{$u->streak} hari beruntun",
            ];

            $rankedList[] = $item;

            if ($isCurrent) {
                $currentUserRank = $rank;
                $currentUserEntry = $item;
            }
        }

        $top3 = array_slice($rankedList, 0, 3);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'coins' => $user->coins ?? 50,
                'xp' => $user->xp ?? 0,
                'level' => $user->level ?? 1,
                'streak' => $user->streak ?? 0,
                'max_streak' => max((int) ($user->max_streak ?? 0), (int) $user->streak),
                'current_level_xp' => $currentLevelXp,
                'xp_to_next_level' => $xpToNextLevel,
                'xp_percent' => $xpPercent,
                'completed_stories' => $completedStoryIds,
            ],
            'levels' => $levels,
            'completedLevelsCount' => count(array_filter($levels, fn ($l) => $l['status'] === 'completed')),
            'missions' => $missionsDef,
            'completedMissionsCount' => $completedMissionsCount,
            'allMissionsCompleted' => $allMissionsCompleted,
            'bonusClaimed' => $bonusClaimed,
            'unlockedStoryIds' => $unlockedStoryIds,
            'completedStoryIds' => $completedStoryIds,
            'streakData' => [
                'currentStreak' => (int) $user->streak,
                'maxStreak' => max((int) ($user->max_streak ?? 0), (int) $user->streak),
                'hasCheckedInToday' => $hasCheckedInToday,
                'lastActivityAt' => $user->last_activity_at,
                'weeklyTracker' => $weeklyTracker,
            ],
            'leaderboard' => [
                'top3' => $top3,
                'currentUser' => $currentUserEntry,
                'isInTop3' => $currentUserRank !== null && $currentUserRank <= 3,
            ],
        ];
    }

    /**
     * Helper: Catat Check-in Harian & Perbarui Runtutan Belajar (Streak)
     */
    public static function recordDailyCheckin(User $user, string $type = 'auto'): array
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $existing = UserCheckin::where('user_id', $user->id)
            ->where('checkin_date', $todayStr)
            ->first();

        $isNewCheckin = false;
        $message = '';

        if (!$existing) {
            UserCheckin::create([
                'user_id' => $user->id,
                'checkin_date' => $todayStr,
                'checkin_type' => $type,
            ]);
            $isNewCheckin = true;

            $lastActivity = $user->last_activity_at ? Carbon::parse($user->last_activity_at)->startOfDay() : null;

            if (is_null($lastActivity)) {
                $user->streak = 1;
            } elseif ($lastActivity->equalTo($today)) {
                // Streak tetap
            } elseif ($lastActivity->equalTo($today->copy()->subDay())) {
                $user->streak += 1;
            } else {
                $user->streak = 1;
            }

            $user->last_activity_at = $todayStr;
            $user->max_streak = max((int) ($user->max_streak ?? 0), (int) $user->streak);

            if ($type === 'manual') {
                $user->xp += 10;
                $user->coins += 5;
                $message = "Check-in harian berhasil! (+10 XP & +5 🪙) Api belajarmu tetap menyala! 🔥";
            } else {
                $message = "Check-in harian otomatis tercatat! Runtutan: {$user->streak} hari! 🔥";
            }

            $user->save();
        } else {
            $message = "Kamu sudah check-in hari ini! Ritme belajarmu terjaga.";
        }

        return [
            'isNewCheckin' => $isNewCheckin,
            'streak' => $user->streak,
            'message' => $message,
        ];
    }

    /**
     * Endpoint API: Check-in Manual dari Modal Runtutan Belajar
     */
    public function checkIn(Request $request): JsonResponse
    {
        $user = Auth::user();
        $result = self::recordDailyCheckin($user, 'manual');

        return response()->json([
            'success' => true,
            'isNewCheckin' => $result['isNewCheckin'],
            'message' => $result['message'],
            'state' => self::getGamificationState($user),
        ]);
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

            // Otomatisasi Check-in Harian (Aktivitas Belajar)
            $checkinRes = self::recordDailyCheckin($user, 'auto_quiz');
            if ($checkinRes['isNewCheckin']) {
                $messages[] = $checkinRes['message'];
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

        // Otomatisasi Check-in Harian (Aktivitas Belajar)
        $checkinRes = self::recordDailyCheckin($user, 'auto_canvas');
        if ($checkinRes['isNewCheckin']) {
            $messages[] = $checkinRes['message'];
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
        $messages = [];

        // Cek apakah dongeng ini sudah pernah dibaca dan diklaim hadiahnya
        $storyRecord = UserUnlockedStory::firstOrNew([
            'user_id' => $user->id,
            'story_id' => $storyId,
        ]);

        $alreadyClaimed = (bool) $storyRecord->is_read;

        if ($alreadyClaimed) {
            // Sudah pernah klaim, tidak beri XP dan koin lagi
            return response()->json([
                'success' => true,
                'alreadyClaimed' => true,
                'xpEarned' => 0,
                'coinsEarned' => 0,
                'messages' => ['Dongeng ini sudah selesai dibaca sebelumnya. Hadiah sudah pernah diklaim! 👍'],
                'state' => self::getGamificationState($user),
            ]);
        }

        // Klaim Pertama Kali: Berikan hadiah XP dan Koin Budaya
        $xpEarned = 50;
        $coinsEarned = 10;
        $user->xp += $xpEarned;
        $user->coins += $coinsEarned;

        $storyRecord->is_read = true;
        $storyRecord->read_at = now();
        $storyRecord->save();

        $messages[] = "Selamat! Kamu menyelesaikan dongeng ini dan mendapatkan +{$xpEarned} XP & +{$coinsEarned} 🪙!";

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

        // Otomatisasi Check-in Harian (Aktivitas Belajar)
        $checkinRes = self::recordDailyCheckin($user, 'auto_story');
        if ($checkinRes['isNewCheckin']) {
            $messages[] = $checkinRes['message'];
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
