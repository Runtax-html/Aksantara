<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    initialCharacter: {
        type: String,
        default: 'ᮊ',
    },
    latin: {
        type: String,
        default: 'Ka',
    }
});

const characters = [
    { char: 'ᮃ', latin: 'A' },
    { char: 'ᮄ', latin: 'I' },
    { char: 'ᮅ', latin: 'U' },
    { char: 'ᮈ', latin: 'E' },
    { char: 'ᮇ', latin: 'O' },
    { char: 'ᮊ', latin: 'Ka' },
    { char: 'ᮌ', latin: 'Ga' },
    { char: 'ᮎ', latin: 'Ca' },
    { char: 'ᮏ', latin: 'Ja' },
    { char: 'ᮓ', latin: 'Da' },
    { char: 'ᮔ', latin: 'Na' },
    { char: 'ᮕ', latin: 'Pa' },
    { char: 'ᮘ', latin: 'Ba' },
    { char: 'ᮙ', latin: 'Ma' },
    { char: 'ᮞ', latin: 'Sa' },
    { char: 'ᮠ', latin: 'Ha' },
];

const selectedChar = ref(props.initialCharacter);
const selectedLatin = ref(props.latin);
const canvasRef = ref(null);
const isDrawing = ref(false);
const currentColor = ref('#FF4D30');
const currentSize = ref(8);
const feedbackMessage = ref('');

const colors = ['#FF4D30', '#10B981', '#6366F1', '#F59E0B', '#111827'];

function selectCharacter(item) {
    selectedChar.value = item.char;
    selectedLatin.value = item.latin;
    clearCanvas();
    feedbackMessage.value = '';
}

function getPos(e) {
    const canvas = canvasRef.value;
    if (!canvas) return { x: 0, y: 0 };
    const rect = canvas.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
        x: (clientX - rect.left) * (canvas.width / rect.width),
        y: (clientY - rect.top) * (canvas.height / rect.height)
    };
}

function startDrawing(e) {
    isDrawing.value = true;
    const ctx = canvasRef.value.getContext('2d');
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function draw(e) {
    if (!isDrawing.value) return;
    e.preventDefault();
    const ctx = canvasRef.value.getContext('2d');
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
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    feedbackMessage.value = '';
}

function checkTracing() {
    feedbackMessage.value = 'Hore! Goresan Aksara Sunda milikmu bagus sekali! 🎉 (+10 XP)';
}

onMounted(() => {
    clearCanvas();
});
</script>

<template>
    <div class="flex flex-col items-center w-full">
        <!-- Character Selector Pills (Aksara Sunda Only) -->
        <div class="flex items-center gap-2 mb-4 overflow-x-auto pb-1 max-w-full no-scrollbar">
            <button
                v-for="item in characters"
                :key="item.char"
                @click="selectCharacter(item)"
                class="px-3.5 py-1.5 rounded-xl font-extrabold text-sm border-2 transition-all cursor-pointer shrink-0 active:translate-y-0.5"
                :class="selectedChar === item.char ? 'bg-[#FF4D30] text-white border-orange-600 shadow-md' : 'bg-orange-50 text-gray-700 border-orange-200 hover:bg-orange-100'"
            >
                {{ item.char }} ({{ item.latin }})
            </button>
        </div>

        <!-- Tracing Canvas Box -->
        <div class="relative w-full max-w-sm aspect-square bg-orange-50/60 rounded-3xl border-4 border-orange-200 overflow-hidden shadow-inner flex items-center justify-center">
            <!-- Background Tracing Watermark Character -->
            <div class="absolute inset-0 flex items-center justify-center select-none pointer-events-none">
                <span class="text-9xl sm:text-[140px] font-bold text-orange-200/70">
                    {{ selectedChar }}
                </span>
            </div>

            <!-- HTML5 Canvas -->
            <canvas
                ref="canvasRef"
                width="320"
                height="320"
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

        <!-- Controls: Color & Size -->
        <div class="flex items-center justify-between w-full max-w-sm mt-4 gap-2">
            <!-- Palette -->
            <div class="flex items-center gap-1.5 bg-orange-100 p-1.5 rounded-2xl border-2 border-orange-200">
                <button
                    v-for="color in colors"
                    :key="color"
                    @click="currentColor = color"
                    class="w-7 h-7 rounded-xl border-2 transition-transform cursor-pointer"
                    :style="{ backgroundColor: color }"
                    :class="currentColor === color ? 'scale-110 border-white ring-2 ring-orange-400' : 'border-transparent opacity-80'"
                ></button>
            </div>

            <!-- Size Buttons -->
            <div class="flex items-center gap-1 bg-orange-100 p-1.5 rounded-2xl border-2 border-orange-200">
                <button
                    @click="currentSize = 5"
                    class="px-2 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                    :class="currentSize === 5 ? 'bg-white text-gray-800 shadow' : 'text-gray-500'"
                >Tipis</button>
                <button
                    @click="currentSize = 10"
                    class="px-2 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                    :class="currentSize === 10 ? 'bg-white text-gray-800 shadow' : 'text-gray-500'"
                >Tebal</button>
            </div>
        </div>

        <!-- Feedback alert -->
        <div v-if="feedbackMessage" class="mt-3 p-3 bg-emerald-100 border-2 border-emerald-300 text-emerald-800 rounded-2xl text-xs font-extrabold text-center animate-bounce w-full max-w-sm">
            {{ feedbackMessage }}
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 w-full max-w-sm mt-4">
            <button
                @click="clearCanvas"
                class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold text-xs rounded-2xl border-2 border-gray-300 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.1)] active:translate-y-0.5 active:shadow-none transition-all cursor-pointer"
            >
                Hapus 🗑️
            </button>
            <button
                @click="checkTracing"
                class="flex-1 py-3 bg-[#FF4D30] hover:bg-[#e03e22] text-white font-extrabold text-xs rounded-2xl border-2 border-orange-600 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:shadow-none transition-all cursor-pointer"
            >
                Periksa 🌟
            </button>
        </div>
    </div>
</template>
