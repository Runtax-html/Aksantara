/**
 * Audio Player Helper untuk Aksantara
 * Memutar audio Edge-TTS logat Sunda asli (su-ID-JajangNeural).
 * 1. Prioritas 1: File pre-rendered lokal di /audio/sunda/{slug}.mp3 (0ms latency).
 * 2. Prioritas 2: Sintesis dinamis via /api/tts (Edge-TTS backend su-ID-JajangNeural) untuk kalimat bebas di keyboard.
 * 3. Prioritas 3: Fallback ke Web Speech API jika jaringan/server offline.
 */

import { ref } from 'vue';

// Instance Audio tunggal untuk mencegah audio tumpang tindih
let currentAudio = null;

// State reaktif global untuk pelacakan audio yang sedang aktif dimainkan
export const activePlayingSlug = ref('');
export const isAnyAudioPlaying = ref(false);

/**
 * Peta karakter Aksara Sunda dasar ke Latin fonetik
 */
const sundaCharToLatin = {
    'ᮃ': 'a', 'ᮄ': 'i', 'ᮅ': 'u', 'ᮈ': 'e', 'ᮇ': 'o', 'ᮉ': 'eu',
    'ᮊ': 'ka', 'ᮌ': 'ga', 'ᮍ': 'nga', 'ᮎ': 'ca', 'ᮏ': 'ja', 'ᮑ': 'nya',
    'ᮒ': 'ta', 'ᮓ': 'da', 'ᮔ': 'na', 'ᮕ': 'pa', 'ᮘ': 'ba', 'ᮙ': 'ma',
    'ᮞ': 'sa', 'ᮠ': 'ha', 'ᮚ': 'ya', 'ᮛ': 'ra', 'ᮜ': 'la', 'ᮝ': 'wa',
    'ᮖ': 'fa', 'ᮗ': 'va', 'ᮐ': 'za',
    // Angka
    '᮱': 'hiji', '᮲': 'dua', '᮳': 'tilu', '᮴': 'opat', '᮵': 'lima',
    '᮶': 'genep', '᮷': 'tujuh', '᮸': 'dalapan', '᮹': 'salapan', '᮰': 'nol',
};

/**
 * Transliterasi sederhana dari Aksara Sunda ke teks Latin
 */
export function transliterateAksaraToLatin(text) {
    if (!text) return '';
    let result = '';
    const chars = Array.from(text);
    for (let char of chars) {
        if (sundaCharToLatin[char]) {
            result += sundaCharToLatin[char];
        } else if (char === 'ᮤ') {
            result = result.replace(/[aeou]$/, '') + 'i';
        } else if (char === 'ᮥ') {
            result = result.replace(/[aeou]$/, '') + 'u';
        } else if (char === 'ᮦ') {
            result = result.replace(/[aeou]$/, '') + 'e';
        } else if (char === 'ᮧ') {
            result = result.replace(/[aeou]$/, '') + 'o';
        } else if (char === 'ᮨ') {
            result = result.replace(/[aeou]$/, '') + 'e';
        } else if (char === 'ᮩ') {
            result = result.replace(/[aeou]$/, '') + 'eu';
        } else if (char === '᮪') {
            result = result.replace(/[aeou]$/, ''); // pamaéh
        } else if (char === 'ᮀ') {
            result += 'ng';
        } else if (char === 'ᮁ') {
            result += 'r';
        } else if (char === 'ᮂ') {
            result += 'h';
        } else {
            result += char;
        }
    }
    return result.trim();
}

/**
 * Normalisasi teks ke nama file slug (contoh: "Hatur Nuhun" -> "hatur-nuhun")
 */
export function textToSlug(text) {
    if (!text) return '';
    
    // Jika input mengandung karakter Aksara Sunda (U+1B80 - U+1BBF)
    if (/[\u1B80-\u1BBF]/.test(text)) {
        text = transliterateAksaraToLatin(text);
    }

    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // hilangkan aksen seperti é -> e
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
}

/**
 * Request audio URL dari backend Edge-TTS untuk kalimat bebas
 */
async function fetchEdgeTtsAudioUrl(text) {
    try {
        const response = await fetch(`/api/tts?text=${encodeURIComponent(text)}`);
        if (!response.ok) return null;
        const data = await response.json();
        return data.url || null;
    } catch (err) {
        return null;
    }
}

