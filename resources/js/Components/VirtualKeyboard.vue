<script setup>
import { ref, computed } from 'vue';

const typedText = ref('');
const activePronunciation = ref('');

// Exclusively Aksara Sunda base keys (Swara & Ngalagena)
const sundaKeys = [
    { char: 'ᮃ', latin: 'a' }, { char: 'ᮄ', latin: 'i' }, { char: 'ᮅ', latin: 'u' }, { char: 'ᮈ', latin: 'e' }, { char: 'ᮇ', latin: 'o' },
    { char: 'ᮊ', latin: 'ka' }, { char: 'ᮌ', latin: 'ga' }, { char: 'ᮎ', latin: 'ca' }, { char: 'ᮏ', latin: 'ja' }, { char: 'ᮓ', latin: 'da' },
    { char: 'ᮔ', latin: 'na' }, { char: 'ᮕ', latin: 'pa' }, { char: 'ᮘ', latin: 'ba' }, { char: 'ᮙ', latin: 'ma' }, { char: 'ᮞ', latin: 'sa' },
    { char: 'ᮠ', latin: 'ha' }, { char: 'ᮑ', latin: 'nya' }, { char: 'ᮒ', latin: 'ta' }, { char: 'ᮖ', latin: 'fa' }, { char: 'ᮗ', latin: 'va' },
    { char: 'ᮚ', latin: 'ya' }, { char: 'ᮛ', latin: 'ra' }, { char: 'ᮜ', latin: 'la' }, { char: 'ᮝ', latin: 'wa' }
];

// Rarangèn (Tanda Vokalisasi, Sisipan & Konsonan Akhir)
const rarangenKeys = [
    // Vokal (Ngarubah Vokal)
    { char: 'ᮤ', display: '◌ᮤ', name: 'Panghulu', latin: '-i', type: 'vowel', val: 'i' },
    { char: 'ᮥ', display: '◌ᮥ', name: 'Panyuku', latin: '-u', type: 'vowel', val: 'u' },
    { char: 'ᮦ', display: '◌ᮦ', name: 'Panéléng', latin: '-é', type: 'vowel', val: 'é' },
    { char: 'ᮧ', display: '◌ᮧ', name: 'Panolong', latin: '-o', type: 'vowel', val: 'o' },
    { char: 'ᮨ', display: '◌ᮨ', name: 'Pamepet', latin: '-e', type: 'vowel', val: 'e' },
    { char: 'ᮩ', display: '◌ᮩ', name: 'Paneuleung', latin: '-eu', type: 'vowel', val: 'eu' },
    // Consonant Killers & Final Markers
    { char: '᮪', display: '◌᮪', name: 'Pamaéh', latin: 'Paten', type: 'killer', val: '' },
    { char: 'ᮀ', display: '◌ᮀ', name: 'Panyecek', latin: '+ng', type: 'final', val: 'ng' },
    { char: 'ᮁ', display: '◌ᮁ', name: 'Panglayar', latin: '+r', type: 'final', val: 'r' },
    { char: 'ᮂ', display: '◌ᮂ', name: 'Pangwisad', latin: '+h', type: 'final', val: 'h' },
    // Medials / Sisipan
    { char: 'ᮡ', display: '◌ᮡ', name: 'Pamingkal', latin: '+ya', type: 'medial', val: 'y' },
    { char: 'ᮢ', display: '◌ᮢ', name: 'Panyakra', latin: '+ra', type: 'medial', val: 'r' },
    { char: 'ᮣ', display: '◌ᮣ', name: 'Panyiku', latin: '+la', type: 'medial', val: 'l' },
];

const baseCharMap = {
    'ᮃ': { base: '', vowel: 'a' },
    'ᮄ': { base: '', vowel: 'i' },
    'ᮅ': { base: '', vowel: 'u' },
    'ᮈ': { base: '', vowel: 'e' },
    'ᮇ': { base: '', vowel: 'o' },
    'ᮉ': { base: '', vowel: 'e' },
    'ᮊ': { base: 'k', vowel: 'a' },
    'ᮌ': { base: 'g', vowel: 'a' },
    'ᮎ': { base: 'c', vowel: 'a' },
    'ᮏ': { base: 'j', vowel: 'a' },
    'ᮓ': { base: 'd', vowel: 'a' },
    'ᮔ': { base: 'n', vowel: 'a' },
    'ᮕ': { base: 'p', vowel: 'a' },
    'ᮘ': { base: 'b', vowel: 'a' },
    'ᮙ': { base: 'm', vowel: 'a' },
    'ᮞ': { base: 's', vowel: 'a' },
    'ᮠ': { base: 'h', vowel: 'a' },
    'ᮑ': { base: 'ny', vowel: 'a' },
    'ᮒ': { base: 't', vowel: 'a' },
    'ᮖ': { base: 'f', vowel: 'a' },
    'ᮗ': { base: 'v', vowel: 'a' },
    'ᮚ': { base: 'y', vowel: 'a' },
    'ᮛ': { base: 'r', vowel: 'a' },
    'ᮜ': { base: 'l', vowel: 'a' },
    'ᮝ': { base: 'w', vowel: 'a' },
};

