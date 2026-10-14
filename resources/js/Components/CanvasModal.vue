<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import axios from 'axios';
import { playSundaAudio } from '@/Utils/audioPlayer';

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

const emit = defineEmits(['close', 'xpGained', 'gamificationUpdated']);

// ─── DICTIONARY CONFIG HURUF (CHARACTER_CONFIGS) ───
// Vokal sederhana -> paddingStroke: 10
// Konsonan -> paddingStroke: 12 (zona toleransi coretan anak SD)
const CHARACTER_CONFIGS = {
    // Vokal Sederhana (Swara)
    'ᮃ': { fontSize: 130, paddingStroke: 10, offsetY: 0 },
    'ᮄ': { fontSize: 130, paddingStroke: 10, offsetY: 0 },
    'ᮅ': { fontSize: 130, paddingStroke: 10, offsetY: 0 },
    'ᮈ': { fontSize: 130, paddingStroke: 10, offsetY: 0 },
    'ᮇ': { fontSize: 130, paddingStroke: 10, offsetY: 0 },
    'ᮉ': { fontSize: 130, paddingStroke: 10, offsetY: 0 },
    // Konsonan (Ngalagena)
    'ᮊ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮌ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮍ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮎ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮏ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮑ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮒ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮓ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮔ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮕ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮘ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮙ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮞ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮠ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮚ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮛ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮜ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
    'ᮝ': { fontSize: 125, paddingStroke: 12, offsetY: 2 },
};

const DEFAULT_CONFIG = { fontSize: 125, paddingStroke: 12, offsetY: 0 };

const currentConfig = computed(() => {
    return CHARACTER_CONFIGS[props.character] || DEFAULT_CONFIG;
});

// Audio / Speech State
const isSpeaking = ref(false);

// Canvas & Drawing State (Satu Warna Standar & Satu Ukuran Kuas Standar)
const canvasRef = ref(null);
const isDrawing = ref(false);
const currentColor = '#1e293b'; // Warna hitam pekat standar (#1e293b)
const currentSize = 14;         // Kunci ketebalan kuas standar 14px
const userStrokeCount = ref(0);

// Validation Result State
const validationResult = ref(null);

// Audio Pronunciation (Edge-TTS Sundanese su-ID-JajangNeural)
function speakCharacter() {
    isSpeaking.value = true;
    playSundaAudio(props.latin, {
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
    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = currentColor;
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function draw(e) {
    if (!isDrawing.value) return;
    e.preventDefault();
    const ctx = canvasRef.value.getContext('2d', { willReadFrequently: true });
    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = currentColor;
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
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

    // Dynamic Target Mask Properties (Solid letter fill + outer padding stroke boundary)
    maskCtx.fillStyle = '#000000';
    maskCtx.strokeStyle = '#000000';
    maskCtx.lineWidth = config.paddingStroke;
    maskCtx.lineCap = 'round';
    maskCtx.lineJoin = 'round';
    maskCtx.font = `bold ${config.fontSize}px sans-serif`;
    maskCtx.textAlign = 'center';
    maskCtx.textBaseline = 'middle';

    const targetX = width / 2;
    const targetY = (height / 2) + config.offsetY;

    // 1. Fill solid character body
    maskCtx.fillText(props.character, targetX, targetY);
    // 2. Stroke outer tolerance boundary with paddingStroke
    maskCtx.strokeText(props.character, targetX, targetY);

    const maskData = maskCtx.getImageData(0, 0, width, height).data;

    let strokeTargetPixels = 0;
    let userPixels = 0;
    let overlapPixels = 0;

    // 4. Count Pixels
    for (let i = 0; i < userData.length; i += 4) {
        const userAlpha = userData[i + 3];
        const maskAlpha = maskData[i + 3];

        const isUser = userAlpha > 30;
        const isTarget = maskAlpha > 128; // Dynamic solid letter + tolerance corridor target pixels

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
        fontSize: config.fontSize,
        paddingStroke: config.paddingStroke,
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

    if (finalScore >= 70) {
        validationResult.value = {
            score: finalScore,
            success: true,
            message: 'Luar Biasa! Goresanmu Sangat Presisi! 🎉',
            details: `Akurasi: ${finalScore}% • Jalur Terisi: ${Math.round(recall)}% • Presisi: ${Math.round(precision)}% • Bonus: +15 XP & +5 Koin`
        };
        emit('xpGained', 15);
        // Otomatisasi Misi Harian Latihan Menulis di Canvas
        axios.post('/api/gamification/practice-writing')
            .then(res => {
                if (res.data.success && res.data.state) {
                    emit('gamificationUpdated', res.data.state);
                }
            })
            .catch(err => {
                console.error('Gagal mencatat latihan nulis:', err);
            });
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
        validationResult.value = null;
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
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 text-[#FF4D30] rounded-full text-xs font-black border border-orange-200 mb-1.5 shadow-sm">
                    <span>Latihan Nulis Aksara Sunda</span>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                        <span>Huruf "{{ latin }}"</span>
                        <span class="text-3xl text-[#FF4D30]">{{ character }}</span>
                    </h2>
                    <!-- Audio Pronunciation Button -->
                    <button
                        @click="speakCharacter"
                        :disabled="isSpeaking"
                        class="px-3 py-1.5 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-700 border-2 border-purple-300 font-extrabold text-xs transition-all cursor-pointer active:scale-95 flex items-center gap-1.5 shadow-sm"
                        title="Dengarkan Lafal"
                    >
                        <span class="text-sm" :class="{ 'animate-bounce': isSpeaking }">🔊</span>
                        <span>Dengar</span>
                    </button>
                </div>
            </div>

            <!-- MODUL LATIHAN CANVAS (Langsung Tampil Tanpa Tab) -->
            <div class="flex flex-col items-center">
                <!-- HTML5 Canvas Box -->
                <div class="relative w-full max-w-xs aspect-square bg-orange-50/60 rounded-3xl border-4 border-orange-200 overflow-hidden shadow-inner flex items-center justify-center">
                    <!-- Background Watermark -->
                    <div
                        class="absolute inset-0 flex items-center justify-center select-none pointer-events-none"
                        :style="{ transform: `translateY(${currentConfig.offsetY}px)` }"
                    >
                        <span
                            class="font-bold text-orange-200/60 leading-none select-none"
                            :style="{ fontSize: `${currentConfig.fontSize}px` }"
                        >
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

                <!-- Result & Validation Feedback Box -->
                <div v-if="validationResult" class="mt-3.5 p-3.5 w-full max-w-xs rounded-2xl border-3 text-center animate-bounce" :class="validationResult.success ? 'bg-emerald-100 border-emerald-400 text-emerald-900' : 'bg-red-100 border-red-400 text-red-900'">
                    <div class="text-sm font-black">{{ validationResult.message }}</div>
                    <div class="text-[11px] font-bold mt-0.5 opacity-90">{{ validationResult.details }}</div>
                </div>

                <!-- Action Buttons: Hapus & Cek Hasil -->
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
