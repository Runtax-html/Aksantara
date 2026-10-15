<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-2xl">📝</span>
                <h2 class="text-base sm:text-lg font-black text-gray-900">
                    Informasi Profil
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Perbarui nama dan alamat email akun petualang aksaramu.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-5"
        >
            <div>
                <InputLabel for="name" value="Nama Lengkap / Panggilan" class="text-xs font-black text-gray-700 mb-1" />

                <TextInput
                    id="name"
                    type="text"
                    class="block w-full rounded-2xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-2 focus:ring-orange-200 text-sm font-semibold p-3 transition"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-1.5 text-xs font-bold text-red-500" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Alamat Email" class="text-xs font-black text-gray-700 mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full rounded-2xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-2 focus:ring-orange-200 text-sm font-semibold p-3 transition"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-1.5 text-xs font-bold text-red-500" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="p-3 bg-amber-50 rounded-2xl border border-amber-200">
                <p class="text-xs text-amber-900 font-semibold">
                    Alamat email belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-xs text-[#FF4D30] font-black underline hover:text-orange-700 transition"
                    >
                        Kirim ulang tautan verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-xs font-bold text-emerald-600"
                >
                    Tautan verifikasi baru telah dikirim ke email kamu!
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-[#FF4D30] hover:from-orange-600 hover:to-orange-700 text-white font-black text-xs sm:text-sm rounded-xl border-b-4 border-orange-800 shadow-sm active:translate-y-0.5 active:border-b-2 transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                >
                    <span>💾</span>
                    <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}</span>
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-extrabold text-emerald-600 flex items-center gap-1"
                    >
                        <span>✓</span>
                        <span>Berhasil disimpan!</span>
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
