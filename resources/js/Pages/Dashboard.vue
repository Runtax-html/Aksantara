<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import CanvasTracing from '@/Components/CanvasTracing.vue';
import CanvasModal from '@/Components/CanvasModal.vue';
import StrokeGuideModal from '@/Components/StrokeGuideModal.vue';
import VirtualKeyboard from '@/Components/VirtualKeyboard.vue';
import SpeechPronunciation from '@/Components/SpeechPronunciation.vue';
import QuizModal from '@/Components/QuizModal.vue';
import KamusAksara from '@/Components/KamusAksara.vue';
import StoryReaderModal from '@/Components/StoryReaderModal.vue';

const props = defineProps({
    userProgress: {
        type: Array,
        default: () => [],
    },
    gamification: {
        type: Object,
        default: () => null,
    }
});

// State Tab Navigasi Bebas
const activeTab = ref('map'); // 'map' | 'nulis' | 'ketik' | 'kamus' | 'cerita'

// ═══════════════════════════════════════════════════════════════════
// GAMIFIKASI & USER STATS REACTIVE STATE
// ═══════════════════════════════════════════════════════════════════
const userCoins = ref(props.gamification?.user?.coins ?? 50);
const userXP = ref(props.gamification?.user?.xp ?? 0);
const userLevel = ref(props.gamification?.user?.level ?? 1);
const userStreak = ref(props.gamification?.user?.streak ?? 1);

const userCurrentLevelXp = computed(() => userXP.value % 100);
const userXpPercent = computed(() => Math.min(100, Math.round((userCurrentLevelXp.value / 100) * 100)));

// Default Adventure Levels if not provided by backend
const defaultLevelsData = [
    {
        id: 1,
        title: 'Level 1: Swara Aksara Sunda',
        subtitle: 'a, i, u, é, o, e, eu (Pilihan & Tulis)',
        status: 'active',
        stars: 0,
        icon: '🚀',
        color: 'from-[#FF4D30] to-orange-500',
    },
    {
        id: 2,
        title: 'Level 2: Ngalagena Dasar 1',
        subtitle: 'ka, ga, nga, ca, ja, nya',
        status: 'locked',
        stars: 0,
        icon: '🔒',
        color: 'from-slate-300 to-slate-400',
    },
    {
        id: 3,
        title: 'Level 3: Ngalagena Dasar 2',
        subtitle: 'ta, da, na, pa, ba, ma',
        status: 'locked',
        stars: 0,
        icon: '🔒',
        color: 'from-slate-300 to-slate-400',
    },
    {
        id: 4,
        title: 'Level 4: Kosakata Alam & Sato',
        subtitle: 'Ucing, Hayam, Maung, Tangkal',
        status: 'locked',
        stars: 0,
        icon: '🔒',
        color: 'from-slate-300 to-slate-400',
    },
    {
        id: 5,
        title: 'Level 5: Awak & Dahareun',
        subtitle: 'Panon, Leungeun, Cau, Huis',
        status: 'locked',
        stars: 0,
        icon: '🔒',
        color: 'from-slate-300 to-slate-400',
    },
    {
        id: 6,
        title: 'Level 6: Jawara Aksara Sunda',
        subtitle: 'Tantangan Menulis & Tebak Kata',
        status: 'locked',
        stars: 0,
        icon: '👑🔒',
        color: 'from-amber-400 to-yellow-500',
    },
];

const levels = ref(props.gamification?.levels ?? defaultLevelsData);
const completedLevelsCount = computed(() => levels.value.filter(l => l.status === 'completed').length);

// ═══════════════════════════════════════════════════════════════════
// MISI HARIAN OTOMATIS STATE
// ═══════════════════════════════════════════════════════════════════
const defaultMissionsData = [
    { id: 1, key: 'complete_level', title: 'Selesaikan 1 Level Petualangan', reward: 20, completed: false },
    { id: 2, key: 'practice_writing', title: 'Latihan Menulis di Canvas', reward: 15, completed: false },
    { id: 3, key: 'read_story', title: 'Baca 1 Cerita Dongeng Sunda', reward: 25, completed: false },
];

const missions = ref(props.gamification?.missions ?? defaultMissionsData);
const completedMissionsCount = computed(() => missions.value.filter(m => m.completed).length);
const allMissionsCompleted = computed(() => missions.value.length > 0 && completedMissionsCount.value === missions.value.length);
const bonusClaimed = ref(props.gamification?.bonusClaimed ?? false);
const isClaimingBonus = ref(false);
const toastMessage = ref('');

function showToast(msg) {
    toastMessage.value = msg;
    setTimeout(() => {
        toastMessage.value = '';
    }, 4500);
}

async function claimSpecialBonus() {
    if (isClaimingBonus.value || bonusClaimed.value || !allMissionsCompleted.value) return;
    isClaimingBonus.value = true;
    try {
        const res = await axios.post('/api/gamification/claim-mission-bonus');
        if (res.data.success) {
            updateGamificationState(res.data.state);
            showToast(res.data.message || 'Selamat! Hadiah Spesial Box Berhasil Diklaim! 🎁');
        }
    } catch (err) {
        console.error('Gagal klaim bonus misi:', err);
    } finally {
        isClaimingBonus.value = false;
    }
}

// ═══════════════════════════════════════════════════════════════════
// DONGENG SUNDA BERBASIS KOIN
// ═══════════════════════════════════════════════════════════════════
const unlockedStoryIds = ref(props.gamification?.unlockedStoryIds ?? [1]);