const currentTransliteration = computed(() => {
    return getLatinTransliteration(typedText.value);
});

function pressKey(key) {
    typedText.value += key.char;
    const sound = getLastSyllableLatin(typedText.value) || key.latin;
    speak(sound);
}

function pressRarangen(rKey) {
    if (!typedText.value) {
        // If text is empty, append character directly
        typedText.value += rKey.char;
        speak(rKey.name);
        return;
    }

    const chars = Array.from(typedText.value);
    let lastBaseIdx = -1;
    for (let i = chars.length - 1; i >= 0; i--) {
        if (baseCharMap[chars[i]]) {
            lastBaseIdx = i;
            break;
        }
    }

    if (lastBaseIdx === -1) {
        typedText.value += rKey.char;
        speak(rKey.name);
        return;
    }

    let baseChar = chars[lastBaseIdx];
    let medialChar = '';
    let vowelChar = '';
    let finalChar = '';

    for (let i = lastBaseIdx + 1; i < chars.length; i++) {
        let c = chars[i];
        if (['ᮡ', 'ᮢ', 'ᮣ'].includes(c)) medialChar = c;
        else if (['ᮤ', 'ᮥ', 'ᮦ', 'ᮧ', 'ᮨ', 'ᮩ', '᮪'].includes(c)) vowelChar = c;
        else if (['ᮀ', 'ᮁ', 'ᮂ'].includes(c)) finalChar = c;
    }

    if (rKey.type === 'vowel' || rKey.type === 'killer') {
        vowelChar = rKey.char;
    } else if (rKey.type === 'medial') {
        medialChar = rKey.char;
    } else if (rKey.type === 'final') {
        finalChar = rKey.char;
    }

    // Reconstruct canonical Aksara Sunda sequence: Base + Medial + Vowel/Killer + Final
    const reconstructedSyllable = baseChar + medialChar + vowelChar + finalChar;
    typedText.value = chars.slice(0, lastBaseIdx).join('') + reconstructedSyllable;

    const syllableSound = getLastSyllableLatin(typedText.value);
    speak(syllableSound || rKey.name);
}

function getLastSyllableLatin(text) {
    if (!text) return '';
    const fullTrans = getLatinTransliteration(text);
    const words = fullTrans.trim().split(/\s+/);
    return words[words.length - 1] || '';
}

function getLatinTransliteration(text) {
    if (!text) return '';
    let result = [];
    let chars = Array.from(text);
    let i = 0;

    while (i < chars.length) {
        let char = chars[i];
        if (char === ' ') {
            result.push(' ');
            i++;
            continue;
        }

        if (baseCharMap[char]) {
            let info = baseCharMap[char];
            let baseConsonant = info.base;
            let currentVowel = info.vowel;
            let medial = '';
            let finalConsonant = '';
            let isKilled = false;

            i++;
            while (i < chars.length) {
                let c = chars[i];
                if (c === 'ᮡ') { medial = 'y'; i++; }
                else if (c === 'ᮢ') { medial = 'r'; i++; }
                else if (c === 'ᮣ') { medial = 'l'; i++; }
                else if (c === 'ᮤ') { currentVowel = 'i'; i++; }
                else if (c === 'ᮥ') { currentVowel = 'u'; i++; }
                else if (c === 'ᮦ') { currentVowel = 'é'; i++; }
                else if (c === 'ᮧ') { currentVowel = 'o'; i++; }
                else if (c === 'ᮨ') { currentVowel = 'e'; i++; }
                else if (c === 'ᮩ') { currentVowel = 'eu'; i++; }
                else if (c === '᮪') { isKilled = true; i++; }
                else if (c === 'ᮀ') { finalConsonant = 'ng'; i++; }
                else if (c === 'ᮁ') { finalConsonant = 'r'; i++; }
                else if (c === 'ᮂ') { finalConsonant = 'h'; i++; }
                else {
                    break;
                }
            }

            let syllableLatin = baseConsonant + medial + (isKilled ? '' : currentVowel) + finalConsonant;
            result.push(syllableLatin);
        } else {
            let rar = rarangenKeys.find(r => r.char === char);
            if (rar) {
                result.push(rar.latin);
            } else {
                result.push(char);
            }
            i++;
        }
    }
    return result.join('');
}

