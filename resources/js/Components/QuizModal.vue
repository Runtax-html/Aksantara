<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: { type: Boolean, default: false },
    levelId: { type: Number, default: 1 },
    levelTitle: { type: String, default: '' },
    userCoins: { type: Number, default: 50 },
});

const emit = defineEmits(['close', 'levelCompleted', 'xpGained', 'coinsUpdated']);

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
const hearts = ref(3);

// Hint & Assist State
const localCoins = ref(props.userCoins);
const hintUsedForCurrentQuestion = ref(false);
const hintMessage = ref('');
const eliminatedOptions = ref([]); // array of indices eliminated by hint
const showDrawingGuide = ref(false); // hint for writing question: show guide watermark
const isSubmittingResult = ref(false);
const completionReward = ref({ xp: 0, coins: 0, messages: [] });

// Canvas Writing State (For Type 2 Questions)
const quizCanvasRef = ref(null);
const isDrawing = ref(false);
const strokeCount = ref(0);
const writingFeedback = ref('');

watch(() => props.userCoins, (newVal) => {
    localCoins.value = newVal;
});

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
    hintUsedForCurrentQuestion.value = false;
    hintMessage.value = '';
    eliminatedOptions.value = [];
    showDrawingGuide.value = false;
    strokeCount.value = 0;
    writingFeedback.value = '';
    localCoins.value = props.userCoins;
    nextTick(() => {
        initCanvasIfWriting();
    });
}

