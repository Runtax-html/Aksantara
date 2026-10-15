<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: { type: Boolean, default: false },
    story: { type: Object, default: () => null },
    isCompleted: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'storyCompleted']);

const isCompleting = ref(false);
const completedSuccess = ref(false);
const rewardData = ref({ xp: 50, coins: 10, missionBonus: 0 });

async function finishReading() {
    if (!props.story || isCompleting.value || props.isCompleted || completedSuccess.value) return;
    isCompleting.value = true;

    try {
        const response = await axios.post('/api/gamification/read-story', {
            story_id: props.story.id,
        });

        if (response.data.success) {
            completedSuccess.value = true;
            rewardData.value = {
                xp: response.data.xpEarned || 0,
                coins: response.data.coinsEarned || 0,
                messages: response.data.messages || [],
            };
            emit('storyCompleted', response.data);
        }
    } catch (err) {
        console.error('Gagal menyelesaikan baca dongeng:', err);
    } finally {
        isCompleting.value = false;
    }
}

function closeModal() {
    completedSuccess.value = false;
    emit('close');
}
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-fade">
            <div
                v-if="show && story"
                class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6"
                @click.self="closeModal"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

                <!-- Modal Container -->
                <div class="relative z-[105] w-full max-w-2xl max-h-[90vh] flex flex-col bg-white rounded-3xl border-4 border-teal-300 shadow-2xl overflow-hidden">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-teal-500 to-emerald-600 p-4 sm:p-5 text-white flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl p-2 bg-white/20 rounded-2xl">{{ story.icon || '📖' }}</span>
                            <div>
                                <h3 class="text-base sm:text-lg font-black leading-tight">{{ story.title }}</h3>
                                <div class="flex items-center gap-2 text-xs font-bold text-teal-100 mt-0.5">
                                    <span>⏱️ {{ story.readTime }}</span>
                                    <span>•</span>
                                    <span>Daerah: {{ story.region }}</span>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="closeModal"
                            class="w-9 h-9 rounded-2xl bg-white/20 hover:bg-white/30 flex items-center justify-center text-white font-extrabold text-sm transition cursor-pointer"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Content (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">

                        <!-- Reward Notice Card -->
                        <div v-if="completedSuccess" class="bg-emerald-50 border-3 border-emerald-300 rounded-2xl p-4 text-center">
                            <span class="text-4xl block mb-1">🎉</span>
                            <h4 class="text-base font-black text-emerald-800">Hebat! Kamu Selesai Membaca!</h4>
                            <p class="text-xs text-emerald-700 font-bold mt-1">
                                Hadiah: +{{ rewardData.xp }} XP & +{{ rewardData.coins }} Koin Budaya!
                            </p>
                            <div v-if="rewardData.messages && rewardData.messages.length > 0" class="mt-2 space-y-1">
                                <span
                                    v-for="(msg, i) in rewardData.messages"
                                    :key="i"
                                    class="inline-block px-3 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full text-[11px] font-black mr-1"
                                >
                                    {{ msg }}
                                </span>
                            </div>
                        </div>

                        <!-- Ringkasan Cerita -->
                        <div class="bg-teal-50/70 border-2 border-teal-200 rounded-2xl p-4">
                            <h5 class="text-xs font-black text-teal-800 uppercase tracking-wider mb-1">Ringkasan Dongeng</h5>
                            <p class="text-xs sm:text-sm text-gray-700 leading-relaxed font-semibold">
                                {{ story.summary }}
                            </p>
                        </div>

                        <!-- Teks Cerita Aksara Sunda & Latin -->
                        <div class="space-y-4">
                            <div class="border-b-2 border-dashed border-teal-200 pb-2 flex items-center justify-between">
                                <h4 class="text-sm font-black text-gray-800">Isi Carita (Basa Sunda & Aksara)</h4>
                                <span class="text-[11px] font-bold text-teal-700 bg-teal-100 px-2 py-0.5 rounded-lg">Aksara Sunda</span>
                            </div>

                            <!-- Paragraf 1 -->
                            <div class="bg-amber-50/40 rounded-2xl p-4 border border-amber-200/60 space-y-2">
                                <p class="text-lg sm:text-xl font-bold text-gray-900 font-sunda leading-loose">
                                    {{ story.aksaraText1 || 'ᮓᮤ ᮠᮤᮏᮤ ᮝᮊ᮪ᮒᮥ, ᮞᮤ ᮊᮘᮚᮔ᮪ ᮓᮤᮕᮤ3ᮛᮥᮂ ᮊᮥ ᮙᮤᮒᮧᮠᮔ ᮍᮜ ᮒᮥᮒᮥᮒ᮪ ᮓᮤ ᮞᮝᮂ. ᮒᮥᮒᮥᮒ᮪ ᮒᮦᮂ ᮊᮛᮤ ᮍᮜᮃᮔ᮪ ᮓᮤᮔ ᮎᮄ ᮔᮥ ᮠᮦᮛᮀ.' }}
                                </p>
                                <p class="text-xs sm:text-sm text-gray-700 italic font-medium leading-relaxed">
                                    "{{ story.latinText1 || 'Dina hiji poé, Si Kabayan dititah ku mitohana ngala tutut di sawah. Tutut téh kari ngalaan dina cai anu hérang pisan.' }}"
                                </p>
                                <p class="text-[11px] sm:text-xs text-gray-500 font-semibold leading-relaxed border-t border-amber-200/40 pt-1.5">
                                    Artinya: Pada suatu hari, Si Kabayan disuruh oleh mertuanya mencari tutut (keong sawah). Tutut tinggal diambil di dalam air yang jernih.
                                </p>
                            </div>

                            <!-- Paragraf 2 -->
                            <div class="bg-amber-50/40 rounded-2xl p-4 border border-amber-200/60 space-y-2">
                                <p class="text-lg sm:text-xl font-bold text-gray-900 font-sunda leading-loose">
                                    {{ story.aksaraText2 || 'ᮒ Tapi ᮞᮤ ᮊᮘᮚᮔ᮪ ᮙᮜᮃᮂ ᮍᮧᮏᮨᮊ᮪ ᮕᮊᮦ ᮒᮥᮀᮊᮥᮜ᮪ ᮞᮘᮘ᮪ ᮍᮜᮤᮃᮒ᮪ ᮜᮔ᮪ᮌᮤᮒ᮪ ᮓᮤᮔ ᮎᮄ ᮞᮝᮂ. ᮘᮒᮤ ᮎᮄ ᮞᮝᮂ ᮏᮨᮛᮧ ᮕᮤᮞᮔ᮪ ᮎᮨᮔᮂ.' }}
                                </p>
                                <p class="text-xs sm:text-sm text-gray-700 italic font-medium leading-relaxed">
                                    "{{ story.latinText2 || 'Tapi Si Kabayan kalahka ngajanteng di galengan, ceuk manéhna: Jerona cai sawah téh, ningali langit aya di handap!' }}"
                                </p>
                                <p class="text-[11px] sm:text-xs text-gray-500 font-semibold leading-relaxed border-t border-amber-200/40 pt-1.5">
                                    Artinya: Namun Si Kabayan malah terdiam di pematang sawah, katanya: Dalam sekali air sawah ini, buktinya ada bayangan langit di bawah!
                                </p>
                            </div>

                            <!-- Paragraf 3 / Penutup -->
                            <div class="bg-amber-50/40 rounded-2xl p-4 border border-amber-200/60 space-y-2">
                                <p class="text-xs sm:text-sm text-gray-700 italic font-medium leading-relaxed">
                                    "{{ story.latinText3 || 'Mitohana anu keuheul tuluy nyurungkeun Si Kabayan kana sawah. Gebyur! Si Kabayan reuwas sabab caina ngan sakeluheun mumuncangan.' }}"
                                </p>
                                <p class="text-[11px] sm:text-xs text-gray-500 font-semibold leading-relaxed border-t border-amber-200/40 pt-1.5">
                                    Pesan Moral: Jangan takut sebelum mencoba dan jangan mudah tertipu oleh bayangan semata!
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Action -->
                    <div class="bg-gray-50 p-4 border-t-2 border-gray-200 shrink-0 flex items-center justify-between gap-3">
                        <button
                            @click="closeModal"
                            class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-extrabold text-xs rounded-xl transition cursor-pointer"
                        >
                            Tutup
                        </button>

                        <!-- Status Tombol: Jika sudah selesai dibaca (sekali klaim), tombol menjadi disabled / redup -->
                        <button
                            v-if="isCompleted || completedSuccess"
                            disabled
                            class="flex-1 py-3 bg-emerald-100 text-emerald-800 border-2 border-emerald-300 font-extrabold text-xs sm:text-sm rounded-xl cursor-not-allowed opacity-90 flex items-center justify-center gap-2 shadow-xs"
                            title="Hadiah dongeng ini sudah pernah diklaim"
                        >
                            <span class="text-base">✓</span>
                            <span>Selesai Dibaca</span>
                            <span class="text-[11px] bg-emerald-200/80 text-emerald-900 px-2 py-0.5 rounded-md font-bold">
                                Hadiah Sudah Diambil
                            </span>
                        </button>

                        <button
                            v-else
                            @click="finishReading"
                            :disabled="isCompleting"
                            class="flex-1 py-3 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-black text-xs sm:text-sm rounded-xl border-2 border-teal-700 border-b-4 shadow-[0_2px_0_0_rgba(0,0,0,0.15)] active:translate-y-0.5 active:border-b-2 active:shadow-none transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>{{ isCompleting ? 'Menyimpan...' : 'Tandai Selesai Membaca 📖' }}</span>
                            <span class="bg-white/20 px-2 py-0.5 rounded-lg text-[11px] font-bold">+50 XP & +10 🪙</span>
                        </button>
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
.font-sunda {
    font-family: 'Noto Sans Sundanese', 'Sundanese Unicode', sans-serif;
}
</style>