function speak(text) {
    if (!text) return;
    activePronunciation.value = text;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.25;
        window.speechSynthesis.speak(utterance);
    }
}

function speakAll() {
    if (!typedText.value.trim()) return;
    const transliteratedText = getLatinTransliteration(typedText.value);
    activePronunciation.value = transliteratedText;

    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(transliteratedText);
        utterance.lang = 'id-ID';
        utterance.rate = 0.8;
        utterance.pitch = 1.2;
        window.speechSynthesis.speak(utterance);
    }
}

function backspace() {
    let chars = Array.from(typedText.value);
    chars.pop();
    typedText.value = chars.join('');
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
            <span>⌨️ Papan Ketik Aksara Sunda + Rarangèn</span>
        </div>

        <!-- Typed Display Box -->
        <div class="w-full bg-purple-50/90 rounded-2xl border-3 border-purple-200 p-4 mb-4 text-center min-h-[100px] flex flex-col items-center justify-center relative shadow-inner">
            <div class="text-3xl sm:text-4xl font-bold text-purple-900 tracking-wider break-words w-full">
                {{ typedText || 'Ketik aksara Sunda & rarangèn di bawah...' }}
            </div>
            
            <div class="flex flex-wrap items-center justify-center gap-2 mt-2">
                <div v-if="currentTransliteration" class="inline-flex items-center gap-1 px-3 py-0.5 bg-amber-100 text-amber-900 rounded-full text-xs font-extrabold border border-amber-300">
                    <span>🔤 Transliterasi:</span>
                    <span class="font-black uppercase text-amber-900 font-mono">{{ currentTransliteration }}</span>
                </div>
                <div v-if="activePronunciation" class="inline-flex items-center gap-1 px-3 py-0.5 bg-purple-200 text-purple-900 rounded-full text-xs font-black animate-pulse border border-purple-300">
                    <span>🔊 Pelafalan:</span>
                    <span class="uppercase font-black text-purple-900">"{{ activePronunciation }}"</span>
                </div>
            </div>
        </div>

        <!-- Keyboard Key Box -->
        <div class="w-full bg-purple-100/70 p-3 sm:p-4 rounded-3xl border-3 border-purple-200 mb-4 shadow-md">
            
            <!-- Section Title 1: Aksara Utama -->
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black text-purple-800 uppercase tracking-wider flex items-center gap-1">
                    <span>🔤 Aksara Utama (Swara & Ngalagena)</span>
                </span>
            </div>

            <!-- Main Key Grid -->
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
                    <span class="text-[9px] font-extrabold text-purple-600 uppercase -mt-0.5">
                        {{ key.latin }}
                    </span>
                </button>
            </div>

            <!-- Section Title 2: Rarangèn -->
            <div class="mt-4 pt-3 border-t-2 border-dashed border-purple-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black text-amber-800 uppercase tracking-wider flex items-center gap-1">
                        <span>✨ Rarangèn (Vokalisasi, Sisipan & Paten)</span>
                    </span>
                </div>

                <!-- Rarangèn Grid -->
                <div class="grid grid-cols-4 sm:grid-cols-7 gap-1.5 sm:gap-2">
                    <button
                        v-for="rKey in rarangenKeys"
                        :key="rKey.name"
                        @click="pressRarangen(rKey)"
                        class="h-13 sm:h-14 bg-amber-50 hover:bg-amber-100 border-2 border-amber-300 border-b-4 border-b-amber-400 rounded-xl flex flex-col items-center justify-center shadow-[0_2px_0_0_rgba(0,0,0,0.08)] active:translate-y-1 active:border-b-2 active:shadow-none transition-all cursor-pointer group p-1"
                        :title="rKey.name + ' (' + rKey.latin + ')'"
                    >
                        <span class="text-lg sm:text-xl font-bold text-amber-950 group-hover:scale-110 transition-transform">
                            {{ rKey.display }}
                        </span>
                        <span class="text-[9px] font-extrabold text-amber-700 uppercase -mt-0.5 truncate w-full text-center">
                            {{ rKey.latin }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Space and Action Row -->
            <div class="grid grid-cols-4 gap-2 mt-4 pt-2">
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
            @click="speakAll"
            class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-sm rounded-2xl border-2 border-purple-800 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-2"
        >
            <span>Dengarkan Semua Pelafalan</span>
            <span class="text-base">🔊</span>
        </button>
    </div>
</template>
