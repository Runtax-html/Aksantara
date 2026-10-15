<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

// ─── Sound Synthesizer & Speech Helper ───
function playSound(type = 'pop') {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'success') {
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(523.25, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(659.25, ctx.currentTime + 0.08);
            osc.frequency.exponentialRampToValueAtTime(783.99, ctx.currentTime + 0.18);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        } else if (type === 'wrong') {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(170, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.22);
            osc.start();
            osc.stop(ctx.currentTime + 0.22);
        } else {
            osc.type = 'sine';
            osc.frequency.setValueAtTime(type === 'key' ? 440 : 587, ctx.currentTime);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        }
    } catch {
        // AudioContext not permitted or blocked
    }
}

function speakWord(text) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text);
        u.lang = 'id-ID';
        u.rate = 0.85;
        window.speechSynthesis.speak(u);
    }
}

// ─── Card 1: Canvas Tracing ───
const miniCanvasRef = ref(null);
const isDrawing = ref(false);
const activeTraceChar = ref('ᮊ');
const activeTraceName = ref('Ka');
const traceOptions = [
    { char: 'ᮊ', name: 'Ka' },
    { char: 'ᮃ', name: 'A' },
    { char: 'ᮛ', name: 'Ra' },
    { char: 'ᮒ', name: 'Ta' },
];

function drawCanvasBackground() {
    const canvas = miniCanvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Soft dashed guide crosshairs
    ctx.save();
    ctx.strokeStyle = '#FED7AA';
    ctx.lineWidth = 1.5;
    ctx.setLineDash([4, 4]);

    // Horizontal line
    ctx.beginPath();
    ctx.moveTo(16, canvas.height / 2);
    ctx.lineTo(canvas.width - 16, canvas.height / 2);
    ctx.stroke();

    // Vertical line
    ctx.beginPath();
    ctx.moveTo(canvas.width / 2, 16);
    ctx.lineTo(canvas.width / 2, canvas.height - 16);
    ctx.stroke();

    // Guide watermark
    ctx.setLineDash([]);
    ctx.font = 'bold 96px "Noto Sans Sundanese", "Segoe UI Historic", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillStyle = '#FED7AA';
    ctx.fillText(activeTraceChar.value, canvas.width / 2, canvas.height / 2 + 6);
    ctx.restore();
}

function selectTraceChar(opt) {
    activeTraceChar.value = opt.char;
    activeTraceName.value = opt.name;
    playSound('pop');
    drawCanvasBackground();
}

function clearMiniCanvas() {
    playSound('pop');
    drawCanvasBackground();
}

function getPos(e) {
    const canvas = miniCanvasRef.value;
    if (!canvas) return { x: 0, y: 0 };
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    let clientX = 0;
    let clientY = 0;
    if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    } else {
        clientX = e.clientX;
        clientY = e.clientY;
    }
    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY,
    };
}

function startDrawing(e) {
    isDrawing.value = true;
    const pos = getPos(e);
    const canvas = miniCanvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
    ctx.strokeStyle = '#FF4D30';
    ctx.lineWidth = 8;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
}

function drawTrace(e) {
    if (!isDrawing.value) return;
    const pos = getPos(e);
    const canvas = miniCanvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing.value = false;
}

// ─── Card 2: Daily Streak & Level ───
const streakDays = ref([
    { label: 'Sen', checked: true },
    { label: 'Sel', checked: true },
    { label: 'Rab', checked: true },
    { label: 'Kam', checked: true },
    { label: 'Jum', checked: true },
    { label: 'Sab', checked: true },
    { label: 'Min', checked: false },
]);
const streakCount = ref(6);
const hasCheckedInToday = ref(false);
const xpCount = ref(450);
const streakNotification = ref('');

function toggleCheckIn() {
    if (!hasCheckedInToday.value) {
        hasCheckedInToday.value = true;
        streakDays.value[6].checked = true;
        streakCount.value = 7;
        xpCount.value = 500;
        streakNotification.value = '🎉 Hore! Check-in berhasil: +50 XP & 10 Koin!';
        playSound('success');
    } else {
        hasCheckedInToday.value = false;
        streakDays.value[6].checked = false;
        streakCount.value = 6;
        xpCount.value = 450;
        streakNotification.value = '';
        playSound('pop');
    }
}

// ─── Card 3: Kuis Aksara ───
const quizList = [
    {
        char: 'ᮊ',
        question: 'Huruf aksara Sunda di atas berbunyi apa?',
        options: [
            { text: 'Ka', correct: true },
            { text: 'Ga', correct: false },
            { text: 'Nga', correct: false },
            { text: 'Ta', correct: false },
        ],
        hint: 'Bentuknya khas menyerupai angka 77 kembar!',
        sound: 'Ka'
    },
    {
        char: 'ᮃ',
        question: 'Aksara Swara berikut melambangkan vokal apa?',
        options: [
            { text: 'I', correct: false },
            { text: 'A', correct: true },
            { text: 'U', correct: false },
            { text: 'E', correct: false },
        ],
        hint: 'Merupakan huruf vokal pertama dalam aksara Sunda!',
        sound: 'A'
    },
    {
        char: 'ᮛ',
        question: 'Manakah nama yang tepat untuk konsonan ini?',
        options: [
            { text: 'La', correct: false },
            { text: 'Wa', correct: false },
            { text: 'Ra', correct: true },
            { text: 'Sa', correct: false },
        ],
        hint: 'Konsonan ini berbunyi getar lidah "Ra"!',
        sound: 'Ra'
    }
];

