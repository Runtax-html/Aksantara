<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    streakData: {
        type: Object,
        default: () => ({
            currentStreak: 0,
            maxStreak: 0,
            hasCheckedInToday: false,
            weeklyTracker: [],
        }),
    },
});

const emit = defineEmits(['close', 'checkinSuccess']);

const isSubmitting = ref(false);
const errorMsg = ref('');

const currentStreak = computed(() => props.streakData?.currentStreak ?? 0);
const maxStreak = computed(() => Math.max(props.streakData?.maxStreak ?? 0, currentStreak.value));
const hasCheckedInToday = computed(() => props.streakData?.hasCheckedInToday ?? false);
const weeklyTracker = computed(() => props.streakData?.weeklyTracker ?? []);

async function handleManualCheckin() {
    if (hasCheckedInToday.value || isSubmitting.value) return;

    isSubmitting.value = true;
    errorMsg.value = '';

    try {
        const res = await axios.post('/api/gamification/check-in');
        if (res.data.success) {
            emit('checkinSuccess', res.data);
        } else {
            errorMsg.value = res.data.message || 'Gagal melakukan check-in.';
        }
    } catch (err) {
        console.error('Error saat check-in manual:', err);
        errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan saat check-in.';
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <Transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @click.self="$emit('close')"
        >
            <div
                class="bg-white w-full max-w-lg rounded-3xl border-4 border-amber-300 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200"
            >
                <!-- MODAL HEADER -->
                <div class="relative bg-gradient-to-r from-amber-400 via-orange-500 to-[#FF4D30] p-6 text-white text-center">
                    <!-- Close button -->
                    <button
                        @click="$emit('close')"
                        class="absolute top-4 right-4 w-9 h-9 bg-black/20 hover:bg-black/30 text-white rounded-full flex items-center justify-center font-bold text-lg transition cursor-pointer"
                        title="Tutup"
                    >
                        ✕
                    </button>

                    <!-- Top Badge: Rekor Terlama -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-black text-white mb-2 border border-white/30 shadow-xs">
                        <span>🏆</span>
                        <span>Rekor Terlama: {{ maxStreak }} Hari</span>
                    </div>

                    <!-- Title & Description -->
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight drop-shadow-sm flex items-center justify-center gap-2">
                        <span>🔥</span>
                        <span>Runtutan Belajar</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-amber-100 font-medium mt-1 max-w-xs mx-auto">
                        Check-in harian untuk menjaga ritme belajar setiap hari.
                    </p>
                </div>

                <!-- MODAL BODY -->
                <div class="p-6 space-y-6">

                    <!-- STATUS UTAMA: FLAME & STREAK INFO -->
                    <div class="bg-gradient-to-b from-orange-50 to-amber-50/60 p-5 rounded-2xl border-2 border-orange-200 text-center relative overflow-hidden shadow-xs">
                        <div class="flex flex-col items-center justify-center">
                            <!-- Big Flame Icon with Glow -->
                            <div class="relative mb-2">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-tr from-amber-300 via-orange-400 to-[#FF4D30] flex items-center justify-center text-4xl sm:text-5xl shadow-lg ring-4 ring-orange-200 animate-pulse">
                                    🔥
                                </div>
                                <div
                                    v-if="hasCheckedInToday"
                                    class="absolute -bottom-1 -right-1 bg-emerald-500 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-black border-2 border-white shadow"
                                    title="Sudah Check-in"
                                >
                                    ✓
                                </div>
                            </div>

                            <!-- Streak Count -->
                            <h3 class="text-2xl sm:text-3xl font-black text-gray-900 mt-1">
                                {{ currentStreak }} Hari Beruntun
                            </h3>

                            <!-- Check-in Status Badge -->
                            <div class="mt-1.5">
                                <span
                                    v-if="hasCheckedInToday"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-extrabold rounded-full border border-emerald-300"
                                >
                                    <span>✓</span>
                                    <span>Sudah Check-in Hari Ini</span>
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-900 text-xs font-extrabold rounded-full border border-amber-300"
                                >
                                    <span>⏳</span>
                                    <span>Belum Check-in Hari Ini</span>
                                </span>
                            </div>

                            <p class="text-xs text-gray-500 font-semibold mt-2 max-w-sm">
                                Belajar minimal 1 aktivitas atau tekan tombol check-in di bawah agar runtutan apimu tidak padam!
                            </p>
                        </div>
                    </div>

                    <!-- 7-DAY TRACKER: JUM, SAB, MIN, SEN, SEL, RAB, KAM -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs font-extrabold text-gray-700 px-1">
                            <span>📅 Tracker 7 Hari Terakhir</span>
                            <span class="text-[11px] text-gray-500 font-medium">Ritme Mingguan</span>
                        </div>

                        <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                            <div
                                v-for="(day, idx) in weeklyTracker"
                                :key="idx"
                                class="flex flex-col items-center justify-center p-2 rounded-2xl border-2 transition-all"
                                :class="[
                                    day.isToday ? 'bg-orange-50 border-[#FF4D30] shadow-sm' : 'bg-slate-50 border-slate-200',
                                    day.isCheckedIn ? 'border-amber-400 bg-amber-50/50' : ''
                                ]"
                            >
                                <!-- Day Name (Jum, Sab, Min, etc) -->
                                <span
                                    class="text-[11px] sm:text-xs font-black mb-1"
                                    :class="day.isToday ? 'text-[#FF4D30]' : 'text-gray-600'"
                                >
                                    {{ day.dayName }}
                                </span>

                                <!-- Fire Icon / Circle -->
                                <div
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-base sm:text-lg transition-transform"
                                    :class="[
                                        day.isCheckedIn
                                            ? 'bg-gradient-to-tr from-amber-400 via-orange-500 to-[#FF4D30] text-white shadow-md ring-2 ring-orange-200 scale-105'
                                            : 'bg-slate-200/80 text-slate-400 grayscale opacity-40'
                                    ]"
                                    :title="day.isCheckedIn ? `${day.dayName}: Aktif Check-in` : `${day.dayName}: Belum`"
                                >
                                    🔥
                                </div>

                                <!-- Subtext for Today -->
                                <span
                                    v-if="day.isToday"
                                    class="text-[9px] font-black text-[#FF4D30] mt-1 uppercase tracking-tight"
                                >
                                    Hari Ini
                                </span>
                                <span
                                    v-else
                                    class="text-[9px] font-bold text-gray-400 mt-1"
                                >
                                    {{ day.dayNumber }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ERROR MESSAGE (IF ANY) -->
                    <div v-if="errorMsg" class="p-3 bg-red-50 border border-red-200 text-red-600 text-xs font-bold rounded-xl text-center">
                        ⚠️ {{ errorMsg }}
                    </div>

                    <!-- TOMBOL AKSI CHECK-IN -->
                    <div>
                        <button
                            v-if="!hasCheckedInToday"
                            @click="handleManualCheckin"
                            :disabled="isSubmitting"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-orange-500 to-[#FF4D30] hover:from-orange-600 hover:to-[#e03e22] text-white text-sm sm:text-base font-black rounded-2xl shadow-lg border-b-4 border-orange-800 active:translate-y-0.5 active:border-b-2 active:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span class="text-xl">🔥</span>
                            <span>{{ isSubmitting ? 'Mencatat Check-in...' : 'Check-in Sekarang (+10 XP & +5 🪙)' }}</span>
                        </button>

                        <div
                            v-else
                            class="w-full py-3 px-4 bg-emerald-50 border-2 border-emerald-300 text-emerald-800 font-extrabold text-sm rounded-2xl flex items-center justify-center gap-2 text-center"
                        >
                            <span class="text-base">✓</span>
                            <span>Check-in Hari Ini Berhasil! Ritme Belajarmu Aman 🔥</span>
                        </div>
                    </div>

                </div>

                <!-- MODAL FOOTER -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 text-center">
                    <p class="text-[11px] text-gray-500 font-medium">
                        💡 Tips: Check-in juga otomatis tercatat setiap kali kamu menyelesaikan kuis, latihan nulis, atau membaca dongeng!
                    </p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
