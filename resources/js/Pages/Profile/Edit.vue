<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
    userStats: {
        type: Object,
        default: () => null,
    },
});

const pageUser = usePage().props.auth.user;

const stats = computed(() => {
    return {
        name: props.userStats?.name || pageUser?.name || 'Petualang Aksara',
        email: props.userStats?.email || pageUser?.email || '',
        level: props.userStats?.level || pageUser?.level || 1,
        xp: props.userStats?.xp ?? pageUser?.xp ?? 0,
        coins: props.userStats?.coins ?? pageUser?.coins ?? 50,
        streak: props.userStats?.streak ?? pageUser?.streak ?? 0,
        max_streak: props.userStats?.max_streak ?? pageUser?.max_streak ?? 0,
        current_level_xp: (props.userStats?.xp ?? pageUser?.xp ?? 0) % 100,
        xp_percent: Math.min(100, Math.round(((props.userStats?.xp ?? pageUser?.xp ?? 0) % 100))),
    };
});
</script>

<template>
    <Head title="Profil Petualang - Aksantara" />

    <div class="min-h-screen bg-[#FFFDF5] text-gray-800 font-sans selection:bg-[#FF4D30] selection:text-white pb-20">
        
        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 1. TOP BAR NAVBAR                                       -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b-4 border-orange-200 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex items-center justify-between gap-4">
                    
                    <!-- Left: Brand & Back to Dashboard -->
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('dashboard')"
                            class="px-3.5 py-2 bg-orange-50 hover:bg-orange-100 text-[#FF4D30] font-black text-xs sm:text-sm rounded-2xl border-2 border-orange-200 shadow-xs transition flex items-center gap-2 cursor-pointer active:scale-95"
                        >
                            <span>←</span>
                            <span class="hidden xs:inline">Kembali ke Petualangan</span>
                            <span class="xs:hidden">Kembali</span>
                        </Link>

                        <div class="hidden md:flex items-center gap-2">
                            <span class="text-2xl">ᮞ</span>
                            <span class="text-lg font-black text-gray-900">Aksantara Sunda</span>
                        </div>
                    </div>

                    <!-- Right: Quick Stat Badges & Logout -->
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <!-- Koin Budaya -->
                        <div class="flex items-center gap-1 bg-amber-100 px-3 py-1.5 rounded-2xl border-2 border-amber-300 text-amber-800 font-extrabold text-xs sm:text-sm shadow-xs">
                            <span class="text-base sm:text-lg">🪙</span>
                            <span>{{ stats.coins }}</span>
                        </div>

                        <!-- Streak Hari -->
                        <div class="flex items-center gap-1 bg-orange-100 px-3 py-1.5 rounded-2xl border-2 border-orange-300 text-orange-700 font-extrabold text-xs sm:text-sm shadow-xs">
                            <span class="text-base sm:text-lg animate-pulse">🔥</span>
                            <span>{{ stats.streak }} Hari</span>
                        </div>

                        <!-- Logout Button -->
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="p-2 sm:px-3 sm:py-1.5 text-xs font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-xl border-2 border-red-200 transition cursor-pointer flex items-center gap-1"
                            title="Keluar"
                        >
                            <span>🚪</span>
                            <span class="hidden sm:inline">Keluar</span>
                        </Link>
                    </div>

                </div>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 2. MAIN CONTAINER                                       -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-8 space-y-6">

            <!-- HERO PROFILE CARD: AVATAR, BADGE & GAME STATS -->
            <div class="bg-gradient-to-r from-amber-400 via-orange-500 to-[#FF4D30] rounded-3xl p-6 sm:p-8 text-white shadow-2xl border-4 border-amber-300 relative overflow-hidden">
                <!-- Background Accent Graphic -->
                <div class="absolute -right-8 -bottom-8 text-9xl opacity-15 select-none pointer-events-none">ᮞ</div>
                <div class="absolute top-4 right-6 text-3xl opacity-20 select-none pointer-events-none">✨</div>

                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                    <!-- Big Avatar -->
                    <div class="relative shrink-0">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-white/20 backdrop-blur-md p-1 shadow-2xl border-4 border-white/60 flex items-center justify-center text-5xl sm:text-6xl hover:rotate-6 transition-transform">
                            🦸‍♂️
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-yellow-400 text-yellow-950 px-2 py-0.5 rounded-full text-[10px] font-black border-2 border-white shadow-md">
                            ★ Lv. {{ stats.level }}
                        </div>
                    </div>

                    <!-- User Identity -->
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-black mb-2 border border-white/30 shadow-xs">
                            <span>⭐</span>
                            <span>Level {{ stats.level }} • Jawara Aksara Sunda</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight drop-shadow-sm leading-tight">
                            {{ stats.name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-amber-100 font-medium mt-0.5">
                            {{ stats.email }}
                        </p>
                    </div>
                </div>

                <!-- 3 GAMIFIED STATS TILES -->
                <div class="relative z-10 grid grid-cols-1 sm:grid-cols-3 gap-3.5 mt-6 pt-6 border-t-2 border-white/20">
                    <!-- Stat 1: XP -->
                    <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/25 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between text-xs font-black text-amber-100 mb-1">
                            <span>🧪 Total XP</span>
                            <span class="text-white font-mono">{{ stats.current_level_xp }}/100</span>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-white">
                            {{ stats.xp }} XP
                        </div>
                        <!-- XP Progress bar -->
                        <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2 border border-white/30">
                            <div
                                class="h-full bg-gradient-to-r from-yellow-300 to-amber-200 rounded-full transition-all duration-500"
                                :style="{ width: stats.xp_percent + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Stat 2: Koin Budaya -->
                    <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/25 shadow-xs flex flex-col justify-between">
                        <div class="text-xs font-black text-amber-100 mb-1">
                            🪙 Koin Budaya
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-white">
                            {{ stats.coins }} Koin
                        </div>
                        <p class="text-[10px] text-amber-100/90 font-medium mt-1">
                            Dipakai untuk hint kuis & buka dongeng
                        </p>
                    </div>

                    <!-- Stat 3: Streak Belajar -->
                    <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/25 shadow-xs flex flex-col justify-between">
                        <div class="text-xs font-black text-amber-100 mb-1">
                            🔥 Runtutan Belajar
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-white">
                            {{ stats.streak }} Hari
                        </div>
                        <p class="text-[10px] text-amber-100/90 font-bold mt-1">
                            🏆 Rekor: {{ stats.max_streak }} Hari Beruntun
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 1: INFORMASI PROFIL FORM -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border-4 border-amber-300 shadow-xl">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <!-- CARD 2: GANTI KATA SANDI FORM -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border-4 border-orange-300 shadow-xl">
                <UpdatePasswordForm />
            </div>

            <!-- CARD 3: HAPUS AKUN (ZONA BAHAYA) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border-4 border-red-200 shadow-xl">
                <DeleteUserForm />
            </div>

        </main>

    </div>
</template>