const currentQuizIndex = ref(0);
const selectedQuizOption = ref(null);
const quizState = ref('idle');
const isHintActive = ref(false);
const eliminatedOptions = ref([]);

function answerQuiz(option) {
    if (quizState.value === 'correct') return;
    selectedQuizOption.value = option.text;
    if (option.correct) {
        quizState.value = 'correct';
        playSound('success');
    } else {
        quizState.value = 'wrong';
        playSound('wrong');
    }
}

function activateHint() {
    isHintActive.value = true;
    playSound('pop');
    const current = quizList[currentQuizIndex.value];
    const wrong = current.options.filter(o => !o.correct).slice(0, 2).map(o => o.text);
    eliminatedOptions.value = wrong;
}

function nextQuiz() {
    currentQuizIndex.value = (currentQuizIndex.value + 1) % quizList.length;
    selectedQuizOption.value = null;
    quizState.value = 'idle';
    isHintActive.value = false;
    eliminatedOptions.value = [];
    playSound('pop');
}

// ─── Card 4: Virtual Keyboard & Suara ───
const keyboardKeys = [
    { char: 'ᮃ', latin: 'a' },
    { char: 'ᮊ', latin: 'ka' },
    { char: 'ᮞ', latin: 'sa' },
    { char: 'ᮔ', latin: 'na' },
    { char: 'ᮒ', latin: 'ta' },
    { char: 'ᮛ', latin: 'ra' },
    { char: 'ᮕ', latin: 'pa' },
    { char: 'ᮘ', latin: 'ba' },
    { char: 'ᮙ', latin: 'ma' },
    { char: 'ᮠ', latin: 'ha' },
];

const typedItems = ref([
    { char: 'ᮃ', latin: 'a' },
    { char: 'ᮊ', latin: 'ka' },
    { char: 'ᮞ', latin: 'sa' },
    { char: 'ᮔ', latin: 'na' },
    { char: 'ᮒ', latin: 'ta' },
    { char: 'ᮛ', latin: 'ra' },
]);

const activeKeyChar = ref(null);

function pressKey(key) {
    playSound('key');
    activeKeyChar.value = key.char;
    setTimeout(() => {
        if (activeKeyChar.value === key.char) activeKeyChar.value = null;
    }, 180);

    if (typedItems.value.length < 12) {
        typedItems.value.push(key);
    }
}

function backspaceKey() {
    playSound('pop');
    typedItems.value.pop();
}

function clearTyped() {
    playSound('pop');
    typedItems.value = [];
}

function playVoice() {
    playSound('pop');
    if (typedItems.value.length === 0) {
        speakWord('Ketik aksara terlebih dahulu');
        return;
    }
    const pronunciation = typedItems.value.map(k => k.latin).join(' ');
    speakWord(pronunciation);
}

onMounted(() => {
    drawCanvasBackground();
});

// ─── FAQ Accordion ───
const faqs = [
    {
        q: 'Aksantara itu apa sih?',
        a: 'Aksantara adalah aplikasi seru untuk belajar aksara Nusantara kayak Aksara Jawa, Sunda, dan Bali. Di sini kamu bisa belajar sambil main, nulis aksara, dengerin suara huruf, baca cerita rakyat, dan kumpulin XP buat naik level! 🎮',
    },
    {
        q: 'Aku harus bayar nggak?',
        a: 'Nggak dong! Aksantara GRATIS buat semua anak Indonesia. Tinggal daftar dan langsung mulai petualanganmu! 🆓',
    },
    {
        q: 'Gimana cara mainnya?',
        a: 'Gampang banget! Pilih aksara yang mau kamu pelajari, terus ikuti langkah-langkahnya: belajar huruf → latihan nulis di canvas → jawab kuis → buka cerita rakyat. Setiap hari belajar kamu dapat streak & XP! 🔥',
    },
    {
        q: 'Bisa dipake di HP nggak?',
        a: 'Bisa banget! Aksantara bisa dibuka dari HP, tablet, atau komputer. Belajar kapan aja, di mana aja! 📱',
    },
];

const openFaq = ref(null);

function toggleFaq(index) {
    openFaq.value = openFaq.value === index ? null : index;
}

// ─── Scroll to section ───
function scrollTo(id) {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
}

// ─── Mobile nav ───
const mobileMenuOpen = ref(false);
</script>