// ═══════════════════════════════════════════════════════════════════
// MATERI SOAL BERDASARKAN KAMUS AKSARA SUNDA (MINIMAL 10 SOAL PER LEVEL)
// ═══════════════════════════════════════════════════════════════════
const allQuestions = {
    // ─────────────────────────────────────────────────────────
    // LEVEL 1: Swara Aksara Sunda (10 Soal)
    // ─────────────────────────────────────────────────────────
    1: [
        {
            type: 'choice',
            question: 'Aksara Sunda di atas adalah huruf Swara yang dibaca...',
            aksara: 'ᮃ',
            latin: 'a',
            badge: 'Swara (Vokal)',
            emoji: '🔤',
            options: ['a', 'i', 'u', 'o'],
            correctIndex: 0,
            explanation: 'ᮃ adalah huruf Swara yang berbunyi "a". Swara adalah sebutan huruf vokal dalam Aksara Sunda.',
            hint: 'Huruf vokal pertama dalam abjad.',
        },
        {
            type: 'choice',
            question: 'Manakah bentuk aksara Sunda untuk bunyi vokal "i"?',
            aksara: '?',
            latin: 'Bunyi "i"',
            badge: 'Tebak Bentuk',
            emoji: '👀',
            options: ['ᮃ (a)', 'ᮄ (i)', 'ᮅ (u)', 'ᮈ (é)'],
            correctIndex: 1,
            explanation: 'ᮄ adalah aksara Swara untuk bunyi "i".',
            hint: 'Bentuknya memiliki lengkungan halus menghadap ke atas.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara vokal "a" (ᮃ) pada kotak canvas di bawah!',
            aksara: 'ᮃ',
            latin: 'a',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Perhatikan lekukan kiri dan garis tegak lurus aksara ᮃ!',
            explanation: 'Luar biasa! Kamu berhasil menuliskan huruf Swara "a" (ᮃ) dengan rapi!',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas melambangkan bunyi vokal apa?',
            aksara: 'ᮅ',
            latin: 'u',
            badge: 'Swara (Vokal)',
            emoji: '📖',
            options: ['e', 'o', 'u', 'eu'],
            correctIndex: 2,
            explanation: 'ᮅ dibaca "u". Dalam Aksara Sunda terdapat 7 vokal: a, i, u, é, o, e, eu.',
            hint: 'Bunyi vokal yang sama seperti awal kata "Ucing".',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas dibaca...',
            aksara: 'ᮈ',
            latin: 'é',
            badge: 'Swara (Vokal)',
            emoji: '🎯',
            options: ['a', 'u', 'o', 'é'],
            correctIndex: 3,
            explanation: 'ᮈ dibaca "é" (seperti huruf e pada kata "bebek").',
            hint: 'Bunyi e pada kata ember atau sate.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara vokal "u" (ᮅ) pada kotak canvas di bawah!',
            aksara: 'ᮅ',
            latin: 'u',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Mulai dari garis kiri melengkung ke kanan bawah.',
            explanation: 'Hebat sekali! Tulisan aksara "u" (ᮅ) kamu sangat jelas!',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas dibaca apa?',
            aksara: 'ᮇ',
            latin: 'o',
            badge: 'Swara (Vokal)',
            emoji: '⭐',
            options: ['u', 'e', 'o', 'eu'],
            correctIndex: 2,
            explanation: 'ᮇ adalah aksara Swara yang berbunyi "o".',
            hint: 'Bunyi vokal bulat seperti pada kata "obat".',
        },
        {
            type: 'choice',
            question: 'Berapa jumlah seluruh huruf vokal dasar (Swara) dalam Aksara Sunda?',
            aksara: 'ᮃ ᮄ ᮅ ᮈ ᮇ ᮉ ᮩ',
            latin: 'Swara Sunda',
            badge: 'Wawasan Aksara',
            emoji: '🧠',
            options: ['5 huruf', '7 huruf', '3 huruf', '10 huruf'],
            correctIndex: 1,
            explanation: 'Aksara Sunda memiliki 7 huruf Swara: a, i, u, é, o, e (pepet), dan eu (paneuleung).',
            hint: 'Lebih banyak 2 huruf dibanding vokal alfabet biasa (5+2).',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara vokal "i" (ᮄ) pada kotak canvas di bawah!',
            aksara: 'ᮄ',
            latin: 'i',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Tarik garis melengkung dari kiri ke kanan atas.',
            explanation: 'Keren! Huruf "i" (ᮄ) berhasil kamu torehkan!',
        },
        {
            type: 'choice',
            question: 'Huruf vokal dalam Aksara Sunda disebut dengan istilah apa?',
            aksara: 'ᮃ ᮄ ᮅ',
            latin: 'Vokal Sunda',
            badge: 'Wawasan Aksara',
            emoji: '💡',
            options: ['Ngalagena', 'Swara', 'Rarangkén', 'Wilangan'],
            correctIndex: 1,
            explanation: '"Swara" adalah huruf vokal murni. Kalau konsonan disebut "Ngalagena".',
            hint: 'Mirip kata "Suara" dalam bahasa Indonesia.',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 2: Ngalagena Dasar 1 & Kosakata Hewan (10 Soal)
    // ─────────────────────────────────────────────────────────
    2: [
        {
            type: 'choice',
            question: 'Aksara Sunda di atas adalah huruf konsonan (Ngalagena) yang dibaca...',
            aksara: 'ᮊ',
            latin: 'Ka',
            badge: 'Ngalagena',
            emoji: '🎮',
            options: ['Ga', 'Ka', 'Ca', 'Ja'],
            correctIndex: 1,
            explanation: 'ᮊ dibaca "Ka". Ini adalah konsonan pertama dan bentuknya mirip angka 77 berjajar.',
            hint: 'Huruf pertama dalam kata Kucing dan Kancil.',
        },
        {
            type: 'choice',
            question: 'Di modul kamus, kata "Ucing" (Kucing) ditulis dengan aksara...',
            aksara: '🐱',
            latin: 'Ucing (Kucing)',
            badge: 'Kamus Sato',
            emoji: '🐱',
            options: ['ᮅᮎᮤᮀ (Ucing)', 'ᮠᮚᮙ᮪ (Hayam)', 'ᮙᮅᮀ (Maung)', 'ᮊᮥᮚ (Kuya)'],
            correctIndex: 0,
            explanation: 'ᮅᮎᮤᮀ dibaca "Ucing" yang berarti Kucing dalam bahasa Sunda.',
            hint: 'Mulai dari huruf vokal U (ᮅ) lalu Ca diberi Panghulu dan Panyecek.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara "Ka" (ᮊ) bentuk 77 pada canvas di bawah!',
            aksara: 'ᮊ',
            latin: 'Ka',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Bentuk dua angka 7 berjajar dengan ujung rapi!',
            explanation: 'Hebat! Goresan huruf Ka (ᮊ) kamu sangat mantap!',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas dibaca apa?',
            aksara: 'ᮌ',
            latin: 'Ga',
            badge: 'Ngalagena',
            emoji: '👁️',
            options: ['Ka', 'Ga', 'Nga', 'Ca'],
            correctIndex: 1,
            explanation: 'ᮌ dibaca "Ga". Mirip dengan Ka tapi bagian tengahnya menyatu melengkung.',
            hint: 'Konsonan kedua setelah Ka dalam susunan Ngalagena.',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮠᮚᮙ᮪" dalam kamus Sunda adalah nama hewan apa?',
            aksara: 'ᮠᮚᮙ᮪',
            latin: 'Hayam',
            badge: 'Kamus Sato',
            emoji: '🐔',
            options: ['Bebek', 'Ayam', 'Burung', 'Kambing'],
            correctIndex: 1,
            explanation: 'ᮠᮚᮙ᮪ dibaca "Hayam" yang artinya Ayam.',
            hint: 'Unggas yang sering berkokok di pagi hari (kongkorongok).',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara "Ga" (ᮌ) pada canvas di bawah!',
            aksara: 'ᮌ',
            latin: 'Ga',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Tarik garis atas lalu lengkungkan ke bawah seperti busur.',
            explanation: 'Pintar! Aksara Ga (ᮌ) berhasil digoreskan!',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas melambangkan bunyi apa?',
            aksara: 'ᮎ',
            latin: 'Ca',
            badge: 'Ngalagena',
            emoji: '📚',
            options: ['Ja', 'Ka', 'Ca', 'Nya'],
            correctIndex: 2,
            explanation: 'ᮎ dibaca "Ca", seperti pada kata "Carita" (cerita) atau "Cau" (pisang).',
            hint: 'Huruf depan untuk kata Cau dan Cai.',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮙᮅᮀ" adalah nama hewan pemberani khas Tatar Sunda yaitu...',
            aksara: 'ᮙᮅᮀ',
            latin: 'Maung',
            badge: 'Kamus Sato',
            emoji: '🐯',
            options: ['Gajah', 'Singa', 'Harimau (Maung)', 'Badak'],
            correctIndex: 2,
            explanation: 'ᮙᮅᮀ dibaca "Maung" yang artinya Harimau.',
            hint: 'Sering disebut Maung Lodaya atau Maung Bandung.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara "Ca" (ᮎ) pada canvas di bawah!',
            aksara: 'ᮎ',
            latin: 'Ca',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Tarik garis lengkung melingkar ke kanan bawah.',
            explanation: 'Bagus sekali! Aksara Ca (ᮎ) kamu sangat proporsional!',
        },
        {
            type: 'choice',
            question: 'Huruf konsonan dalam Aksara Sunda disebut dengan istilah apa?',
            aksara: 'ᮊ ᮌ ᮎ ᮏ',
            latin: 'Konsonan Sunda',
            badge: 'Wawasan Aksara',
            emoji: '💡',
            options: ['Swara', 'Rarangkén', 'Ngalagena', 'Panolong'],
            correctIndex: 2,
            explanation: 'Huruf konsonan disebut "Ngalagena". Ada 18 huruf Ngalagena murni.',
            hint: 'Kata berawalan huruf "N" yang khas Sunda.',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 3: Ngalagena Dasar 2 & Kosakata (10 Soal)
    // ─────────────────────────────────────────────────────────
    3: [
        {
            type: 'choice',
            question: 'Aksara Sunda di atas dibaca...',
            aksara: 'ᮒ',
            latin: 'Ta',
            badge: 'Ngalagena',
            emoji: '🔤',
            options: ['Da', 'Na', 'Ta', 'Pa'],
            correctIndex: 2,
            explanation: 'ᮒ dibaca "Ta", seperti bunyi t pada kata "Tahu" atau "Tangkal".',
            hint: 'Bunyi t pada kata Tangkal.',
        },
        {
            type: 'choice',
            question: 'Manakah bentuk aksara Sunda untuk bunyi "Da"?',
            aksara: '?',
            latin: 'Bunyi "Da"',
            badge: 'Tebak Bentuk',
            emoji: '🍃',
            options: ['ᮒ (Ta)', 'ᮓ (Da)', 'ᮔ (Na)', 'ᮕ (Pa)'],
            correctIndex: 1,
            explanation: 'ᮓ adalah aksara Ngalagena yang berbunyi "Da".',
            hint: 'Bentuknya melengkung halus dengan garis vertikal pendek di kanan.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Da" (ᮓ) pada canvas di bawah!',
            aksara: 'ᮓ',
            latin: 'Da',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Goreskan lengkungan membulat di sisi kiri lalu tarik ke bawah.',
            explanation: 'Sempurna! Tulisan aksara Da (ᮓ) kamu sangat jelas!',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas melambangkan bunyi apa?',
            aksara: 'ᮔ',
            latin: 'Na',
            badge: 'Ngalagena',
            emoji: '🍚',
            options: ['Ma', 'Ba', 'Pa', 'Na'],
            correctIndex: 3,
            explanation: 'ᮔ dibaca "Na", seperti bunyi n pada kata "Nasi".',
            hint: 'Konsonan berbunyi n.',
        },
        {
            type: 'choice',
            question: 'Kata "Lauk" (ᮜᮅᮊ᮪) di modul kamus artinya apa dalam bahasa Indonesia?',
            aksara: 'ᮜᮅᮊ᮪',
            latin: 'Lauk',
            badge: 'Kamus Sato',
            emoji: '🐟',
            options: ['Ayam', 'Ikan', 'Daging', 'Sayur'],
            correctIndex: 1,
            explanation: 'Dalam bahasa Sunda, "Lauk" berarti Ikan (misal lauk emas).',
            hint: 'Hewan air yang berenang dengan sirip.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Na" (ᮔ) pada canvas di bawah!',
            aksara: 'ᮔ',
            latin: 'Na',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Buat garis vertikal lalu lengkungkan ke kanan atas.',
            explanation: 'Hebat! Aksara Na (ᮔ) berhasil diselesaikan!',
        },
        {
            type: 'choice',
            question: 'Aksara Sunda di atas dibaca apa?',
            aksara: 'ᮕ',
            latin: 'Pa',
            badge: 'Ngalagena',
            emoji: '🏪',
            options: ['Ba', 'Pa', 'Ma', 'Ta'],
            correctIndex: 1,
            explanation: 'ᮕ dibaca "Pa", seperti bunyi p pada kata "Pasar" atau "Panon".',
            hint: 'Konsonan berbunyi p.',
        },
        {
            type: 'choice',
            question: 'Kata "Kuya" (ᮊᮥᮚ) dalam kamus Sunda adalah hewan...',
            aksara: 'ᮊᮥᮚ',
            latin: 'Kuya',
            badge: 'Kamus Sato',
            emoji: '🐢',
            options: ['Kelinci', 'Kura-kura', 'Kuda', 'Kucing'],
            correctIndex: 1,
            explanation: 'Kuya adalah bahasa Sunda untuk Kura-kura, yang jalannya lambat.',
            hint: 'Hewan bertempurung keras yang jalannya pelan.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Pa" (ᮕ) pada canvas di bawah!',
            aksara: 'ᮕ',
            latin: 'Pa',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Bentuk garis tegak dengan sayap lengkung di sebelah kanan.',
            explanation: 'Pintar! Aksara Pa (ᮕ) kamu sangat rapi!',
        },
        {
            type: 'choice',
            question: 'Gabungan aksara ᮘ dan ᮕ jika dibaca berbunyi...',
            aksara: 'ᮘᮕ',
            latin: 'Ba-Pa',
            badge: 'Membaca Kata',
            emoji: '👨',
            options: ['Bapa (Ayah)', 'Batu', 'Baju', 'Bata'],
            correctIndex: 0,
            explanation: 'ᮘ (Ba) + ᮕ (Pa) = "Bapa" (Ayah). Ngalagena sudah mengandung vokal "a" bawaan.',
            hint: 'Sebutan untuk orang tua laki-laki (Ayah).',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 4: Kosakata Alam & Sato Sunda (10 Soal)
    // ─────────────────────────────────────────────────────────
    4: [
        {
            type: 'choice',
            question: 'Aksara Sunda di atas adalah kata dari kamus yang dibaca...',
            aksara: 'ᮜᮅᮊ᮪',
            latin: 'Lauk',
            badge: 'Kamus Sato',
            emoji: '🐟',
            options: ['Lauk (Ikan)', 'Maung (Harimau)', 'Manuk (Burung)', 'Monyét (Monyet)'],
            correctIndex: 0,
            explanation: 'ᮜᮅᮊ᮪ dibaca "Lauk" (Ikan). Ada tanda pamaéh di akhir huruf Ka agar vokalnya mati.',
            hint: 'Hewan yang hidup di air.',
        },
        {
            type: 'choice',
            question: 'Manakah aksara Sunda untuk kata "Maung" (Harimau)?',
            aksara: '🐯',
            latin: 'Maung',
            badge: 'Tebak Aksara',
            emoji: '🐯',
            options: ['ᮙᮅᮀ (Maung)', 'ᮙᮔᮥᮊ᮪ (Manuk)', 'ᮙᮧᮑᮦᮒ᮪ (Monyét)', 'ᮙᮥᮔ᮪ᮓᮤᮀ (Munding)'],
            correctIndex: 0,
            explanation: 'ᮙᮅᮀ dibaca "Maung". Menggunakan huruf Ma, vokal U, dan panyecek (ng).',
            hint: 'Mulai dari huruf Ma (ᮙ) ditambah U dan Panyecek.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Ba" (ᮘ) pada canvas di bawah!',
            aksara: 'ᮘ',
            latin: 'Ba',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Bentuk dua lengkungan mirip sayap kupu-kupu.',
            explanation: 'Mantap! Aksara Ba (ᮘ) berhasil kamu tuliskan!',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮙᮧᮑᮦᮒ᮪" dibaca apa?',
            aksara: 'ᮙᮧᮑᮦᮒ᮪',
            latin: 'Monyét',
            badge: 'Kamus Sato',
            emoji: '🐒',
            options: ['Kuya', 'Munding', 'Monyét', 'Domba'],
            correctIndex: 2,
            explanation: 'ᮙᮧᮑᮦᮒ᮪ dibaca "Monyét" (Kera/Monyet).',
            hint: 'Hewan yang suka bergelantungan di dahan pohon.',
        },
        {
            type: 'choice',
            question: 'Kata "Manuk" (ᮙᮔᮥᮊ᮪) dalam bahasa Indonesia artinya...',
            aksara: 'ᮙᮔᮥᮊ᮪',
            latin: 'Manuk',
            badge: 'Kamus Sato',
            emoji: '🐦',
            options: ['Ikan', 'Burung', 'Ayam', 'Kucing'],
            correctIndex: 1,
            explanation: 'Manuk dalam bahasa Sunda artinya Burung (misal Manuk Dadali).',
            hint: 'Hewan bersayap yang bisa terbang tinggi di langit.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Ma" (ᮙ) pada canvas di bawah!',
            aksara: 'ᮙ',
            latin: 'Ma',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Garis tegak dengan lengkungan bawah dan garis atas.',
            explanation: 'Luar biasa! Aksara Ma (ᮙ) kamu sangat tepat!',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮓᮧᮙ᮪ᮘ" dibaca apa?',
            aksara: 'ᮓᮧᮙ᮪ᮘ',
            latin: 'Domba',
            badge: 'Kamus Sato',
            emoji: '🐑',
            options: ['Domba', 'Kuda', 'Munding', 'Hayam'],
            correctIndex: 0,
            explanation: 'ᮓᮧᮙ᮪ᮘ dibaca "Domba", terkenal dengan Domba Garut.',
            hint: 'Hewan berbulu tebal dan bertanduk gagah.',
        },
        {
            type: 'choice',
            question: 'Hewan yang sering membantu petani membajak sawah adalah "Munding" (ᮙᮥᮔ᮪ᮓᮤᮀ), yaitu...',
            aksara: 'ᮙᮥᮔ᮪ᮓᮤᮀ',
            latin: 'Munding',
            badge: 'Kamus Sato',
            emoji: '🐃',
            options: ['Sapi', 'Kuda', 'Kerbau (Munding)', 'Gajah'],
            correctIndex: 2,
            explanation: 'Munding adalah bahasa Sunda untuk Kerbau.',
            hint: 'Hewan bertanduk besar yang suka berkubang di lumpur.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Sa" (ᮞ) pada canvas di bawah!',
            aksara: 'ᮞ',
            latin: 'Sa',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Bentuknya meliuk indah seperti ombak di laut.',
            explanation: 'Keren! Aksara Sa (ᮞ) berhasil kamu goreskan!',
        },
        {
            type: 'choice',
            question: 'Kata "Tangkal" dalam bahasa Sunda artinya...',
            aksara: 'ᮒᮀᮊᮜ᮪',
            latin: 'Tangkal',
            badge: 'Kamus Alam',
            emoji: '🌳',
            options: ['Sungai', 'Gunung', 'Pohon', 'Batu'],
            correctIndex: 2,
            explanation: 'Tangkal artinya Pohon (misal: tangkal cau = pohon pisang).',
            hint: 'Tumbuhan berkayu dengan daun rimbun.',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 5: Awak (Tubuh) & Dahareun Sunda (10 Soal)
    // ─────────────────────────────────────────────────────────
    5: [
        {
            type: 'choice',
            question: 'Aksara "ᮕᮔᮧᮔ᮪" dalam kamus anggota tubuh (Awak) dibaca...',
            aksara: 'ᮕᮔᮧᮔ᮪',
            latin: 'Panon',
            badge: 'Kamus Awak',
            emoji: '👀',
            options: ['Panon (Mata)', 'Ceuli (Telinga)', 'Irung (Hidung)', 'Bitis (Betis)'],
            correctIndex: 0,
            explanation: 'ᮕᮔᮧᮔ᮪ dibaca "Panon" yang berarti Mata.',
            hint: 'Indera penglihatan untuk melihat keindahan dunia.',
        },
        {
            type: 'choice',
            question: 'Anggota tubuh "Leungeun" (ᮜᮩᮍᮩᮔ᮪) artinya...',
            aksara: 'ᮜᮩᮍᮩᮔ᮪',
            latin: 'Leungeun',
            badge: 'Kamus Awak',
            emoji: '✋',
            options: ['Kaki', 'Tangan (Leungeun)', 'Kepala', 'Perut'],
            correctIndex: 1,
            explanation: 'Leungeun adalah bahasa Sunda untuk Tangan.',
            hint: 'Bagian tubuh dengan jari-jari untuk memegang benda.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Ha" (ᮠ) pada canvas di bawah!',
            aksara: 'ᮠ',
            latin: 'Ha',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Bentuk lekukan di kiri lalu tarik ke bawah menyerupai angka 3 terbalik.',
            explanation: 'Bagus! Aksara Ha (ᮠ) berhasil kamu tuliskan!',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮎᮩᮜᮤ" dibaca apa?',
            aksara: 'ᮎᮩᮜᮤ',
            latin: 'Ceuli',
            badge: 'Kamus Awak',
            emoji: '👂',
            options: ['Panon', 'Irung', 'Ceuli (Telinga)', 'Waos'],
            correctIndex: 2,
            explanation: 'ᮎᮩᮜᮤ dibaca "Ceuli" yang artinya Telinga.',
            hint: 'Indera pendengaran untuk mendengarkan nasihat.',
        },
        {
            type: 'choice',
            question: '"Cau" (ᮎᮅ) dalam bahasa Sunda adalah nama buah apa?',
            aksara: 'ᮎᮅ',
            latin: 'Cau',
            badge: 'Kamus Dahareun',
            emoji: '🍌',
            options: ['Mangga', 'Jeruk', 'Pisang (Cau)', 'Kelapa'],
            correctIndex: 2,
            explanation: 'Cau adalah bahasa Sunda untuk buah Pisang.',
            hint: 'Buah berwarna kuning yang manis disukai monyét.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Ja" (ᮏ) pada canvas di bawah!',
            aksara: 'ᮏ',
            latin: 'Ja',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Garis meliuk dengan dua ujung vertikal.',
            explanation: 'Pintar! Aksara Ja (ᮏ) kamu sangat mantap!',
        },
        {
            type: 'choice',
            question: 'Bahan makanan "Béas" (ᮘᮦᮃᮞ᮪) artinya...',
            aksara: 'ᮘᮦᮃᮞ᮪',
            latin: 'Béas',
            badge: 'Kamus Dahareun',
            emoji: '🍚',
            options: ['Beras', 'Jagung', 'Gandum', 'Ketan'],
            correctIndex: 0,
            explanation: 'Béas adalah Beras yang jika dimasak menjadi sangu (nasi).',
            hint: 'Padi yang sudah digiling dan siap ditanak menjadi nasi.',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮎᮤᮜᮨᮙ᮪ᮘᮥ" adalah ubi jalar manis khas tanah Sunda, yaitu...',
            aksara: 'ᮎᮤᮜᮨᮙ᮪ᮘᮥ',
            latin: 'Cilembu',
            badge: 'Kamus Dahareun',
            emoji: '🍠',
            options: ['Cireng', 'Cilembu (Ubi)', 'Cilok', 'Combro'],
            correctIndex: 1,
            explanation: 'Ubi Cilembu khas Sumedang manis seperti madu.',
            hint: 'Ubi manis beraroma madu asal Sumedang.',
        },
        {
            type: 'write',
            question: 'Ayo tuliskan aksara konsonan "Ta" (ᮒ) pada canvas di bawah!',
            aksara: 'ᮒ',
            latin: 'Ta',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Garis tegak lalu lengkungkan setengah lingkaran ke kanan.',
            explanation: 'Keren! Aksara Ta (ᮒ) terselesaikan dengan baik!',
        },
        {
            type: 'choice',
            question: 'Tanda Rarangkén "Panghulu" (◌ᮤ) berfungsi mengubah vokal menjadi bunyi...',
            aksara: 'ᮊᮤ',
            latin: 'Panghulu (i)',
            badge: 'Rarangkén',
            emoji: '✨',
            options: ['a', 'i', 'u', 'o'],
            correctIndex: 1,
            explanation: 'Panghulu (◌ᮤ) diletakkan di atas huruf konsonan untuk mengubah vokal menjadi "i" (misal: Ka jadi Ki).',
            hint: 'Bunyi vokal tajam berbunyi "i".',
        },
    ],

    // ─────────────────────────────────────────────────────────
    // LEVEL 6: Jawara Aksara Sunda (11 Soal Bos Final)
    // ─────────────────────────────────────────────────────────
    6: [
        {
            type: 'choice',
            question: 'Kata "Sunda" dalam Aksara Sunda yang benar ditulis...',
            aksara: 'ᮞᮥᮔ᮪ᮓ',
            latin: 'Sunda',
            badge: 'Tantangan Jawara',
            emoji: '🌟',
            options: ['ᮞᮥᮓ (Suda)', 'ᮞᮥᮔ᮪ᮓ (Sunda)', 'ᮞᮔ᮪ᮓ (Sanda)', 'ᮞᮤᮔ᮪ᮓ (Sinda)'],
            correctIndex: 1,
            explanation: 'ᮞ+panyuku(u)=Su, ᮔ+pamaéh=n, ᮓ=Da → "Sunda"! 🎉',
            hint: 'Terdiri dari Sa berpanyuku, Na berpamaéh, dan Da.',
        },
        {
            type: 'choice',
            question: 'Berapa jumlah huruf konsonan Ngalagena murni dalam Aksara Sunda?',
            aksara: 'ᮊ ᮌ ᮍ ᮎ ᮏ ᮑ ...',
            latin: 'Ngalagena',
            badge: 'Wawasan Jawara',
            emoji: '👑',
            options: ['12 huruf', '18 huruf', '20 huruf', '26 huruf'],
            correctIndex: 1,
            explanation: 'Aksara Sunda memiliki 18 huruf Ngalagena murni.',
            hint: 'Tepat 18 karakter konsonan bawaan.',
        },
        {
            type: 'write',
            question: 'Tantangan Jawara: Tuliskan aksara "Ka" (ᮊ) bentuk 77 dengan sempurna!',
            aksara: 'ᮊ',
            latin: 'Ka',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Goreskan dua bentuk 7 yang tebal dan proporsional!',
            explanation: 'Luar biasa sekali! Tulisan Ka (ᮊ) kamu seperti pujangga Sunda!',
        },
        {
            type: 'choice',
            question: 'Rarangkén apa yang berfungsi "mematikan" bunyi vokal konsonan?',
            aksara: '◌᮪',
            latin: 'Pamaéh',
            badge: 'Rarangkén',
            emoji: '🔇',
            options: ['Panghulu', 'Panyuku', 'Pamaéh', 'Panolong'],
            correctIndex: 2,
            explanation: 'Pamaéh (◌᮪) membunuh vokal konsonan, misalnya ᮔ (na) + ᮪ = n.',
            hint: 'Artinya mematikan bunyi (pembunuh vokal).',
        },
        {
            type: 'choice',
            question: 'Aksara "ᮅᮎᮤᮀ" adalah nama hewan peliharaan yang lucu yaitu...',
            aksara: 'ᮅᮎᮤᮀ',
            latin: 'Ucing',
            badge: 'Kamus Sato',
            emoji: '🐱',
            options: ['Ayam', 'Kucing (Ucing)', 'Kelinci', 'Hamster'],
            correctIndex: 1,
            explanation: 'Ucing artinya Kucing dalam bahasa Sunda.',
            hint: 'Suka mengeong dan suka dielus bulunya.',
        },
        {
            type: 'write',
            question: 'Tantangan Jawara: Tuliskan huruf vokal Swara "a" (ᮃ) pada canvas!',
            aksara: 'ᮃ',
            latin: 'a',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Tarik garis tegak dengan sayap lengkung di sebelah kiri.',
            explanation: 'Sempurna! Huruf Swara "a" berhasil ditulis dengan sangat baik!',
        },
        {
            type: 'choice',
            question: 'Berapa hasil penjumlahan Angka Sunda ᮳ (3) + ᮴ (4)?',
            aksara: '᮳ + ᮴ = ?',
            latin: 'Wilangan Sunda',
            badge: 'Angka Sunda',
            emoji: '🧮',
            options: ['5', '6', '7 (᮷)', '8'],
            correctIndex: 2,
            explanation: '3 + 4 = 7! Dalam angka Sunda, 7 ditulis ᮷.',
            hint: 'Hasil penjumlahan 3 dan 4.',
        },
        {
            type: 'choice',
            question: 'Rarangkén "Pamepet" (◌ᮨ) menghasilkan bunyi vokal apa?',
            aksara: '◌ᮨ',
            latin: 'Pamepet',
            badge: 'Rarangkén',
            emoji: '🎯',
            options: ['i', 'u', 'e (pepet)', 'o'],
            correctIndex: 2,
            explanation: 'Pamepet (◌ᮨ) menghasilkan bunyi e pepet seperti pada kata "seger" atau "peuteuy".',
            hint: 'Bunyi e pada kata sedih atau sepi.',
        },
        {
            type: 'write',
            question: 'Tantangan Jawara: Tuliskan huruf vokal Swara "u" (ᮅ) pada canvas!',
            aksara: 'ᮅ',
            latin: 'u',
            badge: 'Tantangan Menulis ✏️',
            emoji: '✍️',
            hint: 'Goreskan lengkungan dari kiri menuju kanan bawah.',
            explanation: 'Mantap! Huruf Swara "u" kamu sangat anggun!',
        },
        {
            type: 'choice',
            question: 'Apa arti dan makna dari nama aplikasi "Aksantara"?',
            aksara: 'ᮃᮊ᮪ᮞᮔ᮪ᮒᮛ',
            latin: 'Aksantara',
            badge: 'Aksantara',
            emoji: '👑',
            options: ['Huruf Bahasa Kuno', 'Aksara + Nusantara', 'Cerita Dongeng', 'Peta Permainan'],
            correctIndex: 1,
            explanation: 'Aksantara adalah gabungan dari "Aksara" dan "Nusantara" untuk melestarikan warisan budaya!',
            hint: 'Aksara dan Nusantara.',
        },
        {
            type: 'choice',
            question: 'Hewan gagah lambang tatar Sunda di kamus adalah "Maung" yang ditulis...',
            aksara: '🐯',
            latin: 'Maung (Harimau)',
            badge: 'Bos Jawara',
            emoji: '🐯',
            options: ['ᮙᮔᮥᮊ᮪ (Manuk)', 'ᮙᮅᮀ (Maung)', 'ᮙᮥᮔ᮪ᮓᮤᮀ (Munding)', 'ᮙᮧᮑᮦᮒ᮪ (Monyét)'],
            correctIndex: 1,
            explanation: 'ᮙᮅᮀ dibaca Maung! Selamat, kamu telah menaklukkan tantangan Bos Jawara!',
            hint: 'Huruf Ma dan vokal U dengan panyecek.',
        },
    ],
};

// ═══════════════════════════════════════════════════════════════════
// COMPUTED & METHODS
// ═══════════════════════════════════════════════════════════════════
const currentQuestions = computed(() => {
    return allQuestions[props.levelId] || allQuestions[1];
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
    if (ratio >= 0.9) return 3;
    if (ratio >= 0.8) return 2;
    if (ratio >= 0.7) return 1;
    return 0;
});

const xpRewardEstimated = computed(() => {
    const baseXP = props.levelId * 20;
    return baseXP + (starsEarned.value * 10);
});

// ─────────────────────────────────────────────────────────────
// FITUR BANTUAN / HINT (BERBAYAR 5 KOIN)
// ─────────────────────────────────────────────────────────────
async function useHint() {
    if (hintUsedForCurrentQuestion.value || isAnswered.value) return;

    if (localCoins.value < 5) {
        hintMessage.value = '⚠️ Koin tidak cukup! Butuh 5 koin untuk bantuan.';
        return;
    }

    try {
        const response = await axios.post('/api/gamification/use-hint', { cost: 5 });
        if (response.data.success) {
            hintUsedForCurrentQuestion.value = true;
            localCoins.value = response.data.remainingCoins;
            emit('coinsUpdated', response.data.remainingCoins);

            if (currentQuestion.value.type === 'choice') {
                // Eliminasi 2 opsi yang salah
                const correct = currentQuestion.value.correctIndex;
                const wrongIndices = currentQuestion.value.options
                    .map((_, i) => i)
                    .filter(i => i !== correct);
                // Acak dan ambil 2 untuk dieliminasi
                const shuffled = wrongIndices.sort(() => 0.5 - Math.random());
                eliminatedOptions.value = shuffled.slice(0, 2);
                hintMessage.value = `💡 Petunjuk aktif! 2 opsi salah telah disingkirkan. Hint: "${currentQuestion.value.hint}"`;
            } else if (currentQuestion.value.type === 'write') {
                // Tampilkan watermark panduan goresan
                showDrawingGuide.value = true;
                hintMessage.value = `💡 Petunjuk aktif! Garis panduan huruf muncul di canvas. Hint: "${currentQuestion.value.hint}"`;
                drawWatermarkGuide();
            }
        }
    } catch (err) {
        console.error('Gagal menggunakan hint:', err);
        hintMessage.value = 'Gagal memakai bantuan. Silakan coba lagi.';
    }
}

// ─────────────────────────────────────────────────────────────
// INTERACTIVE CANVAS WRITING IN QUIZ
// ─────────────────────────────────────────────────────────────
function initCanvasIfWriting() {
    if (!currentQuestion.value || currentQuestion.value.type !== 'write') return;
    const canvas = quizCanvasRef.value;
    if (!canvas) return;

    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    strokeCount.value = 0;
    writingFeedback.value = '';

    if (showDrawingGuide.value) {
        drawWatermarkGuide();
    }
}

function drawWatermarkGuide() {
    const canvas = quizCanvasRef.value;
    if (!canvas || !currentQuestion.value) return;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });

    ctx.save();
    ctx.font = 'bold 84px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.strokeStyle = 'rgba(255, 77, 48, 0.35)';
    ctx.fillStyle = 'rgba(255, 120, 70, 0.12)';
    ctx.lineWidth = 4;
    ctx.setLineDash([6, 6]);
    ctx.strokeText(currentQuestion.value.aksara, canvas.width / 2, canvas.height / 2);
    ctx.fillText(currentQuestion.value.aksara, canvas.width / 2, canvas.height / 2);
    ctx.restore();
}

function getCanvasPos(e) {
    const canvas = quizCanvasRef.value;
    if (!canvas) return { x: 0, y: 0 };
    const rect = canvas.getBoundingClientRect();
    let clientX = e.clientX;
    let clientY = e.clientY;

    if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    }
    return {
        x: (clientX - rect.left) * (canvas.width / rect.width),
        y: (clientY - rect.top) * (canvas.height / rect.height)
    };
}

function startQuizDrawing(e) {
    if (isAnswered.value) return;
    isDrawing.value = true;
    strokeCount.value++;
    const canvas = quizCanvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#1e293b';
    const pos = getCanvasPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function drawQuizStroke(e) {
    if (!isDrawing.value || isAnswered.value) return;
    e.preventDefault();
    const canvas = quizCanvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#1e293b';
    const pos = getCanvasPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}

function stopQuizDrawing() {
    isDrawing.value = false;
}

function clearQuizCanvas() {
    if (isAnswered.value) return;
    const canvas = quizCanvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    strokeCount.value = 0;
    writingFeedback.value = '';
    if (showDrawingGuide.value) {
        drawWatermarkGuide();
    }
}

// Periksa Tulisan Tangan Anak SD
async function checkWritingAnswer() {
    if (isAnswered.value) return;
    const canvas = quizCanvasRef.value;
    if (!canvas) return;

    // Hitung apakah ada coretan
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
    let userPixelCount = 0;
    for (let i = 0; i < imgData.length; i += 4) {
        if (imgData[i + 3] > 80) userPixelCount++;
    }

    if (userPixelCount < 60) {
        writingFeedback.value = '⚠️ Belum ada coretan! Tulis aksara Sunda pada canvas ya.';
        return;
    }

    // Buat mask target
    const targetChar = currentQuestion.value.aksara;
    const maskCanvas = document.createElement('canvas');
    maskCanvas.width = canvas.width;
    maskCanvas.height = canvas.height;
    const mCtx = maskCanvas.getContext('2d', { willReadFrequently: true });
    mCtx.font = 'bold 84px sans-serif';
    mCtx.textAlign = 'center';
    mCtx.textBaseline = 'middle';
    mCtx.fillStyle = '#000';
    mCtx.fillText(targetChar, canvas.width / 2, canvas.height / 2);
    mCtx.lineWidth = 14;
    mCtx.strokeStyle = '#000';
    mCtx.strokeText(targetChar, canvas.width / 2, canvas.height / 2);

    const maskData = mCtx.getImageData(0, 0, canvas.width, canvas.height).data;
    let overlapCount = 0;
    let targetPixelCount = 0;

    for (let i = 0; i < maskData.length; i += 4) {
        const isTarget = maskData[i + 3] > 80;
        const isUser = imgData[i + 3] > 80;
        if (isTarget) targetPixelCount++;
        if (isTarget && isUser) overlapCount++;
    }

    // Untuk anak SD: toleransi tinggi dan ramah
    const overlapRatio = targetPixelCount > 0 ? (overlapCount / targetPixelCount) : 0;
    const isValid = overlapRatio >= 0.20 || overlapCount > 100;

    isAnswered.value = true;
    isCorrect.value = isValid;

    if (isValid) {
        score.value++;
        writingFeedback.value = '🎉 Hebat! Bentuk aksara yang kamu tulis sudah sangat tepat!';
        // Panggil endpoint practice writing agar otomatis menyelesaikan misi harian 2
        try {
            axios.post('/api/gamification/practice-writing');
        } catch (e) {
            console.error(e);
        }
    } else {
        hearts.value = Math.max(0, hearts.value - 1);
        writingFeedback.value = '😢 Bentuk hurufnya belum pas. Gunakan tombol Bantuan untuk melihat panduan ya!';
    }
}

// ─────────────────────────────────────────────────────────────
// JAWAB SOAL PILIHAN GANDA
// ─────────────────────────────────────────────────────────────
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

async function nextQuestion() {
    if (currentQuestionIndex.value < totalQuestions.value - 1) {
        currentQuestionIndex.value++;
        selectedAnswer.value = null;
        isAnswered.value = false;
        isCorrect.value = false;
        hintUsedForCurrentQuestion.value = false;
        hintMessage.value = '';
        eliminatedOptions.value = [];
        showDrawingGuide.value = false;
        strokeCount.value = 0;
        writingFeedback.value = '';
        nextTick(() => {
            initCanvasIfWriting();
        });
    } else {
        quizFinished.value = true;
        if (isPassed.value) {
            showConfetti.value = true;
            await submitQuizResult();
            setTimeout(() => { showConfetti.value = false; }, 4000);
        }
    }
}

async function submitQuizResult() {
    isSubmittingResult.value = true;
    try {
        const response = await axios.post('/api/gamification/complete-level', {
            level_id: props.levelId,
            stars: starsEarned.value,
            score: score.value,
            total: totalQuestions.value,
        });

        if (response.data.success) {
            completionReward.value = {
                xp: response.data.xpEarned,
                coins: response.data.coinsEarned,
                messages: response.data.messages || [],
            };
            emit('xpGained', response.data.xpEarned);
            emit('coinsUpdated', response.data.state.user.coins);
            emit('levelCompleted', {
                levelId: props.levelId,
                stars: starsEarned.value,
                score: score.value,
                total: totalQuestions.value,
                xp: response.data.xpEarned,
                coins: response.data.coinsEarned,
                state: response.data.state,
            });
        }
    } catch (err) {
        console.error('Gagal menyimpan hasil quiz:', err);
    } finally {
        isSubmittingResult.value = false;
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
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

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
                        {{ ['🎉','⭐','🌟','🎊','✨','🏆','💫','🎯','🪙'][Math.floor(Math.random() * 9)] }}
                    </div>
                </div>

                <!-- Modal Card -->
                <div class="relative z-[105] w-full max-w-lg max-h-[92vh] overflow-y-auto bg-white rounded-3xl border-4 border-orange-300 shadow-2xl flex flex-col">

                    <!-- ═══ HEADER ═══ -->
                    <div class="sticky top-0 bg-gradient-to-r from-[#FF4D30] to-orange-500 p-4 sm:p-5 rounded-t-[22px] z-10 shrink-0">
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
                                <!-- User Coins Pill -->
                                <div class="flex items-center gap-1 bg-amber-400 text-amber-950 font-black text-xs px-2.5 py-1 rounded-xl shadow-xs border border-amber-300">
                                    <span>🪙</span>
                                    <span>{{ localCoins }}</span>
                                </div>

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
                    <div class="p-4 sm:p-6 flex-1">

                        <!-- ──── QUESTION VIEW ──── -->
                        <div v-if="!quizFinished && currentQuestion">
                            
                            <!-- Question Card -->
                            <div class="bg-amber-50 rounded-2xl border-3 border-amber-200 p-4 sm:p-5 mb-4 text-center">
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-orange-100 text-[#FF4D30] border border-orange-200">
                                        {{ currentQuestion.badge }}
                                    </span>

                                    <!-- Tombol Bantuan / Hint Berbayar Koin -->
                                    <button
                                        v-if="!isAnswered && !hintUsedForCurrentQuestion"
                                        @click="useHint"
                                        class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 font-extrabold text-xs rounded-xl border border-amber-300 transition-all flex items-center gap-1 shadow-xs active:scale-95 cursor-pointer"
                                    >
                                        <span>💡 Bantuan</span>
                                        <span class="text-[10px] bg-amber-200 px-1.5 py-0.2 rounded-md font-black text-amber-900">-5 🪙</span>
                                    </button>
                                    <span v-else-if="hintUsedForCurrentQuestion" class="text-xs font-black text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-xl border border-emerald-300">
                                        ✓ Bantuan Aktif
                                    </span>
                                </div>

                                <!-- Aksara Graphic Display -->
                                <div class="my-3 p-4 bg-white rounded-2xl border-2 border-amber-200/80 shadow-xs inline-block min-w-[140px]">
                                    <div class="text-4xl sm:text-5xl font-black text-gray-900 leading-none">
                                        {{ currentQuestion.aksara }}
                                    </div>
                                    <div class="text-xs font-extrabold text-orange-600 mt-1.5">
                                        {{ currentQuestion.latin }}
                                    </div>
                                </div>

                                <p class="text-sm sm:text-base font-extrabold text-gray-900 leading-relaxed mt-1">
                                    {{ currentQuestion.question }}
                                </p>

                                <!-- Hint Message Banner -->
                                <div v-if="hintMessage" class="mt-3 p-2 bg-amber-100 border border-amber-300 rounded-xl text-xs font-extrabold text-amber-900 text-left">
                                    {{ hintMessage }}
                                </div>
                            </div>

                            <!-- ──── JENIS SOAL 1: PILIHAN GANDA ──── -->
                            <div v-if="currentQuestion.type === 'choice'" class="space-y-2.5 mb-5">
                                <button
                                    v-for="(option, idx) in currentQuestion.options"
                                    :key="idx"
                                    @click="selectAnswer(idx)"
                                    :disabled="isAnswered || eliminatedOptions.includes(idx)"
                                    class="w-full flex items-center gap-3 p-3.5 sm:p-4 rounded-2xl border-3 border-b-4 font-bold text-sm text-left transition-all duration-150 cursor-pointer"
                                    :class="[
                                        // Eliminated by hint
                                        eliminatedOptions.includes(idx)
                                            ? 'opacity-30 bg-gray-100 border-gray-200 line-through cursor-not-allowed'
                                            : '',
                                        // Default state
                                        !isAnswered && selectedAnswer !== idx && !eliminatedOptions.includes(idx)
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

                            <!-- ──── JENIS SOAL 2: MENULIS DI CANVAS ──── -->
                            <div v-else-if="currentQuestion.type === 'write'" class="mb-5">
                                <div class="bg-white rounded-2xl border-3 border-orange-300 p-3 shadow-md flex flex-col items-center">
                                    <div class="relative w-full max-w-[320px] aspect-[4/3] bg-[#FFFBF0] rounded-xl border-2 border-orange-200 overflow-hidden touch-none flex items-center justify-center">
                                        <canvas
                                            ref="quizCanvasRef"
                                            width="320"
                                            height="240"
                                            class="w-full h-full cursor-crosshair"
                                            @mousedown="startQuizDrawing"
                                            @mousemove="drawQuizStroke"
                                            @mouseup="stopQuizDrawing"
                                            @mouseleave="stopQuizDrawing"
                                            @touchstart.prevent="startQuizDrawing"
                                            @touchmove.prevent="drawQuizStroke"
                                            @touchend.prevent="stopQuizDrawing"
                                        ></canvas>

                                        <!-- Empty Placeholder if no strokes yet -->
                                        <div
                                            v-if="strokeCount === 0 && !showDrawingGuide"
                                            class="absolute pointer-events-none text-xs font-bold text-gray-400 text-center px-4"
                                        >
                                            ✏️ Coretkan aksara di sini
                                        </div>
                                    </div>

                                    <!-- Canvas Action Buttons -->
                                    <div v-if="!isAnswered" class="w-full flex items-center justify-between gap-2 mt-3">
                                        <button
                                            @click="clearQuizCanvas"
                                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold text-xs rounded-xl border border-gray-300 transition cursor-pointer flex items-center gap-1"
                                        >
                                            <span>🔄 Bersihkan</span>
                                        </button>

                                        <button
                                            @click="checkWritingAnswer"
                                            class="flex-1 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-extrabold text-xs sm:text-sm rounded-xl border-2 border-green-700 border-b-4 shadow-[0_2px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition cursor-pointer flex items-center justify-center gap-1.5"
                                        >
                                            <span>✓ Periksa Tulisan</span>
                                        </button>
                                    </div>

                                    <!-- Writing Feedback -->
                                    <div v-if="writingFeedback" class="w-full mt-2 text-xs font-extrabold text-center" :class="isCorrect ? 'text-emerald-700' : 'text-orange-700'">
                                        {{ writingFeedback }}
                                    </div>
                                </div>
                            </div>

                            <!-- Feedback Box (After answering) -->
                            <Transition name="slide-up">
                                <div v-if="isAnswered" class="mb-4">
                                    <div
                                        class="p-4 rounded-2xl border-3 text-sm font-bold leading-relaxed"
                                        :class="isCorrect
                                            ? 'bg-emerald-50 border-emerald-300 text-emerald-900'
                                            : 'bg-red-50 border-red-300 text-red-900'"
                                    >
                                        <div class="flex items-center gap-2 mb-1.5 font-extrabold text-base">
                                            <span>{{ isCorrect ? '🎉 Benar & Hebat!' : '😢 Belum Tepat!' }}</span>
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
                                    {{ isPassed ? 'Selamat, Kamu Lulus!' : 'Yuk Coba Lagi!' }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 font-bold">
                                    {{ isPassed
                                        ? 'Hebat banget! Progres dan level berikutnya telah tersimpan ke akunmu!'
                                        : 'Belum berhasil kali ini, tapi kamu pasti bisa mencobanya lagi!'
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

                                <!-- Rewards Earned (XP & Coins) -->
                                <div v-if="isPassed" class="space-y-2">
                                    <div class="flex items-center justify-center gap-3">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-100 text-purple-900 rounded-xl border border-purple-300 font-extrabold text-xs sm:text-sm">
                                            <span>🧪</span>
                                            <span>+{{ completionReward.xp || xpRewardEstimated }} XP</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 text-amber-900 rounded-xl border border-amber-300 font-extrabold text-xs sm:text-sm">
                                            <span>🪙</span>
                                            <span>+{{ completionReward.coins || 25 }} Koin</span>
                                        </span>
                                    </div>

                                    <!-- Notifications / Messages -->
                                    <div v-if="completionReward.messages && completionReward.messages.length > 0" class="mt-2 space-y-1">
                                        <div
                                            v-for="(msg, i) in completionReward.messages"
                                            :key="i"
                                            class="text-xs font-black text-emerald-800 bg-emerald-100 border border-emerald-300 rounded-lg p-1.5"
                                        >
                                            {{ msg }}
                                        </div>
                                    </div>
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
                                    v-else
                                    @click="closeModal"
                                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm rounded-2xl border-2 border-emerald-800 border-b-4 shadow-[0_3px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-2"
                                >
                                    <span>Lanjut Petualangan 🗺️</span>
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
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
    transform: scale(0.96);
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.2s ease;
}
.slide-up-enter-from {
    opacity: 0;
    transform: translateY(10px);
}
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

@keyframes confettiFall {
    0% {
        transform: translateY(-20px) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}
.animate-confetti-fall {
    animation: confettiFall 3s linear forwards;
}
</style>
