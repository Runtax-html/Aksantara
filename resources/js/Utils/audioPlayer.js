/**
 * Audio Player Helper untuk Aksantara
 * Memutar audio Edge-TTS logat Sunda asli (su-ID-JajangNeural).
 * 
 * Arsitektur Bebas Delay & Tanpa Model Lama:
 * 1. STATIC_AUDIO_MANIFEST (O(1) Set): Pencarian instan untuk 230+ audio pre-rendered (suku kata, huruf, kamus, angka).
 * 2. Audio Pool/Cache: Menggunakan kembali instance Audio agar tidak ada alokasi buffer delay dari browser.
 * 3. Sequence Player: Memutar frasa/kalimat gabungan kata secara berurutan instan tanpa menunggu loading server.
 * 4. Dynamic Edge-TTS (/api/tts): Mensintesis kalimat bebas di backend dengan model su-ID-JajangNeural.
 * 5. Fallback Aman: Hanya jika server benar-benar offline.
 */

import { ref } from 'vue';

// Instance Audio yang sedang aktif diputar
let currentAudio = null;
let currentSequenceTimer = null;

// Audio Object Cache (mencegah lag inisialisasi decoder browser)
const audioObjectCache = new Map();

// State reaktif global untuk pelacakan audio yang sedang aktif
export const activePlayingSlug = ref('');
export const isAnyAudioPlaying = ref(false);

/**
 * Manifest seluruh audio MP3 pre-rendered lokal yang tersedia di public/audio/sunda/
 * Pencarian O(1) menghilangkan 100% delay HTTP 404.
 */
const STATIC_AUDIO_MANIFEST = new Set([
    // Vokal Mandiri (Swara)
    'a', 'i', 'u', 'e', 'e-taling', 'o', 'eu',
    // Konsonan Mati / Akhir
    'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'ng', 'ny', 'p', 'r', 's', 't', 'v', 'w', 'y', 'z',
    // Seluruh Kombinasi Suku Kata Sunda (Konsonan + 7 Vokal)
    'ba', 'be', 'beu', 'bi', 'bo', 'bu',
    'ca', 'ce', 'ceu', 'ci', 'co', 'cu',
    'da', 'de', 'deu', 'di', 'do', 'du',
    'fa', 'fe', 'feu', 'fi', 'fo', 'fu',
    'ga', 'ge', 'geu', 'gi', 'go', 'gu',
    'ha', 'he', 'heu', 'hi', 'ho', 'hu',
    'ja', 'je', 'jeu', 'ji', 'jo', 'ju',
    'ka', 'ke', 'ke-taling', 'keu', 'ki', 'ko', 'ku',
    'la', 'le', 'leu', 'li', 'lo', 'lu',
    'ma', 'me', 'meu', 'mi', 'mo', 'mu',
    'na', 'ne', 'neu', 'ni', 'no', 'nu',
    'nga', 'nge', 'ngeu', 'ngi', 'ngo', 'ngu',
    'nya', 'nye', 'nyeu', 'nyi', 'nyo', 'nyu',
    'pa', 'pe', 'peu', 'pi', 'po', 'pu',
    'ra', 're', 'reu', 'ri', 'ro', 'ru',
    'sa', 'se', 'seu', 'si', 'so', 'su',
    'ta', 'te', 'teu', 'ti', 'to', 'tu',
    'va', 've', 'veu', 'vi', 'vo', 'vu',
    'wa', 'we', 'weu', 'wi', 'wo', 'wu',
    'ya', 'ye', 'yeu', 'yi', 'yo', 'yu',
    'za', 'ze', 'zeu', 'zi', 'zo', 'zu',
    // Nama-Nama Tanda Vokalisasi Rarangèn
    'pamaeh', 'pamepet', 'pamingkal', 'paneleng', 'paneuleung',
    'panghulu', 'panglayar', 'pangwisad', 'panolong', 'panyakra',
    'panyecek', 'panyiku', 'panyuku',
    // Angka Sunda
    'hiji', 'dua', 'tilu', 'opat', 'lima', 'genep', 'tujuh', 'dalapan', 'salapan', 'sapuluh',
    // Kosakata Kamus & Frasa Umum
    'adi', 'aki', 'amis', 'bapa', 'bentang', 'biwir', 'buku', 'bulan', 'buuk',
    'cai', 'cau', 'ceuli', 'domba', 'gunung', 'hatur-nuhun', 'hayam', 'hujan', 'huntu',
    'imah', 'indung', 'irung', 'kembang', 'korsi', 'kuya', 'lanceuk', 'lauk', 'leungeun',
    'mangga', 'manuk', 'maung', 'meja', 'monyet', 'munding', 'nini', 'panon', 'panonpoe',
    'patlot', 'punten', 'rampes', 'sakola', 'sampeu', 'sampurasun', 'sangu', 'sawah',
    'suku', 'sunda', 'tangkal', 'ucing', 'wilujeng-sumping'
]);

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
 * Transliterasi Aksara Sunda ke teks Latin
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
 * Normalisasi teks ke nama file slug
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
 * Mengambil atau membuat instance Audio ter-cache
 */
