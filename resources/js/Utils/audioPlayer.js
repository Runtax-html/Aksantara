/**
 * Audio Player Helper untuk Aksantara
 * Memutar file audio MP3 pre-rendered (Edge-TTS logat Sunda: su-ID-JajangNeural)
 * dari folder publik /audio/sunda/{slug}.mp3 dengan otomatis fallback ke Web Speech API.
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
 * Fallback ke Web Speech API jika file MP3 belum ada
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
 * 1. Mencari file lokal di /audio/sunda/{slug}.mp3
 * 2. Jika 404/error, otomatis fallback ke Web Speech API
 * 
 * @param {string} text - Kata Sunda Latin atau Aksara Sunda
 * @param {object} options - { onStart, onEnd, onError, fallback = true }
 */
export function playSundaAudio(text, options = {}) {
    if (!text) return;

    const slug = textToSlug(text);
    activePlayingSlug.value = slug;
    isAnyAudioPlaying.value = true;

    if (options.onStart) options.onStart(slug);

    // Hentikan audio sebelumnya jika ada
    if (currentAudio) {
        try {
            currentAudio.pause();
            currentAudio.currentTime = 0;
        } catch (e) {
            // Abaikan error pause
        }
    }

    // Batalkan juga speech synthesis jika aktif
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }

    const audioUrl = `/audio/sunda/${slug}.mp3`;
    const audio = new Audio(audioUrl);
    currentAudio = audio;

    const cleanup = () => {
        if (activePlayingSlug.value === slug) {
            activePlayingSlug.value = '';
            isAnyAudioPlaying.value = false;
        }
    };

    audio.onended = () => {
        cleanup();
        if (options.onEnd) options.onEnd();
    };

    audio.onerror = (e) => {
        console.warn(`[Aksantara Audio] File lokal '${audioUrl}' tidak ditemukan. Menggunakan fallback TTS browser.`);
        
        if (options.fallback !== false) {
            playFallbackTTS(text, () => {
                cleanup();
                if (options.onEnd) options.onEnd();
            });
        } else {
            cleanup();
            if (options.onError) options.onError(e);
        }
    };

    const playPromise = audio.play();
    if (playPromise !== undefined) {
        playPromise.catch((err) => {
            console.warn(`[Aksantara Audio] Gagal memutar file MP3:`, err.message);
            if (options.fallback !== false) {
                playFallbackTTS(text, () => {
                    cleanup();
                    if (options.onEnd) options.onEnd();
                });
            } else {
                cleanup();
                if (options.onError) options.onError(err);
            }
        });
    }

    return audio;
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
