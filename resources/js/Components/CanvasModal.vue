<script setup>
import { ref, watch, nextTick } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    character: {
        type: String,
        default: 'ᮊ',
    },
    latin: {
        type: String,
        default: 'Ka',
    }
});

const emit = defineEmits(['close', 'xpGained']);

// ─── DICTIONARY CONFIG HURUF (CHARACTER_CONFIGS) ───
const CHARACTER_CONFIGS = {
    'ᮃ': { fontSize: 130, strokeWidth: 28, offsetY: 0 },
    'ᮄ': { fontSize: 130, strokeWidth: 28, offsetY: 0 },
    'ᮅ': { fontSize: 130, strokeWidth: 28, offsetY: 0 },
    'ᮈ': { fontSize: 130, strokeWidth: 28, offsetY: 0 },
    'ᮇ': { fontSize: 130, strokeWidth: 28, offsetY: 0 },
    'ᮊ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮌ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮎ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮏ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮓ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮔ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮕ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮘ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮙ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮞ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
    'ᮠ': { fontSize: 125, strokeWidth: 26, offsetY: 2 },
};

const DEFAULT_CONFIG = { fontSize: 130, strokeWidth: 28, offsetY: 0 };

// Tab state: 'guide' | 'practice'
const activeTab = ref('guide');

// Audio / Speech State
const isSpeaking = ref(false);

// Canvas & Drawing State
const canvasRef = ref(null);
const isDrawing = ref(false);
const currentColor = ref('#FF4D30');
const currentSize = ref(16); // Default brush size
const userStrokeCount = ref(0);

// Validation Result State
const validationResult = ref(null);

// Guide Animation State
const animationKey = ref(0);

const colors = ['#FF4D30', '#10B981', '#6366F1', '#F59E0B', '#111827'];

// Audio Pronunciation
function speakCharacter() {
    isSpeaking.value = true;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(props.latin);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.2;
        utterance.onend = () => { isSpeaking.value = false; };
        window.speechSynthesis.speak(utterance);
    } else {
        setTimeout(() => { isSpeaking.value = false; }, 800);
    }
}

function restartGuideAnimation() {
    animationKey.value++;
}

// Canvas Position Helper (Fixed Math & Touch Scaling)
function getPos(e) {
    const canvas = canvasRef.value;
    if (!canvas) return { x: 0, y: 0 };
    const rect = canvas.getBoundingClientRect();
    
    let clientX = e.clientX;
    let clientY = e.clientY;

    if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    } else if (e.changedTouches && e.changedTouches.length > 0) {
        clientX = e.changedTouches[0].clientX;
        clientY = e.changedTouches[0].clientY;
    }

    return {
        x: (clientX - rect.left) * (canvas.width / rect.width),
        y: (clientY - rect.top) * (canvas.height / rect.height)
    };
}

function startDrawing(e) {
    isDrawing.value = true;
    userStrokeCount.value++;
    const ctx = canvasRef.value.getContext('2d', { willReadFrequently: true });
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function draw(e) {
    if (!isDrawing.value) return;
    e.preventDefault();
    const ctx = canvasRef.value.getContext('2d', { willReadFrequently: true });
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.strokeStyle = currentColor.value;
    ctx.lineWidth = currentSize.value;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.stroke();
}

function stopDrawing() {
    isDrawing.value = false;
}

function clearCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    userStrokeCount.value = 0;
    validationResult.value = null;
}