<template>
    <Head title="Aksantara — Petualangan Aksara Nusantara" />

    <div class="min-h-screen bg-orange-50 text-gray-800 font-sans selection:bg-[#FF4D30] selection:text-white overflow-x-hidden">

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  NAVBAR                                                -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-orange-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <span class="text-3xl">🔤</span>
                        <span class="text-xl font-extrabold text-[#FF4D30]">Aksantara</span>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-6 text-sm font-semibold text-gray-600">
                        <button @click="scrollTo('kenapa')" class="hover:text-[#FF4D30] transition">Kenapa Aksantara?</button>
                        <button @click="scrollTo('fitur')" class="hover:text-[#FF4D30] transition">Fitur</button>
                        <button @click="scrollTo('faq')" class="hover:text-[#FF4D30] transition">FAQ</button>
                        <template v-if="canLogin">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="ml-2 px-5 py-2 bg-[#FF4D30] text-white rounded-xl font-bold hover:bg-[#e64328] transition"
                            >
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link :href="route('login')" class="hover:text-[#FF4D30] transition">Masuk</Link>
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="ml-1 px-5 py-2 bg-[#FF4D30] text-white rounded-xl font-bold hover:bg-[#e64328] transition"
                                >
                                    Daftar Gratis
                                </Link>
                            </template>
                        </template>
                    </div>

                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl hover:bg-orange-100 transition">
                        <svg class="w-6 h-6 text-[#FF4D30]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div v-if="mobileMenuOpen" class="md:hidden pb-4 space-y-2 text-sm font-semibold text-gray-600">
                        <button @click="scrollTo('kenapa'); mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-xl hover:bg-orange-100 transition">Kenapa Aksantara?</button>
                        <button @click="scrollTo('fitur'); mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-xl hover:bg-orange-100 transition">Fitur</button>
                        <button @click="scrollTo('faq'); mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-xl hover:bg-orange-100 transition">FAQ</button>
                        <template v-if="canLogin">
                            <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="block px-3 py-2 text-[#FF4D30] font-bold">Dashboard</Link>
                            <template v-else>
                                <Link :href="route('login')" class="block px-3 py-2 rounded-xl hover:bg-orange-100">Masuk</Link>
                                <Link v-if="canRegister" :href="route('register')" class="block px-3 py-2 bg-[#FF4D30] text-white text-center rounded-xl font-bold">Daftar Gratis</Link>
                            </template>
                        </template>
                    </div>
                </Transition>
            </div>
        </nav>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  HERO                                                  -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section class="relative py-20 sm:py-28 lg:py-36 overflow-hidden">
            <!-- Background decorations -->
            <div class="absolute top-10 left-10 w-72 h-72 bg-orange-200/40 rounded-full blur-3xl -z-10"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-red-200/30 rounded-full blur-3xl -z-10"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-yellow-100/30 rounded-full blur-3xl -z-10"></div>

            <!-- Floating emoji decorations -->
            <div class="absolute top-16 right-[15%] text-5xl animate-bounce" style="animation-delay: 0s; animation-duration: 3s;">✏️</div>
            <div class="absolute top-32 left-[10%] text-4xl animate-bounce" style="animation-delay: 0.5s; animation-duration: 3.5s;">📜</div>
            <div class="absolute bottom-20 right-[20%] text-5xl animate-bounce" style="animation-delay: 1s; animation-duration: 4s;">🏆</div>
            <div class="absolute bottom-32 left-[18%] text-4xl animate-bounce" style="animation-delay: 1.5s; animation-duration: 3.2s;">🎯</div>
            <div class="hidden lg:block absolute top-24 right-[35%] text-3xl animate-bounce" style="animation-delay: 0.7s; animation-duration: 3.8s;">⭐</div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <!-- Left: Text -->
                    <div class="flex-1 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-[#FF4D30] rounded-full text-sm font-bold mb-6">
                            <span class="animate-pulse">🔥</span> Gratis & Seru untuk Anak Indonesia!
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight text-gray-900">
                            Yuk, Jadi Pahlawan
                            <span class="text-[#FF4D30] relative">
                                Aksara Nusantara!
                                <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 300 12" fill="none">
                                    <path d="M2 10C50 2 100 2 150 6C200 10 250 4 298 8" stroke="#FF4D30" stroke-width="3" stroke-linecap="round" opacity="0.3"/>
                                </svg>
                            </span>
                        </h1>

                        <p class="mt-6 text-lg sm:text-xl text-gray-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                            Petualangan seru belajar menulis dan membaca
                            <strong class="text-gray-800">Aksara Jawa, Sunda, & Bali</strong>!
                            Kumpulkan XP, jaga streak harianmu, dan buka cerita rakyat keren! 🚀
                        </p>

                        <div class="mt-8 flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                            <Link
                                :href="canRegister ? route('register') : '#'"
                                class="w-full sm:w-auto px-8 py-4 bg-[#FF4D30] text-white text-lg font-extrabold rounded-2xl shadow-lg shadow-[#FF4D30]/30 hover:bg-[#e64328] hover:shadow-xl hover:scale-105 transition-all duration-200 text-center"
                            >
                                🎮 Mulai Belajar
                            </Link>
                            <button
                                @click="scrollTo('fitur')"
                                class="w-full sm:w-auto px-8 py-4 bg-white text-[#FF4D30] text-lg font-extrabold rounded-2xl border-2 border-[#FF4D30] hover:bg-orange-50 hover:scale-105 transition-all duration-200"
                            >
                                ✨ Lihat Fitur
                            </button>
                        </div>

                        <!-- Social proof -->
                        <div class="mt-10 flex items-center gap-4 justify-center lg:justify-start">
                            <div class="flex -space-x-3">
                                <div class="w-10 h-10 rounded-full bg-orange-300 border-2 border-white flex items-center justify-center text-sm">👧</div>
                                <div class="w-10 h-10 rounded-full bg-red-300 border-2 border-white flex items-center justify-center text-sm">👦</div>
                                <div class="w-10 h-10 rounded-full bg-yellow-300 border-2 border-white flex items-center justify-center text-sm">👧</div>
                                <div class="w-10 h-10 rounded-full bg-green-300 border-2 border-white flex items-center justify-center text-sm">👦</div>
                            </div>
                            <p class="text-sm text-gray-500"><strong class="text-gray-700">500+</strong> anak sudah bergabung!</p>
                        </div>
                    </div>

                    <!-- Right: Illustration / visual card -->
                    <div class="flex-1 max-w-md lg:max-w-lg">
                        <div class="relative">
                            <!-- Main card -->
                            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-orange-100">
                                <div class="text-center">
                                    <div class="text-8xl mb-4">ꦲ</div>
                                    <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Aksara Jawa</div>
                                    <div class="text-3xl font-extrabold text-gray-800">"Ha"</div>
                                    <div class="mt-4 flex justify-center gap-2">
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">✅ Dipelajari</span>
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">+10 XP</span>
                                    </div>
                                </div>

                                <!-- Progress bar -->
                                <div class="mt-6">
                                    <div class="flex justify-between text-xs font-bold text-gray-500 mb-2">
                                        <span>Penguasaan</span>
                                        <span class="text-[#FF4D30]">75%</span>
                                    </div>
                                    <div class="w-full h-3 bg-orange-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-[#FF4D30] to-orange-400 rounded-full transition-all duration-1000" style="width: 75%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating badge: streak -->
                            <div class="absolute -top-4 -right-4 bg-white rounded-2xl shadow-lg px-4 py-3 border border-orange-100 flex items-center gap-2">
                                <span class="text-2xl">🔥</span>
                                <div>
                                    <div class="text-xs text-gray-400 font-bold">Streak</div>
                                    <div class="text-lg font-extrabold text-[#FF4D30]">7 Hari</div>
                                </div>
                            </div>

                            <!-- Floating badge: level -->
                            <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lg px-4 py-3 border border-orange-100 flex items-center gap-2">
                                <span class="text-2xl">⭐</span>
                                <div>
                                    <div class="text-xs text-gray-400 font-bold">Level</div>
                                    <div class="text-lg font-extrabold text-yellow-500">12</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  KENAPA BELAJAR DI AKSANTARA?                          -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="kenapa" class="py-20 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="inline-block px-4 py-1.5 bg-orange-100 text-[#FF4D30] rounded-full text-sm font-bold mb-4">🤔 Kenapa Aksantara?</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Kenapa Belajar di Aksantara?</h2>
                    <p class="mt-3 text-gray-500 text-lg">Bikin belajar aksara daerah jadi seru, gampang, dan nggak ngebosenin!</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto">

                    <!-- Card 1 — Belajar Sambil Main -->
                    <div class="group bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-lg transition-all duration-300 text-center">
                        <!-- Icon -->
                        <div class="w-16 h-16 mx-auto bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#FF4D30]/10 transition-colors duration-300">
                            <svg class="w-8 h-8 text-[#FF4D30]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875S10.5 3.089 10.5 4.125c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.491 48.491 0 0 1-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 0 1-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 0 0-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 0 1-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 0 0 .657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 0 1-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 0 0 5.427-.63 48.05 48.05 0 0 0 .582-4.717.532.532 0 0 0-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 0 0 .657-.663 48.422 48.422 0 0 0-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 0 1-.61-.58v0Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-800 mb-3">Belajar Sambil Main</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Latihan nulis dan kuis interaktif yang seru serasa lagi main game petualangan. 🎮
                        </p>
                    </div>

                    <!-- Card 2 — Gambar & Suara Lucu -->
                    <div class="group bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-lg transition-all duration-300 text-center">
                        <!-- Icon -->
                        <div class="w-16 h-16 mx-auto bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#FF4D30]/10 transition-colors duration-300">
                            <svg class="w-8 h-8 text-[#FF4D30]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-800 mb-3">Gambar & Suara Lucu</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Ada animasi karakter ceria dan suara pelafalan asli biar kamu cepet paham. 🔊
                        </p>
                    </div>

                    <!-- Card 3 — Petualangan Cerita -->
                    <div class="group bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-lg transition-all duration-300 text-center">
                        <!-- Icon -->
                        <div class="w-16 h-16 mx-auto bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#FF4D30]/10 transition-colors duration-300">
                            <svg class="w-8 h-8 text-[#FF4D30]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m0 0-3-3m3 3 3-3m-3 3V6.75M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h1.811a2.956 2.956 0 0 1 2.939 2.667l.037.333a1.5 1.5 0 0 0 1.49 1.334h.678a1.5 1.5 0 0 0 1.49-1.334l.036-.333A2.956 2.956 0 0 1 12.92 13.5h1.811V6.375c0-1.036-.84-1.875-1.875-1.875h-9.48ZM12.92 13.5h1.811V6.375c0-1.036.84-1.875 1.875-1.875h3.019c1.035 0 1.875.84 1.875 1.875V13.5h-1.811a2.956 2.956 0 0 0-2.939 2.667l-.036.333a1.5 1.5 0 0 1-1.49 1.334h-.679a1.5 1.5 0 0 1-1.49-1.334l-.036-.333A2.956 2.956 0 0 0 12.92 13.5Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-800 mb-3">Petualangan Cerita</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Kumpulin poin tiap level buat ngebuka cerita rakyat Nusantara yang keren! 📖
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  FITUR UTAMA — CARD SHOWCASE GRID                      -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="fitur" class="py-20 sm:py-28 bg-[#FFFDF7] relative overflow-hidden">
            <!-- Subtle background glowing bubbles -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-100/50 rounded-full blur-3xl -z-10"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-yellow-100/50 rounded-full blur-3xl -z-10"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16 sm:mb-20">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-orange-100 text-[#FF4D30] rounded-full text-sm font-extrabold shadow-sm mb-4">
                        <span>🎯</span> Fitur Interaktif Unggulan
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-black text-gray-900 tracking-tight">
                        Eksplorasi Fitur Seru Aksantara!
                    </h2>
                    <p class="mt-4 text-gray-600 text-lg sm:text-xl max-w-2xl mx-auto leading-relaxed">
                        Coba langsung simulasi interaktif di bawah ini dan rasakan pengalaman belajar aksara yang hidup dan menyenangkan! ✨
                    </p>
                </div>

                <!-- 2x2 Showcase Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">

                    <!-- ═══════════════════════════════════════════════ -->
                    <!-- CARD 1: CANVAS TRACING                          -->
                    <!-- ═══════════════════════════════════════════════ -->
                    <div class="group bg-gradient-to-br from-amber-50/90 via-orange-50/50 to-white rounded-3xl p-7 sm:p-9 border border-amber-200/70 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Header Bar -->
                            <div class="flex items-center justify-between mb-5">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white shadow-lg shadow-orange-500/20 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform duration-300">
                                    ✏️
                                </div>
                                <span class="px-3.5 py-1 bg-amber-100/80 text-amber-800 rounded-full text-xs font-black tracking-wide border border-amber-200">
                                    ✍️ LATIHAN MENULIS
                                </span>
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-black text-gray-800 mb-3 tracking-tight">
                                Canvas Tracing
                            </h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-5">
                                Latih kelenturan tangan menulis aksara langsung di layar! Ikuti garis bantu transparan, nikmati goresan kuas halus, dan kuasai lekukan setiap huruf secara alami.
                            </p>

                            <!-- Feature Tags -->
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-3 py-1 bg-white/90 text-amber-800 rounded-xl text-xs font-bold border border-amber-100 shadow-xs">🖊️ Goresan Halus</span>
                                <span class="px-3 py-1 bg-white/90 text-amber-800 rounded-xl text-xs font-bold border border-amber-100 shadow-xs">📐 Garis Panduan</span>
                                <span class="px-3 py-1 bg-white/90 text-amber-800 rounded-xl text-xs font-bold border border-amber-100 shadow-xs">🖐️ Sentuh & Mouse</span>
                            </div>
                        </div>

                        <!-- Mini Demo Showcase -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-4 sm:p-5 border border-amber-200/80 shadow-inner">
                            <!-- Character selector buttons -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-xs font-bold text-gray-500">Pilih Huruf:</span>
                                    <button
                                        v-for="opt in traceOptions"
                                        :key="opt.char"
                                        @click="selectTraceChar(opt)"
                                        class="px-2.5 py-1 rounded-lg text-xs font-black transition-all"
                                        :class="activeTraceChar === opt.char
                                            ? 'bg-[#FF4D30] text-white shadow-sm'
                                            : 'bg-amber-100/70 text-amber-900 hover:bg-amber-200/70'"
                                    >
                                        {{ opt.char }} ({{ opt.name }})
                                    </button>
                                </div>
                                <button
                                    @click="clearMiniCanvas"
                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded-lg text-xs font-bold transition flex items-center gap-1 border border-amber-200"
                                    title="Bersihkan kanvas"
                                >
                                    <span>🧹</span> Hapus
                                </button>
                            </div>

                            <!-- Interactive Canvas Box -->
                            <div class="relative w-full rounded-2xl border-2 border-dashed border-amber-300 bg-amber-50/40 overflow-hidden flex items-center justify-center">
                                <canvas
                                    ref="miniCanvasRef"
                                    width="360"
                                    height="180"
                                    class="w-full h-44 cursor-crosshair touch-none select-none"
                                    @mousedown="startDrawing"
                                    @mousemove="drawTrace"
                                    @mouseup="stopDrawing"
                                    @mouseleave="stopDrawing"
                                    @touchstart.passive="startDrawing"
                                    @touchmove.prevent="drawTrace"
                                    @touchend="stopDrawing"
                                ></canvas>

                                <!-- Helper instruction overlay -->
                                <div class="absolute bottom-2 right-3 pointer-events-none text-[11px] font-bold text-amber-600/80 bg-white/70 px-2 py-0.5 rounded-md backdrop-blur-xs">
                                    ✍️ Coretkan jarimu / mouse di sini
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════════════ -->
                    <!-- CARD 2: DAILY STREAK & LEVEL                    -->
                    <!-- ═══════════════════════════════════════════════ -->
                    <div class="group bg-gradient-to-br from-yellow-50/90 via-amber-50/50 to-white rounded-3xl p-7 sm:p-9 border border-yellow-200/70 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Header Bar -->
                            <div class="flex items-center justify-between mb-5">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-yellow-400 to-amber-500 text-white shadow-lg shadow-yellow-500/20 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform duration-300">
                                    🔥
                                </div>
                                <span class="px-3.5 py-1 bg-yellow-100/80 text-yellow-800 rounded-full text-xs font-black tracking-wide border border-yellow-200">
                                    ⚡ GAMIFIKASI
                                </span>
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-black text-gray-800 mb-3 tracking-tight">
                                Daily Streak & Level
                            </h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-5">
                                Ritme belajar harian yang memotivasi! Jaga api streak tetap membara, raih bonus koin, dan kumpulkan XP untuk menaikkan level pahlawan aksaramu.
                            </p>

                            <!-- Feature Tags -->
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-3 py-1 bg-white/90 text-yellow-800 rounded-xl text-xs font-bold border border-yellow-100 shadow-xs">🔥 Api Streak</span>
                                <span class="px-3 py-1 bg-white/90 text-yellow-800 rounded-xl text-xs font-bold border border-yellow-100 shadow-xs">⭐ Akumulasi XP</span>
                                <span class="px-3 py-1 bg-white/90 text-yellow-800 rounded-xl text-xs font-bold border border-yellow-100 shadow-xs">🪙 Koin Hadiah</span>
                            </div>
                        </div>

                        <!-- Mini Demo Showcase -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-4 sm:p-5 border border-yellow-200/80 shadow-inner space-y-4">
                            <!-- Top Streak Banner -->
                            <div class="flex items-center justify-between bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-3 border border-amber-200">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#FF4D30] to-orange-400 text-white flex items-center justify-center text-xl shadow-sm animate-pulse">
                                        🔥
                                    </div>
                                    <div>
                                        <div class="text-base font-black text-gray-800">{{ streakCount }} Hari Beruntun!</div>
                                        <div class="text-xs text-gray-500 font-bold">Ritme belajar terjaga sangat baik</div>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-white text-orange-600 rounded-lg text-xs font-black shadow-xs border border-orange-100">
                                    🏆 Rekor: 14 Hari
                                </span>
                            </div>

                            <!-- 7-Day Circles Tracker -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-500">Tracker Mingguan:</span>
                                    <span class="text-xs font-extrabold text-[#FF4D30]">
                                        {{ hasCheckedInToday ? '✓ Hari ini selesai' : '⏳ Belum check-in' }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                                    <div
                                        v-for="(item, idx) in streakDays"
                                        :key="idx"
                                        class="flex flex-col items-center gap-1"
                                    >
                                        <div
                                            class="w-full aspect-square max-w-[42px] rounded-xl flex items-center justify-center text-xs font-black transition-all duration-200"
                                            :class="item.checked
                                                ? 'bg-gradient-to-tr from-[#FF4D30] to-orange-400 text-white shadow-sm scale-100'
                                                : 'bg-yellow-100/70 text-yellow-600 border border-yellow-200/70'"
                                        >
                                            <span v-if="item.checked">🔥</span>
                                            <span v-else class="text-[11px] opacity-60">○</span>
                                        </div>
                                        <span class="text-[11px] font-bold" :class="item.checked ? 'text-gray-800' : 'text-gray-400'">
                                            {{ item.label }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Check-in Button -->
                            <div class="flex flex-col sm:flex-row items-center gap-2 pt-1">
                                <button
                                    @click="toggleCheckIn"
                                    class="w-full py-2.5 px-4 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all shadow-sm"
                                    :class="hasCheckedInToday
                                        ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                                        : 'bg-gradient-to-r from-[#FF4D30] to-orange-500 hover:from-[#e64328] hover:to-orange-600 text-white hover:scale-[1.02]'"
                                >
                                    <span>{{ hasCheckedInToday ? '✅ Sudah Check-in Hari Ini' : '🎯 Coba Check-in Hari Ini (+50 XP)' }}</span>
                                </button>
                            </div>

                            <!-- Interactive Level Progress Bar -->
                            <div class="pt-1 border-t border-yellow-100">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <span class="text-gray-700 flex items-center gap-1">
                                        <span>⭐</span> <strong>Level 5</strong> • Penjelajah Cilik
                                    </span>
                                    <span class="text-amber-700 font-extrabold">{{ xpCount }} / 600 XP</span>
                                </div>
                                <div class="w-full h-2.5 bg-yellow-100 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full transition-all duration-500"
                                        :style="{ width: `${(xpCount / 600) * 100}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════════════ -->
                    <!-- CARD 3: KUIS AKSARA                             -->
                    <!-- ═══════════════════════════════════════════════ -->
                    <div class="group bg-gradient-to-br from-emerald-50/90 via-teal-50/50 to-white rounded-3xl p-7 sm:p-9 border border-emerald-200/70 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Header Bar -->
                            <div class="flex items-center justify-between mb-5">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white shadow-lg shadow-emerald-500/20 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform duration-300">
                                    🎯
                                </div>
                                <span class="px-3.5 py-1 bg-emerald-100/80 text-emerald-800 rounded-full text-xs font-black tracking-wide border border-emerald-200">
                                    🧠 EVALUASI CEPAT
                                </span>
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-black text-gray-800 mb-3 tracking-tight">
                                Kuis Aksara
                            </h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-5">
                                Tantang daya ingat dengan kuis tebak aksara yang interaktif! Lengkap dengan tombol bantuan hint pintar untuk mengeliminasi jawaban yang salah.
                            </p>

                            <!-- Feature Tags -->
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-3 py-1 bg-white/90 text-emerald-800 rounded-xl text-xs font-bold border border-emerald-100 shadow-xs">🎮 Pilihan Ganda</span>
                                <span class="px-3 py-1 bg-white/90 text-emerald-800 rounded-xl text-xs font-bold border border-emerald-100 shadow-xs">💡 Bantuan Hint</span>
                                <span class="px-3 py-1 bg-white/90 text-emerald-800 rounded-xl text-xs font-bold border border-emerald-100 shadow-xs">⚡ Umpan Balik Instan</span>
                            </div>
                        </div>

                        <!-- Mini Demo Showcase -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-4 sm:p-5 border border-emerald-200/80 shadow-inner space-y-3.5">
                            <!-- Question Bar -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-500">
                                    Soal {{ currentQuizIndex + 1 }} dari {{ quizList.length }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <button
                                        @click="activateHint"
                                        :disabled="isHintActive"
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold flex items-center gap-1 transition"
                                        :class="isHintActive
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                            : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'"
                                        title="Eliminasi 2 pilihan yang salah"
                                    >
                                        <span>💡</span> Hint
                                    </button>
                                    <button
                                        @click="nextQuiz"
                                        class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                        title="Ganti ke soal berikutnya"
                                    >
                                        <span>🔄</span> Soal Lain
                                    </button>
                                </div>
                            </div>

                            <!-- Featured Aksara Card -->
                            <div class="flex items-center justify-between bg-emerald-50/60 rounded-xl p-3 border border-emerald-100">
                                <div>
                                    <div class="text-xs font-bold text-emerald-800">Tebak lafal huruf berikut:</div>
                                    <div class="text-xs text-gray-500">{{ quizList[currentQuizIndex].question }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-4xl sm:text-5xl font-black text-emerald-900 px-3 py-1 bg-white rounded-xl shadow-xs border border-emerald-100">
                                        {{ quizList[currentQuizIndex].char }}
                                    </span>
                                </div>
                            </div>

                            <!-- Hint Banner (if active) -->
                            <div v-if="isHintActive" class="px-3 py-1.5 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-800 font-bold flex items-center gap-2">
                                <span>💡</span>
                                <span>{{ quizList[currentQuizIndex].hint }}</span>
                            </div>

                            <!-- 2x2 Answer Grid -->
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    v-for="opt in quizList[currentQuizIndex].options"
                                    :key="opt.text"
                                    @click="answerQuiz(opt)"
                                    :disabled="eliminatedOptions.includes(opt.text) || quizState === 'correct'"
                                    class="py-2.5 px-3 rounded-xl font-black text-sm flex items-center justify-between transition-all"
                                    :class="[
                                        eliminatedOptions.includes(opt.text)
                                            ? 'bg-gray-100 text-gray-400 line-through opacity-50 cursor-not-allowed'
                                            : selectedQuizOption === opt.text
                                                ? opt.correct
                                                    ? 'bg-emerald-500 text-white shadow-sm ring-2 ring-emerald-300'
                                                    : 'bg-rose-500 text-white shadow-sm ring-2 ring-rose-300'
                                                : 'bg-white hover:bg-emerald-50 text-gray-700 border border-emerald-100 shadow-xs'
                                    ]"
                                >
                                    <span>{{ opt.text }}</span>
                                    <span v-if="selectedQuizOption === opt.text">
                                        {{ opt.correct ? '✓' : '✕' }}
                                    </span>
                                </button>
                            </div>

                            <!-- Feedback Toast -->
                            <div
                                v-if="quizState !== 'idle'"
                                class="text-center py-1.5 px-3 rounded-xl text-xs font-black transition-all"
                                :class="quizState === 'correct' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                            >
                                <span v-if="quizState === 'correct'">🎉 Jawaban Tepat! Kamu mendapatkan +20 XP!</span>
                                <span v-else>❌ Belum tepat, coba gunakan tombol hint atau opsi lain!</span>
                            </div>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════════════ -->
                    <!-- CARD 4: VIRTUAL KEYBOARD & SUARA                -->
                    <!-- ═══════════════════════════════════════════════ -->
                    <div class="group bg-gradient-to-br from-violet-50/90 via-purple-50/50 to-white rounded-3xl p-7 sm:p-9 border border-violet-200/70 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Header Bar -->
                            <div class="flex items-center justify-between mb-5">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-violet-600 to-purple-500 text-white shadow-lg shadow-violet-500/20 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform duration-300">
                                    ⌨️
                                </div>
                                <span class="px-3.5 py-1 bg-violet-100/80 text-violet-800 rounded-full text-xs font-black tracking-wide border border-violet-200">
                                    🔊 PAPAN KETIK AUDIO
                                </span>
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-black text-gray-800 mb-3 tracking-tight">
                                Virtual Keyboard & Suara
                            </h3>
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-5">
                                Ketik aksara dengan keyboard digital yang ramah anak. Setiap huruf dilengkapi audio pelafalan jelas untuk melatih kemampuan berbicara dan transliterasi.
                            </p>

                            <!-- Feature Tags -->
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-3 py-1 bg-white/90 text-violet-800 rounded-xl text-xs font-bold border border-violet-100 shadow-xs">⌨️ Tombol Aksara</span>
                                <span class="px-3 py-1 bg-white/90 text-violet-800 rounded-xl text-xs font-bold border border-violet-100 shadow-xs">🔊 Audio Pelafalan</span>
                                <span class="px-3 py-1 bg-white/90 text-violet-800 rounded-xl text-xs font-bold border border-violet-100 shadow-xs">🔤 Transliterasi Latin</span>
                            </div>
                        </div>

                        <!-- Mini Demo Showcase -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-4 sm:p-5 border border-violet-200/80 shadow-inner space-y-3.5">
                            <!-- Typed Output Screen -->
                            <div class="bg-violet-50/60 rounded-xl p-3 border border-violet-100 min-h-[66px] flex flex-col justify-center">
                                <div v-if="typedItems.length > 0" class="flex flex-wrap items-baseline gap-1.5">
                                    <span
                                        v-for="(item, idx) in typedItems"
                                        :key="idx"
                                        class="text-xl sm:text-2xl font-black text-violet-900 tracking-wider inline-block"
                                    >
                                        {{ item.char }}
                                    </span>
                                </div>
                                <div v-else class="text-xs italic text-gray-400 font-bold">
                                    Klik tombol aksara di bawah untuk mulai mengetik...
                                </div>

                                <!-- Phonetic transcription bar -->
                                <div v-if="typedItems.length > 0" class="text-[11px] font-bold text-violet-600 mt-1">
                                    Lafal: {{ typedItems.map(k => k.latin).join(' • ') }}
                                </div>
                            </div>

                            <!-- Virtual Keyboard Grid (2 Rows of 5 Keys) -->
                            <div class="grid grid-cols-5 gap-1.5">
                                <button
                                    v-for="key in keyboardKeys"
                                    :key="key.char"
                                    @click="pressKey(key)"
                                    class="h-12 bg-white hover:bg-violet-500 hover:text-white rounded-xl border border-violet-100 shadow-xs flex flex-col items-center justify-center transition-all cursor-pointer select-none active:scale-95"
                                    :class="{ 'bg-violet-600 text-white ring-2 ring-violet-300 scale-95': activeKeyChar === key.char }"
                                >
                                    <span class="text-base font-black leading-none">{{ key.char }}</span>
                                    <span class="text-[10px] font-extrabold opacity-70 leading-none mt-0.5">{{ key.latin }}</span>
                                </button>
                            </div>

                            <!-- Bottom Action Controls -->
                            <div class="flex items-center gap-2 pt-1">
                                <button
                                    @click="playVoice"
                                    class="flex-1 py-2 px-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-xl text-xs font-black flex items-center justify-center gap-1.5 transition shadow-sm"
                                >
                                    <span>🔊</span> Bunyikan Lafal
                                </button>
                                <button
                                    @click="backspaceKey"
                                    class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-black transition"
                                    title="Hapus huruf terakhir"
                                >
                                    ⌫ Hapus
                                </button>
                                <button
                                    @click="clearTyped"
                                    class="py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-black transition border border-rose-100"
                                    title="Bersihkan teks"
                                >
                                    🧹 Reset
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  FAQ                                                   -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="faq" class="py-20 sm:py-24">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="inline-block px-4 py-1.5 bg-orange-100 text-[#FF4D30] rounded-full text-sm font-bold mb-4">❓ FAQ</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Sering Ditanyain Nih!</h2>
                    <p class="mt-3 text-gray-500 text-lg">Kalau masih bingung, baca dulu di sini ya 👇</p>
                </div>

                <div class="space-y-4">
                    <div v-for="(faq, index) in faqs" :key="index"
                         class="bg-white rounded-2xl border border-orange-100 overflow-hidden shadow-sm hover:shadow-md transition">
                        <button
                            @click="toggleFaq(index)"
                            class="w-full flex items-center justify-between px-6 py-5 text-left"
                        >
                            <span class="font-bold text-gray-800 text-base pr-4">{{ faq.q }}</span>
                            <svg
                                class="w-5 h-5 text-[#FF4D30] flex-shrink-0 transition-transform duration-300"
                                :class="{ 'rotate-180': openFaq === index }"
                                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="max-h-0 opacity-0"
                            enter-to-class="max-h-96 opacity-100"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="max-h-96 opacity-100"
                            leave-to-class="max-h-0 opacity-0"
                        >
                            <div v-if="openFaq === index" class="overflow-hidden">
                                <div class="px-6 pb-5 text-gray-600 leading-relaxed text-sm">
                                    {{ faq.a }}
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  CTA BOTTOM                                            -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section class="py-20 sm:py-24">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-[#FF4D30] to-orange-500 rounded-3xl p-10 sm:p-14 text-center text-white relative overflow-hidden">
                    <!-- Decorative circles -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                    <div class="relative">
                        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Siap Jadi Pahlawan Aksara? 🦸</h2>
                        <p class="text-white/90 text-lg max-w-xl mx-auto mb-8">
                            Gabung sekarang dan mulai petualangan serumu melestarikan aksara Nusantara. Gratis selamanya!
                        </p>
                        <Link
                            :href="canRegister ? route('register') : '#'"
                            class="inline-block px-10 py-4 bg-white text-[#FF4D30] text-lg font-extrabold rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200"
                        >
                            🚀 Daftar Gratis Sekarang!
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  FOOTER                                                -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-2xl">🔤</span>
                            <span class="text-xl font-extrabold text-white">Aksantara</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Aplikasi belajar aksara Nusantara yang seru dan interaktif untuk anak-anak Indonesia. 🇮🇩
                        </p>
                    </div>

                    <!-- Navigasi -->
                    <div>
                        <h4 class="font-bold text-white mb-4">Navigasi</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><button @click="scrollTo('kenapa')" class="hover:text-[#FF4D30] transition">Kenapa Aksantara?</button></li>
                            <li><button @click="scrollTo('fitur')" class="hover:text-[#FF4D30] transition">Fitur</button></li>
                            <li><button @click="scrollTo('faq')" class="hover:text-[#FF4D30] transition">FAQ</button></li>
                        </ul>
                    </div>

                    <!-- Aksara -->
                    <div>
                        <h4 class="font-bold text-white mb-4">Belajar Aksara</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><span class="hover:text-[#FF4D30] transition cursor-pointer">Aksara Jawa</span></li>
                            <li><span class="hover:text-[#FF4D30] transition cursor-pointer">Aksara Sunda</span></li>
                            <li><span class="hover:text-[#FF4D30] transition cursor-pointer">Aksara Bali</span></li>
                        </ul>
                    </div>

                    <!-- Bantuan -->
                    <div>
                        <h4 class="font-bold text-white mb-4">Bantuan</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><button @click="scrollTo('faq')" class="hover:text-[#FF4D30] transition">Cara Main</button></li>
                            <li><span class="hover:text-[#FF4D30] transition cursor-pointer">Hubungi Kami</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom bar -->
                <div class="mt-10 pt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        &copy; {{ new Date().getFullYear() }} Aksantara. Dibuat dengan ❤️ untuk anak Indonesia.
                    </p>
                    <div class="flex items-center gap-4 text-gray-500 text-sm">
                        <span class="hover:text-[#FF4D30] transition cursor-pointer">Kebijakan Privasi</span>
                        <span>•</span>
                        <span class="hover:text-[#FF4D30] transition cursor-pointer">Syarat & Ketentuan</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