function getCachedAudio(url) {
    if (!audioObjectCache.has(url)) {
        const audio = new Audio(url);
        audioObjectCache.set(url, audio);
    }
    const audio = audioObjectCache.get(url);
    audio.currentTime = 0;
    return audio;
}

/**
 * Request audio URL dari backend Edge-TTS untuk kalimat kustom bebas
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
 * Fallback darurat ke Web Speech API jika backend & file lokal tidak merespons
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
        }, 500);
    }
}

/**
 * Memutar runtutan kata audio yang sudah ada di manifest secara berurutan instan (0ms delay)
 */
function playAudioSequence(slugs, options, cleanup) {
    let index = 0;

    function playNext() {
        if (index >= slugs.length) {
            cleanup();
            if (options.onEnd) options.onEnd();
            return;
        }

        const currentSlug = slugs[index];
        index++;

        const url = `/audio/sunda/${currentSlug}.mp3`;
        const audio = getCachedAudio(url);
        currentAudio = audio;

        audio.onended = () => {
            currentSequenceTimer = setTimeout(playNext, 90); // jeda natural 90ms antar kata
        };

        audio.onerror = () => {
            playNext();
        };

        audio.play().catch(() => {
            playNext();
        });
    }

    playNext();
}

/**
 * Fungsi Utama Pemutar Audio Sunda Edge-TTS
 * 
 * @param {string} text - Kata atau Kalimat Sunda
 * @param {object} options - { onStart, onEnd, onError, fallback = true }
 */
export async function playSundaAudio(text, options = {}) {
    if (!text) return;

    // Bersihkan teks dari aksara Sunda jika ada
    let cleanText = text.trim();
    if (/[\u1B80-\u1BBF]/.test(cleanText)) {
        cleanText = transliterateAksaraToLatin(cleanText);
    }
    const slug = textToSlug(cleanText);

    activePlayingSlug.value = slug;
    isAnyAudioPlaying.value = true;
    if (options.onStart) options.onStart(slug);

    // Hentikan audio sebelumnya
    stopSundaAudio();

    const cleanup = () => {
        if (activePlayingSlug.value === slug) {
            activePlayingSlug.value = '';
            isAnyAudioPlaying.value = false;
        }
    };

    // ── STRATEGI 1: Audio Pre-rendered Tunggal (O(1) Instant - 0ms Delay) ──
    if (STATIC_AUDIO_MANIFEST.has(slug)) {
        const url = `/audio/sunda/${slug}.mp3`;
        const audio = getCachedAudio(url);
        currentAudio = audio;

        audio.onended = () => {
            cleanup();
            if (options.onEnd) options.onEnd();
        };

        try {
            await audio.play();
            return audio;
        } catch (e) {
            // Jika ada halangan browser play, lanjutkan
        }
    }

    // ── STRATEGI 2: Kalimat Gabungan dari Kata-Kata yang Ada di Manifest ──
    // Contoh user mengetik: "ka ga nga", "hiji dua tilu", "kuring diajar aksara sunda"
    const words = cleanText.split(/\s+/).map(w => textToSlug(w)).filter(Boolean);
    const allWordsInManifest = words.length > 1 && words.every(w => STATIC_AUDIO_MANIFEST.has(w));

    if (allWordsInManifest) {
        playAudioSequence(words, options, cleanup);
        return;
    }

    // ── STRATEGI 3: Sintesis Dinamis Edge-TTS Backend (/api/tts) ──
    // Untuk kalimat bebas atau frasa panjang kustom anak-anak
    try {
        const edgeTtsUrl = await fetchEdgeTtsAudioUrl(cleanText);
        if (edgeTtsUrl) {
            const audio = getCachedAudio(edgeTtsUrl);
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
        console.warn('[Aksantara Audio] Sintesis Edge-TTS gagal:', e);
    }

    // ── STRATEGI 4: Fallback Darurat Web Speech API ──
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
    if (currentSequenceTimer) {
        clearTimeout(currentSequenceTimer);
        currentSequenceTimer = null;
    }
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
 * Composable Vue untuk integrasi di template
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
