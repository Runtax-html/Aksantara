<script setup>
import { ref, computed } from 'vue';

const typedText = ref('');
const activePronunciation = ref('');
const showToast = ref(false);
const toastMessage = ref('');
const showInfoModal = ref(false);

// ═════════════════════════════════════════════════════════════════════
// KEYBOARD ROWS DEFINITIONS (10-COLUMN GRID LAYOUT)
// ═════════════════════════════════════════════════════════════════════

// Row 1: Rarangèn (Vokalisasi, Paten & Konsonan Akhir)
const row1Rarangen = [
    { char: 'ᮤ', display: '◌ᮤ', name: 'Panghulu', latin: 'i', type: 'vowel', val: 'i' },
    { char: 'ᮥ', display: '◌ᮥ', name: 'Panyuku', latin: 'u', type: 'vowel', val: 'u' },
    { char: 'ᮨ', display: '◌ᮨ', name: 'Pamepet', latin: 'e', type: 'vowel', val: 'e' },
    { char: 'ᮩ', display: '◌ᮩ', name: 'Paneuleung', latin: 'eu', type: 'vowel', val: 'eu' },
    { char: 'ᮦ', display: '◌ᮦ', name: 'Panéléng', latin: 'é', type: 'vowel', val: 'é' },
    { char: 'ᮧ', display: '◌ᮧ', name: 'Panolong', latin: 'o', type: 'vowel', val: 'o' },
    { char: '᮪', display: '◌᮪', name: 'Pamaéh', latin: 'paten', type: 'killer', val: '' },
    { char: 'ᮀ', display: '◌ᮀ', name: 'Panyecek', latin: '+ng', type: 'final', val: 'ng' },
    { char: 'ᮁ', display: '◌ᮁ', name: 'Panglayar', latin: '+r', type: 'final', val: 'r' },
    { char: 'ᮂ', display: '◌ᮂ', name: 'Pangwisad', latin: '+h', type: 'final', val: 'h' },
];

// Row 2: Aksara Swara & Ngalagena Bagian 1
const row2Keys = [
    { char: 'ᮃ', latin: 'a' },
    { char: 'ᮄ', latin: 'i' },
    { char: 'ᮅ', latin: 'u' },
    { char: 'ᮈ', latin: 'é' },
    { char: 'ᮇ', latin: 'o' },
    { char: 'ᮊ', latin: 'ka' },
    { char: 'ᮌ', latin: 'ga' },
    { char: 'ᮍ', latin: 'nga' },
    { char: 'ᮎ', latin: 'ca' },
    { char: 'ᮏ', latin: 'ja' },
];

// Row 3: Ngalagena Bagian 2
const row3Keys = [
    { char: 'ᮑ', latin: 'nya' },
    { char: 'ᮒ', latin: 'ta' },
    { char: 'ᮓ', latin: 'da' },
    { char: 'ᮔ', latin: 'na' },
    { char: 'ᮕ', latin: 'pa' },
    { char: 'ᮘ', latin: 'ba' },
    { char: 'ᮙ', latin: 'ma' },
    { char: 'ᮞ', latin: 'sa' },
    { char: 'ᮠ', latin: 'ha' },
    { char: 'ᮉ', latin: 'eu' },
];

// Row 4: Ngalagena Bagian 3 + Sisipan (Kiri-Tengah) + Del (Kanan)
const row4Keys = [
    { char: 'ᮚ', latin: 'ya' },
    { char: 'ᮛ', latin: 'ra' },
    { char: 'ᮜ', latin: 'la' },
    { char: 'ᮝ', latin: 'wa' },
    { char: 'ᮖ', latin: 'fa' },
    { char: 'ᮗ', latin: 'va' },
    { char: 'ᮐ', latin: 'za' },
    { char: 'ᮡ', display: '◌ᮡ', name: 'Pamingkal', latin: '+ya', type: 'medial', val: 'y' },
    { char: 'ᮢ', display: '◌ᮢ', name: 'Panyakra', latin: '+ra', type: 'medial', val: 'r' },
];

