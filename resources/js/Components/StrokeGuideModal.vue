<script setup>
import { ref, watch, nextTick } from 'vue';
import { playSundaAudio } from '@/Utils/audioPlayer';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['close', 'startPractice']);

const isAnimating = ref(false);
const animationKey = ref(0);
const isSpeaking = ref(false);
let animationTimer = null;

function startAnimation() {
    if (animationTimer) {
        clearTimeout(animationTimer);
        animationTimer = null;
    }
    isAnimating.value = false;
    animationKey.value++;
    nextTick(() => {
        isAnimating.value = true;
        animationTimer = setTimeout(() => {
            isAnimating.value = false;
        }, 3200);
    });
}

function replayAnimation() {
    startAnimation();
}

function speakKa() {
    isSpeaking.value = true;
    playSundaAudio('Ka', {
        onStart: () => {
            isSpeaking.value = true;
        },
        onEnd: () => {
            isSpeaking.value = false;
        },
        onError: () => {
            isSpeaking.value = false;
        },
    });
}

watch(() => props.show, (newVal) => {
    if (newVal) {
        nextTick(() => {
            startAnimation();
        });
    } else {
        if (animationTimer) {
            clearTimeout(animationTimer);
            animationTimer = null;
        }
        isAnimating.value = false;
    }
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
        <!-- Main Modal Container -->
        <div class="bg-white rounded-3xl p-5 sm:p-7 max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl border-4 border-orange-300 relative animate-scale-up text-gray-800">
            
            <!-- Game-style Close Button 'X' -->
            <button
                @click="emit('close')"
                class="absolute top-4 right-4 w-9 h-9 rounded-full border-2 border-orange-300 bg-orange-100 hover:bg-orange-200 flex items-center justify-center font-black text-orange-600 active:translate-y-0.5 transition cursor-pointer z-10"
                title="Tutup"
            >
                ✕
            </button>

            <!-- Header -->
            <div class="text-center mb-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 text-[#FF4D30] rounded-full text-xs font-black border border-orange-200 mb-1.5 shadow-sm">
                    <span>🎬 Panduan Menulis Aksara</span>
                </div>
                <h2 class="text-2xl font-black text-gray-900 flex items-center justify-center gap-2">
                    <span>Huruf "Ka"</span>
                    <span class="text-3xl text-[#FF4D30]">ᮊ</span>
                    <span class="text-sm font-extrabold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-lg border border-amber-300">(Bentuk 77)</span>
                </h2>
                <p class="text-xs text-gray-600 font-semibold mt-1">
                    Lihat contoh arah dan urutan goresan untuk membentuk huruf Ka.
                </p>
            </div>

            <!-- Visual Animation Stage Box -->
            <div class="flex flex-col items-center">
                <div class="relative w-full max-w-xs aspect-square bg-orange-50/70 rounded-3xl border-4 border-orange-200 overflow-hidden shadow-inner flex items-center justify-center p-2">
                    
                    <!-- Single Unified SVG: Background Shadow & Animated Strokes 100% Presisi -->
                    <div :key="animationKey" class="w-full h-full flex items-center justify-center pointer-events-none">
                        <svg class="w-full h-full" viewBox="0 0 300 300" fill="none">
                            <!-- 1. Background Shadow / Bayangan Huruf (Krem/Abu-abu Muda Presisi) -->
                            <!-- Goresan 1 (7 Kiri) - Bayangan Luar -->
                            <path
                                d="M 70 105 L 125 105 L 92 210"
                                stroke="#CBD5E1"
                                stroke-width="15"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="opacity-40"
                            />
                            <!-- Goresan 1 (7 Kiri) - Jalur Krem Dasar -->
                            <path
                                d="M 70 105 L 125 105 L 92 210"
                                stroke="#FED7AA"
                                stroke-width="13"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <!-- Goresan 1 (7 Kiri) - Garis Putus-putus Panduan -->
                            <path
                                d="M 70 105 L 125 105 L 92 210"
                                stroke="#F97316"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-dasharray="4 4"
                                class="opacity-50"
                            />

                            <!-- Goresan 2 (7 Kanan) - Bayangan Luar (Jarak Lebar 50px dari 7 Kiri) -->
                            <path
                                d="M 175 105 L 230 105 L 197 210"
                                stroke="#CBD5E1"
                                stroke-width="15"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="opacity-40"
                            />
                            <!-- Goresan 2 (7 Kanan) - Jalur Krem Dasar -->
                            <path
                                d="M 175 105 L 230 105 L 197 210"
                                stroke="#FED7AA"
                                stroke-width="13"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <!-- Goresan 2 (7 Kanan) - Garis Putus-putus Panduan -->
                            <path
                                d="M 175 105 L 230 105 L 197 210"
                                stroke="#F97316"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-dasharray="4 4"
                                class="opacity-50"
                            />

                            <!-- 2. Foreground Animated Drawing Strokes (Oranye Cerah 13px) -->
                            <path
                                d="M 70 105 L 125 105 L 92 210"
                                stroke="#FF4D30"
                                stroke-width="13"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="ka-stroke-1"
                            />
                            <path
                                d="M 175 105 L 230 105 L 197 210"
                                stroke="#FF4D30"
                                stroke-width="13"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="ka-stroke-2"
                            />

                            <!-- 3. Step Numbers & Direction Arrows -->
                            <!-- Goresan 1 Marker -->
                            <g class="transition-opacity" :class="isAnimating ? 'animate-bounce' : 'opacity-90'">
                                <circle cx="70" cy="86" r="10" fill="#FF4D30" stroke="#FFFFFF" stroke-width="2" />
                                <text x="70" y="90" text-anchor="middle" font-size="11" font-weight="900" fill="#FFFFFF">1</text>
                                <path d="M 84 86 L 94 86 M 91 82 L 95 86 L 91 90" stroke="#FF4D30" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            </g>

                            <!-- Goresan 2 Marker -->
                            <g class="transition-opacity" :class="isAnimating ? 'animate-bounce' : 'opacity-90'">
                                <circle cx="175" cy="86" r="10" fill="#FF4D30" stroke="#FFFFFF" stroke-width="2" />
                                <text x="175" y="90" text-anchor="middle" font-size="11" font-weight="900" fill="#FFFFFF">2</text>
                                <path d="M 189 86 L 199 86 M 196 82 L 200 86 L 196 90" stroke="#FF4D30" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                        </svg>
                    </div>

                    <!-- Label Bentuk 77 -->
                    <div class="absolute bottom-2.5 left-1/2 -translate-x-1/2 px-3 py-1 bg-amber-100/95 text-amber-900 rounded-full border border-amber-300 text-[11px] font-black shadow-sm flex items-center gap-1.5">
                        <span>✍️</span>
                        <span>Dua Angka 7 Berdampingan</span>
                    </div>
                </div>

                <!-- Badge Urutan Goresan: 1 ➔ 2 -->
                <div class="mt-4 inline-flex items-center gap-2 px-4 py-1.5 bg-orange-100 text-orange-700 font-extrabold text-xs rounded-full border-2 border-orange-300 shadow-sm">
                    <span class="text-sm">📌</span>
                    <span>Urutan Goresan: 1 ➔ 2</span>
                </div>

                <p class="text-xs text-gray-500 font-bold mt-2 text-center max-w-xs">
                    Tarik garis mendatar lalu miring ke kiri untuk "7" pertama, lalu ulangi untuk "7" kedua!
                </p>

                <!-- Action Buttons: Audio & Ulangi Animasi -->
                <div class="flex items-center gap-3 w-full max-w-xs mt-5">
                    <button
                        @click="speakKa"
                        :disabled="isSpeaking"
                        class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-2xl border-2 border-purple-800 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span class="text-base" :class="{ 'animate-bounce': isSpeaking }">🔊</span>
                        <span>Dengar Lafal</span>
                    </button>

                    <!-- Tombol Ulangi Animasi -->
                    <button
                        @click="replayAnimation"
                        :disabled="isAnimating"
                        class="flex-1 py-3 bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs rounded-2xl border-2 border-orange-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="{ 'opacity-70 cursor-not-allowed': isAnimating }"
                    >
                        <span class="text-base" :class="{ 'animate-spin': isAnimating }">🔄</span>
                        <span>{{ isAnimating ? 'Memutar...' : 'Ulangi Animasi' }}</span>
                    </button>
                </div>

                <!-- Tombol Lanjut ke Latihan -->
                <button
                    @click="emit('startPractice'); emit('close');"
                    class="w-full max-w-xs mt-3 py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs rounded-2xl border-2 border-emerald-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <span>✏️</span>
                    <span>Coba Latihan Huruf Ini</span>
                </button>
            </div>

        </div>
    </div>
</template>

<style scoped>
@keyframes scaleUp {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
.animate-scale-up {
    animation: scaleUp 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.ka-stroke-1 {
    stroke-dasharray: 220;
    stroke-dashoffset: 220;
    animation: drawKaStroke1 1.4s ease-in-out forwards;
}

.ka-stroke-2 {
    stroke-dasharray: 220;
    stroke-dashoffset: 220;
    animation: drawKaStroke2 1.4s ease-in-out 1.4s forwards;
}

@keyframes drawKaStroke1 {
    0% {
        stroke-dashoffset: 220;
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    100% {
        stroke-dashoffset: 0;
        opacity: 1;
    }
}

@keyframes drawKaStroke2 {
    0% {
        stroke-dashoffset: 220;
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    100% {
        stroke-dashoffset: 0;
        opacity: 1;
    }
}
</style>
