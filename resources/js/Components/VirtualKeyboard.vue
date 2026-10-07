<script setup>
import { ref } from 'vue';

const typedText = ref('');
const activePronunciation = ref('');

// Exclusively Aksara Sunda keys
const sundaKeys = [
    { char: 'ᮃ', latin: 'a' }, { char: 'ᮄ', latin: 'i' }, { char: 'ᮅ', latin: 'u' }, { char: 'ᮈ', latin: 'e' }, { char: 'ᮇ', latin: 'o' },
    { char: 'ᮊ', latin: 'ka' }, { char: 'ᮌ', latin: 'ga' }, { char: 'ᮎ', latin: 'ca' }, { char: 'ᮏ', latin: 'ja' }, { char: 'ᮓ', latin: 'da' },
    { char: 'ᮔ', latin: 'na' }, { char: 'ᮕ', latin: 'pa' }, { char: 'ᮘ', latin: 'ba' }, { char: 'ᮙ', latin: 'ma' }, { char: 'ᮞ', latin: 'sa' },
    { char: 'ᮠ', latin: 'ha' }, { char: 'ᮑ', latin: 'nya' }, { char: 'ᮒ', latin: 'ta' }, { char: 'ᮖ', latin: 'fa' }, { char: 'ᮗ', latin: 'va' }
];

function pressKey(key) {
    typedText.value += key.char;
    speak(key.latin);
}

function speak(text) {
    activePronunciation.value = text;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.9;
        utterance.pitch = 1.25;
        window.speechSynthesis.speak(utterance);
    }
}

function backspace() {
    typedText.value = typedText.value.slice(0, -1);
}

function addSpace() {
    typedText.value += ' ';
}

function clearAll() {
    typedText.value = '';
    activePronunciation.value = '';
}
</script>

<template>
    <div class="flex flex-col items-center w-full">
        <!-- Header Badge -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 text-purple-900 rounded-full text-xs font-black mb-3 border border-purple-300">
            <span>⌨️ Papan Ketik Aksara Sunda</span>
        </div>

        <!-- Typed Display Box -->
        <div class="w-full bg-purple-50/90 rounded-2xl border-3 border-purple-200 p-4 mb-4 text-center min-h-[90px] flex flex-col items-center justify-center relative">
            <div class="text-3xl sm:text-4xl font-bold text-purple-900 tracking-wider break-words w-full">
                {{ typedText || 'Ketik aksara Sunda di bawah...' }}
            </div>
            
            <div v-if="activePronunciation" class="mt-2 inline-flex items-center gap-1.5 px-3 py-0.5 bg-purple-200 text-purple-900 rounded-full text-xs font-black animate-pulse">
                <span>🔊 Pelafalan:</span>
                <span class="uppercase font-black text-purple-900">"{{ activePronunciation }}"</span>
            </div>
        </div>

        <!-- Keyboard Key Grid -->
        <div class="w-full bg-purple-100/70 p-3 sm:p-4 rounded-3xl border-3 border-purple-200 mb-4">
            <div class="grid grid-cols-5 sm:grid-cols-5 gap-1.5 sm:gap-2">
                <button
                    v-for="key in sundaKeys"
                    :key="key.char"
                    @click="pressKey(key)"
                    class="h-12 sm:h-14 bg-white hover:bg-purple-200 border-2 border-purple-300 border-b-4 border-b-purple-400 rounded-xl flex flex-col items-center justify-center shadow-[0_2px_0_0_rgba(0,0,0,0.1)] active:translate-y-1 active:border-b-2 active:shadow-none transition-all cursor-pointer group"
                >
                    <span class="text-lg sm:text-2xl font-bold text-purple-900 group-hover:scale-110 transition-transform">
                        {{ key.char }}
                    </span>
                    <span class="text-[9px] font-extrabold text-purple-500 uppercase -mt-0.5">
                        {{ key.latin }}
                    </span>
                </button>
            </div>

            <!-- Space and Action Row -->
            <div class="grid grid-cols-4 gap-2 mt-3">
                <button
                    @click="addSpace"
                    class="col-span-2 py-2.5 bg-white hover:bg-purple-50 border-2 border-purple-300 border-b-4 rounded-xl font-extrabold text-xs text-purple-700 active:translate-y-0.5 active:border-b-2 transition cursor-pointer"
                >
                    ␣ Spasi
                </button>
                <button
                    @click="backspace"
                    class="py-2.5 bg-purple-200 hover:bg-purple-300 border-2 border-purple-400 border-b-4 rounded-xl font-extrabold text-xs text-purple-900 active:translate-y-0.5 active:border-b-2 transition cursor-pointer"
                >
                    ⌫ Hapus
                </button>
                <button
                    @click="clearAll"
                    class="py-2.5 bg-red-100 hover:bg-red-200 border-2 border-red-300 border-b-4 rounded-xl font-extrabold text-xs text-red-700 active:translate-y-0.5 active:border-b-2 transition cursor-pointer"
                >
                    🗑️ Bersihkan
                </button>
            </div>
        </div>

        <!-- Speak Whole Sentence Button -->
        <button
            v-if="typedText.trim()"
            @click="speak(typedText)"
            class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-2xl border-2 border-purple-800 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:shadow-none transition-all cursor-pointer"
        >
            Dengarkan Semua 🔊
        </button>
    </div>
</template>