// Base Character Mapping for Transliteration & Combination
const baseCharMap = {
    'ᮃ': { base: '', vowel: 'a' },
    'ᮄ': { base: '', vowel: 'i' },
    'ᮅ': { base: '', vowel: 'u' },
    'ᮈ': { base: '', vowel: 'é' },
    'ᮇ': { base: '', vowel: 'o' },
    'ᮉ': { base: '', vowel: 'eu' },
    'ᮊ': { base: 'k', vowel: 'a' },
    'ᮌ': { base: 'g', vowel: 'a' },
    'ᮍ': { base: 'ng', vowel: 'a' },
    'ᮎ': { base: 'c', vowel: 'a' },
    'ᮏ': { base: 'j', vowel: 'a' },
    'ᮑ': { base: 'ny', vowel: 'a' },
    'ᮒ': { base: 't', vowel: 'a' },
    'ᮓ': { base: 'd', vowel: 'a' },
    'ᮔ': { base: 'n', vowel: 'a' },
    'ᮕ': { base: 'p', vowel: 'a' },
    'ᮘ': { base: 'b', vowel: 'a' },
    'ᮙ': { base: 'm', vowel: 'a' },
    'ᮞ': { base: 's', vowel: 'a' },
    'ᮠ': { base: 'h', vowel: 'a' },
    'ᮚ': { base: 'y', vowel: 'a' },
    'ᮛ': { base: 'r', vowel: 'a' },
    'ᮜ': { base: 'l', vowel: 'a' },
    'ᮝ': { base: 'w', vowel: 'a' },
    'ᮖ': { base: 'f', vowel: 'a' },
    'ᮗ': { base: 'v', vowel: 'a' },
    'ᮐ': { base: 'z', vowel: 'a' },
};

const allRarangenList = [
    ...row1Rarangen,
    { char: 'ᮡ', display: '◌ᮡ', name: 'Pamingkal', latin: '+ya', type: 'medial', val: 'y' },
    { char: 'ᮢ', display: '◌ᮢ', name: 'Panyakra', latin: '+ra', type: 'medial', val: 'r' },
    { char: 'ᮣ', display: '◌ᮣ', name: 'Panyiku', latin: '+la', type: 'medial', val: 'l' },
];

const currentTransliteration = computed(() => {
    return getLatinTransliteration(typedText.value);
});

// ═════════════════════════════════════════════════════════════════════
// KEY PRESS & COMBINING LOGIC
// ═════════════════════════════════════════════════════════════════════

function pressKey(key) {
    typedText.value += key.char;
    const sound = getLastSyllableLatin(typedText.value) || key.latin;
    speak(sound);
}

function pressRarangen(rKey) {
    if (!typedText.value) {
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

    // Canonical Unicode sequence: Base + Medial + Vowel/Killer + Final
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
            let rar = allRarangenList.find(r => r.char === char);
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

// ═════════════════════════════════════════════════════════════════════
// AUDIO & UTILITY ACTIONS
// ═════════════════════════════════════════════════════════════════════

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

function addChar(char) {
    typedText.value += char;
}

function addSpace() {
    typedText.value += ' ';
}

function clearAll() {
    typedText.value = '';
    activePronunciation.value = '';
}

function triggerToast(msg) {
    toastMessage.value = msg;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 2200);
}

async function copyToClipboard() {
    if (!typedText.value) return;
    try {
        await navigator.clipboard.writeText(typedText.value);
        triggerToast('Teks Aksara Sunda tersalin! 📋');
    } catch (err) {
        triggerToast('Gagal menyalin teks');
    }
}

async function shareText() {
    if (!typedText.value) return;
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'Aksantara - Aksara Sunda',
                text: `${typedText.value} (${currentTransliteration.value})`,
            });
        } catch (err) {
            // User cancelled share
        }
    } else {
        await copyToClipboard();
        triggerToast('Tautan/Teks disalin untuk dibagikan! 📤');
    }
}
</script>

