<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const selectedCategory = ref('semua');
const activeAudioWord = ref('');

const categories = [
    { id: 'semua', label: 'Semua Kosakata', icon: '✨' },
    { id: 'hewan', label: 'Sasatoan (Hewan)', icon: '🐱' },
    { id: 'tubuh', label: 'Anggota Awak (Tubuh)', icon: '👀' },
    { id: 'alam', label: 'Alam & Sabudeureun', icon: '🌳' },
    { id: 'sapaan', label: 'Bagea & Sopan Santun', icon: '👋' },
    { id: 'angka', label: 'Wilangan (Angka)', icon: '🔢' },
];

const dictionaryItems = [
    {
        id: 1,
        sundaLatin: 'Ucing',
        sundaAksara: 'ᮅᮎᮤᮀ',
        indonesia: 'Kucing',
        category: 'hewan',
        contoh: 'Ucing téh mani lucu pisan.',
        contohAksara: 'ᮅᮎᮤᮀ ᮒᮦᮂ ᮙᮔᮤ ᮜᮥᮎᮥ ᮕᮤᮞᮔ᮪.',
        emoji: '🐱',
    },
    {
        id: 2,
        sundaLatin: 'Hayam',
        sundaAksara: 'ᮠᮚᮙ᮪',
        indonesia: 'Ayam',
        category: 'hewan',
        contoh: 'Hayam jago kongkorongok subuh.',
        contohAksara: 'ᮠᮚᮙ᮪ ᮏᮌᮧ ᮊᮧᮀᮊᮧᮛᮧᮍᮧᮊ᮪ ᮞᮥᮘᮥᮂ.',
        emoji: '🐔',
    },
    {
        id: 3,
        sundaLatin: 'Panon',
        sundaAksara: 'ᮕᮔᮧᮔ᮪',
        indonesia: 'Mata',
        category: 'tubuh',
        contoh: 'Panon paranti ningal kaendahan alam.',
        contohAksara: 'ᮕᮔᮧᮔ᮪ ᮕᮛᮔ᮪ᮒᮤ ᮔᮤ8ᮜ᮪ ᮊᮈᮔ᮪ᮓᮠᮔ᮪ ᮃᮜᮙ᮪.',
        emoji: '👀',
    },
    {
        id: 4,
        sundaLatin: 'Leungeun',
        sundaAksara: 'ᮜᮩ8ᮩᮔ᮪',
        indonesia: 'Tangan',
        category: 'tubuh',
        contoh: 'Kudu wawasuh leungeun sateuacan emam.',
        contohAksara: 'ᮊᮥᮓᮥ ᮝᮝᮞᮥᮂ ᮜᮩ8ᮩᮔ᮪ ᮞᮒᮩᮃᮎᮔ᮪ ᮈᮙᮙ᮪.',
        emoji: '✋',
    },
    {
        id: 5,
        sundaLatin: 'Cai',
        sundaAksara: 'ᮎᮄ',
        indonesia: 'Air',
        category: 'alam',
        contoh: 'Cai herang dina pancuran.',
        contohAksara: 'ᮎᮄ ᮠᮦᮛᮀ ᮓᮤᮔ ᮕᮔ᮪ᮎᮥᮛᮔ᮪.',
        emoji: '💧',
    },
    {
        id: 6,
        sundaLatin: 'Gunung',
        sundaAksara: 'ᮌᮥᮔᮥᮀ',
        indonesia: 'Gunung',
        category: 'alam',
        contoh: 'Gunung Tangkuban Parahu éndah pisan.',
        contohAksara: 'ᮌᮥᮔᮥᮀ ᮒᮀᮊᮥᮘᮔ᮪ ᮕᮛᮠᮥ ᮈᮔ᮪ᮓᮂ ᮕᮤᮞᮔ᮪.',
        emoji: '⛰️',
    },
    {
        id: 7,
        sundaLatin: 'Hatur Nuhun',
        sundaAksara: 'ᮠᮒᮥᮁ ᮔᮥᮠᮥᮔ᮪',
        indonesia: 'Terima Kasih',
        category: 'sapaan',
        contoh: 'Hatur nuhun kana kasaéanana.',
        contohAksara: 'ᮠᮒᮥᮁ ᮔᮥᮠᮥᮔ᮪ ᮊᮔ ᮊᮞᮈᮃᮔᮔ.',
        emoji: '🙏',
    },
    {
        id: 8,
        sundaLatin: 'Sampurasun',
        sundaAksara: 'ᮞᮙ᮪ᮕᮥᮛᮞᮥᮔ᮪',
        indonesia: 'Salam Sapaan Sunda (Semoga Sempurna)',
        category: 'sapaan',
        contoh: 'Sampurasun! - Rampés!',
        contohAksara: 'ᮞᮙ᮪ᮕᮥᮛᮞᮥᮔ᮪! - ᮛᮙ᮪ᮕᮦᮞ᮪!',
        emoji: '👋',
    },
    {
        id: 9,
        sundaLatin: 'Hiji',
        sundaAksara: 'ᮠᮤᮏᮤ',
        indonesia: 'Satu (1 - ᮱)',
        category: 'angka',
        contoh: 'Abdi gaduh buku hiji.',
        contohAksara: 'ᮃᮘ᮪ᮓᮤ ᮌᮓᮥᮂ ᮘᮥᮊᮥ ᮠᮤᮏᮤ.',
        emoji: '1️⃣',
    },
    {
        id: 10,
        sundaLatin: 'Dua',
        sundaAksara: 'ᮓᮥᮃ',
        indonesia: 'Dua (2 - ᮲)',
        category: 'angka',
        contoh: 'Soca abdi aya dua.',
        contohAksara: 'ᮞᮧᮎ ᮃᮘ᮪ᮓᮤ ᮃᮚ ᮓᮥᮃ.',
        emoji: '2️⃣',
    },
];

