<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar - Aksantara" />

    <div class="min-h-screen bg-gradient-to-br from-orange-100 via-amber-50 to-orange-200 flex flex-col items-center justify-center p-4 sm:p-6 selection:bg-[#FF4D30] selection:text-white relative overflow-hidden font-sans">
        <!-- Floating background elements -->
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-orange-300/30 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-red-300/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/4 left-10 text-4xl animate-bounce pointer-events-none hidden sm:block" style="animation-duration: 3.5s;">✏️</div>
        <div class="absolute bottom-1/4 right-10 text-4xl animate-bounce pointer-events-none hidden sm:block" style="animation-duration: 4.5s; animation-delay: 0.5s;">⭐</div>

        <!-- Header Logo -->
        <Link href="/" class="mb-6 flex items-center gap-2 transition hover:scale-105">
            <span class="text-4xl">🔤</span>
            <span class="text-3xl font-extrabold text-[#FF4D30]">Aksantara</span>
        </Link>

        <!-- Card Melayang Tengah -->
        <div class="w-full max-w-4xl bg-orange-50 rounded-3xl shadow-2xl border-4 border-orange-200/80 overflow-hidden flex flex-col md:flex-row">
            
            <!-- Side Illustration & Mascot -->
            <div class="md:w-5/12 bg-gradient-to-br from-[#FF4D30] to-orange-500 p-8 text-white flex flex-col justify-between items-center text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10">
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        Gabung Gratis!
                    </span>
                    <h3 class="text-xl font-extrabold">Jadi Pahlawan Aksara!</h3>
                </div>

                <!-- Mascot Visual / Badge -->
                <div class="my-6 relative z-10 flex flex-col items-center">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 bg-white/20 backdrop-blur-md rounded-full border-4 border-white/40 flex items-center justify-center shadow-inner relative group">
                        <span class="text-7xl sm:text-8xl transition-transform duration-300 group-hover:scale-110">🦸‍♀️</span>
                        <div class="absolute -bottom-2 -right-2 bg-yellow-400 text-gray-900 font-extrabold px-3 py-1 rounded-full text-xs shadow-md">
                            🎁 Free Badge!
                        </div>
                    </div>
                    <div class="mt-4 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-2xl border border-white/20">
                        <p class="text-xs font-semibold text-orange-100">Buka Cerita Rakyat & Kuis Seru! 🏆</p>
                    </div>
                </div>

                <div class="relative z-10 text-xs text-orange-100/90 font-medium">
                    "Daftar sekarang dan kumpulkan poin budayamu!"
                </div>
            </div>

            <!-- Form Area -->
            <div class="md:w-7/12 p-6 sm:p-10 flex flex-col justify-center bg-orange-50">
                <div class="mb-6">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 flex items-center gap-2">
                        Mulai Petualanganmu! 🚀
                    </h2>
                    <p class="text-sm text-gray-600 font-medium mt-1">
                        Buat akunmu dan kumpulkan poin budaya!
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Nama Lengkap Field -->
                    <div>
                        <label for="name" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Lengkap
                        </label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-4 focus:ring-[#FF4D30]/20 outline-none transition-all duration-200 text-gray-800 font-semibold bg-white placeholder-gray-400"
                        />
                        <InputError class="mt-1 text-xs font-bold" :message="form.errors.name" />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="budi@email.com"
                            class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-4 focus:ring-[#FF4D30]/20 outline-none transition-all duration-200 text-gray-800 font-semibold bg-white placeholder-gray-400"
                        />
                        <InputError class="mt-1 text-xs font-bold" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-4 focus:ring-[#FF4D30]/20 outline-none transition-all duration-200 text-gray-800 font-semibold bg-white placeholder-gray-400"
                        />
                        <InputError class="mt-1 text-xs font-bold" :message="form.errors.password" />
                    </div>

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                            Ulangi Kata Sandi
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi di atas"
                            class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-4 focus:ring-[#FF4D30]/20 outline-none transition-all duration-200 text-gray-800 font-semibold bg-white placeholder-gray-400"
                        />
                        <InputError class="mt-1 text-xs font-bold" :message="form.errors.password_confirmation" />
                    </div>

                    <!-- Submit Button CTA -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full mt-2 py-4 bg-[#FF4D30] hover:bg-[#e03e22] text-white font-extrabold text-lg rounded-2xl shadow-lg shadow-[#FF4D30]/30 hover:scale-105 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <span>Daftar Sekarang!</span>
                        <span class="text-xl">🌟</span>
                    </button>
                </form>

                <!-- Footer Switch -->
                <div class="mt-6 text-center text-sm font-medium text-gray-600">
                    Sudah punya akun?
                    <Link :href="route('login')" class="font-extrabold text-[#FF4D30] hover:underline ms-1">
                        Ayo Masuk!
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
