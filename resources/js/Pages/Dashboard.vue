<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import CanvasTracing from '@/Components/CanvasTracing.vue';
import CanvasModal from '@/Components/CanvasModal.vue';
import VirtualKeyboard from '@/Components/VirtualKeyboard.vue';
import SpeechPronunciation from '@/Components/SpeechPronunciation.vue';

const props = defineProps({
    userProgress: {
        type: Array,
        default: () => [],
    }
});

// State Tab Navigasi Bebas
const activeTab = ref('map'); // 'map' | 'nulis' | 'ketik' | 'cerita'

// CanvasModal State
const showCanvasModal = ref(false);
const selectedCanvasChar = ref('ᮊ');
const selectedCanvasLatin = ref('Ka');
const userXP = ref(145);

function openModalForChar(item) {
    selectedCanvasChar.value = item.char;
    selectedCanvasLatin.value = item.latin;
    showCanvasModal.value = true;
}

function handleXpGained(amount) {
    userXP.value += amount;
}

// Misi harian state
const missions = ref([
    { id: 1, title: 'Belajar 3 Karakter Aksara Sunda', reward: 50, completed: true },
    { id: 2, title: 'Latihan Nulis 1 Karakter di Canvas', reward: 30, completed: false },
    { id: 3, title: 'Baca 1 Cerita Rakyat Sunda', reward: 100, completed: false },
]);

function toggleMission(mission) {
    mission.completed = !mission.completed;
}

const completedMissionsCount = computed(() => {
    return missions.value.filter(m => m.completed).length;
});

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

// Level Nodes Data (Khusus Aksara Sunda)
const defaultLevels = [
    {
        id: 1,
        title: 'Level 1: Swara Aksara Sunda',
        subtitle: 'a, i, u, e, o, eup, eu',
        status: 'completed',
        stars: 3,
        icon: '⭐',
        color: 'from-emerald-400 to-green-500',
    },
    {
        id: 2,
        title: 'Level 2: Ngalagena Dasar 1',
        subtitle: 'ka, ga, nga, ca, ja, nya',
        status: 'active',
        stars: 0,
        icon: '🚀',
        color: 'from-[#FF4D30] to-orange-500',
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
        title: 'Level 4: Rarangken Vokal',
        subtitle: 'Panghulu, Pamepet, Paneuleung',
        status: 'locked',
        stars: 0,
        icon: '🔒',
        color: 'from-slate-300 to-slate-400',
    },
    {
        id: 5,
        title: 'Level 5: Angka Sunda',
        subtitle: 'Angka 1 - 10',
        status: 'locked',
        stars: 0,
        icon: '🔒',
        color: 'from-slate-300 to-slate-400',
    },
    {
        id: 6,
        title: 'Level 6: Ujian Jawara Aksara',
        subtitle: 'Bos Final Aksara Sunda',
        status: 'locked',
        stars: 0,
        icon: '👑🔒',
        color: 'from-amber-400 to-yellow-500',
    },
];

const levels = computed(() => {
    if (props.userProgress && props.userProgress.length > 0) {
        return defaultLevels.map(level => {
            const prog = props.userProgress.find(p => p.character_id === level.id);
            if (prog) {
                return {
                    ...level,
                    status: prog.is_mastered ? 'completed' : (prog.is_learned ? 'active' : 'locked'),
                };
            }
            return level;
        });
    }
    return defaultLevels;
});

// Cerita Rakyat Sunda Data
const sundaFolktales = [
    {
        id: 1,
        title: 'Si Kabayan Ngala Tutut',
        region: 'Jawa Barat',
        readTime: '3 Menit',
        difficulty: 'Pemula',
        unlocked: true,
        icon: '👨‍🌾',
        summary: 'Dongeng lucu Si Kabayan anu disuruh ku mitoha ngala tutut di sawah.',
        xpReward: 50
    },
    {
        id: 2,
        title: 'Lutung Kasarung',
        region: 'Jawa Barat',
        readTime: '5 Menit',
        difficulty: 'Menengah',
        unlocked: true,
        icon: '🐒',
        summary: 'Kisah Guruminda nu menjelma jadi lutung hideung pikeun nulungan Purbasari.',
        xpReward: 75
    },
    {
        id: 3,
        title: 'Sangkuriang & Tangkuban Parahu',
        region: 'Jawa Barat',
        readTime: '7 Menit',
        difficulty: 'Menengah',
        unlocked: false,
        icon: '🏔️',
        summary: 'Sasakala gunung Tangkuban Parahu jeung kapal laut nu ditendang ku Sangkuriang.',
        xpReward: 100
    },
    {
        id: 4,
        title: 'Ciung Wanara',
        region: 'Jawa Barat',
        readTime: '6 Menit',
        difficulty: 'Mahir',
        unlocked: false,
        icon: '🐓',
        summary: 'Kisah pahlawan karajaan Galuh nu gaduh hayam aduan sakti.',
        xpReward: 100
    }
];