const filteredItems = computed(() => {
    return dictionaryItems.filter(item => {
        const matchesCategory = selectedCategory.value === 'semua' || item.category === selectedCategory.value;
        const q = searchQuery.value.toLowerCase().trim();
        const matchesSearch = !q ||
            item.sundaLatin.toLowerCase().includes(q) ||
            item.indonesia.toLowerCase().includes(q) ||
            item.sundaAksara.includes(q);
        return matchesCategory && matchesSearch;
    });
});

function speak(text) {
    if (!text) return;
    activeAudioWord.value = text;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.2;
        window.speechSynthesis.speak(utterance);
    }
}
</script>

<template>
    <div class="space-y-6">
        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- BANNER HEADER KAMUS PLAYFUL & RAMAH ANAK                 -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="relative overflow-hidden bg-gradient-to-r from-amber-200 via-orange-200 to-yellow-200 rounded-3xl p-6 sm:p-8 border-4 border-amber-300 shadow-xl">
            <!-- Awan & Bintang Dekorasi Lucu -->
            <div class="absolute top-2 left-4 text-3xl opacity-60 animate-bounce select-none">☁️</div>
            <div class="absolute top-4 right-10 text-3xl opacity-60 select-none">✨</div>
            <div class="absolute bottom-2 right-4 text-4xl opacity-50 select-none">📖</div>
            <div class="absolute bottom-3 left-1/3 text-2xl opacity-40 select-none">🌟</div>

            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/90 backdrop-blur-sm text-amber-900 rounded-full text-xs font-black mb-3 border-2 border-amber-300 shadow-xs">
                    <span>📚 Kamus Bergambar & Bersuara</span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-amber-950 tracking-tight leading-snug">
                    Kamus Aksara Sunda
                </h1>
                
                <p class="text-xs sm:text-sm text-amber-900/90 font-bold mt-2 leading-relaxed">
                    Yuk cari kosakata bahasa Sunda, lihat tulisan aksaranya, dan dengarkan pelafalan suaranya yang seru! 🎨🔊
                </p>

                <!-- Search Box Interaktif -->
                <div class="mt-5 relative">
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-lg text-gray-400">🔍</span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari kata Sunda atau Indonesia (misal: Ucing, Air, Hatur nuhun)..."
                            class="w-full pl-11 pr-10 py-3.5 bg-white rounded-2xl border-3 border-amber-300 focus:border-[#FF4D30] focus:ring-4 focus:ring-orange-200 text-sm font-extrabold text-gray-800 placeholder-gray-400 shadow-md transition-all outline-none"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3.5 w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-xs font-bold transition"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- KATEGORI PILLS (FILTER CEPAT RAMAH ANAK)                -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            <button
                v-for="cat in categories"
                :key="cat.id"
                @click="selectedCategory = cat.id"
                class="shrink-0 px-3.5 py-2 rounded-2xl text-xs font-extrabold border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center gap-1.5"
                :class="selectedCategory === cat.id
                    ? 'bg-[#FF4D30] text-white border-orange-700 shadow-sm translate-y-0'
                    : 'bg-white text-gray-700 border-amber-200 hover:bg-amber-50 active:translate-y-0.5'"
            >
                <span>{{ cat.icon }}</span>
                <span>{{ cat.label }}</span>
            </button>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- GRID KARTU KAMUS SUNDA                                  -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div v-if="filteredItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            <div
                v-for="item in filteredItems"
                :key="item.id"
                class="bg-white rounded-3xl p-5 border-3 border-amber-200 hover:border-orange-400 shadow-md hover:shadow-lg transition-all duration-200 flex flex-col justify-between group"
            >
                <div>
                    <!-- Top Card Badge & Emoji -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-12 h-12 rounded-2xl bg-amber-50 border-2 border-amber-200 flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform">
                            {{ item.emoji }}
                        </span>
                        
                        <button
                            @click="speak(item.sundaLatin)"
                            class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-extrabold text-xs flex items-center gap-1 shadow-xs active:translate-y-0.5 transition cursor-pointer"
                            title="Dengarkan Suara"
                        >
                            <span>🔊</span>
                            <span>Dengar</span>
                        </button>
                    </div>

                    <!-- Aksara Sunda Display -->
                    <div class="bg-amber-50/60 rounded-2xl p-3 border-2 border-dashed border-amber-200 text-center mb-3">
                        <span class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-wider">
                            {{ item.sundaAksara }}
                        </span>
                    </div>

                    <!-- Latin & Artinya -->
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-1.5">
                        <span>{{ item.sundaLatin }}</span>
                    </h3>
                    <p class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg inline-block mt-1 border border-emerald-200">
                        🇮🇩 {{ item.indonesia }}
                    </p>
                </div>

                <!-- Contoh Kalimat -->
                <div class="mt-4 pt-3 border-t border-amber-100 text-xs text-gray-600 font-medium">
                    <p class="font-bold text-gray-700 text-[11px] mb-0.5">Contoh Kalimat:</p>
                    <p class="italic text-gray-800 font-semibold leading-relaxed">"{{ item.contoh }}"</p>
                </div>
            </div>
        </div>

        <!-- Empty State Search -->
        <div v-else class="bg-white rounded-3xl p-8 border-3 border-dashed border-amber-300 text-center">
            <span class="text-5xl block mb-2">🔍</span>
            <h3 class="text-base font-extrabold text-gray-800">Kosakata Tidak Ditemukan</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Coba kata kunci lain atau pilih kategori yang berbeda ya!</p>
            <button
                @click="searchQuery = ''; selectedCategory = 'semua'"
                class="mt-4 px-4 py-2 bg-[#FF4D30] text-white text-xs font-extrabold rounded-xl border-b-2 border-orange-700 cursor-pointer"
            >
                Reset Pencarian
            </button>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