const sundaFolktales = [
    {
        id: 1,
        title: 'Si Kabayan Ngala Tutut',
        region: 'Jawa Barat',
        readTime: '3 Menit',
        difficulty: 'Pemula',
        cost: 0, // GRATIS untuk semua pengguna termasuk baru
        icon: '👨‍🌾',
        summary: 'Dongeng lucu Si Kabayan anu disuruh ku mitoha ngala tutut di sawah tapi sieun ku kalangkang langit dina cai.',
        aksaraText1: 'ᮓᮤ ᮠᮤᮏᮤ ᮝᮊ᮪ᮒᮥ, ᮞᮤ ᮊᮘᮚᮔ᮪ ᮓᮤᮒᮤᮒᮂ ᮊᮥ ᮙᮤᮒᮧᮠᮔ ᮍᮜ ᮒᮥᮒᮥᮒ᮪ ᮓᮤ ᮞᮝᮂ. ᮒᮥᮒᮥᮒ᮪ ᮒᮦᮂ ᮊᮛᮤ ᮍᮜᮃᮔ᮪ ᮓᮤᮔ ᮎᮄ ᮔᮥ ᮠᮦᮛᮀ.',
        latinText1: 'Dina hiji poé, Si Kabayan dititah ku mitohana ngala tutut di sawah. Tutut téh kari ngalaan dina cai anu hérang pisan.',
        aksaraText2: 'ᮒ Tapi ᮞᮤ ᮊᮘᮚᮔ᮪ ᮙᮜᮃᮂ ᮍᮧᮏᮨᮊ᮪ ᮕᮊᮦ ᮒᮥᮀᮊᮥᮜ᮪ ᮞᮘᮘ᮪ ᮍᮜᮤᮃᮒ᮪ ᮜᮔ᮪ᮌᮤᮒ᮪ ᮓᮤᮔ ᮎᮄ ᮞᮝᮂ. ᮘᮒᮤ ᮎᮄ ᮞᮝᮂ ᮏᮨᮛᮧ ᮕᮤᮞᮔ᮪ ᮎᮨᮔᮂ.',
        latinText2: 'Tapi Si Kabayan kalahka ngajanteng di galengan sawah, ceuk manéhna: Jerona cai sawah téh, ningali langit aya di handap!',
        latinText3: 'Mitohana anu keuheul tuluy nyurungkeun Si Kabayan kana sawah. Gebyur! Si Kabayan reuwas sabab caina ngan sakeluheun mumuncangan.',
        xpReward: 50,
        coinReward: 10,
    },
    {
        id: 2,
        title: 'Lutung Kasarung',
        region: 'Jawa Barat',
        readTime: '5 Menit',
        difficulty: 'Menengah',
        cost: 50, // Dibeli menggunakan 50 koin
        icon: '🐒',
        summary: 'Kisah Guruminda nu menjelma jadi lutung hideung pikeun nulungan Putri Purbasari nu dihianat ku rakana.',
        aksaraText1: 'ᮕᮥᮁᮘᮞᮛᮤ ᮓᮤᮒᮥᮜᮥᮍᮔ᮪ ᮊᮥ ᮜᮥᮒᮥᮀ ᮊᮞᮛᮥᮀ ᮓᮤ ᮜᮨᮝᮨᮀ.',
        latinText1: 'Purbasari ditulungan ku Lutung Kasarung di leuweung. Lutung téh sajatina pangéran Guruminda ti Kahyangan.',
        latinText2: 'Ku kasobaran Purbasari, manéhna akhirna bisa balik deui ka karaton Pasir Batang jadi ratu anu adil tur bijaksana.',
        latinText3: 'Pesan Moral: Kebaikan dan ketulusan hati akan selalu menang melawan kelicikan.',
        xpReward: 75,
        coinReward: 15,
    },
    {
        id: 3,
        title: 'Sangkuriang & Tangkuban Parahu',
        region: 'Jawa Barat',
        readTime: '7 Menit',
        difficulty: 'Menengah',
        cost: 75,
        icon: '🏔️',
        summary: 'Sasakala gunung Tangkuban Parahu jeung kapal laut nu ditendang ku Sangkuriang.',
        aksaraText1: 'ᮞᮀᮊᮥᮛᮤᮃᮀ ᮍᮏᮩᮔ᮪ ᮕᮛᮠᮥ ᮓᮤᮔ ᮞᮕᮧᮈ.',
        latinText1: 'Sangkuriang nyieun parahu dina waktu sapeuting pikeun nyumponan pamenta Dayang Sumbi.',
        latinText2: 'Dayang Sumbi ngaburalkeun lawon bodas di beulah wétan sangkan disangka geus fajar.',
        latinText3: 'Pesan Moral: Jangan memaksakan kehendak dan hormati orang tua.',
        xpReward: 100,
        coinReward: 20,
    },
    {
        id: 4,
        title: 'Ciung Wanara',
        region: 'Jawa Barat',
        readTime: '6 Menit',
        difficulty: 'Mahir',
        cost: 100,
        icon: '🐓',
        summary: 'Kisah pahlawan karajaan Galuh nu gaduh hayam aduan sakti pikeun nungtut kaadilan.',
        aksaraText1: 'ᮎᮤᮅᮀ ᮝᮔᮛ 001 hayam aduan sakti.',
        latinText1: 'Ciung Wanara mawa hayam jago sakti ka karaton Galuh pikeun ngalawan hayam raja.',
        latinText2: 'Adu hayam jadi jalan ngungkab kajahatan jeung mulangkeun kahormatan indungna.',
        latinText3: 'Pesan Moral: Kebenaran pasti akan terungkap pada waktunya.',
        xpReward: 100,
        coinReward: 25,
    }
];