/**
 * Fallback ke Web Speech API jika MP3 statis & API backend tidak tersedia
 */
function playFallbackTTS(text, onEnd) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.25;
        
        utterance.onend = () => {
            activePlayingSlug.value = '';
            isAnyAudioPlaying.value = false;
            if (onEnd) onEnd();
        };

        utterance.onerror = () => {
            activePlayingSlug.value = '';
            isAnyAudioPlaying.value = false;
            if (onEnd) onEnd();
        };

        window.speechSynthesis.speak(utterance);
    } else {
        setTimeout(() => {
            activePlayingSlug.value = '';
            isAnyAudioPlaying.value = false;
            if (onEnd) onEnd();
        }, 800);
    }
}

/**
 * Memutar audio kata/frasa bahasa Sunda
 * Menggunakan model Edge-TTS logat Sunda asli (su-ID-JajangNeural)
 * 
 * @param {string} text - Kata Sunda Latin atau Aksara Sunda
 * @param {object} options - { onStart, onEnd, onError, fallback = true }
 */
export async function playSundaAudio(text, options = {}) {
    if (!text) return;

    // Bersihkan teks jika ada karakter Aksara Sunda
    let cleanText = text;
    if (/[\u1B80-\u1BBF]/.test(cleanText)) {
        cleanText = transliterateAksaraToLatin(cleanText);
    }
    const slug = textToSlug(cleanText);

    activePlayingSlug.value = slug;
    isAnyAudioPlaying.value = true;

    if (options.onStart) options.onStart(slug);

    // Hentikan audio sebelumnya jika ada
    stopSundaAudio();

    const cleanup = () => {
        if (activePlayingSlug.value === slug) {
            activePlayingSlug.value = '';
            isAnyAudioPlaying.value = false;
        }
    };

    // ── 1. Coba putar file pre-rendered lokal jika berupa kata tunggal atau frasa umum ──
    const isStaticEligible = !slug.includes('-') || ['hatur-nuhun', 'wilujeng-sumping', 'panon-poe', 'panonpoe'].includes(slug);
    
    if (isStaticEligible) {
        const staticUrl = `/audio/sunda/${slug}.mp3`;
        try {
            const audio = new Audio(staticUrl);
            currentAudio = audio;

            audio.onended = () => {
                cleanup();
                if (options.onEnd) options.onEnd();
            };

            const playPromise = audio.play();
            if (playPromise !== undefined) {
                await playPromise;
                return audio;
            }
        } catch (e) {
            // File statis belum ada, lanjutkan ke langkah 2 (Dynamic Edge-TTS)
        }
    }

    // ── 2. Request Edge-TTS ke backend Laravel (/api/tts) dengan model su-ID-JajangNeural ──
    try {
        const edgeTtsUrl = await fetchEdgeTtsAudioUrl(cleanText);
        if (edgeTtsUrl) {
            const audio = new Audio(edgeTtsUrl);
            currentAudio = audio;

            audio.onended = () => {
                cleanup();
                if (options.onEnd) options.onEnd();
            };

            const playPromise = audio.play();
            if (playPromise !== undefined) {
                await playPromise;
                return audio;
            }
        }
    } catch (e) {
        // Lanjutkan ke fallback jika API gagal
    }

    // ── 3. Fallback terakhir jika backend/jaringan offline: Web Speech API ──
    if (options.fallback !== false) {
        playFallbackTTS(cleanText, () => {
            cleanup();
            if (options.onEnd) options.onEnd();
        });
    } else {
        cleanup();
        if (options.onError) options.onError(new Error('Audio playback failed'));
    }
}

/**
 * Hentikan pemutaran audio yang sedang berjalan
 */
export function stopSundaAudio() {
    if (currentAudio) {
        try {
            currentAudio.pause();
            currentAudio.currentTime = 0;
        } catch (e) {}
        currentAudio = null;
    }
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
    activePlayingSlug.value = '';
    isAnyAudioPlaying.value = false;
}

/**
 * Helper Composable untuk Vue Component
 */
export function useSundaAudio() {
    function isPlaying(word) {
        if (!word) return false;
        return activePlayingSlug.value === textToSlug(word);
    }

    function play(word, options = {}) {
        return playSundaAudio(word, options);
    }

    return {
        activePlayingSlug,
        isAnyAudioPlaying,
        isPlaying,
        play,
        stop: stopSundaAudio,
        textToSlug,
    };
}