<template>
    <div class="w-full max-w-3xl mx-auto bg-white rounded-3xl border border-gray-200 shadow-md overflow-hidden relative">
        
        <!-- Toast Notification -->
        <Transition name="fade-slide">
            <div
                v-if="showToast"
                class="absolute top-4 left-1/2 -translate-x-1/2 z-50 bg-gray-900/90 backdrop-blur-sm text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg flex items-center gap-2 border border-gray-700"
            >
                <span>{{ toastMessage }}</span>
            </div>
        </Transition>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 1. HEADER ATAS                                          -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center text-sm shadow-xs font-bold">
                    ⌨️
                </span>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-gray-800 leading-tight">
                        Papan Ketik Aksara Sunda
                    </h3>
                    <p class="text-[11px] text-gray-500 font-medium">
                        Ketik aksara ngalagena, swara, & rarangèn dengan mudah
                    </p>
                </div>
            </div>

            <!-- Header Action Icons -->
            <div class="flex items-center gap-1.5">
                <!-- Info Button -->
                <button
                    @click="showInfoModal = !showInfoModal"
                    class="w-8 h-8 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center text-xs font-bold transition-all cursor-pointer"
                    title="Petunjuk Penggunaan"
                >
                    ⓘ
                </button>
                <!-- Clear / Close Button -->
                <button
                    @click="clearAll"
                    class="w-8 h-8 rounded-xl bg-gray-50 hover:bg-red-50 text-gray-500 hover:text-red-600 border border-gray-200 flex items-center justify-center text-xs font-bold transition-all cursor-pointer"
                    title="Bersihkan Teks"
                >
                    ✕
                </button>
            </div>
        </div>

        <!-- Info Dropdown Banner -->
        <div v-if="showInfoModal" class="px-5 py-3 bg-amber-50/80 border-b border-amber-200 text-xs text-amber-900 leading-relaxed flex items-start gap-2.5">
            <span class="text-base shrink-0 mt-0.5">💡</span>
            <div class="flex-1">
                <p class="font-bold mb-0.5">Cara Penggabungan Rarangèn:</p>
                <p class="text-amber-800 text-[11px]">
                    Ketik aksara konsonan terlebih dahulu (misal: <strong>ᮊ</strong> / Ka), lalu tekan tanda <strong>Rarangèn</strong> di baris atas (misal: <strong>◌ᮤ</strong> / Panghulu) untuk mengubah menjadi <strong>ᮊᮤ</strong> (Ki).
                </p>
            </div>
            <button @click="showInfoModal = false" class="text-amber-700 hover:text-amber-900 font-bold px-1.5">✕</button>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 2. TEXTAREA INPUT AREA                                  -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="p-4 sm:p-5 bg-gray-50/50">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs focus-within:border-orange-400 focus-within:ring-2 focus-within:ring-orange-100 transition-all p-3 sm:p-4 relative">
                
                <!-- Native Textarea Input -->
                <textarea
                    v-model="typedText"
                    rows="3"
                    placeholder="Ketik ka, ki, ku..."
                    class="w-full bg-transparent resize-none focus:outline-none text-xl sm:text-2xl font-bold text-gray-800 placeholder-gray-400 tracking-wider leading-relaxed"
                ></textarea>

                <!-- Bottom Row Inside Textarea: Transliteration & Action Buttons -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5 pt-2 border-t border-gray-100 mt-2">
                    
                    <!-- Transliteration Preview Badge -->
                    <div class="flex flex-wrap items-center gap-1.5 min-h-[24px]">
                        <span v-if="currentTransliteration" class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-orange-50 text-[#FF4D30] rounded-lg text-xs font-extrabold border border-orange-200">
                            <span class="text-[10px] uppercase text-orange-600 font-bold">Latin:</span>
                            <span class="font-mono tracking-wide">{{ currentTransliteration }}</span>
                        </span>
                        <span v-if="activePronunciation" class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-extrabold border border-purple-200 animate-pulse">
                            <span>🔊</span>
                            <span>"{{ activePronunciation }}"</span>
                        </span>
                    </div>

                    <!-- Action Icons: Audio, Copy, Share -->
                    <div class="flex items-center gap-1.5 self-end sm:self-auto">
                        <!-- Play TTS Button -->
                        <button
                            v-if="typedText.trim()"
                            @click="speakAll"
                            class="px-2.5 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold flex items-center gap-1 transition-all cursor-pointer shadow-2xs active:scale-95"
                            title="Dengarkan Pelafalan"
                        >
                            <span>🔊</span>
                            <span class="hidden xs:inline">Dengar</span>
                        </button>

                        <!-- Copy Button -->
                        <button
                            @click="copyToClipboard"
                            class="px-2.5 py-1.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold flex items-center gap-1 transition-all cursor-pointer shadow-2xs active:scale-95"
                            title="Salin Teks"
                        >
                            <span>📋</span>
                            <span class="hidden xs:inline">Salin</span>
                        </button>

                        <!-- Share Button -->
                        <button
                            @click="shareText"
                            class="px-2.5 py-1.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold flex items-center gap-1 transition-all cursor-pointer shadow-2xs active:scale-95"
                            title="Bagikan Teks"
                        >
                            <span>📤</span>
                            <span class="hidden xs:inline">Bagikan</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- 3. TATA LETAK GRID KEYBOARD MODERN (5 BARIS)            -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="p-2 sm:p-4 bg-gray-100/90 border-t border-gray-200 select-none">
            <div class="space-y-1.5 sm:space-y-2">

                <!-- ──── BARIS 1: RARANGÈN (VOKALISASI DENGAN LINGKARAN TITIK-TITIK) ──── -->
                <div class="grid grid-cols-10 gap-1 sm:gap-1.5">
                    <button
                        v-for="rKey in row1Rarangen"
                        :key="rKey.name"
                        @click="pressRarangen(rKey)"
                        class="h-12 sm:h-14 bg-white hover:bg-amber-50/80 active:bg-amber-100 text-gray-800 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex flex-col items-center justify-center shadow-xs transition-all cursor-pointer p-0.5 group"
                        :title="rKey.name + ' (' + rKey.latin + ')'"
                    >
                        <span class="text-base sm:text-xl font-bold text-amber-950 group-hover:scale-105 transition-transform leading-tight">
                            {{ rKey.display }}
                        </span>
                        <span class="text-[9px] sm:text-[10px] font-bold text-amber-700/80 uppercase -mt-0.5">
                            {{ rKey.latin }}
                        </span>
                    </button>
                </div>

                <!-- ──── BARIS 2: AKSARA SWARA & NGALAGENA 1 ──── -->
                <div class="grid grid-cols-10 gap-1 sm:gap-1.5">
                    <button
                        v-for="key in row2Keys"
                        :key="key.char"
                        @click="pressKey(key)"
                        class="h-12 sm:h-14 bg-white hover:bg-orange-50/60 active:bg-orange-100 text-gray-800 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex flex-col items-center justify-center shadow-xs transition-all cursor-pointer p-0.5 group"
                    >
                        <span class="text-base sm:text-xl font-bold text-gray-900 group-hover:scale-105 transition-transform leading-tight">
                            {{ key.char }}
                        </span>
                        <span class="text-[9px] sm:text-[10px] font-medium text-gray-400 uppercase -mt-0.5">
                            {{ key.latin }}
                        </span>
                    </button>
                </div>

                <!-- ──── BARIS 3: NGALAGENA 2 ──── -->
                <div class="grid grid-cols-10 gap-1 sm:gap-1.5">
                    <button
                        v-for="key in row3Keys"
                        :key="key.char"
                        @click="pressKey(key)"
                        class="h-12 sm:h-14 bg-white hover:bg-orange-50/60 active:bg-orange-100 text-gray-800 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex flex-col items-center justify-center shadow-xs transition-all cursor-pointer p-0.5 group"
                    >
                        <span class="text-base sm:text-xl font-bold text-gray-900 group-hover:scale-105 transition-transform leading-tight">
                            {{ key.char }}
                        </span>
                        <span class="text-[9px] sm:text-[10px] font-medium text-gray-400 uppercase -mt-0.5">
                            {{ key.latin }}
                        </span>
                    </button>
                </div>

                <!-- ──── BARIS 4: NGALAGENA 3 + SISIPAN + TOMBOL DEL (POJOK KANAN SOFT RED) ──── -->
                <div class="grid grid-cols-10 gap-1 sm:gap-1.5">
                    <!-- Konsonan & Sisipan (9 Kolom) -->
                    <button
                        v-for="key in row4Keys"
                        :key="key.char"
                        @click="key.type === 'medial' ? pressRarangen(key) : pressKey(key)"
                        class="h-12 sm:h-14 bg-white hover:bg-orange-50/60 active:bg-orange-100 text-gray-800 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex flex-col items-center justify-center shadow-xs transition-all cursor-pointer p-0.5 group"
                    >
                        <span class="text-base sm:text-xl font-bold text-gray-900 group-hover:scale-105 transition-transform leading-tight">
                            {{ key.display || key.char }}
                        </span>
                        <span class="text-[9px] sm:text-[10px] font-medium text-gray-400 uppercase -mt-0.5">
                            {{ key.latin }}
                        </span>
                    </button>

                    <!-- Tombol Del (Backspace) Soft Red -->
                    <button
                        @click="backspace"
                        class="h-12 sm:h-14 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-600 border border-rose-200 border-b-2 border-b-rose-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex flex-col items-center justify-center shadow-xs transition-all cursor-pointer p-0.5 font-bold group"
                        title="Hapus Karakter Terakhir (Del)"
                    >
                        <span class="text-base sm:text-lg group-hover:scale-110 transition-transform">⌫</span>
                        <span class="text-[9px] sm:text-[10px] font-extrabold uppercase -mt-0.5">Del</span>
                    </button>
                </div>

                <!-- ──── BARIS 5: TANDA BACA, SPACE (SOFT PINK), PEMISAH, ENTER ──── -->
                <div class="grid grid-cols-10 gap-1 sm:gap-1.5">
                    <!-- Tombol Titik (.) -->
                    <button
                        @click="addChar('.')"
                        class="h-11 sm:h-13 bg-white hover:bg-gray-100 active:bg-gray-200 text-gray-700 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex items-center justify-center font-bold text-lg shadow-xs transition-all cursor-pointer"
                    >
                        .
                    </button>

                    <!-- Tombol Koma (,) -->
                    <button
                        @click="addChar(',')"
                        class="h-11 sm:h-13 bg-white hover:bg-gray-100 active:bg-gray-200 text-gray-700 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex items-center justify-center font-bold text-base shadow-xs transition-all cursor-pointer"
                    >
                        ,
                    </button>

                    <!-- Tombol SPACE Lebar (Soft Pink) - Mengambil 5 Kolom -->
                    <button
                        @click="addSpace"
                        class="col-span-5 h-11 sm:h-13 bg-pink-50 hover:bg-pink-100 active:bg-pink-200 text-pink-700 border border-pink-200 border-b-2 border-b-pink-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex items-center justify-center gap-1.5 font-extrabold text-xs sm:text-sm shadow-xs transition-all cursor-pointer tracking-wider"
                    >
                        <span>␣</span>
                        <span>SPACE</span>
                    </button>

                    <!-- Tombol Pemisah Sunda / Garis Batas (|) -->
                    <button
                        @click="addChar('|')"
                        class="h-11 sm:h-13 bg-white hover:bg-gray-100 active:bg-gray-200 text-gray-700 border border-gray-200 border-b-2 border-b-gray-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex items-center justify-center font-bold text-sm shadow-xs transition-all cursor-pointer"
                        title="Pemisah Angka / Pembatas Aksara Sunda"
                    >
                        |
                    </button>

                    <!-- Tombol Sisipkan / Enter (Pojok Kanan Bawah) - Mengambil 2 Kolom -->
                    <button
                        @click="addChar('\n')"
                        class="col-span-2 h-11 sm:h-13 bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 text-emerald-700 border border-emerald-200 border-b-2 border-b-emerald-300 active:border-b active:translate-y-0.5 rounded-lg sm:rounded-xl flex items-center justify-center gap-1 font-extrabold text-xs sm:text-sm shadow-xs transition-all cursor-pointer"
                        title="Baris Baru / Sisipkan"
                    >
                        <span>↵</span>
                        <span class="hidden xs:inline">Enter</span>
                    </button>
                </div>

            </div>
        </div>

    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.25s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translate(-50%, -10px);
}
</style>