const showStoryModal = ref(false);
const selectedStory = ref(null);

function isStoryUnlocked(storyId) {
    return storyId === 1 || unlockedStoryIds.value.includes(storyId);
}

function openStoryReader(story) {
    if (!isStoryUnlocked(story.id)) return;
    selectedStory.value = story;
    showStoryModal.value = true;
}

async function unlockStoryWithCoins(story) {
    if (userCoins.value < story.cost) {
        showToast(`⚠️ Koin tidak cukup! Butuh ${story.cost} koin (kamu punya ${userCoins.value} koin).`);
        return;
    }

    try {
        const res = await axios.post('/api/gamification/unlock-story', {
            story_id: story.id,
            cost: story.cost,
        });
        if (res.data.success) {
            updateGamificationState(res.data.state);
            showToast(`🎉 Dongeng "${story.title}" berhasil dibuka!`);
        }
    } catch (err) {
        console.error('Gagal membuka dongeng:', err);
        showToast(err.response?.data?.message || 'Gagal membuka dongeng.');
    }
}

function handleStoryCompleted(result) {
    if (result && result.state) {
        updateGamificationState(result.state);
    }
}

// ═══════════════════════════════════════════════════════════════════
// STATE SYNC METHOD
// ═══════════════════════════════════════════════════════════════════
function updateGamificationState(state) {
    if (!state) return;
    if (state.user) {
        userCoins.value = state.user.coins;
        userXP.value = state.user.xp;
        userLevel.value = state.user.level;
        userStreak.value = state.user.streak;
    }
    if (state.levels) {
        levels.value = state.levels;
    }
    if (state.missions) {
        missions.value = state.missions;
    }
    if (state.unlockedStoryIds) {
        unlockedStoryIds.value = state.unlockedStoryIds;
    }
    if (state.bonusClaimed !== undefined) {
        bonusClaimed.value = state.bonusClaimed;
    }
}

// CanvasModal State
const showCanvasModal = ref(false);
const selectedCanvasChar = ref('ᮊ');
const selectedCanvasLatin = ref('Ka');

// StrokeGuideModal State (Panduan Animasi Khusus Huruf Ka 77)
const showStrokeGuideModal = ref(false);

function openModalForChar(item) {
    selectedCanvasChar.value = item.char;
    selectedCanvasLatin.value = item.latin;
    showCanvasModal.value = true;
}

function handleStartPracticeFromGuide() {
    const kaItem = sundaGrid.find(item => item.latin === 'Ka') || { char: 'ᮊ', latin: 'Ka' };
    openModalForChar(kaItem);
}

function handleXpGained(amount) {
    userXP.value += amount;
}

function handleCoinsUpdated(coins) {
    userCoins.value = coins;
}

function handleGamificationUpdated(state) {
    updateGamificationState(state);
}

// QuizModal State
const showQuizModal = ref(false);
const quizLevelId = ref(1);
const quizLevelTitle = ref('');

function openQuizForLevel(level) {
    if (level.status === 'locked') return;
    quizLevelId.value = level.id;
    quizLevelTitle.value = level.title;
    showQuizModal.value = true;
}

function handleLevelCompleted(result) {
    if (result && result.state) {
        updateGamificationState(result.state);
    } else {
        // Fallback local update
        const completedLevel = levels.value.find(l => l.id === result.levelId);
        if (completedLevel) {
            completedLevel.status = 'completed';
            completedLevel.stars = Math.max(completedLevel.stars, result.stars);
            completedLevel.icon = '⭐';
            completedLevel.color = 'from-emerald-400 to-green-500';
        }
        const nextLevel = levels.value.find(l => l.id === result.levelId + 1);
        if (nextLevel && nextLevel.status === 'locked') {
            nextLevel.status = 'active';
            nextLevel.icon = '🚀';
            nextLevel.color = 'from-[#FF4D30] to-orange-500';
        }
    }
}

function startLevel(level) {
    if (level.status !== 'locked') {
        openQuizForLevel(level);
    }
}

// Leaderboard Singkat (Top Jawara Aksara Sunda)
const leaderboard = [
    { rank: 1, name: 'Budi Santoso', level: 5, xp: 450, avatar: '👑', badge: '🥇 Jawara 1' },
    { rank: 2, name: 'Ani Suryani', level: 4, xp: 380, avatar: '⭐', badge: '🥈 Jawara 2' },
    { rank: 3, name: 'Cecep Supriatna', level: 3, xp: 290, avatar: '🚀', badge: '🥉 Jawara 3' },
];

