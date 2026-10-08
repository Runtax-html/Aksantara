<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    levelId: { type: Number, default: 1 },
    levelTitle: { type: String, default: '' },
});

const emit = defineEmits(['close', 'levelCompleted', 'xpGained']);

// ═══════════════════════════════════════════════════════════════════
// QUIZ STATE
// ═══════════════════════════════════════════════════════════════════
const currentQuestionIndex = ref(0);
const selectedAnswer = ref(null);
const isAnswered = ref(false);
const isCorrect = ref(false);
const score = ref(0);
const quizFinished = ref(false);
const showConfetti = ref(false);
const hearts = ref(3); // nyawa/kesempatan

// Reset quiz state when level changes or modal opens
watch(() => [props.show, props.levelId], () => {
    if (props.show) {
        resetQuiz();
    }
});

function resetQuiz() {
    currentQuestionIndex.value = 0;
    selectedAnswer.value = null;
    isAnswered.value = false;
    isCorrect.value = false;
    score.value = 0;
    quizFinished.value = false;
    showConfetti.value = false;
    hearts.value = 3;
}

// ═══════════════════════════════════════════════════════════════════
// QUIZ QUESTIONS DATABASE - LEVEL 1 to 6
// ═══════════════════════════════════════════════════════════════════

const allQuestions = {
    // ─────────────────────────────────────────────────────────
    // LEVEL 1: Swara Aksara Sunda (a, i, u, e, o, eu)
    // ─────────────────────────────────────────────────────────
    1: [
        {
            question: 'Huruf Aksara Sunda "ᮃ" dibaca apa?',
            options: ['a', 'i', 'u', 'o'],
            correctIndex: 0,
            explanation: 'Huruf ᮃ adalah aksara Swara yang dibaca "a". Swara artinya huruf vokal dalam Aksara Sunda.',
            emoji: '🔤',
        },
        {
            question: 'Mana huruf Aksara Sunda yang dibaca "i"?',
            options: ['ᮃ', 'ᮄ', 'ᮅ', 'ᮈ'],
            correctIndex: 1,
            explanation: 'Huruf ᮄ adalah aksara Swara "i". Bentuknya mirip seperti garis melengkung ke atas.',
            emoji: '👀',
        },
        {
            question: 'Huruf Aksara Sunda "ᮅ" dibaca apa?',
            options: ['e', 'o', 'u', 'a'],
            correctIndex: 2,
            explanation: 'Huruf ᮅ dibaca "u". Ingat ya, huruf vokal Sunda itu ada 7: a, i, u, é, e, o, eu!',
            emoji: '📖',
        },
        {
            question: 'Aksara Sunda "ᮈ" melambangkan bunyi apa?',
            options: ['a', 'u', 'o', 'é'],
            correctIndex: 3,
            explanation: 'Huruf ᮈ dibaca "é" (seperti bunyi "e" pada kata "bebek"). Ini berbeda dengan "e" pepet ya!',
            emoji: '🎯',
        },
        {
            question: 'Huruf Aksara Sunda "ᮇ" dibaca apa?',
            options: ['u', 'e', 'o', 'eu'],
            correctIndex: 2,
            explanation: 'Huruf ᮇ adalah aksara Swara yang dibaca "o", seperti bunyi "o" pada kata "obat".',
            emoji: '⭐',
        },
        {
            question: 'Ada berapa huruf vokal dasar (Swara) dalam Aksara Sunda?',
            options: ['5 huruf', '7 huruf', '3 huruf', '10 huruf'],
            correctIndex: 1,
            explanation: 'Aksara Sunda punya 7 huruf vokal (Swara): a (ᮃ), i (ᮄ), u (ᮅ), é (ᮈ), o (ᮇ), e (ᮉ), eu (ᮩ).',
            emoji: '🧠',
        },
        {
            question: 'Huruf vokal Sunda disebut apa?',
            options: ['Ngalagena', 'Swara', 'Rarangkén', 'Angka'],
            correctIndex: 1,
            explanation: '"Swara" artinya "suara" — yaitu huruf-huruf vokal dasar dalam Aksara Sunda. Kalau konsonan namanya "Ngalagena".',
            emoji: '💡',
        },
        {
            question: 'Manakah yang BUKAN huruf Swara (vokal) Aksara Sunda?',
            options: ['ᮃ (a)', 'ᮊ (ka)', 'ᮄ (i)', 'ᮅ (u)'],
            correctIndex: 1,
            explanation: 'ᮊ (ka) bukan Swara, melainkan Ngalagena (konsonan). Swara itu huruf vokal seperti ᮃ, ᮄ, ᮅ.',
            emoji: '🔍',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 2: Ngalagena Dasar 1 (ka, ga, nga, ca, ja, nya)
    // ─────────────────────────────────────────────────────────
    2: [
        {
            question: 'Huruf Aksara Sunda "ᮊ" dibaca apa?',
            options: ['ga', 'ka', 'ca', 'ja'],
            correctIndex: 1,
            explanation: 'Huruf ᮊ dibaca "ka". Ini adalah huruf konsonan (Ngalagena) pertama dalam Aksara Sunda!',
            emoji: '🎮',
        },
        {
            question: 'Mana huruf Aksara Sunda yang dibaca "ga"?',
            options: ['ᮊ', 'ᮎ', 'ᮌ', 'ᮏ'],
            correctIndex: 2,
            explanation: 'Huruf ᮌ dibaca "ga". Perhatikan baik-baik bentuknya ya, mirip tapi beda sama "ka" (ᮊ)!',
            emoji: '👁️',
        },
        {
            question: 'Aksara Sunda "ᮍ" melambangkan bunyi apa?',
            options: ['na', 'nya', 'nga', 'ma'],
            correctIndex: 2,
            explanation: 'Huruf ᮍ dibaca "nga". Bunyinya seperti "ng" pada kata "ngarit" dalam bahasa Sunda.',
            emoji: '🗣️',
        },
        {
            question: 'Huruf Aksara Sunda "ᮎ" dibaca apa?',
            options: ['ja', 'ka', 'ca', 'nya'],
            correctIndex: 2,
            explanation: 'Huruf ᮎ dibaca "ca", seperti bunyi "c" pada kata "carita" (cerita dalam bahasa Sunda).',
            emoji: '📚',
        },
        {
            question: 'Mana huruf Aksara Sunda yang dibaca "ja"?',
            options: ['ᮎ', 'ᮌ', 'ᮊ', 'ᮏ'],
            correctIndex: 3,
            explanation: 'Huruf ᮏ dibaca "ja", seperti bunyi "j" pada kata "jalan".',
            emoji: '🏃',
        },
        {
            question: 'Aksara Sunda "ᮑ" melambangkan bunyi apa?',
            options: ['na', 'nga', 'ma', 'nya'],
            correctIndex: 3,
            explanation: 'Huruf ᮑ dibaca "nya", seperti bunyi "ny" pada kata "nyanyian".',
            emoji: '🎵',
        },
        {
            question: 'Huruf konsonan dalam Aksara Sunda disebut apa?',
            options: ['Swara', 'Rarangkén', 'Ngalagena', 'Panghulu'],
            correctIndex: 2,
            explanation: '"Ngalagena" artinya huruf konsonan dalam Aksara Sunda. Kalau huruf vokal disebut "Swara".',
            emoji: '💡',
        },
        {
            question: 'Pasangan huruf mana yang benar?',
            options: ['ᮊ = ga', 'ᮌ = ka', 'ᮎ = ca', 'ᮏ = nga'],
            correctIndex: 2,
            explanation: 'ᮎ = ca itu benar! Ingat: ᮊ = ka, ᮌ = ga, ᮍ = nga, ᮎ = ca, ᮏ = ja, ᮑ = nya.',
            emoji: '✅',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 3: Ngalagena Dasar 2 (ta, da, na, pa, ba, ma)
    // ─────────────────────────────────────────────────────────
    3: [
        {
            question: 'Huruf Aksara Sunda "ᮒ" dibaca apa?',
            options: ['da', 'na', 'ta', 'pa'],
            correctIndex: 2,
            explanation: 'Huruf ᮒ dibaca "ta", seperti bunyi "t" pada kata "tahu".',
            emoji: '🔤',
        },
        {
            question: 'Mana huruf Aksara Sunda yang dibaca "da"?',
            options: ['ᮒ', 'ᮓ', 'ᮔ', 'ᮕ'],
            correctIndex: 1,
            explanation: 'Huruf ᮓ dibaca "da", seperti bunyi "d" pada kata "daun".',
            emoji: '🍃',
        },
        {
            question: 'Aksara Sunda "ᮔ" melambangkan bunyi apa?',
            options: ['ma', 'ba', 'pa', 'na'],
            correctIndex: 3,
            explanation: 'Huruf ᮔ dibaca "na", seperti bunyi "n" pada kata "nasi".',
            emoji: '🍚',
        },
        {
            question: 'Huruf Aksara Sunda "ᮕ" dibaca apa?',
            options: ['ba', 'pa', 'ma', 'ta'],
            correctIndex: 1,
            explanation: 'Huruf ᮕ dibaca "pa", seperti bunyi "p" pada kata "pasar".',
            emoji: '🏪',
        },
        {
            question: 'Mana huruf Aksara Sunda yang dibaca "ba"?',
            options: ['ᮕ', 'ᮙ', 'ᮘ', 'ᮒ'],
            correctIndex: 2,
            explanation: 'Huruf ᮘ dibaca "ba", seperti bunyi "b" pada kata "baju".',
            emoji: '👕',
        },
        {
            question: 'Aksara Sunda "ᮙ" melambangkan bunyi apa?',
            options: ['na', 'pa', 'ba', 'ma'],
            correctIndex: 3,
            explanation: 'Huruf ᮙ dibaca "ma", seperti bunyi "m" pada kata "makan".',
            emoji: '🍽️',
        },
        {
            question: 'Susun huruf Aksara Sunda ini: ᮔ-ᮃ-ᮞ-ᮄ. Apa bacaannya?',
            options: ['nasi', 'padi', 'sapi', 'nami'],
            correctIndex: 0,
            explanation: 'ᮔ = na, ᮃ = a, ᮞ = sa, ᮄ = i. Kalau digabung jadi: na-a-sa-i = "nasi" 🍚!',
            emoji: '🧩',
        },
        {
            question: 'Pasangan huruf mana yang SALAH?',
            options: ['ᮒ = ta', 'ᮓ = da', 'ᮕ = ba', 'ᮙ = ma'],
            correctIndex: 2,
            explanation: 'ᮕ = pa, bukan ba! Huruf "ba" yang benar itu ᮘ. Perhatikan bentuknya ya!',
            emoji: '❌',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 4: Rarangkén Vokal (Panghulu, Pamepet, Paneuleung, dll)
    // ─────────────────────────────────────────────────────────
    4: [
        {
            question: 'Apa fungsi Rarangkén dalam Aksara Sunda?',
            options: [
                'Mengubah bentuk huruf',
                'Mengubah bunyi vokal huruf konsonan',
                'Menambah angka',
                'Menghapus huruf',
            ],
            correctIndex: 1,
            explanation: 'Rarangkén (tanda vokalisasi) berfungsi mengubah bunyi vokal bawaan huruf Ngalagena. Misalnya "ka" jadi "ki" atau "ku".',
            emoji: '✨',
        },
        {
            question: 'Rarangkén "Panghulu" (◌ᮤ) mengubah vokal menjadi apa?',
            options: ['u', 'o', 'i', 'é'],
            correctIndex: 2,
            explanation: 'Panghulu (◌ᮤ) mengubah vokal menjadi "i". Contoh: ᮊ (ka) + ◌ᮤ = ᮊᮤ (ki).',
            emoji: '📝',
        },
        {
            question: 'Kalau huruf ᮊ (ka) ditambah Panyuku (◌ᮥ), jadi bunyi apa?',
            options: ['ki', 'ko', 'ku', 'ké'],
            correctIndex: 2,
            explanation: 'Panyuku (◌ᮥ) mengubah vokal jadi "u". Jadi ᮊ + ᮥ = ᮊᮥ, dibaca "ku".',
            emoji: '🎯',
        },
        {
            question: 'Rarangkén "Panéléng" (◌ᮦ) mengubah vokal menjadi apa?',
            options: ['e pepet', 'eu', 'o', 'é'],
            correctIndex: 3,
            explanation: 'Panéléng (◌ᮦ) mengubah vokal menjadi "é" (e terbuka, seperti pada kata "bébék").',
            emoji: '🦆',
        },
        {
            question: 'Rarangkén apa yang mengubah vokal menjadi "o"?',
            options: ['Panghulu', 'Pamepet', 'Panolong', 'Paneuleung'],
            correctIndex: 2,
            explanation: 'Panolong (◌ᮧ) mengubah vokal menjadi "o". Contoh: ᮊ + ᮧ = ᮊᮧ, dibaca "ko".',
            emoji: '🔊',
        },
        {
            question: 'Apa bunyi dari aksara ini: ᮕᮨ?',
            options: ['pa', 'pu', 'pe', 'po'],
            correctIndex: 2,
            explanation: 'ᮕ = "pa", ditambah Pamepet (◌ᮨ) yang mengubah vokal jadi "e" pepet. Hasilnya: "pe".',
            emoji: '👂',
        },
        {
            question: 'Rarangkén "Paneuleung" (◌ᮩ) mengubah vokal menjadi bunyi apa?',
            options: ['e', 'o', 'eu', 'i'],
            correctIndex: 2,
            explanation: 'Paneuleung (◌ᮩ) mengubah vokal jadi "eu", bunyi khas bahasa Sunda seperti pada kata "deui" (lagi).',
            emoji: '🌟',
        },
        {
            question: 'Apa bunyi dari aksara ini: ᮙᮤ?',
            options: ['mu', 'ma', 'mi', 'mo'],
            correctIndex: 2,
            explanation: 'ᮙ = "ma", ditambah Panghulu (◌ᮤ) jadi "mi". Contoh kata: "milu" (ikut).',
            emoji: '🎓',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 5: Angka Sunda (Angka 1 - 10)
    // ─────────────────────────────────────────────────────────
    5: [
        {
            question: 'Angka Sunda "᮱" melambangkan angka berapa?',
            options: ['2', '3', '1', '4'],
            correctIndex: 2,
            explanation: 'Simbol ᮱ adalah angka Sunda untuk angka 1. Angka Sunda mirip angka biasa tapi bentuknya berbeda!',
            emoji: '1️⃣',
        },
        {
            question: 'Mana angka Sunda yang melambangkan angka 2?',
            options: ['᮱', '᮲', '᮳', '᮴'],
            correctIndex: 1,
            explanation: 'Simbol ᮲ adalah angka Sunda untuk angka 2.',
            emoji: '2️⃣',
        },
        {
            question: 'Angka Sunda "᮳" melambangkan angka berapa?',
            options: ['1', '4', '3', '5'],
            correctIndex: 2,
            explanation: 'Simbol ᮳ adalah angka Sunda untuk angka 3.',
            emoji: '3️⃣',
        },
        {
            question: 'Angka Sunda "᮵" melambangkan angka berapa?',
            options: ['4', '5', '6', '3'],
            correctIndex: 1,
            explanation: 'Simbol ᮵ adalah angka Sunda untuk angka 5. Lima jari tangan! ✋',
            emoji: '5️⃣',
        },
        {
            question: 'Mana angka Sunda yang melambangkan angka 7?',
            options: ['᮶', '᮷', '᮸', '᮹'],
            correctIndex: 1,
            explanation: 'Simbol ᮷ adalah angka Sunda untuk angka 7.',
            emoji: '7️⃣',
        },
        {
            question: 'Angka Sunda "᮰" melambangkan angka berapa?',
            options: ['1', '10', '0', '9'],
            correctIndex: 2,
            explanation: 'Simbol ᮰ adalah angka Sunda untuk angka 0 (nol). Angka 10 ditulis ᮱᮰ (satu-nol)!',
            emoji: '0️⃣',
        },
        {
            question: 'Bagaimana menulis angka 10 dalam Angka Sunda?',
            options: ['᮱᮰', '᮲᮰', '᮱᮱', '᮰᮱'],
            correctIndex: 0,
            explanation: 'Angka 10 ditulis ᮱᮰ (angka 1 diikuti angka 0), sama seperti konsep angka desimal!',
            emoji: '🔟',
        },
        {
            question: 'Urutan angka Sunda yang benar dari 1 sampai 5 adalah?',
            options: [
                '᮰ ᮱ ᮲ ᮳ ᮴',
                '᮱ ᮲ ᮳ ᮴ ᮵',
                '᮲ ᮳ ᮴ ᮵ ᮶',
                '᮱ ᮳ ᮵ ᮷ ᮹',
            ],
            correctIndex: 1,
            explanation: 'Urutan yang benar: ᮱(1) ᮲(2) ᮳(3) ᮴(4) ᮵(5). Perhatikan polanya, mirip angka Arab tapi bentuknya khas Sunda!',
            emoji: '🏆',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 6: Ujian Jawara Aksara (Bos Final — Campuran Semua)
    // ─────────────────────────────────────────────────────────
    6: [
        {
            question: 'Huruf Aksara Sunda ᮃ, ᮄ, ᮅ termasuk kelompok apa?',
            options: ['Ngalagena', 'Rarangkén', 'Angka', 'Swara'],
            correctIndex: 3,
            explanation: 'ᮃ (a), ᮄ (i), ᮅ (u) adalah huruf Swara (vokal). Swara = suara/vokal.',
            emoji: '🧠',
        },
        {
            question: 'Apa bunyi dari aksara ini: ᮞᮥᮔ᮪ᮓ?',
            options: ['suda', 'sunda', 'sanda', 'sinda'],
            correctIndex: 1,
            explanation: 'ᮞ=sa, +ᮥ=u → "su", ᮔ=na, +᮪(pamaéh)= mati → "n", ᮓ=da → "da". Jadi: su-n-da = "Sunda"! 🎉',
            emoji: '🌟',
        },
        {
            question: 'Rarangkén apa yang membuat huruf konsonan jadi "mati" (tanpa vokal)?',
            options: ['Panghulu', 'Panolong', 'Pamaéh', 'Pamepet'],
            correctIndex: 2,
            explanation: 'Pamaéh (◌᮪) berfungsi sebagai "pembunuh vokal" — membuat huruf Ngalagena kehilangan vokalnya. Contoh: ᮔ᮪ = "n" (bukan "na").',
            emoji: '🔇',
        },
        {
            question: 'Manakah cara menulis kata "bapa" yang BENAR dalam Aksara Sunda?',
            options: ['ᮘᮕ', 'ᮘᮃᮕᮃ', 'ᮘᮙ', 'ᮕᮘ'],
            correctIndex: 0,
            explanation: 'ᮘ sudah berbunyi "ba" dan ᮕ sudah berbunyi "pa". Jadi ᮘᮕ = ba-pa = "bapa". Tidak perlu menambahkan huruf Swara karena Ngalagena sudah mengandung vokal "a"!',
            emoji: '👨',
        },
        {
            question: 'Berapa jumlah angka Sunda ᮳ + ᮴?',
            options: ['5', '6', '7', '8'],
            correctIndex: 2,
            explanation: '᮳ = 3 dan ᮴ = 4. Jadi 3 + 4 = 7! Dalam angka Sunda, 7 ditulis ᮷.',
            emoji: '🧮',
        },
        {
            question: 'Apa bunyi dari aksara ini: ᮌᮩ?',
            options: ['gu', 'go', 'geu', 'gi'],
            correctIndex: 2,
            explanation: 'ᮌ = "ga", ditambah Paneuleung (◌ᮩ) → vokal berubah jadi "eu". Hasilnya: "geu".',
            emoji: '🔤',
        },
        {
            question: 'Huruf konsonan Aksara Sunda ada berapa jumlahnya?',
            options: ['7 huruf', '18 huruf', '10 huruf', '26 huruf'],
            correctIndex: 1,
            explanation: 'Aksara Sunda memiliki 18 huruf Ngalagena (konsonan): ka, ga, nga, ca, ja, nya, ta, da, na, pa, ba, ma, sa, ha, ya, ra, la, wa.',
            emoji: '📊',
        },
        {
            question: 'Manakah pasangan Rarangkén dan bunyi yang BENAR?',
            options: [
                'Panghulu = o',
                'Panolong = i',
                'Pamepet = e',
                'Paneuleung = é',
            ],
            correctIndex: 2,
            explanation: 'Pamepet (◌ᮨ) = e (pepet). Yang benar: Panghulu=i, Panolong=o, Pamepet=e, Paneuleung=eu, Panéléng=é.',
            emoji: '✅',
        },
        {
            question: 'Selamat! Pertanyaan terakhir: Apa arti kata "Aksantara"?',
            options: [
                'Huruf nusantara',
                'Aksara + Nusantara',
                'Cerita rakyat',
                'Bahasa kuno',
            ],
            correctIndex: 1,
            explanation: 'Aksantara = "Aksara" + "Nusantara"! Aplikasi ini dibuat untuk melestarikan aksara-aksara tradisional Nusantara, khususnya Aksara Sunda. Kamu sudah jadi Jawara Aksara! 🏆👑',
            emoji: '👑',
        },
    ],
};

// ═══════════════════════════════════════════════════════════════════
// COMPUTED & METHODS
// ═══════════════════════════════════════════════════════════════════

const currentQuestions = computed(() => {
    return allQuestions[props.levelId] || [];
});

const currentQuestion = computed(() => {
    return currentQuestions.value[currentQuestionIndex.value] || null;
});

const totalQuestions = computed(() => currentQuestions.value.length);

const progressPercent = computed(() => {
    if (totalQuestions.value === 0) return 0;
    return Math.round((currentQuestionIndex.value / totalQuestions.value) * 100);
});

const isPassed = computed(() => {
    if (totalQuestions.value === 0) return false;
    return (score.value / totalQuestions.value) >= 0.7; // 70% to pass
});

const starsEarned = computed(() => {
    if (totalQuestions.value === 0) return 0;
    const ratio = score.value / totalQuestions.value;
    if (ratio >= 0.95) return 3;
    if (ratio >= 0.8) return 2;
    if (ratio >= 0.7) return 1;
    return 0;
});

const xpReward = computed(() => {
    const baseXP = props.levelId * 15;
    return baseXP + (starsEarned.value * 10);
});

function selectAnswer(index) {
    if (isAnswered.value) return;
    
    selectedAnswer.value = index;
    isAnswered.value = true;
    isCorrect.value = index === currentQuestion.value.correctIndex;
    
    if (isCorrect.value) {
        score.value++;
    } else {
        hearts.value = Math.max(0, hearts.value - 1);
    }
}

function nextQuestion() {
    if (currentQuestionIndex.value < totalQuestions.value - 1) {
        currentQuestionIndex.value++;
        selectedAnswer.value = null;
        isAnswered.value = false;
        isCorrect.value = false;
    } else {
        quizFinished.value = true;
        if (isPassed.value) {
            showConfetti.value = true;
            emit('xpGained', xpReward.value);
            emit('levelCompleted', {
                levelId: props.levelId,
                stars: starsEarned.value,
                score: score.value,
                total: totalQuestions.value,
                xp: xpReward.value,
            });
            setTimeout(() => { showConfetti.value = false; }, 4000);
        }
    }
}

function retryQuiz() {
    resetQuiz();
}

function closeModal() {
    emit('close');
}

function getOptionLetter(index) {
    return ['A', 'B', 'C', 'D'][index];
}
</script>

<template>
    <!-- Modal Overlay -->
    <Teleport to="body">
        <Transition name="modal-fade">
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6"
                @click.self="closeModal"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

                <!-- Confetti Layer -->
                <div v-if="showConfetti" class="absolute inset-0 pointer-events-none z-[110] overflow-hidden">
                    <div v-for="i in 40" :key="i"
                        class="absolute animate-confetti-fall"
                        :style="{
                            left: Math.random() * 100 + '%',
                            animationDelay: Math.random() * 2 + 's',
                            animationDuration: (2 + Math.random() * 3) + 's',
                            fontSize: (12 + Math.random() * 16) + 'px',
                        }"
                    >
                        {{ ['🎉','⭐','🌟','🎊','✨','🏆','💫','🎯'][Math.floor(Math.random() * 8)] }}
                    </div>
                </div>

                <!-- Modal Card -->
                <div class="relative z-[105] w-full max-w-lg max-h-[92vh] overflow-y-auto bg-white rounded-3xl border-4 border-orange-300 shadow-2xl">

                    <!-- ═══ HEADER ═══ -->
                    <div class="sticky top-0 bg-gradient-to-r from-[#FF4D30] to-orange-500 p-4 sm:p-5 rounded-t-[22px] z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="text-2xl sm:text-3xl p-1.5 bg-white/20 rounded-2xl">📝</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-extrabold text-white leading-tight">
                                        {{ levelTitle || `Level ${levelId}` }}
                                    </h2>
                                    <p v-if="!quizFinished" class="text-[11px] text-orange-100 font-bold">
                                        Soal {{ currentQuestionIndex + 1 }} dari {{ totalQuestions }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Hearts -->
                                <div class="flex items-center gap-0.5 bg-white/20 px-2 py-1 rounded-xl">
                                    <span v-for="h in 3" :key="h" class="text-sm" :class="h <= hearts ? '' : 'grayscale opacity-40'">
                                        ❤️
                                    </span>
                                </div>
                                <!-- Close -->
                                <button
                                    @click="closeModal"
                                    class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white font-extrabold text-sm transition cursor-pointer"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div v-if="!quizFinished" class="mt-3 w-full h-2.5 bg-white/20 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-yellow-300 rounded-full transition-all duration-500 ease-out"
                                :style="{ width: progressPercent + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- ═══ QUIZ BODY ═══ -->
                    <div class="p-4 sm:p-6">

                        <!-- ──── QUESTION VIEW ──── -->
                        <div v-if="!quizFinished && currentQuestion">
                            
                            <!-- Question Card -->
                            <div class="bg-amber-50 rounded-2xl border-3 border-amber-200 p-4 sm:p-5 mb-5 text-center">
                                <span class="text-3xl mb-2 block">{{ currentQuestion.emoji }}</span>
                                <p class="text-sm sm:text-base font-extrabold text-gray-900 leading-relaxed">
                                    {{ currentQuestion.question }}
                                </p>
                            </div>

                            <!-- Options Grid -->
                            <div class="space-y-2.5 mb-5">
                                <button
                                    v-for="(option, idx) in currentQuestion.options"
                                    :key="idx"
                                    @click="selectAnswer(idx)"
                                    :disabled="isAnswered"
                                    class="w-full flex items-center gap-3 p-3.5 sm:p-4 rounded-2xl border-3 border-b-4 font-bold text-sm text-left transition-all duration-150 cursor-pointer"
                                    :class="[
                                        // Default state
                                        !isAnswered && selectedAnswer !== idx
                                            ? 'bg-white border-gray-200 hover:border-orange-300 hover:bg-orange-50 active:translate-y-0.5 active:border-b-2'
                                            : '',
                                        // After answered: correct answer
                                        isAnswered && idx === currentQuestion.correctIndex
                                            ? 'bg-emerald-50 border-emerald-400 text-emerald-900 ring-2 ring-emerald-300 scale-[1.02]'
                                            : '',
                                        // After answered: wrong selected
                                        isAnswered && selectedAnswer === idx && idx !== currentQuestion.correctIndex
                                            ? 'bg-red-50 border-red-400 text-red-900 ring-2 ring-red-300 opacity-80'
                                            : '',
                                        // After answered: unselected wrong
                                        isAnswered && selectedAnswer !== idx && idx !== currentQuestion.correctIndex
                                            ? 'opacity-50 border-gray-200 bg-gray-50 cursor-not-allowed'
                                            : '',
                                    ]"
                                >
                                    <span
                                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center font-extrabold text-xs shrink-0 border-2"
                                        :class="[
                                            !isAnswered ? 'bg-orange-100 text-orange-700 border-orange-200' : '',
                                            isAnswered && idx === currentQuestion.correctIndex ? 'bg-emerald-200 text-emerald-800 border-emerald-300' : '',
                                            isAnswered && selectedAnswer === idx && idx !== currentQuestion.correctIndex ? 'bg-red-200 text-red-800 border-red-300' : '',
                                            isAnswered && selectedAnswer !== idx && idx !== currentQuestion.correctIndex ? 'bg-gray-200 text-gray-500 border-gray-300' : '',
                                        ]"
                                    >
                                        <template v-if="isAnswered && idx === currentQuestion.correctIndex">✓</template>
                                        <template v-else-if="isAnswered && selectedAnswer === idx && idx !== currentQuestion.correctIndex">✗</template>
                                        <template v-else>{{ getOptionLetter(idx) }}</template>
                                    </span>
                                    <span class="flex-1 text-sm sm:text-base">{{ option }}</span>
                                </button>
                            </div>

                            <!-- Feedback Box (after answering) -->
                            <Transition name="slide-up">
                                <div v-if="isAnswered" class="mb-4">
                                    <div
                                        class="p-4 rounded-2xl border-3 text-sm font-bold leading-relaxed"
                                        :class="isCorrect
                                            ? 'bg-emerald-50 border-emerald-300 text-emerald-900'
                                            : 'bg-red-50 border-red-300 text-red-900'"
                                    >
                                        <div class="flex items-center gap-2 mb-1.5 font-extrabold text-base">
                                            <span>{{ isCorrect ? '🎉 Benar!' : '😢 Belum Tepat!' }}</span>
                                        </div>
                                        <p class="text-xs sm:text-sm opacity-90">{{ currentQuestion.explanation }}</p>
                                    </div>
                                </div>
                            </Transition>

                            <!-- Next Button -->
                            <button
                                v-if="isAnswered"
                                @click="nextQuestion"
                                class="w-full py-3.5 bg-[#FF4D30] hover:bg-[#e03e22] text-white font-extrabold text-sm rounded-2xl border-2 border-orange-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span>{{ currentQuestionIndex < totalQuestions - 1 ? 'Soal Berikutnya ➡️' : 'Lihat Hasil 🏆' }}</span>
                            </button>
                        </div>

                        <!-- ──── RESULT VIEW ──── -->
                        <div v-else-if="quizFinished" class="text-center py-4">
                            
                            <!-- Result Badge -->
                            <div class="mb-5">
                                <div class="text-5xl sm:text-6xl mb-3">
                                    {{ isPassed ? '🏆' : '💪' }}
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold mb-1" :class="isPassed ? 'text-emerald-700' : 'text-orange-700'">
                                    {{ isPassed ? 'Kamu Lulus!' : 'Yuk Coba Lagi!' }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 font-bold">
                                    {{ isPassed
                                        ? 'Keren banget! Level berikutnya terbuka!'
                                        : 'Belum berhasil kali ini, tapi kamu pasti bisa!'
                                    }}
                                </p>
                            </div>

                            <!-- Score Card -->
                            <div class="bg-amber-50 rounded-2xl border-3 border-amber-200 p-5 mb-5">
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-extrabold" :class="isPassed ? 'text-emerald-600' : 'text-red-500'">
                                            {{ score }}/{{ totalQuestions }}
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-500 uppercase">Benar</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-extrabold text-amber-600">
                                            {{ Math.round((score / totalQuestions) * 100) }}%
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-500 uppercase">Akurasi</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl">
                                            <span v-for="s in 3" :key="s" :class="s <= starsEarned ? '' : 'grayscale opacity-30'">⭐</span>
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-500 uppercase">Bintang</div>
                                    </div>
                                </div>

                                <!-- XP Reward (only if passed) -->
                                <div v-if="isPassed" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-800 rounded-xl border-2 border-emerald-300 font-extrabold text-sm">
                                    <span>🧪</span>
                                    <span>+{{ xpReward }} XP Diperoleh!</span>
                                </div>
                                <div v-else class="text-xs font-bold text-gray-500">
                                    Kamu butuh minimal 70% jawaban benar untuk lulus level ini.
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2.5">
                                <button
                                    v-if="!isPassed"
                                    @click="retryQuiz"
                                    class="w-full py-3.5 bg-[#FF4D30] hover:bg-[#e03e22] text-white font-extrabold text-sm rounded-2xl border-2 border-orange-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-2"
                                >
                                    <span>🔄 Coba Lagi</span>
                                </button>
                                <button
                                    v-if="isPassed"
                                    @click="closeModal"
                                    class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-sm rounded-2xl border-2 border-emerald-700 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-2"
                                >
                                    <span>🎉 Lanjut Petualangan!</span>
                                </button>
                                <button
                                    @click="closeModal"
                                    class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold text-xs rounded-2xl border-2 border-gray-200 border-b-4 active:translate-y-0.5 active:border-b-2 transition-all cursor-pointer"
                                >
                                    Kembali ke Peta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* ── Modal Transition ── */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: all 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
    transform: scale(0.92);
}

/* ── Slide Up Feedback ── */
.slide-up-enter-active {
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
    opacity: 0;
    transform: translateY(16px);
}

/* ── Confetti Animation ── */
@keyframes confetti-fall {
    0% {
        transform: translateY(-10vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}
.animate-confetti-fall {
    animation: confetti-fall 3s ease-in forwards;
}
</style>