function startLevel(level) {
    if (level.status !== 'locked') {
        openModalForChar({ char: 'ᮊ', latin: 'Ka' });
    }
}
</script>

<template>
    <Head title="Petualangan Aksara Sunda - Aksantara" />

    <div class="min-h-screen bg-amber-50 text-gray-800 font-sans selection:bg-[#FF4D30] selection:text-white pb-28 sm:pb-32">
        
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
                                    ⭐ Level {{ $page.props.auth.user.level || 3 }} - Jawara Sunda
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
                                        <span>{{ userXP }}/200</span>
                                    </div>
                                    <div class="w-full h-2 sm:h-2.5 bg-purple-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full" style="width: 72%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Streak Hari -->
                            <div class="flex items-center gap-1 bg-orange-100 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-2xl border-2 border-orange-300 text-orange-700 font-extrabold text-xs sm:text-sm shadow-sm shrink-0">
                                <span class="text-base sm:text-xl animate-pulse">🔥</span>
                                <span>{{ $page.props.auth.user.streak || 7 }} <span class="hidden sm:inline">Hari</span></span>
                            </div>

                            <!-- Koin Budaya -->
                            <div class="flex items-center gap-1 bg-amber-100 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-2xl border-2 border-amber-300 text-amber-800 font-extrabold text-xs sm:text-sm shadow-sm shrink-0">
                                <span class="text-base sm:text-xl">🪙</span>
                                <span>250</span>
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
        <!-- 2. DESKTOP TAB SWITCHER (NATIVE GAME FEEL TABS)        -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 hidden lg:block">
            <div class="bg-white p-2.5 rounded-3xl border-4 border-orange-200 shadow-md flex items-center justify-between gap-2">
                <button
                    @click="activeTab = 'map'"
                    class="flex-1 py-3 px-4 rounded-2xl font-extrabold text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-2"
                    :class="activeTab === 'map' ? 'bg-[#FF4D30] text-white border-orange-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-orange-50/60 text-gray-700 border-orange-200 hover:bg-orange-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-xl">🗺️</span>
                    <span>Peta Petualangan</span>
                </button>

                <button
                    @click="activeTab = 'nulis'"
                    class="flex-1 py-3 px-4 rounded-2xl font-extrabold text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-2"
                    :class="activeTab === 'nulis' ? 'bg-[#FF4D30] text-white border-orange-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-orange-50/60 text-gray-700 border-orange-200 hover:bg-orange-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-xl">✏️</span>
                    <span>Modul Canvas Nulis</span>
                </button>

                <button
                    @click="activeTab = 'ketik'"
                    class="flex-1 py-3 px-4 rounded-2xl font-extrabold text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-2"
                    :class="activeTab === 'ketik' ? 'bg-purple-600 text-white border-purple-800 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-purple-50/60 text-purple-900 border-purple-200 hover:bg-purple-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-xl">⌨️</span>
                    <span>Ketik & Audio Suara</span>
                </button>

                <button
                    @click="activeTab = 'cerita'"
                    class="flex-1 py-3 px-4 rounded-2xl font-extrabold text-sm border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center justify-center gap-2"
                    :class="activeTab === 'cerita' ? 'bg-teal-500 text-white border-teal-700 shadow-[0_3px_0_0_rgba(0,0,0,0.15)]' : 'bg-teal-50/60 text-teal-900 border-teal-200 hover:bg-teal-100 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-xl">📖</span>
                    <span>Cerita Rakyat Sunda</span>
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
                                    🌟 1 / 6 Selesai
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
                                                Selesai ⭐⭐⭐
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
                                                Latihan Nulis ✏️
                                            </button>
                                        </div>
                                        <div v-else-if="level.status === 'completed'" class="mt-3">
                                            <button
                                                @click="startLevel(level)"
                                                class="w-full py-1.5 bg-emerald-50 text-emerald-700 border-2 border-emerald-200 border-b-4 text-xs font-extrabold rounded-xl hover:bg-emerald-100 active:translate-y-0.5 transition-all cursor-pointer"
                                            >
                                                Ulangi Level 🔄
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: MISI HARIAN + LEADERBOARD (4 COLS) -->
                    <div class="lg:col-span-4 space-y-6">

                        <!-- WIDGET 1: MISI HARIAN -->
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
                                    @click="toggleMission(mission)"
                                    class="flex items-center justify-between p-3 rounded-2xl border-2 transition cursor-pointer select-none"
                                    :class="mission.completed ? 'bg-emerald-50/90 border-emerald-300 shadow-sm' : 'bg-orange-50/50 border-orange-200 hover:border-amber-400'"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-6 h-6 rounded-lg border-2 flex items-center justify-center text-xs font-black transition"
                                            :class="mission.completed ? 'bg-emerald-500 border-emerald-600 text-white shadow-sm' : 'border-orange-300 bg-white'"
                                        >
                                            {{ mission.completed ? '✓' : '' }}
                                        </div>
                                        <span class="text-xs font-bold" :class="mission.completed ? 'line-through text-gray-400' : 'text-gray-800'">
                                            {{ mission.title }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-amber-700 bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-lg flex items-center gap-1 shrink-0">
                                        🪙 +{{ mission.reward }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3.5 pt-3 border-t-2 border-dashed border-amber-200 flex items-center justify-between text-xs text-gray-600 font-bold">
                                <span>Bonus Semua Misi:</span>
                                <span class="text-[#FF4D30] font-extrabold">🎁 Box Hadiah Rahasia!</span>
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
                            Klik salah satu kartu huruf Aksara Sunda di bawah ini untuk membuka modal panduan & canvas latihan!
                        </p>
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

                    <!-- Interactive Embedded Canvas -->
                    <div class="pt-6 border-t-2 border-dashed border-orange-200">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-extrabold text-gray-800">Canvas Langsung</h3>
                        </div>
                        <CanvasTracing initialCharacter="ᮊ" latin="Ka" />
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

                <!-- Speech Pronunciation Standalone -->
                <SpeechPronunciation />
            </div>

            <!-- =================================================== -->
            <!-- TAB 4: CERITA RAKYAT SUNDA (FULL-WIDTH)             -->
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
                            :class="story.unlocked ? 'border-teal-300 shadow-md hover:scale-[1.02]' : 'border-gray-300 opacity-75 bg-gray-50'"
                        >
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-4xl p-2 bg-white rounded-2xl border-2 border-teal-200 shadow-sm">
                                        {{ story.icon }}
                                    </span>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-extrabold border"
                                        :class="story.unlocked ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-gray-200 text-gray-600 border-gray-300'"
                                    >
                                        {{ story.unlocked ? '🗝️ Terbuka' : '🔒 Terkunci' }}
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

                                <Link
                                    v-if="story.unlocked"
                                    :href="`/folktales/${story.id}`"
                                    class="block w-full py-3 bg-teal-500 hover:bg-teal-600 text-white font-extrabold text-xs rounded-2xl border-2 border-teal-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all text-center cursor-pointer"
                                >
                                    Baca Cerita 📖
                                </Link>
                                <button
                                    v-else
                                    disabled
                                    class="w-full py-3 bg-gray-200 text-gray-500 font-extrabold text-xs rounded-2xl border-2 border-gray-300 cursor-not-allowed text-center"
                                >
                                    Terkunci (Level Lebih Tinggi) 🔒
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
        <nav class="lg:hidden fixed bottom-3 left-4 right-4 z-50 bg-white/95 backdrop-blur-md rounded-3xl border-4 border-orange-300 shadow-2xl p-1.5 flex items-center justify-around">
            <button
                @click="activeTab = 'map'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[11px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'map' ? 'bg-[#FF4D30] text-white shadow-md' : 'text-gray-600 hover:bg-orange-100'"
            >
                <span class="text-xl leading-none">🗺️</span>
                <span class="mt-1">Peta</span>
            </button>

            <button
                @click="activeTab = 'nulis'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[11px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'nulis' ? 'bg-[#FF4D30] text-white shadow-md' : 'text-gray-600 hover:bg-orange-100'"
            >
                <span class="text-xl leading-none">✏️</span>
                <span class="mt-1">Nulis</span>
            </button>

            <button
                @click="activeTab = 'ketik'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[11px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'ketik' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-600 hover:bg-purple-100'"
            >
                <span class="text-xl leading-none">⌨️</span>
                <span class="mt-1">Ketik</span>
            </button>

            <button
                @click="activeTab = 'cerita'"
                class="flex-1 py-2 px-1 rounded-2xl font-extrabold text-[11px] flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                :class="activeTab === 'cerita' ? 'bg-teal-500 text-white shadow-md' : 'text-gray-600 hover:bg-teal-100'"
            >
                <span class="text-xl leading-none">📖</span>
                <span class="mt-1">Cerita</span>
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
        />

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
