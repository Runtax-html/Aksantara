<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-4">
        <header>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-2xl">⚠️</span>
                <h2 class="text-base sm:text-lg font-black text-red-700">
                    Hapus Akun Pengguna
                </h2>
            </div>

            <p class="text-xs sm:text-sm text-gray-500 font-medium leading-relaxed">
                Setelah akun dihapus, seluruh data petualangan, progress kuis, level, koin, dan streak kamu akan dihapus secara permanen.
            </p>
        </header>

        <button
            type="button"
            @click="confirmUserDeletion"
            class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-black text-xs rounded-xl border-2 border-red-200 transition cursor-pointer flex items-center gap-1.5"
        >
            <span>🗑️</span>
            <span>Hapus Akun Saya</span>
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-white rounded-3xl border-4 border-red-200">
                <h2 class="text-lg font-black text-gray-900">
                    Yakin ingin menghapus akun petualanganmu?
                </h2>

                <p class="mt-1 text-xs sm:text-sm text-gray-600 font-medium leading-relaxed">
                    Tindakan ini tidak bisa dibatalkan. Masukkan kata sandi kamu untuk mengonfirmasi bahwa kamu benar-benar ingin menghapus akun.
                </p>

                <div class="mt-5">
                    <InputLabel
                        for="password"
                        value="Kata Sandi Akun"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="block w-full rounded-2xl border-2 border-red-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 text-sm font-semibold p-3"
                        placeholder="Masukkan kata sandi..."
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2 text-xs font-bold text-red-500" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        @click="deleteUser"
                        :disabled="form.processing"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-black text-xs rounded-xl border-b-4 border-red-900 shadow-sm transition cursor-pointer disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menghapus...' : 'Ya, Hapus Akun' }}
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