// Aksara Sunda Grid List for Canvas Modal Selection
const sundaGrid = [
    { char: 'ᮃ', latin: 'A', category: 'Swara' },
    { char: 'ᮄ', latin: 'I', category: 'Swara' },
    { char: 'ᮅ', latin: 'U', category: 'Swara' },
    { char: 'ᮈ', latin: 'E', category: 'Swara' },
    { char: 'ᮇ', latin: 'O', category: 'Swara' },
    { char: 'ᮊ', latin: 'Ka', category: 'Ngalagena' },
    { char: 'ᮌ', latin: 'Ga', category: 'Ngalagena' },
    { char: 'ᮎ', latin: 'Ca', category: 'Ngalagena' },
    { char: 'ᮏ', latin: 'Ja', category: 'Ngalagena' },
    { char: 'ᮓ', latin: 'Da', category: 'Ngalagena' },
    { char: 'ᮔ', latin: 'Na', category: 'Ngalagena' },
    { char: 'ᮕ', latin: 'Pa', category: 'Ngalagena' },
    { char: 'ᮘ', latin: 'Ba', category: 'Ngalagena' },
    { char: 'ᮙ', latin: 'Ma', category: 'Ngalagena' },
    { char: 'ᮞ', latin: 'Sa', category: 'Ngalagena' },
    { char: 'ᮠ', latin: 'Ha', category: 'Ngalagena' },
];
</script>