// DYNAMIC ACCURACY VALIDATION WITH RECALL & PRECISION ANTI-CHEAT
function validateStroke() {
    const canvas = canvasRef.value;
    if (!canvas) return;

    const width = canvas.width;
    const height = canvas.height;
    
    // 1. Get User Canvas ImageData
    const userCtx = canvas.getContext('2d', { willReadFrequently: true });
    const userData = userCtx.getImageData(0, 0, width, height).data;

    // 2. Fetch Character Dynamic Configuration
    const config = CHARACTER_CONFIGS[props.character] || DEFAULT_CONFIG;

    // 3. Offscreen Target Mask Canvas using CHARACTER_CONFIGS
    const maskCanvas = document.createElement('canvas');
    maskCanvas.width = width;
    maskCanvas.height = height;
    const maskCtx = maskCanvas.getContext('2d', { willReadFrequently: true });

    maskCtx.clearRect(0, 0, width, height);

    // Dynamic Target Mask Properties
    maskCtx.strokeStyle = '#000000';
    maskCtx.lineWidth = config.strokeWidth;
    maskCtx.lineCap = 'round';
    maskCtx.lineJoin = 'round';
    maskCtx.font = `bold ${config.fontSize}px sans-serif`;
    maskCtx.textAlign = 'center';
    maskCtx.textBaseline = 'middle';

    // RENDER DYNAMIC STROKE MASK (No fillText)
    maskCtx.strokeText(props.character, width / 2, (height / 2) + config.offsetY);

    const maskData = maskCtx.getImageData(0, 0, width, height).data;

    let strokeTargetPixels = 0;
    let userPixels = 0;
    let overlapPixels = 0;

    // 4. Count Pixels
    for (let i = 0; i < userData.length; i += 4) {
        const userAlpha = userData[i + 3];
        const maskAlpha = maskData[i + 3];

        const isUser = userAlpha > 30;
        const isTarget = maskAlpha > 128; // Dynamic stroke line target pixels

        if (isTarget) strokeTargetPixels++;
        if (isUser) userPixels++;
        if (isUser && isTarget) overlapPixels++;
    }

    if (userPixels < 60) {
        validationResult.value = {
            score: 0,
            success: false,
            message: 'Yuk coret hurufnya terlebih dahulu! ✏️',
            details: 'Canvas masih kosong atau terlalu sedikit coretan.'
        };
        return;
    }

    // 5. RECALL & PRECISION SCORING METHOD WITH ANTI-CHEAT
    // Recall: % of target stroke corridor covered by user
    const recall = strokeTargetPixels > 0 ? (overlapPixels / strokeTargetPixels) * 100 : 0;
    
    // Precision: % of user strokes that actually land inside target corridor
    const precision = userPixels > 0 ? (overlapPixels / userPixels) * 100 : 0;

    // Weighted Raw Score: (Recall * 0.6) + (Precision * 0.4)
    const rawScore = (recall * 0.6) + (precision * 0.4);

    let finalScore = 0;

    if (precision < 40) {
        // Heavy penalty if user scribbles randomly outside ("cat tembok")
        const penalty = (40 - precision) * 2.5;
        finalScore = Math.max(0, Math.round(rawScore - penalty));
    } else if (recall >= 35 && precision >= 40) {
        // User traced the character shape nicely -> Award 80%+
        finalScore = Math.min(100, Math.round(80 + ((rawScore - 35) / 65) * 20));
    } else {
        finalScore = Math.round((rawScore / 35) * 75);
    }

    finalScore = Math.max(0, Math.min(100, finalScore));

    // Console Logging for Browser Debugging
    console.log('[Aksantara Dynamic Tracing Validation]:', {
        huruf: props.character,
        latin: props.latin,
        configUsed: config,
        canvasSize: `${width}x${height}`,
        totalStrokeTargetPixels: strokeTargetPixels,
        userPixelsDrawn: userPixels,
        overlapPixels: overlapPixels,
        recallPercent: recall.toFixed(2) + '%',
        precisionPercent: precision.toFixed(2) + '%',
        rawScore: rawScore.toFixed(2),
        skorAkurasiAkhir: finalScore + '%'
    });

    if (finalScore >= 80) {
        validationResult.value = {
            score: finalScore,
            success: true,
            message: 'Luar Biasa! Goresanmu Sangat Presisi! 🎉',
            details: `Akurasi: ${finalScore}% • Jalur Terisi: ${Math.round(recall)}% • Presisi: ${Math.round(precision)}% • Bonus: +10 XP`
        };
        emit('xpGained', 10);
    } else {
        validationResult.value = {
            score: finalScore,
            success: false,
            message: 'Yuk Coba Lagi! 💪',
            details: `Akurasi: ${finalScore}% (Jalur: ${Math.round(recall)}%, Presisi: ${Math.round(precision)}%) • ${precision < 40 ? 'Coretan terlalu banyak melenceng keluar!' : 'Coretan belum memenuhi bentuk huruf.'}`
        };
    }
}

