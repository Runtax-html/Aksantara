<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-2xl">🔒</span>
                <h2 class="text-base sm:text-lg font-black text-gray-900">
                    Perbarui Kata Sandi
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Gunakan kata sandi yang aman dan mudah diingat untuk melindungi akun petualanganmu.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-5">
            <div>
                <InputLabel for="current_password" value="Kata Sandi Saat Ini" class="text-xs font-black text-gray-700 mb-1" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="block w-full rounded-2xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-2 focus:ring-orange-200 text-sm font-semibold p-3 transition"
                    autocomplete="current-password"
                />

                <InputError :message="form.errors.current_password" class="mt-1.5 text-xs font-bold text-red-500" />
            </div>

            <div>
                <InputLabel for="password" value="Kata Sandi Baru" class="text-xs font-black text-gray-700 mb-1" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="block w-full rounded-2xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-2 focus:ring-orange-200 text-sm font-semibold p-3 transition"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-1.5 text-xs font-bold text-red-500" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Kata Sandi Baru"
                    class="text-xs font-black text-gray-700 mb-1"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="block w-full rounded-2xl border-2 border-orange-200 focus:border-[#FF4D30] focus:ring-2 focus:ring-orange-200 text-sm font-semibold p-3 transition"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-1.5 text-xs font-bold text-red-500"
                />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-[#FF4D30] hover:from-orange-600 hover:to-orange-700 text-white font-black text-xs sm:text-sm rounded-xl border-b-4 border-orange-800 shadow-sm active:translate-y-0.5 active:border-b-2 transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                >
                    <span>🔑</span>
                    <span>{{ form.processing ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}</span>
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
                        <span>Kata sandi berhasil diperbarui!</span>
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
