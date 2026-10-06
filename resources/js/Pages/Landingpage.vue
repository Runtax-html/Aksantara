<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
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
        <!--  FITUR UTAMA — BENTO GRID                              -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="fitur" class="py-20 sm:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="inline-block px-4 py-1.5 bg-orange-100 text-[#FF4D30] rounded-full text-sm font-bold mb-4">🎯 Fitur Seru</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Belajar yang Nggak Bikin Bosan!</h2>
                    <p class="mt-3 text-gray-500 text-lg">Semua fitur didesain khusus buat bikin kamu makin semangat 💪</p>
                </div>

                <!-- Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Canvas Tracing — spans 2 cols on lg -->
                    <div class="lg:col-span-2 bg-gradient-to-br from-orange-50 to-red-50 rounded-3xl p-8 sm:p-10 border border-orange-100 group hover:shadow-xl transition-all duration-300">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <div class="flex-1">
                                <div class="w-14 h-14 bg-[#FF4D30] rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg shadow-[#FF4D30]/20 mb-5">
                                    ✏️
                                </div>
                                <h3 class="text-2xl font-extrabold text-gray-800 mb-3">Canvas Tracing</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Latihan nulis aksara langsung di layar! Ikuti garis bantu, terus tulis sendiri.
                                    Makin sering latihan, tulisanmu makin keren! 🌟
                                </p>
                                <div class="mt-4 flex gap-2 flex-wrap">
                                    <span class="px-3 py-1 bg-white/80 rounded-full text-xs font-bold text-[#FF4D30]">🖊️ Tulis di Layar</span>
                                    <span class="px-3 py-1 bg-white/80 rounded-full text-xs font-bold text-[#FF4D30]">📐 Garis Bantu</span>
                                    <span class="px-3 py-1 bg-white/80 rounded-full text-xs font-bold text-[#FF4D30]">⭐ Skor Otomatis</span>
                                </div>
                            </div>
                            <!-- Mini canvas preview -->
                            <div class="w-full sm:w-48 h-48 bg-white rounded-2xl border-2 border-dashed border-orange-200 flex items-center justify-center flex-shrink-0">
                                <div class="text-center">
                                    <div class="text-6xl text-orange-200 group-hover:text-[#FF4D30] transition-colors duration-500">ꦲ</div>
                                    <div class="text-xs text-gray-400 mt-2 font-bold">Coba tulis di sini!</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Streak & Level -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-3xl p-8 border border-orange-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-14 h-14 bg-yellow-400 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-yellow-400/20 mb-5">
                            🔥
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-800 mb-3">Daily Streak & Level</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Belajar setiap hari biar streakmu nggak putus! Kumpulkan XP dan naik level kayak main game 🎮
                        </p>

                        <!-- Mini streak display -->
                        <div class="mt-6 flex gap-1.5">
                            <div v-for="day in ['S', 'S', 'R', 'K', 'J', 'S', 'M']" :key="day"
                                 class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-extrabold"
                                 :class="day !== 'M' ? 'bg-[#FF4D30] text-white' : 'bg-orange-100 text-orange-300'"
                            >
                                {{ day }}
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 font-bold">🔥 6 hari berturut-turut!</p>
                    </div>

                    <!-- Folktale Unlocker -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-8 border border-emerald-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg shadow-emerald-500/20 mb-5">
                            📖
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-800 mb-3">Petualangan Cerita Rakyat</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Buka cerita rakyat keren setelah belajar aksara! Dari Timun Mas sampai Sangkuriang, semua ada di sini 🏔️
                        </p>

                        <!-- Mini story cards -->
                        <div class="mt-5 space-y-2">
                            <div class="flex items-center gap-3 bg-white/70 rounded-xl px-4 py-2.5">
                                <span class="text-lg">🗝️</span>
                                <span class="text-sm font-bold text-gray-700">Timun Mas</span>
                                <span class="ml-auto text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full font-bold">Terbuka</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white/70 rounded-xl px-4 py-2.5">
                                <span class="text-lg">🔒</span>
                                <span class="text-sm font-bold text-gray-400">Sangkuriang</span>
                                <span class="ml-auto text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full font-bold">Level 5</span>
                            </div>
                        </div>
                    </div>

                    <!-- Virtual Keyboard & Suara — spans 2 cols on lg -->
                    <div class="lg:col-span-2 bg-gradient-to-br from-violet-50 to-purple-50 rounded-3xl p-8 sm:p-10 border border-violet-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <div class="flex-1">
                                <div class="w-14 h-14 bg-violet-500 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg shadow-violet-500/20 mb-5">
                                    ⌨️
                                </div>
                                <h3 class="text-2xl font-extrabold text-gray-800 mb-3">Virtual Keyboard & Suara</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Ketik aksara pakai keyboard virtual yang keren! Tekan huruf dan dengarkan cara bacanya.
                                    Belajar jadi makin mudah dan seru! 🔊
                                </p>
                                <div class="mt-4 flex gap-2 flex-wrap">
                                    <span class="px-3 py-1 bg-white/80 rounded-full text-xs font-bold text-violet-600">⌨️ Keyboard Aksara</span>
                                    <span class="px-3 py-1 bg-white/80 rounded-full text-xs font-bold text-violet-600">🔊 Dengar Suara</span>
                                    <span class="px-3 py-1 bg-white/80 rounded-full text-xs font-bold text-violet-600">💬 Ketik & Terjemah</span>
                                </div>
                            </div>
                            <!-- Mini keyboard preview -->
                            <div class="w-full sm:w-56 flex-shrink-0">
                                <div class="bg-white rounded-2xl p-4 border border-violet-100 shadow-sm">
                                    <div class="grid grid-cols-5 gap-1.5">
                                        <div v-for="char in ['ꦲ','ꦤ','ꦕ','ꦫ','ꦏ','ꦢ','ꦠ','ꦱ','ꦮ','ꦭ']" :key="char"
                                             class="h-10 bg-violet-50 rounded-lg flex items-center justify-center text-lg font-bold text-violet-400 hover:bg-violet-500 hover:text-white transition cursor-pointer">
                                            {{ char }}
                                        </div>
                                    </div>
                                    <div class="mt-2 h-8 bg-violet-50 rounded-lg flex items-center justify-center text-xs text-violet-300 font-bold">
                                        spasi
                                    </div>
                                </div>
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