watch(() => props.show, (newVal) => {
    if (newVal) {
        activeTab.value = 'guide';
        validationResult.value = null;
        nextTick(() => {
            clearCanvas();
        });
    }
});

watch(activeTab, (newTab) => {
    if (newTab === 'practice') {
        nextTick(() => {
            clearCanvas();
        });
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
                class="absolute top-4 right-4 w-9 h-9 rounded-full border-2 border-orange-300 bg-orange-100 hover:bg-orange-200 flex items-center justify-center font-black text-orange-600 active:translate-y-0.5 transition cursor-pointer"
            >
                ✕
            </button>

            <!-- Character Header -->
            <div class="text-center mb-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 text-[#FF4D30] rounded-full text-xs font-black border border-orange-200 mb-1">
                    <span>Latihan Nulis Aksara Sunda</span>
                </div>
                <h2 class="text-2xl font-black text-gray-900 flex items-center justify-center gap-2">
                    <span>Huruf "{{ latin }}"</span>
                    <span class="text-3xl text-[#FF4D30]">{{ character }}</span>
                </h2>
            </div>

            <!-- Tab Switcher Inside Modal -->
            <div class="flex items-center bg-orange-100/70 p-1.5 rounded-2xl border-2 border-orange-200 mb-5 gap-2">
                <button
                    @click="activeTab = 'guide'"
                    class="flex-1 py-2.5 px-3 rounded-xl font-extrabold text-xs border-2 border-b-4 transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'guide' ? 'bg-[#FF4D30] text-white border-orange-700 shadow-[0_2px_0_0_rgba(0,0,0,0.15)]' : 'bg-white text-gray-700 border-orange-200 hover:bg-orange-50 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-base">📖</span>
                    <span>Panduan</span>
                </button>

                <button
                    @click="activeTab = 'practice'"
                    class="flex-1 py-2.5 px-3 rounded-xl font-extrabold text-xs border-2 border-b-4 transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    :class="activeTab === 'practice' ? 'bg-[#FF4D30] text-white border-orange-700 shadow-[0_2px_0_0_rgba(0,0,0,0.15)]' : 'bg-white text-gray-700 border-orange-200 hover:bg-orange-50 active:translate-y-0.5 border-b-2'"
                >
                    <span class="text-base">✏️</span>
                    <span>Latihan</span>
                </button>
            </div>

            <!-- TAB 1: PANDUAN (GUIDE TAB) -->
            <div v-if="activeTab === 'guide'" class="flex flex-col items-center">
                <div class="relative w-full max-w-xs aspect-square bg-orange-50/70 rounded-3xl border-4 border-orange-200 flex flex-col items-center justify-center p-4 overflow-hidden mb-4 shadow-inner">
                    <!-- Character Display -->
                    <span class="text-9xl sm:text-[140px] font-black text-gray-800 select-none">
                        {{ character }}
                    </span>

                    <!-- Dotted Arrow Guide Overlay -->
                    <div :key="animationKey" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <svg class="w-full h-full p-6 text-[#FF4D30] stroke-[#FF4D30] animate-pulse" viewBox="0 0 200 200" fill="none">
                            <path d="M 40 50 L 160 50 M 100 50 L 100 150" stroke-width="6" stroke-dasharray="8 8" stroke-linecap="round" />
                            <circle cx="40" cy="50" r="6" fill="#FF4D30" />
                            <polygon points="160,50 150,45 150,55" fill="#FF4D30" />
                            <polygon points="100,150 95,140 105,140" fill="#FF4D30" />
                        </svg>
                    </div>

                    <span class="absolute bottom-2 text-[11px] font-black text-orange-600 bg-orange-100 px-3 py-0.5 rounded-full border border-orange-300">
                        Urutan Goresan: 1 ➔ 2 ➔ 3
                    </span>
                </div>

                <!-- Audio & Repeat Animation Buttons -->
                <div class="flex items-center gap-3 w-full max-w-xs">
                    <button
                        @click="speakCharacter"
                        class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-2xl border-2 border-purple-800 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span class="text-base">🔊</span>
                        <span>Dengarkan Lafal</span>
                    </button>

                    <button
                        @click="restartGuideAnimation"
                        class="flex-1 py-3 bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs rounded-2xl border-2 border-orange-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span class="text-base">🔄</span>
                        <span>Ulangi Animasi</span>
                    </button>
                </div>
            </div>

            <!-- TAB 2: LATIHAN (PRACTICE CANVAS TAB) -->
            <div v-else-if="activeTab === 'practice'" class="flex flex-col items-center">
                <!-- HTML5 Canvas Box -->
                <div class="relative w-full max-w-xs aspect-square bg-orange-50/60 rounded-3xl border-4 border-orange-200 overflow-hidden shadow-inner flex items-center justify-center">
                    <!-- Background Watermark -->
                    <div class="absolute inset-0 flex items-center justify-center select-none pointer-events-none">
                        <span class="text-9xl sm:text-[140px] font-bold text-orange-200/60">
                            {{ character }}
                        </span>
                    </div>

                    <canvas
                        ref="canvasRef"
                        width="300"
                        height="300"
                        class="relative z-10 w-full h-full cursor-crosshair touch-none"
                        @mousedown="startDrawing"
                        @mousemove="draw"
                        @mouseup="stopDrawing"
                        @mouseleave="stopDrawing"
                        @touchstart="startDrawing"
                        @touchmove="draw"
                        @touchend="stopDrawing"
                    ></canvas>
                </div>

                <!-- Brush Color & Size Controls -->
                <div class="flex items-center justify-between w-full max-w-xs mt-3 gap-2">
                    <div class="flex items-center gap-1.5 bg-orange-100 p-1.5 rounded-2xl border-2 border-orange-200">
                        <button
                            v-for="color in colors"
                            :key="color"
                            @click="currentColor = color"
                            class="w-6 h-6 rounded-xl border-2 transition-transform cursor-pointer"
                            :style="{ backgroundColor: color }"
                            :class="currentColor === color ? 'scale-110 border-white ring-2 ring-orange-400' : 'border-transparent opacity-80'"
                        ></button>
                    </div>

                    <div class="flex items-center gap-1 bg-orange-100 p-1 rounded-2xl border-2 border-orange-200">
                        <button
                            @click="currentSize = 12"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold transition cursor-pointer"
                            :class="currentSize === 12 ? 'bg-[#FF4D30] text-white shadow' : 'text-gray-600 hover:bg-orange-200'"
                        >Tipis</button>
                        <button
                            @click="currentSize = 22"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer"
                            :class="currentSize === 22 ? 'bg-[#FF4D30] text-white shadow' : 'text-gray-600 hover:bg-orange-200'"
                        >Tebal</button>
                    </div>
                </div>

                <!-- Result & Validation Feedback Box -->
                <div v-if="validationResult" class="mt-3 p-3.5 w-full max-w-xs rounded-2xl border-3 text-center animate-bounce" :class="validationResult.success ? 'bg-emerald-100 border-emerald-400 text-emerald-900' : 'bg-red-100 border-red-400 text-red-900'">
                    <div class="text-sm font-black">{{ validationResult.message }}</div>
                    <div class="text-[11px] font-bold mt-0.5 opacity-90">{{ validationResult.details }}</div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 w-full max-w-xs mt-4">
                    <button
                        @click="clearCanvas"
                        class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold text-xs rounded-2xl border-2 border-gray-300 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.1)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1"
                    >
                        <span>🗑️ Hapus</span>
                    </button>
                    <button
                        @click="validateStroke"
                        class="flex-1 py-3 bg-[#FF4D30] hover:bg-[#e03e22] text-white font-extrabold text-xs rounded-2xl border-2 border-orange-600 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-1"
                    >
                        <span>🌟 Cek Hasil</span>
                    </button>
                </div>
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
</style>