<template>
    <Head title="Petualangan Aksara Sunda - Aksantara" />

    <div class="min-h-screen bg-[#FFFDF5] text-gray-800 font-sans selection:bg-[#FF4D30] selection:text-white pb-28 sm:pb-32">
        
        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 1. TOP BAR GAME STATUS                                  -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b-4 border-orange-200 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 sm:py-3">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-4">
                    
                    <!-- User Avatar & Sunda Badge -->
                    <div class="flex items-center justify-between w-full sm:w-auto gap-3">
                        <Link href="/" class="flex items-center gap-2 shrink-0">
                            <span class="text-3xl">ᮞ</span>
                            <span class="text-xl font-extrabold text-[#FF4D30] hidden xs:inline">Aksantara Sunda</span>
                        </Link>
                        
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-orange-400 to-[#FF4D30] p-0.5 shadow-md shrink-0">
                                <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center text-xl sm:text-2xl">
                                    🦸‍♂️
                                </div>
                            </div>
                            <div>
                                <h1 class="text-xs sm:text-sm font-extrabold text-gray-900 leading-tight">
                                    {{ $page.props.auth.user.name }}
                                </h1>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-extrabold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                    ⭐ Level {{ userLevel }} - Jawara Sunda
                                </span>
                            </div>
                        </div>

                        <!-- Logout Mobile -->
                        <div class="sm:hidden">
                            <Link :href="route('logout')" method="post" as="button" class="p-1.5 text-xs font-bold text-gray-500 hover:text-[#FF4D30]">
                                🚪
                            </Link>
                        </div>
                    </div>

                    <!-- Game Stats: XP, Streak, Coins -->
                    <div class="w-full sm:w-auto overflow-x-auto no-scrollbar py-1 sm:py-0">
                        <div class="flex items-center gap-2 sm:gap-3 shrink-0 justify-center sm:justify-end">
                            
                            <!-- XP Bar -->
                            <div class="flex items-center gap-1.5 bg-purple-50 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-2xl border-2 border-purple-200">
                                <span class="text-lg sm:text-xl">🧪</span>
                                <div class="w-20 sm:w-28">
                                    <div class="flex justify-between text-[10px] font-extrabold text-purple-700 mb-0.5">
                                        <span class="hidden sm:inline">XP</span>
                                        <span>{{ userCurrentLevelXp }}/100</span>
                                    </div>
                                    <div class="w-full h-2 sm:h-2.5 bg-purple-200 rounded-full overflow-hidden">
                                        <div
                                            class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full transition-all duration-500"
                                            :style="{ width: userXpPercent + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Streak Hari -->
                            <div class="flex items-center gap-1 bg-orange-100 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-2xl border-2 border-orange-300 text-orange-700 font-extrabold text-xs sm:text-sm shadow-sm shrink-0">
                                <span class="text-base sm:text-xl animate-pulse">🔥</span>
                                <span>{{ userStreak }} <span class="hidden sm:inline">Hari</span></span>
                            </div>

                            <!-- Koin Budaya -->
                            <div class="flex items-center gap-1 bg-amber-100 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-2xl border-2 border-amber-300 text-amber-800 font-extrabold text-xs sm:text-sm shadow-sm shrink-0">
                                <span class="text-base sm:text-xl">🪙</span>
                                <span>{{ userCoins }}</span>
                            </div>

                            <!-- Desktop Actions -->
                            <div class="hidden sm:flex items-center gap-1.5 ml-2">
                                <Link :href="route('profile.edit')" class="px-2.5 py-1.5 text-xs font-extrabold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl border-2 border-gray-200 transition">
                                    ⚙️
                                </Link>
                                <Link :href="route('logout')" method="post" as="button" class="px-2.5 py-1.5 text-xs font-extrabold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl border-2 border-red-200 transition">
                                    🚪
                                </Link>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 2. DESKTOP TAB SWITCHER (HORIZONTAL MENU 5 PILIHAN)     -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 hidden lg:block">
            <div class="bg-white p-2 rounded-3xl border-4 border-amber-300 shadow-lg flex items-center justify-between gap-2">
                <!-- 1. Petualangan -->
                <button
                    @click="activeTab = 'map'"
                    class="flex-1 py-3 px-3 rounded-2xl font-extrabold text-xs sm:text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'map' ? 'bg-[#FF4D30] text-white border-orange-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-orange-50/50 text-gray-700 border-orange-200 hover:bg-orange-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-lg">🗺️</span>
                    <span>Petualangan</span>
                </button>

                <!-- 2. Coretan -->
                <button
                    @click="activeTab = 'nulis'"
                    class="flex-1 py-3 px-3 rounded-2xl font-extrabold text-xs sm:text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'nulis' ? 'bg-[#FF4D30] text-white border-orange-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-orange-50/50 text-gray-700 border-orange-200 hover:bg-orange-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-lg">✏️</span>
                    <span>Coretan</span>
                </button>

                <!-- 3. Ketik -->
                <button
                    @click="activeTab = 'ketik'"
                    class="flex-1 py-3 px-3 rounded-2xl font-extrabold text-xs sm:text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'ketik' ? 'bg-purple-600 text-white border-purple-800 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-purple-50/50 text-purple-900 border-purple-200 hover:bg-purple-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-lg">⌨️</span>
                    <span>Ketik</span>
                </button>

                <!-- 4. Kamus -->
                <button
                    @click="activeTab = 'kamus'"
                    class="flex-1 py-3 px-3 rounded-2xl font-extrabold text-xs sm:text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'kamus' ? 'bg-amber-500 text-white border-amber-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-amber-50/50 text-amber-900 border-amber-200 hover:bg-amber-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-lg">📚</span>
                    <span>Kamus</span>
                </button>

                <!-- 5. Dongeng -->
                <button
                    @click="activeTab = 'cerita'"
                    class="flex-1 py-3 px-3 rounded-2xl font-extrabold text-xs sm:text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'cerita' ? 'bg-teal-500 text-white border-teal-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-teal-50/50 text-teal-900 border-teal-200 hover:bg-teal-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-lg">📖</span>
                    <span>Dongeng</span>
                </button>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 3. KONTEN TAB UTAMA                                     -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">

            <!-- =================================================== -->
            <!-- TAB 1: PETA PETUALANGAN (activeTab === 'map')       -->
            <!-- =================================================== -->
            <div v-if="activeTab === 'map'">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                    <!-- KOLOM KIRI: PETA LEVEL AKSARA SUNDA (8 COLS) -->
                    <div class="lg:col-span-8 bg-emerald-500/10 rounded-3xl p-5 sm:p-8 border-4 border-emerald-300 relative overflow-hidden shadow-xl min-h-[580px] flex flex-col justify-between">
                        <div class="absolute top-4 left-6 text-3xl opacity-40 select-none">☁️</div>
                        <div class="absolute top-8 right-12 text-4xl opacity-40 select-none">☁️</div>
                        <div class="absolute bottom-6 left-10 text-4xl opacity-30 select-none">🌳</div>
                        <div class="absolute bottom-12 right-8 text-4xl opacity-30 select-none">🌴</div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8 bg-white/90 backdrop-blur-sm p-4 rounded-2xl border-2 border-emerald-200 shadow-sm">
                                <div>
                                    <h2 class="text-base sm:text-xl font-extrabold text-gray-800 flex items-center gap-2">
                                        <span>🗺️ Petualangan Aksara Sunda</span>
                                        <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md border border-emerald-300">ᮞ Sunda</span>
                                    </h2>
                                    <p class="text-xs text-gray-600 font-semibold mt-0.5">Selesaikan tiap level untuk menjadi Jawara Aksara Sunda!</p>
                                </div>
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-extrabold rounded-xl border border-emerald-300 shrink-0">
                                    🌟 {{ completedLevelsCount }} / 6 Selesai
                                </span>
                            </div>

                            <!-- Level Path Nodes (Centered) -->
                            <div class="relative w-full max-w-sm mx-auto py-2 space-y-8">
                                <div
                                    v-for="(level, index) in levels"
                                    :key="level.id"
                                    class="flex items-center gap-3 relative z-10"
                                    :class="index % 2 === 0 ? 'justify-start sm:pl-4' : 'justify-end sm:pr-4'"
                                >
                                    <div
                                        class="group relative bg-white rounded-3xl p-4 sm:p-5 shadow-lg border-4 transition-all duration-200 w-full max-w-[280px] sm:max-w-xs"
                                        :class="[
                                            level.status === 'active' ? 'border-[#FF4D30] shadow-red-500/20 scale-105 ring-4 ring-[#FF4D30]/20' : '',
                                            level.status === 'completed' ? 'border-emerald-400 hover:scale-105' : '',
                                            level.status === 'locked' ? 'border-slate-300 bg-slate-50/90 opacity-80' : '',
                                        ]"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl flex items-center justify-center text-lg sm:text-xl text-white font-extrabold shadow-md bg-gradient-to-tr"
                                                :class="level.color"
                                            >
                                                {{ level.icon }}
                                            </span>

                                            <span v-if="level.status === 'completed'" class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[11px] font-extrabold rounded-xl border border-emerald-200">
                                                Selesai ✅
                                            </span>
                                            <span v-else-if="level.status === 'active'" class="px-2.5 py-0.5 bg-[#FF4D30] text-white text-[11px] font-extrabold rounded-xl animate-bounce">
                                                Mulai! 🚀
                                            </span>
                                            <span v-else class="px-2 py-0.5 bg-slate-200 text-slate-600 text-[11px] font-extrabold rounded-xl">
                                                Terkunci 🔒
                                            </span>
                                        </div>

                                        <h3 class="font-extrabold text-sm sm:text-base text-gray-800 group-hover:text-[#FF4D30] transition">
                                            {{ level.title }}
                                        </h3>
                                        <p class="text-[11px] sm:text-xs text-gray-500 font-semibold mt-0.5">
                                            {{ level.subtitle }}
                                        </p>

                                        <div v-if="level.status === 'active'" class="mt-3">
                                            <button
                                                @click="startLevel(level)"
                                                class="w-full py-2 bg-[#FF4D30] hover:bg-[#e03e22] text-white text-xs font-extrabold rounded-xl border-b-4 border-orange-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer"
                                            >
                                                Mulai Kuis 📝
                                            </button>
                                        </div>
                                        <div v-else-if="level.status === 'completed'" class="mt-3 space-y-1.5">
                                            <div class="flex items-center justify-center gap-0.5">
                                                <span v-for="s in 3" :key="s" class="text-sm" :class="s <= level.stars ? '' : 'grayscale opacity-30'">⭐</span>
                                            </div>
                                            <button
                                                @click="startLevel(level)"
                                                class="w-full py-1.5 bg-emerald-50 text-emerald-700 border-2 border-emerald-200 border-b-4 text-xs font-extrabold rounded-xl hover:bg-emerald-100 active:translate-y-0.5 transition-all cursor-pointer"
                                            >
                                                Ulangi Kuis 🔄
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: MISI HARIAN + LEADERBOARD (4 COLS) -->
                    <div class="lg:col-span-4 space-y-6">

                        <!-- WIDGET 1: MISI HARIAN (OTOMATIS) -->
                        <div class="bg-white rounded-3xl p-5 sm:p-6 border-3 border-amber-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">📋</span>
                                    <h3 class="text-base font-extrabold text-gray-800">Misi Harian</h3>
                                </div>
                                <span class="text-[11px] font-extrabold text-amber-800 bg-amber-100 border border-amber-300 px-2.5 py-0.5 rounded-full">
                                    {{ completedMissionsCount }}/{{ missions.length }} Selesai
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                <div
                                    v-for="mission in missions"
                                    :key="mission.id"
                                    class="flex items-center justify-between p-3 rounded-2xl border-2 transition select-none"
                                    :class="mission.completed ? 'bg-emerald-50/90 border-emerald-300 shadow-sm' : 'bg-orange-50/50 border-orange-200'"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-6 h-6 rounded-lg border-2 flex items-center justify-center text-xs font-black transition"
                                            :class="mission.completed ? 'bg-emerald-500 border-emerald-600 text-white shadow-sm' : 'border-orange-300 bg-white text-gray-300'"
                                        >
                                            {{ mission.completed ? '✓' : '' }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold block" :class="mission.completed ? 'line-through text-gray-400' : 'text-gray-800'">
                                                {{ mission.title }}
                                            </span>
                                            <span class="text-[10px] font-bold" :class="mission.completed ? 'text-emerald-600' : 'text-orange-500'">
                                                {{ mission.completed ? 'Selesai otomatis' : 'Otomatis saat dikerjakan' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-lg flex items-center gap-1 shrink-0"
                                        :class="mission.completed ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'text-amber-700 bg-amber-100 border border-amber-300'"
                                    >
                                        🪙 +{{ mission.reward }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3.5 pt-3 border-t-2 border-dashed border-amber-200 flex flex-col gap-2">
                                <div class="flex items-center justify-between text-xs text-gray-600 font-bold">
                                    <span>Bonus Semua Misi:</span>
                                    <span class="text-[#FF4D30] font-extrabold">+50 XP & +30 🪙</span>
                                </div>

                                <button
                                    v-if="allMissionsCompleted && !bonusClaimed"
                                    @click="claimSpecialBonus"
                                    :disabled="isClaimingBonus"
                                    class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-[#FF4D30] hover:from-amber-600 hover:to-orange-600 text-white font-black text-xs rounded-xl shadow-md border-2 border-orange-700 active:translate-y-0.5 transition flex items-center justify-center gap-2 cursor-pointer animate-pulse"
                                >
                                    <span>🎁</span>
                                    <span>{{ isClaimingBonus ? 'Mengklaim...' : 'Buka Hadiah Spesial Box!' }}</span>
                                </button>
                                <div
                                    v-else-if="bonusClaimed"
                                    class="w-full py-2 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-extrabold rounded-xl text-center"
                                >
                                    ✓ Hadiah Spesial Hari Ini Sudah Diambil! 🎁
                                </div>
                                <div
                                    v-else
                                    class="text-[11px] text-gray-500 font-semibold text-center italic"
                                >
                                    Selesaikan ketiga misi harian untuk membuka box rahasia!
                                </div>
                            </div>
                        </div>

                        <!-- WIDGET 2: LEADERBOARD SINGKAT -->
                        <div class="bg-white rounded-3xl p-5 sm:p-6 border-3 border-yellow-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🏆</span>
                                    <h3 class="text-base font-extrabold text-gray-800">Jawara Aksara</h3>
                                </div>
                                <span class="text-[11px] font-extrabold text-yellow-800 bg-yellow-100 border border-yellow-300 px-2 py-0.5 rounded-full">
                                    Minggu Ini
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                <div
                                    v-for="user in leaderboard"
                                    :key="user.rank"
                                    class="flex items-center justify-between p-3 rounded-2xl border-2 bg-yellow-50/50 border-yellow-200"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-lg font-black text-amber-800 w-5 text-center">
                                            #{{ user.rank }}
                                        </span>
                                        <div class="w-8 h-8 rounded-xl bg-white border-2 border-yellow-300 flex items-center justify-center text-sm shadow-sm">
                                            {{ user.avatar }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-extrabold text-gray-900 leading-tight">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-[10px] font-extrabold text-amber-700">
                                                Level {{ user.level }} • {{ user.xp }} XP
                                            </div>
                                        </div>
                                    </div>

                                    <span class="text-[10px] font-black px-2 py-0.5 bg-yellow-200 text-yellow-900 rounded-lg border border-yellow-400">
                                        {{ user.badge }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- =================================================== -->
            <!-- TAB 2: MODUL NULIS / CANVAS (FULL-WIDTH + GRID)    -->
            <!-- =================================================== -->
            <div v-else-if="activeTab === 'nulis'" class="max-w-5xl mx-auto space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border-4 border-orange-300 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-orange-100 text-[#FF4D30] rounded-full text-xs font-extrabold mb-2 border border-orange-200">
                            <span>✏️ Modul Nulis Aksara Sunda</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Pilih Huruf untuk Berlatih Nulis</h2>
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold mt-1">
                            Klik salah satu kartu huruf Aksara Sunda di bawah ini untuk membuka modal canvas latihan!
                        </p>
                    </div>

                    <!-- Tombol Khusus di Luar Grid: Panduan Menulis Animasi (Bentuk 77) -->
                    <div class="flex flex-wrap items-center justify-between gap-3 bg-gradient-to-r from-orange-100/90 via-amber-100/80 to-orange-100/90 p-3.5 sm:p-4 rounded-2xl border-2 border-orange-200 mb-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl sm:text-3xl p-2 bg-white rounded-xl shadow-xs border border-orange-200">🎬</span>
                            <div>
                                <h4 class="text-xs sm:text-sm font-black text-gray-800">Panduan Menulis Aksara</h4>
                                <p class="text-[11px] text-gray-600 font-semibold">Tonton animasi gerakan langkah demi langkah bentuk 77 (Ka).</p>
                            </div>
                        </div>
                        <button
                            @click="showStrokeGuideModal = true"
                            class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-[#FF4D30] hover:from-amber-600 hover:to-orange-600 text-white font-black text-xs rounded-xl border-2 border-orange-700 border-b-4 shadow-[0_2px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center gap-1.5 hover:scale-105"
                        >
                            <span>▶</span>
                            <span>Panduan Menulis (Animasi)</span>
                        </button>
                    </div>

                    <!-- Character Card Grid for Canvas Modal Trigger -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3 mb-8">
                        <button
                            v-for="item in sundaGrid"
                            :key="item.char"
                            @click="openModalForChar(item)"
                            class="bg-orange-50 hover:bg-orange-100 p-4 rounded-2xl border-3 border-orange-300 border-b-4 flex flex-col items-center justify-center cursor-pointer transition-all duration-150 active:translate-y-0.5 active:border-b-2 active:shadow-none hover:-translate-y-1 shadow-sm group"
                        >
                            <span class="text-3xl sm:text-4xl font-black text-gray-900 group-hover:scale-110 transition-transform">
                                {{ item.char }}
                            </span>
                            <span class="text-xs font-extrabold text-[#FF4D30] mt-1">
                                {{ item.latin }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- =================================================== -->
            <!-- TAB 3: MODUL KETIK & AUDIO (FULL-WIDTH)             -->
            <!-- =================================================== -->
            <div v-else-if="activeTab === 'ketik'" class="max-w-4xl mx-auto space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border-4 border-purple-300 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-extrabold mb-2 border border-purple-200">
                            <span>⌨️ Modul Ketik Aksara Sunda</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Virtual Keyboard & Audio Suara</h2>
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold mt-1">
                            Tekan tombol papan ketik di bawah untuk mengetik kalimat aksara Sunda dan mendengarkan suaranya!
                        </p>
                    </div>

                    <VirtualKeyboard />
                </div>
            </div>

            <!-- =================================================== -->
            <!-- TAB 4: KAMUS AKSARA SUNDA (FULL-WIDTH)              -->
            <!-- =================================================== -->
            <div v-else-if="activeTab === 'kamus'" class="max-w-5xl mx-auto">
                <KamusAksara />
            </div>

            <!-- =================================================== -->
            <!-- TAB 5: CERITA RAKYAT SUNDA / DONGENG (FULL-WIDTH)   -->
            <!-- =================================================== -->
            <div v-else-if="activeTab === 'cerita'" class="max-w-5xl mx-auto">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border-4 border-teal-300 shadow-xl mb-6">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-teal-100 text-teal-800 rounded-full text-xs font-extrabold mb-2 border border-teal-200">
                            <span>📖 Dongeng Sunda</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Perpustakaan Cerita Rakyat Sunda</h2>
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold mt-1">
                            Kumpulan cerita rakyat berbahasa Sunda & terjemahan untuk menemani petualangan belajarmu!
                        </p>
                    </div>

                    <!-- Folktales Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div
                            v-for="story in sundaFolktales"
                            :key="story.id"
                            class="bg-teal-50/60 rounded-3xl p-6 border-3 transition-all duration-200 flex flex-col justify-between"
                            :class="isStoryUnlocked(story.id) ? 'border-teal-300 shadow-md hover:scale-[1.02]' : 'border-amber-300/80 bg-amber-50/30'"
                        >
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-4xl p-2 bg-white rounded-2xl border-2 border-teal-200 shadow-sm">
                                        {{ story.icon }}
                                    </span>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-extrabold border"
                                        :class="isStoryUnlocked(story.id) ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-amber-100 text-amber-900 border-amber-300'"
                                    >
                                        {{ isStoryUnlocked(story.id) ? '🗝️ Terbuka (Gratis / Dibeli)' : `🔒 Butuh ${story.cost} 🪙` }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-extrabold text-gray-900 mb-1">
                                    {{ story.title }}
                                </h3>
                                <p class="text-xs text-gray-600 leading-relaxed font-medium mb-4">
                                    {{ story.summary }}
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between text-xs font-bold text-gray-500 mb-4 pt-3 border-t border-teal-200/60">
                                    <span>⏱️ {{ story.readTime }}</span>
                                    <span class="text-amber-700 bg-amber-100 px-2 py-0.5 rounded-lg border border-amber-300 font-extrabold">
                                        🪙 +{{ story.xpReward }} XP
                                    </span>
                                </div>

                                <button
                                    v-if="isStoryUnlocked(story.id)"
                                    @click="openStoryReader(story)"
                                    class="w-full py-3 bg-teal-500 hover:bg-teal-600 text-white font-extrabold text-xs rounded-2xl border-2 border-teal-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all text-center cursor-pointer flex items-center justify-center gap-1.5"
                                >
                                    <span>Baca Dongeng 📖</span>
                                </button>
                                <button
                                    v-else
                                    @click="unlockStoryWithCoins(story)"
                                    class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs rounded-2xl border-2 border-orange-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all text-center cursor-pointer flex items-center justify-center gap-1.5"
                                >
                                    <span>🔓 Beli Cerita ({{ story.cost }} 🪙)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 4. MOBILE FIXED BOTTOM NAVBAR (FLOATING GAME UI BAR)    -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <nav class="lg:hidden fixed bottom-3 left-3 right-3 z-50 bg-white/95 backdrop-blur-md rounded-3xl border-4 border-amber-300 shadow-2xl p-1.5 flex items-center justify-around gap-1">
            <button
                @click="activeTab = 'map'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[10px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'map' ? 'bg-[#FF4D30] text-white shadow-md' : 'text-gray-600 hover:bg-orange-100'"
            >
                <span class="text-lg leading-none">🗺️</span>
                <span class="mt-0.5">Peta</span>
            </button>

            <button
                @click="activeTab = 'nulis'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[10px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'nulis' ? 'bg-[#FF4D30] text-white shadow-md' : 'text-gray-600 hover:bg-orange-100'"
            >
                <span class="text-lg leading-none">✏️</span>
                <span class="mt-0.5">Coretan</span>
            </button>

            <button
                @click="activeTab = 'ketik'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[10px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'ketik' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-600 hover:bg-purple-100'"
            >
                <span class="text-lg leading-none">⌨️</span>
                <span class="mt-0.5">Ketik</span>
            </button>

            <button
                @click="activeTab = 'kamus'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[10px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'kamus' ? 'bg-amber-500 text-white shadow-md' : 'text-gray-600 hover:bg-amber-100'"
            >
                <span class="text-lg leading-none">📚</span>
                <span class="mt-0.5">Kamus</span>
            </button>

            <button
                @click="activeTab = 'cerita'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[10px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'cerita' ? 'bg-teal-500 text-white shadow-md' : 'text-gray-600 hover:bg-teal-100'"
            >
                <span class="text-lg leading-none">📖</span>
                <span class="mt-0.5">Dongeng</span>
            </button>
        </nav>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 5. CANVAS MODAL POPUP                                   -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <CanvasModal
            :show="showCanvasModal"
            :character="selectedCanvasChar"
            :latin="selectedCanvasLatin"
            @close="showCanvasModal = false"
            @xpGained="handleXpGained"
            @gamificationUpdated="handleGamificationUpdated"
        />

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 5B. STROKE GUIDE MODAL POPUP (ANIMASI BENTUK 77)        -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <StrokeGuideModal
            :show="showStrokeGuideModal"
            @close="showStrokeGuideModal = false"
            @startPractice="handleStartPracticeFromGuide"
        />

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 6. QUIZ MODAL POPUP (ALUR SOAL & GAMIFIKASI)            -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <QuizModal
            :show="showQuizModal"
            :levelId="quizLevelId"
            :levelTitle="quizLevelTitle"
            :userCoins="userCoins"
            @close="showQuizModal = false"
            @levelCompleted="handleLevelCompleted"
            @xpGained="handleXpGained"
            @coinsUpdated="handleCoinsUpdated"
        />

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 7. STORY READER MODAL (DONGENG SUNDA LENGKAP)          -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <StoryReaderModal
            :show="showStoryModal"
            :story="selectedStory"
            @close="showStoryModal = false"
            @storyCompleted="handleStoryCompleted"
        />

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 8. FLOATING REWARD & LEVEL-UP TOAST NOTIFICATION       -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <Transition name="fade">
            <div
                v-if="toastMessage"
                class="fixed top-20 right-4 sm:right-8 z-50 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white font-black text-xs sm:text-sm px-4 py-3 rounded-2xl shadow-2xl border-2 border-white flex items-center gap-2.5 animate-bounce"
            >
                <span class="text-xl">🎉</span>
                <span>{{ toastMessage }}</span>
            </div>
        </Transition>

    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
