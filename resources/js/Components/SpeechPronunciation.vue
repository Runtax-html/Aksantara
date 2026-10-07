<script setup>
import { ref } from 'vue';

const props = defineProps({
    initialText: {
        type: String,
        default: 'Aksara Sunda',
    }
});

const isSpeaking = ref(false);
const activeWord = ref('Ka');

const sundaAlphabet = [
    { char: 'ᮃ', latin: 'A', meaning: 'Swara A' },
    { char: 'ᮄ', latin: 'I', meaning: 'Swara I' },
    { char: 'ᮅ', latin: 'U', meaning: 'Swara U' },
    { char: 'ᮈ', latin: 'E', meaning: 'Swara E' },
    { char: 'ᮇ', latin: 'O', meaning: 'Swara O' },
    { char: 'ᮊ', latin: 'Ka', meaning: 'Ngalagena Ka' },
    { char: 'ᮌ', latin: 'Ga', meaning: 'Ngalagena Ga' },
    { char: 'ᮎ', latin: 'Ca', meaning: 'Ngalagena Ca' },
    { char: 'ᮏ', latin: 'Ja', meaning: 'Ngalagena Ja' },
    { char: 'ᮓ', latin: 'Da', meaning: 'Ngalagena Da' },
    { char: 'ᮔ', latin: 'Na', meaning: 'Ngalagena Na' },
    { char: 'ᮕ', latin: 'Pa', meaning: 'Ngalagena Pa' },
    { char: 'ᮘ', latin: 'Ba', meaning: 'Ngalagena Ba' },
    { char: 'ᮙ', latin: 'Ma', meaning: 'Ngalagena Ma' },
    { char: 'ᮞ', latin: 'Sa', meaning: 'Ngalagena Sa' },
    { char: 'ᮠ', latin: 'Ha', meaning: 'Ngalagena Ha' },
];

function playAudio(latin) {
    activeWord.value = latin;
    isSpeaking.value = true;

    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(latin);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.25;
        
        utterance.onend = () => {
            isSpeaking.value = false;
        };

        window.speechSynthesis.speak(utterance);
    } else {
        setTimeout(() => {
            isSpeaking.value = false;
        }, 800);
    }
}
</script>

<template>
    <div class="flex flex-col items-center w-full bg-purple-50/80 p-5 rounded-3xl border-3 border-purple-200 shadow-lg">
        <!-- Header -->
        <div class="flex items-center gap-2 mb-4">
            <span class="text-3xl">🔊</span>
            <h3 class="text-xl font-extrabold text-purple-900">Audio Pelafalan Aksara Sunda</h3>
        </div>

        <!-- Active Sound Card Display -->
        <div class="w-full max-w-sm bg-white rounded-2xl border-3 border-purple-300 p-6 text-center shadow-md mb-6 relative overflow-hidden">
            <div class="absolute top-2 right-2 px-2.5 py-0.5 bg-purple-100 text-purple-800 text-[10px] font-black rounded-full uppercase">
                Suara Audio
            </div>

            <div class="text-6xl font-extrabold text-purple-900 mb-2">
                {{ sundaAlphabet.find(item => item.latin === activeWord)?.char || 'ᮊ' }}
            </div>

            <div class="text-2xl font-black text-purple-700 tracking-wide">
                "{{ activeWord }}"
            </div>

            <button
                @click="playAudio(activeWord)"
                class="mt-4 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-xl border-2 border-purple-800 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer inline-flex items-center gap-2"
                :class="{ 'animate-pulse bg-purple-700': isSpeaking }"
            >
                <span class="text-base">🔊</span>
                <span>{{ isSpeaking ? 'Membunyikan Suara...' : 'Putar Suara Pelafalan' }}</span>
            </button>
        </div>

        <!-- Grid of Sound Cards -->
        <div class="w-full">
            <h4 class="text-xs font-extrabold text-purple-800 uppercase tracking-wider mb-2 text-center">
                Pilih Huruf untuk Mendengarkan Suara:
            </h4>

            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                <button
                    v-for="item in sundaAlphabet"
                    :key="item.char"
                    @click="playAudio(item.latin)"
                    class="p-2 bg-white hover:bg-purple-100 border-2 border-purple-300 border-b-4 rounded-xl flex flex-col items-center justify-center transition-all cursor-pointer active:translate-y-0.5 active:border-b-2 active:shadow-none"
                    :class="activeWord === item.latin ? 'ring-2 ring-purple-600 bg-purple-200 border-purple-600 scale-105' : ''"
                >
                    <span class="text-xl font-bold text-purple-900">{{ item.char }}</span>
                    <span class="text-[9px] font-black text-purple-600 uppercase">{{ item.latin }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
