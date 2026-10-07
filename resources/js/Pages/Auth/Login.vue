<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk - Aksantara" />

    <div class="min-h-screen bg-gradient-to-br from-orange-100 via-amber-50 to-orange-200 flex flex-col items-center justify-center p-4 sm:p-6 selection:bg-[#FF4D30] selection:text-white relative overflow-hidden font-sans">
        <!-- Floating background elements -->
        <div class="absolute -top-10 -left-10 w-48 h-48 bg-orange-300/30 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-red-300/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/4 right-10 text-4xl animate-bounce pointer-events-none hidden sm:block" style="animation-duration: 3s;">📜</div>
        <div class="absolute bottom-1/4 left-10 text-4xl animate-bounce pointer-events-none hidden sm:block" style="animation-duration: 4s; animation-delay: 1s;">🏆</div>

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
                        Petualangan Aksara
                    </span>
                    <h3 class="text-xl font-extrabold">Selamat Datang Kembali!</h3>
                </div>

                <!-- Mascot Visual / Badge -->
                <div class="my-6 relative z-10 flex flex-col items-center">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 bg-white/20 backdrop-blur-md rounded-full border-4 border-white/40 flex items-center justify-center shadow-inner relative group">
                        <span class="text-7xl sm:text-8xl transition-transform duration-300 group-hover:scale-110">🦸‍♂️</span>
                        <div class="absolute -bottom-2 -right-2 bg-yellow-400 text-gray-900 font-extrabold px-3 py-1 rounded-full text-xs shadow-md">
                            🔥 Level Up!
                        </div>
                    </div>
                    <div class="mt-4 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-2xl border border-white/20">
                        <p class="text-xs font-semibold text-orange-100">Kumpulkan XP & Streak Harianmu! ✨</p>
                    </div>
                </div>

                <div class="relative z-10 text-xs text-orange-100/90 font-medium">
                    "Melestarikan budaya Nusantara jadi seru dan gampang!"
                </div>
            </div>

            <!-- Form Area -->
            <div class="md:w-7/12 p-6 sm:p-10 flex flex-col justify-center bg-orange-50">
                <div class="mb-6">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 flex items-center gap-2">
                        Halo Pahlawan Aksara! 👋
                    </h2>
                    <p class="text-sm text-gray-600 font-medium mt-1">
                        Yuk masuk dan lanjutkan petualanganmu!
                    </p>
                </div>

                <div v-if="status" class="mb-4 text-sm font-bold text-green-600 bg-green-100 p-3 rounded-xl border border-green-200">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                            Email / Username
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-4 focus:ring-[#FF4D30]/20 outline-none transition-all duration-200 text-gray-800 font-semibold bg-white placeholder-gray-400"
                        />
                        <InputError class="mt-1 text-xs font-bold" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider">
                                Kata Sandi
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-bold text-[#FF4D30] hover:underline"
                            >
                                Lupa kata sandi?
                            </Link>
                        </div>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-4 focus:ring-[#FF4D30]/20 outline-none transition-all duration-200 text-gray-800 font-semibold bg-white placeholder-gray-400"
                        />
                        <InputError class="mt-1 text-xs font-bold" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" class="rounded-lg text-[#FF4D30] focus:ring-[#FF4D30]" />
                            <span class="ms-2 text-xs font-bold text-gray-600">Ingat Saya di Perangkat Ini</span>
                        </label>
                    </div>

                    <!-- Submit Button CTA -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full mt-2 py-4 bg-[#FF4D30] hover:bg-[#e03e22] text-white font-extrabold text-lg rounded-2xl shadow-lg shadow-[#FF4D30]/30 hover:scale-105 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <span>Ayo Masuk!</span>
                        <span class="text-xl">🚀</span>
                    </button>
                </form>

                <!-- Footer Switch -->
                <div class="mt-6 text-center text-sm font-medium text-gray-600">
                    Belum punya akun?
                    <Link :href="route('register')" class="font-extrabold text-[#FF4D30] hover:underline ms-1">
                        Daftar Sekarang!
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
