<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const selectedCategory = ref('semua');
const activeAudioWord = ref('');

const categories = [
    { id: 'semua', label: 'Semua Kosakata', icon: '✨' },
    { id: 'hewan', label: 'Sasatoan (Hewan)', icon: '🐱' },
    { id: 'tubuh', label: 'Awak (Tubuh)', icon: '👀' },
    { id: 'alam', label: 'Alam & Sabudeureun', icon: '🌳' },
    { id: 'dahareun', label: 'Dahareun (Makanan)', icon: '🍚' },
    { id: 'kulawarga', label: 'Kulawarga (Keluarga)', icon: '👨‍👩‍👧' },
    { id: 'sakola', label: 'Sakola & Parabot', icon: '🎒' },
    { id: 'sapaan', label: 'Bagea & Sopan Santun', icon: '👋' },
    { id: 'angka', label: 'Wilangan (Angka)', icon: '🔢' },
];

const dictionaryItems = [
    // ═══════════════════════════════════════════════════════════════
    // 1. SASATOAN (HEWAN)
    // ═══════════════════════════════════════════════════════════════
    {
        id: 1,
        sundaLatin: 'Ucing',
        sundaAksara: 'ᮅᮎᮤᮀ',
        indonesia: 'Kucing',
        category: 'hewan',
        contoh: 'Ucing téh mani lucu pisan buluna bodas.',
        contohAksara: 'ᮅᮎᮤᮀ ᮒᮦᮂ ᮙᮔᮤ ᮜᮥᮎᮥ ᮕᮤᮞᮔ᮪ ᮘᮥᮜᮥᮔ ᮘᮧᮓᮞ᮪.',
        emoji: '🐱',
    },
    {
        id: 2,
        sundaLatin: 'Hayam',
        sundaAksara: 'ᮠᮚᮙ᮪',
        indonesia: 'Ayam',
        category: 'hewan',
        contoh: 'Hayam jago kongkorongok subuh-subuh.',
        contohAksara: 'ᮠᮚᮙ᮪ ᮏᮌᮧ ᮊᮧᮀᮊᮧᮛᮧᮍᮧᮊ᮪ ᮞᮥᮘᮥᮂ-ᮞᮥᮘᮥᮂ.',
        emoji: '🐔',
    },
    {
        id: 3,
        sundaLatin: 'Maung',
        sundaAksara: 'ᮙᮅᮀ',
        indonesia: 'Harimau',
        category: 'hewan',
        contoh: 'Maung Lodaya mangrupa sato gagah ti tatar Sunda.',
        contohAksara: 'ᮙᮅᮀ ᮜᮧᮓᮚ ᮙᮀᮛᮥᮕ ᮞᮒᮧ ᮌᮌᮂ ᮒᮤ ᮒᮒᮁ ᮞᮥᮔ᮪ᮓ.',
        emoji: '🐯',
    },
    {
        id: 4,
        sundaLatin: 'Monyét',
        sundaAksara: 'ᮙᮧᮑᮦᮒ᮪',
        indonesia: 'Monyet',
        category: 'hewan',
        contoh: 'Monyét resep ngagayem cau dina tangkal.',
        contohAksara: 'ᮙᮧᮑᮦᮒ᮪ ᮛᮨᮞᮨᮕ᮪ ᮍᮌᮚᮨᮙ᮪ ᮎᮅ ᮓᮤᮔ ᮒᮀᮊᮜ᮪.',
        emoji: '🐒',
    },
    {
        id: 5,
        sundaLatin: 'Lauk',
        sundaAksara: 'ᮜᮅᮊ᮪',
        indonesia: 'Ikan',
        category: 'hewan',
        contoh: 'Lauk emas ngojay dina balong herang.',
        contohAksara: 'ᮜᮅᮊ᮪ ᮈᮙᮞ᮪ ᮍᮧᮏᮚ᮪ ᮓᮤᮔ ᮘᮜᮧᮀ ᮠᮦᮛᮀ.',
        emoji: '🐟',
    },
    {
        id: 6,
        sundaLatin: 'Manuk',
        sundaAksara: 'ᮙᮔᮥᮊ᮪',
        indonesia: 'Burung',
        category: 'hewan',
        contoh: 'Manuk cangkurileung eunteup dina dahan.',
        contohAksara: 'ᮙᮔᮥᮊ᮪ ᮎᮀᮊᮥᮛᮤᮜᮩᮀ ᮉᮔ᮪ᮒᮩᮕ᮪ ᮓᮤᮔ ᮓᮠᮔ᮪.',
        emoji: '🐦',
    },
    {
        id: 7,
        sundaLatin: 'Kuya',
        sundaAksara: 'ᮊᮥᮚ',
        indonesia: 'Kura-kura',
        category: 'hewan',
        contoh: 'Kuya leumpangna lalaunan pisan.',
        contohAksara: 'ᮊᮥᮚ ᮜᮩᮙ᮪ᮕᮀᮔ ᮜᮜᮅᮔᮔ᮪ ᮕᮤᮞᮔ᮪.',
        emoji: '🐢',
    },
    {
        id: 8,
        sundaLatin: 'Domba',
        sundaAksara: 'ᮓᮧᮙ᮪ᮘ',
        indonesia: 'Domba',
        category: 'hewan',
        contoh: 'Domba Garut kasohor tandukna panjang.',
        contohAksara: 'ᮓᮧᮙ᮪ᮘ ᮌᮛᮥᮒ᮪ ᮊᮞᮧᮠᮧᮁ ᮒᮔ᮪ᮓᮥᮊ᮪ᮔ ᮕᮔ᮪ᮏᮀ.',
        emoji: '🐑',
    },
    {
        id: 9,
        sundaLatin: 'Munding',
        sundaAksara: 'ᮙᮥᮔ᮪ᮓᮤᮀ',
        indonesia: 'Kerbau',
        category: 'hewan',
        contoh: 'Munding mantuan patani ngawuluku sawah.',
        contohAksara: 'ᮙᮥᮔ᮪ᮓᮤᮀ ᮙᮔ᮪ᮒᮥᮃᮔ᮪ ᮕᮒᮔᮤ ᮍᮝᮥᮜᮥᮊᮥ ᮞᮝᮂ.',
        emoji: '🐃',
    },

    // ═══════════════════════════════════════════════════════════════
    // 2. AWAK (ANGGOTA TUBUH)
    // ═══════════════════════════════════════════════════════════════
    {
        id: 10,
        sundaLatin: 'Panon',
        sundaAksara: 'ᮕᮔᮧᮔ᮪',
        indonesia: 'Mata',
        category: 'tubuh',
        contoh: 'Panon paranti ningali kaendahan dunya.',
        contohAksara: 'ᮕᮔᮧᮔ᮪ ᮕᮛᮔ᮪ᮒᮤ ᮔᮤᮍᮜᮤ ᮊᮈᮔ᮪ᮓᮠᮔ᮪ ᮓᮥᮑ.',
        emoji: '👀',
    },
    {
        id: 11,
        sundaLatin: 'Leungeun',
        sundaAksara: 'ᮜᮩᮍᮩᮔ᮪',
        indonesia: 'Tangan',
        category: 'tubuh',
        contoh: 'Kudu wawasuh leungeun sateuacan emam.',
        contohAksara: 'ᮊᮥᮓᮥ ᮝᮝᮞᮥᮂ ᮜᮩᮍᮩᮔ᮪ ᮞᮒᮩᮃᮎᮔ᮪ ᮈᮙᮙ᮪.',
        emoji: '✋',
    },
    {
        id: 12,
        sundaLatin: 'Ceuli',
        sundaAksara: 'ᮎᮩᮜᮤ',
        indonesia: 'Telinga',
        category: 'tubuh',
        contoh: 'Ceuli dipaké pikeun ngaregepkeun piwuruk bapa guru.',
        contohAksara: 'ᮎᮩᮜᮤ ᮓᮤᮕᮊᮦ ᮕᮤᮊᮩᮔ᮪ ᮍᮛᮨᮌᮨᮕ᮪ᮊᮩᮔ᮪ ᮕᮤᮝᮥᮛᮥᮊ᮪ ᮘᮕ ᮌᮥᮛᮥ.',
        emoji: '👂',
    },
    {
        id: 13,
        sundaLatin: 'Irung',
        sundaAksara: 'ᮄᮛᮥᮀ',
        indonesia: 'Hidung',
        category: 'tubuh',
        contoh: 'Irung mangrupa indra paranti ngambeu seuseuhingan.',
        contohAksara: 'ᮄᮛᮥᮀ ᮙᮀᮛᮥᮕ ᮄᮔ᮪ᮓᮢ ᮕᮛᮔ᮪ᮒᮤ ᮍᮙ᮪ᮘᮩ ᮞᮩᮞᮩᮠᮤᮍᮔ᮪.',
        emoji: '👃',
    },
    {
        id: 14,
        sundaLatin: 'Biwir',
        sundaAksara: 'ᮘᮤᮝᮤᮁ',
        indonesia: 'Bibir',
        category: 'tubuh',
        contoh: 'Biwir mesem ngabagéakeun babaturan sakola.',
        contohAksara: 'ᮘᮤᮝᮤᮁ ᮙᮨᮞᮨᮙ᮪ ᮍᮘᮌᮦᮃᮊᮩᮔ᮪ ᮘᮘᮒᮥᮛᮔ᮪ ᮞᮊᮧᮜ.',
        emoji: '👄',
    },
    {
        id: 15,
        sundaLatin: 'Suku',
        sundaAksara: 'ᮞᮥᮊᮥ',
        indonesia: 'Kaki',
        category: 'tubuh',
        contoh: 'Suku kénca jeung katuhu paranti leumpang.',
        contohAksara: 'ᮞᮥᮊᮥ ᮊᮦᮔ᮪ᮎ ᮏᮩᮀ ᮊᮒᮥᮠᮥ ᮕᮛᮔ᮪ᮒᮤ ᮜᮩᮙ᮪ᮕᮀ.',
        emoji: '🦵',
    },
    {
        id: 16,
        sundaLatin: 'Buuk',
        sundaAksara: 'ᮘᮥᮅᮊ᮪',
        indonesia: 'Rambut',
        category: 'tubuh',
        contoh: 'Buuk Hideung dikuramas sangkan beresih tur seungit.',
        contohAksara: 'ᮘᮥᮅᮊ᮪ ᮠᮤᮓᮩᮀ ᮓᮤᮊᮥᮛᮙᮞ᮪ ᮞᮀᮊᮔ᮪ ᮘᮨᮛᮨᮞᮤᮂ ᮒᮥᮁ ᮞᮩᮍᮤᮒ᮪.',
        emoji: '💇',
    },
    {
        id: 17,
        sundaLatin: 'Huntu',
        sundaAksara: 'ᮠᮥᮔ᮪ᮒᮥ',
        indonesia: 'Gigi',
        category: 'tubuh',
        contoh: 'Kudu getol ngosok huntu sangkan henteu bolong.',
        contohAksara: 'ᮊᮥᮓᮥ ᮌᮨᮒᮧᮜ᮪ ᮍᮧᮞᮧᮊ᮪ ᮠᮥᮔ᮪ᮒᮥ ᮞᮀᮊᮔ᮪ ᮠᮨᮔ᮪ᮒᮩ ᮘᮧᮜᮧᮀ.',
        emoji: '🦷',
    },

    // ═══════════════════════════════════════════════════════════════
    // 3. ALAM & SABUDEUREUN
    // ═══════════════════════════════════════════════════════════════
    {
        id: 18,
        sundaLatin: 'Cai',
        sundaAksara: 'ᮎᮄ',
        indonesia: 'Air',
        category: 'alam',
        contoh: 'Cai herang ngagolontor dina pancuran sawah.',
        contohAksara: 'ᮎᮄ ᮠᮦᮛᮀ ᮍᮌᮧᮜᮧᮔ᮪ᮒᮧᮁ ᮓᮤᮔ ᮕᮔ᮪ᮎᮥᮛᮔ᮪ ᮞᮝᮂ.',
        emoji: '💧',
    },
    {
        id: 19,
        sundaLatin: 'Gunung',
        sundaAksara: 'ᮌᮥᮔᮥᮀ',
        indonesia: 'Gunung',
        category: 'alam',
        contoh: 'Gunung Tangkuban Parahu éndah pisan katingalna.',
        contohAksara: 'ᮌᮥᮔᮥᮀ ᮒᮀᮊᮥᮘᮔ᮪ ᮕᮛᮠᮥ ᮈᮔ᮪ᮓᮂ ᮕᮤᮞᮔ᮪ ᮊᮒᮤᮍᮜ᮪ᮔ.',
        emoji: '⛰️',
    },
    {
        id: 20,
        sundaLatin: 'Panonpoé',
        sundaAksara: 'ᮕᮔᮧᮔ᮪ᮕᮧᮦ',
        indonesia: 'Matahari',
        category: 'alam',
        contoh: 'Panonpoé sumorot nyaangan bumi ti beulah wétan.',
        contohAksara: 'ᮕᮔᮧᮔ᮪ᮕᮧᮦ ᮞᮥᮙᮧᮛᮧᮒ᮪ ᮑᮃᮍᮔ᮪ ᮘᮥᮙᮤ ᮒᮤ ᮘᮩᮜᮂ ᮝᮦᮒᮔ᮪.',
        emoji: '☀️',
    },
    {
        id: 21,
        sundaLatin: 'Bulan',
        sundaAksara: 'ᮘᮥᮜᮔ᮪',
        indonesia: 'Bulan',
        category: 'alam',
        contoh: 'Bulan purnama caang padang dina mangsa peuting.',
        contohAksara: 'ᮘᮥᮜᮔ᮪ ᮕᮥᮁᮔᮙ ᮎᮃᮀ ᮕᮓᮀ ᮓᮤᮔ ᮙᮀᮞ ᮕᮩᮒᮤᮀ.',
        emoji: '🌙',
    },
    {
        id: 22,
        sundaLatin: 'Bentang',
        sundaAksara: 'ᮘᮦᮔ᮪ᮒᮀ',
        indonesia: 'Bintang',
        category: 'alam',
        contoh: 'Bentang baranang ngahias langit peuting.',
        contohAksara: 'ᮘᮦᮔ᮪ᮒᮀ ᮘᮛᮔᮀ ᮍᮠᮤᮃᮞ᮪ ᮜ8ᮤᮒ᮪ ᮕᮩᮒᮤᮀ.',
        emoji: '⭐',
    },
    {
        id: 23,
        sundaLatin: 'Tangkal',
        sundaAksara: 'ᮒᮀᮊᮜ᮪',
        indonesia: 'Pohon',
        category: 'alam',
        contoh: 'Tangkal caringin ngiuhan buruan sakola.',
        contohAksara: 'ᮒᮀᮊᮜ᮪ ᮎᮛᮤᮍᮤᮔ᮪ ᮍᮤᮅᮠᮔ᮪ ᮘᮥᮛᮥᮃᮔ᮪ ᮞᮊᮧᮜ.',
        emoji: '🌳',
    },
    {
        id: 24,
        sundaLatin: 'Kembang',
        sundaAksara: 'ᮊᮦᮙ᮪ᮘᮀ',
        indonesia: 'Bunga',
        category: 'alam',
        contoh: 'Kembang malati seungit nyambuang di buruan.',
        contohAksara: 'ᮊᮦᮙ᮪ᮘᮀ ᮙᮜᮒᮤ ᮞᮩᮍᮤᮒ᮪ ᮑᮙ᮪ᮘᮥᮃᮀ ᮓᮤ ᮘᮥᮛᮥᮃᮔ᮪.',
        emoji: '🌸',
    },
    {
        id: 25,
        sundaLatin: 'Sawah',
        sundaAksara: 'ᮞᮝᮂ',
        indonesia: 'Sawah',
        category: 'alam',
        contoh: 'Paré di sawah hejo ngemploh matak seger.',
        contohAksara: 'ᮕᮛᮦ ᮓᮤ ᮞᮝᮂ ᮠᮦᮏᮧ ᮍᮨᮙ᮪ᮕᮣᮧᮂ ᮙᮒᮊ᮪ ᮞᮨᮌᮨᮁ.',
        emoji: '🌾',
    },
    {
        id: 26,
        sundaLatin: 'Hujan',
        sundaAksara: 'ᮠᮥᮏᮔ᮪',
        indonesia: 'Hujan',
        category: 'alam',
        contoh: 'Hujan girimis ngepris maseuhan taneuh.',
        contohAksara: 'ᮠᮥᮏᮔ᮪ ᮌᮤᮛᮤᮙᮤᮞ᮪ ᮍᮨᮕᮢᮤᮞ᮪ ᮙᮞᮩᮠᮔ᮪ ᮒᮔᮩᮂ.',
        emoji: '🌧️',
    },

    // ═══════════════════════════════════════════════════════════════
    // 4. DAHAREUN & INUMAN (MAKANAN & MINUMAN)
    // ═══════════════════════════════════════════════════════════════
    {
        id: 27,
        sundaLatin: 'Sangu',
        sundaAksara: 'ᮞᮍᮥ',
        indonesia: 'Nasi',
        category: 'dahareun',
        contoh: 'Sangu haneut dipirig ku sambel tarasi.',
        contohAksara: 'ᮞᮍᮥ ᮠᮔᮩᮒ᮪ ᮓᮤᮕᮤᮛᮤᮌ᮪ ᮊᮥ ᮞᮙ᮪ᮘᮨᮜ᮪ ᮒᮛᮞᮤ.',
        emoji: '🍚',
    },
    {
        id: 28,
        sundaLatin: 'Cau',
        sundaAksara: 'ᮎᮅ',
        indonesia: 'Pisang',
        category: 'dahareun',
        contoh: 'Cau ambon amis pisan rasana.',
        contohAksara: 'ᮎᮅ ᮃᮙ᮪ᮘᮧᮔ᮪ ᮃᮙᮤᮞ᮪ ᮕᮤᮞᮔ᮪ ᮛᮞᮔ.',
        emoji: '🍌',
    },
    {
        id: 29,
        sundaLatin: 'Sampeu',
        sundaAksara: 'ᮞᮙ᮪ᮕᮩ',
        indonesia: 'Singkong',
        category: 'dahareun',
        contoh: 'Sampeu kulub ngeunah dituang mangsa hujan.',
        contohAksara: 'ᮞᮙ᮪ᮕᮩ ᮊᮥᮜᮥᮘ᮪ ᮍᮩᮔᮂ ᮓᮤᮒᮥᮃᮀ ᮙᮀᮞ ᮠᮥᮏᮔ᮪.',
        emoji: '🥔',
    },
    {
        id: 30,
        sundaLatin: 'Amis',
        sundaAksara: 'ᮃᮙᮤᮞ᮪',
        indonesia: 'Manis',
        category: 'dahareun',
        contoh: 'Gula kawung rasana amis kareueut.',
        contohAksara: 'ᮌᮥᮜ ᮊᮝᮥᮀ ᮛᮞᮔ ᮃᮙᮤᮞ᮪ ᮊᮛᮩᮉᮒ᮪.',
        emoji: '🍬',
    },

    // ═══════════════════════════════════════════════════════════════
    // 5. KULAWARGA (KELUARGA)
    // ═══════════════════════════════════════════════════════════════
    {
        id: 31,
        sundaLatin: 'Bapa',
        sundaAksara: 'ᮘᮕ',
        indonesia: 'Ayah / Bapak',
        category: 'kulawarga',
        contoh: 'Bapa nuju damel di kebon palawija.',
        contohAksara: 'ᮘᮕ ᮔᮥᮏᮥ ᮓᮙᮨᮜ᮪ ᮓᮤ ᮊᮨᮘᮧᮔ᮪ ᮕᮜᮝᮤᮏ.',
        emoji: '👨',
    },
    {
        id: 32,
        sundaLatin: 'Indung',
        sundaAksara: 'ᮄᮔ᮪ᮓᮥᮀ',
        indonesia: 'Ibu',
        category: 'kulawarga',
        contoh: 'Indung mah tunggul rahayu pikeun sakabéh anakna.',
        contohAksara: 'ᮄᮔ᮪ᮓᮥᮀ ᮙᮂ ᮒᮥᮀᮌᮥᮜ᮪ ᮛᮠᮚᮥ ᮕᮤᮊᮩᮔ᮪ ᮞᮊᮘᮦᮂ ᮃᮔᮊ᮪ᮔ.',
        emoji: '👩',
    },
    {
        id: 33,
        sundaLatin: 'Lanceuk',
        sundaAksara: 'ᮜᮔ᮪ᮎᮩᮊ᮪',
        indonesia: 'Kakak',
        category: 'kulawarga',
        contoh: 'Lanceuk kuring sakola di kelas genep SD.',
        contohAksara: 'ᮜᮔ᮪ᮎᮩᮊ᮪ ᮊᮥᮛᮤᮀ ᮞᮊᮧᮜ ᮓᮤ ᮊᮦᮜᮞ᮪ ᮌᮨᮔᮨᮕ᮪ ᮈᮞ᮪ᮓᮦ.',
        emoji: '👦',
    },
    {
        id: 34,
        sundaLatin: 'Adi',
        sundaAksara: 'ᮃᮓᮤ',
        indonesia: 'Adik',
        category: 'kulawarga',
        contoh: 'Adi resep pisan diajar nulis Aksara Sunda.',
        contohAksara: 'ᮃᮓᮤ ᮛᮨᮞᮨᮕ᮪ ᮕᮤᮞᮔ᮪ ᮓᮤᮃᮏᮁ ᮔᮥᮜᮤᮞ᮪ ᮃᮊ᮪ᮞᮛ ᮞᮥᮔ᮪ᮓ.',
        emoji: '👧',
    },
    {
        id: 35,
        sundaLatin: 'Aki',
        sundaAksara: 'ᮃᮊᮤ',
        indonesia: 'Kakek',
        category: 'kulawarga',
        contoh: 'Aki sok ngadongeng Si Kabayan sateuacan kulem.',
        contohAksara: 'ᮃᮊᮤ ᮞᮧᮊ᮪ ᮍᮓᮧᮍᮦᮀ ᮞᮤ ᮊᮘᮚᮔ᮪ ᮞᮒᮩᮃᮎᮔ᮪ ᮊᮥᮜᮨᮙ᮪.',
        emoji: '👴',
    },
    {
        id: 36,
        sundaLatin: 'Nini',
        sundaAksara: 'ᮔᮤᮔᮤ',
        indonesia: 'Nenek',
        category: 'kulawarga',
        contoh: 'Nini nyieun kuéh ali agrem anu raos pisan.',
        contohAksara: 'ᮔᮤᮔᮤ ᮑᮤᮉᮔ᮪ ᮊᮥᮈᮂ ᮃᮜᮤ ᮃᮌᮢᮨᮙ᮪ ᮃᮔᮥ ᮛᮇᮞ᮪ ᮕᮤᮞᮔ᮪.',
        emoji: '👵',
    },

    // ═══════════════════════════════════════════════════════════════
    // 6. SAKOLA & PARABOT (SEKOLAH & BENDA)
    // ═══════════════════════════════════════════════════════════════
    {
        id: 37,
        sundaLatin: 'Buku',
        sundaAksara: 'ᮘᮥᮊᮥ',
        indonesia: 'Buku',
        category: 'sakola',
        contoh: 'Buku carita Sunda dibaca unggal sonten.',
        contohAksara: 'ᮘᮥᮊᮥ ᮎᮛᮤᮒ ᮞᮥᮔ᮪ᮓ ᮓᮤᮘᮎ ᮅᮀᮌᮜ᮪ ᮞᮧᮔ᮪ᮒᮨᮔ᮪.',
        emoji: '📚',
    },
    {
        id: 38,
        sundaLatin: 'Patlot',
        sundaAksara: 'ᮕᮒ᮪ᮜᮧᮒ᮪',
        indonesia: 'Pensil',
        category: 'sakola',
        contoh: 'Patlot paranti nulis aksara dina buku keretas.',
        contohAksara: 'ᮕᮒ᮪ᮜᮧᮒ᮪ ᮕᮛᮔ᮪ᮒᮤ ᮔᮥᮜᮤᮞ᮪ ᮃᮊ᮪ᮞᮛ ᮓᮤᮔ ᮘᮥᮊᮥ ᮊᮨᮛᮨᮒᮞ᮪.',
        emoji: '✏️',
    },
    {
        id: 39,
        sundaLatin: 'Imah',
        sundaAksara: 'ᮄᮙᮂ',
        indonesia: 'Rumah',
        category: 'sakola',
        contoh: 'Imah panggung mangrupa wangunan has urang Sunda.',
        contohAksara: 'ᮄᮙᮂ ᮕᮀᮌᮥᮀ ᮙᮀᮛᮥᮕ ᮝ8ᮥᮔᮔ᮪ ᮠᮞ᮪ ᮅᮛᮀ ᮞᮥᮔ᮪ᮓ.',
        emoji: '🏠',
    },
    {
        id: 40,
        sundaLatin: 'Sakola',
        sundaAksara: 'ᮞᮊᮧᮜ',
        indonesia: 'Sekolah',
        category: 'sakola',
        contoh: 'Murid-murid saruka bungah indit ka sakola.',
        contohAksara: 'ᮙᮥᮛᮤᮓ᮪-ᮙᮥᮛᮤᮓ᮪ ᮞᮛᮥᮊ ᮘᮥ8ᮂ ᮄᮔ᮪ᮓᮤᮒ᮪ ᮊ ᮞᮊᮧᮜ.',
        emoji: '🏫',
    },
    {
        id: 41,
        sundaLatin: 'Korsi',
        sundaAksara: 'ᮊᮧᮁᮞᮤ',
        indonesia: 'Kursi',
        category: 'sakola',
        contoh: 'Korsi kai ditata rapih di jero kelas.',
        contohAksara: 'ᮊᮧᮁᮞᮤ ᮊᮄ ᮓᮤᮒᮒ ᮛᮕᮤᮂ ᮓᮤ ᮏᮨᮛᮧ ᮊᮦᮜᮞ᮪.',
        emoji: '🪑',
    },
    {
        id: 42,
        sundaLatin: 'Méja',
        sundaAksara: 'ᮙᮦᮏ',
        indonesia: 'Meja',
        category: 'sakola',
        contoh: 'Buku jeung patlot disimpen dina luhur méja.',
        contohAksara: 'ᮘᮥᮊᮥ ᮏᮩᮀ ᮕᮒ᮪ᮜᮧᮒ᮪ ᮓᮤᮞᮤᮙ᮪ᮕᮨᮔ᮪ ᮓᮤᮔ ᮜᮥᮠᮥᮁ ᮙᮦᮏ.',
        emoji: '🛋️',
    },

    // ═══════════════════════════════════════════════════════════════
    // 7. BAGEA & SOPAN SANTUN
    // ═══════════════════════════════════════════════════════════════
    {
        id: 43,
        sundaLatin: 'Sampurasun',
        sundaAksara: 'ᮞᮙ᮪ᮕᮥᮛᮞᮥᮔ᮪',
        indonesia: 'Salam Sapaan Sunda (Semoga Sempurna)',
        category: 'sapaan',
        contoh: 'Sampurasun! - Rampés!',
        contohAksara: 'ᮞᮙ᮪ᮕᮥᮛᮞᮥᮔ᮪! - ᮛᮙ᮪ᮕᮦᮞ᮪!',
        emoji: '👋',
    },
    {
        id: 44,
        sundaLatin: 'Rampés',
        sundaAksara: 'ᮛᮙ᮪ᮕᮦᮞ᮪',
        indonesia: 'Jawaban Salam Sampurasun (Sama-sama)',
        category: 'sapaan',
        contoh: 'Rampés, wilujeng sumping ka lembur kuring!',
        contohAksara: 'ᮛᮙ᮪ᮕᮦᮞ᮪, ᮝᮤᮜᮥᮏᮨᮀ ᮞᮥᮙ᮪ᮕᮤᮀ ᮊ ᮜᮨᮙ᮪ᮘᮥᮁ ᮊᮥᮛᮤᮀ!',
        emoji: '🙏',
    },
    {
        id: 45,
        sundaLatin: 'Hatur Nuhun',
        sundaAksara: 'ᮠᮒᮥᮁ ᮔᮥᮠᮥᮔ᮪',
        indonesia: 'Terima Kasih',
        category: 'sapaan',
        contoh: 'Hatur nuhun kana kasaéanana sadayana.',
        contohAksara: 'ᮠᮒᮥᮁ ᮔᮥᮠᮥᮔ᮪ ᮊᮔ ᮊᮞᮈᮃᮔᮔ ᮞᮓᮚᮔ.',
        emoji: '✨',
    },
    {
        id: 46,
        sundaLatin: 'Punten',
        sundaAksara: 'ᮕᮥᮔ᮪ᮒᮨᮔ᮪',
        indonesia: 'Permisi / Maaf',
        category: 'sapaan',
        contoh: 'Punten badé ngiring ngalangkung sakedap.',
        contohAksara: 'ᮕᮥᮔ᮪ᮒᮨᮔ᮪ ᮘᮓᮦ ᮍᮤᮛᮤᮀ ᮍᮜᮀᮊᮥᮀ ᮞᮊᮨᮓᮕ᮪.',
        emoji: '🙇',
    },
    {
        id: 47,
        sundaLatin: 'Mangga',
        sundaAksara: 'ᮙᮀᮌ',
        indonesia: 'Silakan',
        category: 'sapaan',
        contoh: 'Mangga linggih ka lebet bumi.',
        contohAksara: 'ᮙᮀᮌ ᮜᮤᮀᮌᮤᮂ ᮊ ᮜᮨᮘᮨᮒ᮪ ᮘᮥᮙᮤ.',
        emoji: '🤝',
    },
    {
        id: 48,
        sundaLatin: 'Wilujeng Sumping',
        sundaAksara: 'ᮝᮤᮜᮥᮏᮨᮀ ᮞᮥᮙ᮪ᮕᮤᮀ',
        indonesia: 'Selamat Datang',
        category: 'sapaan',
        contoh: 'Wilujeng sumping di tanah Pasundan!',
        contohAksara: 'ᮝᮤᮜᮥᮏᮨᮀ ᮞᮥᮙ᮪ᮕᮤᮀ ᮓᮤ ᮒᮔᮂ ᮕᮞᮥᮔ᮪ᮓᮔ᮪!',
        emoji: '🎊',
    },

    // ═══════════════════════════════════════════════════════════════
    // 8. WILANGAN (ANGKA 1 - 10)
    // ═══════════════════════════════════════════════════════════════
    {
        id: 49,
        sundaLatin: 'Hiji',
        sundaAksara: 'ᮠᮤᮏᮤ',
        indonesia: 'Satu (1 - ᮱)',
        category: 'angka',
        contoh: 'Abdi gaduh buku carita hiji.',
        contohAksara: 'ᮃᮘ᮪ᮓᮤ ᮌᮓᮥᮂ ᮘᮥᮊᮥ ᮎᮛᮤᮒ ᮠᮤᮏᮤ.',
        emoji: '1️⃣',
    },
    {
        id: 50,
        sundaLatin: 'Dua',
        sundaAksara: 'ᮓᮥᮃ',
        indonesia: 'Dua (2 - ᮲)',
        category: 'angka',
        contoh: 'Soca abdi aya dua, kénca jeung katuhu.',
        contohAksara: 'ᮞᮧᮎ ᮃᮘ᮪ᮓᮤ ᮃᮚ ᮓᮥᮃ, ᮊᮦᮔ᮪ᮎ ᮏᮩᮀ ᮊᮒᮥᮠᮥ.',
        emoji: '2️⃣',
    },
    {
        id: 51,
        sundaLatin: 'Tilu',
        sundaAksara: 'ᮒᮤᮜᮥ',
        indonesia: 'Tiga (3 - ᮳)',
        category: 'angka',
        contoh: 'Dina tangkal aya tilu manuk keur disada.',
        contohAksara: 'ᮓᮤᮔ ᮒᮀᮊᮜ᮪ ᮃᮚ ᮒᮤᮜᮥ ᮙᮔᮥᮊ᮪ ᮊᮩᮁ ᮓᮤᮞᮓ.',
        emoji: '3️⃣',
    },
    {
        id: 52,
        sundaLatin: 'Opat',
        sundaAksara: 'ᮇᮕᮒ᮪',
        indonesia: 'Empat (4 - ᮴)',
        category: 'angka',
        contoh: 'Méja diajar ngagaduhan opat suku.',
        contohAksara: 'ᮙᮦᮏ ᮓᮤᮃᮏᮁ ᮍᮌᮓᮥᮠᮔ᮪ ᮇᮕᮒ᮪ ᮞᮥᮊᮥ.',
        emoji: '4️⃣',
    },
    {
        id: 53,
        sundaLatin: 'Lima',
        sundaAksara: 'ᮜᮤᮙ',
        indonesia: 'Lima (5 - ᮵)',
        category: 'angka',
        contoh: 'Ramo leungeun kuring aya lima.',
        contohAksara: 'ᮛᮙᮧ ᮜᮩᮍᮩᮔ᮪ ᮊᮥᮛᮤᮀ ᮃᮚ ᮜᮤᮙ.',
        emoji: '5️⃣',
    },
    {
        id: 54,
        sundaLatin: 'Genep',
        sundaAksara: 'ᮌᮨᮔᮨᮕ᮪',
        indonesia: 'Enam (6 - ᮶)',
        category: 'angka',
        contoh: 'Abdi ayeuna linggih di kelas genep.',
        contohAksara: 'ᮃᮘ᮪ᮓᮤ ᮃᮚᮩᮔ ᮜᮤᮀᮌᮤᮂ ᮓᮤ ᮊᮦᮜᮞ᮪ ᮌᮨᮔᮨᮕ᮪.',
        emoji: '6️⃣',
    },
    {
        id: 55,
        sundaLatin: 'Tujuh',
        sundaAksara: 'ᮒᮥᮏᮥᮂ',
        indonesia: 'Tujuh (7 - ᮷)',
        category: 'angka',
        contoh: 'Dina saminggu aya tujuh dinten.',
        contohAksara: 'ᮓᮤᮔ ᮞᮙᮤᮀᮌᮥ ᮃᮚ ᮒᮥᮏᮥᮂ ᮓᮤᮔ᮪ᮒᮨᮔ᮪.',
        emoji: '7️⃣',
    },
    {
        id: 56,
        sundaLatin: 'Dalapan',
        sundaAksara: 'ᮓᮜᮕᮔ᮪',
        indonesia: 'Delapan (8 - ᮸)',
        category: 'angka',
        contoh: 'Jam dalapan énjing kagiatan diajar dimimitian.',
        contohAksara: 'ᮏᮙ᮪ ᮓᮜᮕᮔ᮪ ᮈᮔ᮪ᮏᮤᮀ ᮊᮌᮤᮃᮒᮔ᮪ ᮓᮤᮃᮏᮁ ᮓᮤᮙᮤᮙᮤᮒᮤᮃᮔ᮪.',
        emoji: '8️⃣',
    },
    {
        id: 57,
        sundaLatin: 'Salapan',
        sundaAksara: 'ᮞᮜᮕᮔ᮪',
        indonesia: 'Sembilan (9 - ᮹)',
        category: 'angka',
        contoh: 'Koleksi patlot gambar abdi aya salapan.',
        contohAksara: 'ᮊᮧᮜᮦᮊ᮪ᮞᮤ ᮕᮒ᮪ᮜᮧᮒ᮪ ᮌᮙ᮪ᮘᮁ ᮃᮘ᮪ᮓᮤ ᮃᮚ ᮞᮜᮕᮔ᮪.',
        emoji: '9️⃣',
    },
    {
        id: 58,
        sundaLatin: 'Sapuluh',
        sundaAksara: 'ᮞᮕᮥᮜᮥᮂ',
        indonesia: 'Sepuluh (10 - ᮱᮰)',
        category: 'angka',
        contoh: 'Sapuluh ramo leungeun diangkat ngadua.',
        contohAksara: 'ᮞᮕᮥᮜᮥᮂ ᮛᮙᮧ ᮜᮩᮍᮩᮔ᮪ ᮓᮤᮃᮀᮊᮒ᮪ ᮍᮓᮥᮃ.',
        emoji: '🔟',
    },
];

const filteredItems = computed(() => {
    return dictionaryItems.filter(item => {
        const matchesCategory = selectedCategory.value === 'semua' || item.category === selectedCategory.value;
        const q = searchQuery.value.toLowerCase().trim();
        const matchesSearch = !q ||
            item.sundaLatin.toLowerCase().includes(q) ||
            item.indonesia.toLowerCase().includes(q) ||
            item.sundaAksara.includes(q);
        return matchesCategory && matchesSearch;
    });
});

function speak(text) {
    if (!text) return;
    activeAudioWord.value = text;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.2;
        window.speechSynthesis.speak(utterance);
    }
}
</script>

<template>
    <div class="space-y-6">
        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- BANNER HEADER KAMUS PLAYFUL & RAMAH ANAK                 -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="relative overflow-hidden bg-gradient-to-r from-amber-200 via-orange-200 to-yellow-200 rounded-3xl p-6 sm:p-8 border-4 border-amber-300 shadow-xl">
            <!-- Awan & Bintang Dekorasi Lucu -->
            <div class="absolute top-2 left-4 text-3xl opacity-60 animate-bounce select-none">☁️</div>
            <div class="absolute top-4 right-10 text-3xl opacity-60 select-none">✨</div>
            <div class="absolute bottom-2 right-4 text-4xl opacity-50 select-none">📖</div>
            <div class="absolute bottom-3 left-1/3 text-2xl opacity-40 select-none">🌟</div>

            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/90 backdrop-blur-sm text-amber-900 rounded-full text-xs font-black mb-3 border-2 border-amber-300 shadow-xs">
                    <span>📚 Kamus Bergambar & Bersuara</span>
                    <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-[10px]">
                        {{ dictionaryItems.length }} Kosakata
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-amber-950 tracking-tight leading-snug">
                    Kamus Aksara Sunda
                </h1>
                
                <p class="text-xs sm:text-sm text-amber-900/90 font-bold mt-2 leading-relaxed">
                    Yuk cari kosakata bahasa Sunda, lihat tulisan aksaranya, dan dengarkan pelafalan suaranya yang seru! 🎨🔊
                </p>

                <!-- Search Box Interaktif -->
                <div class="mt-5 relative">
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-lg text-gray-400">🔍</span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari kata Sunda atau Indonesia (misal: Ucing, Air, Hatur nuhun, Hiji)..."
                            class="w-full pl-11 pr-10 py-3.5 bg-white rounded-2xl border-3 border-amber-300 focus:border-[#FF4D30] focus:ring-4 focus:ring-orange-200 text-sm font-extrabold text-gray-800 placeholder-gray-400 shadow-md transition-all outline-none"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3.5 w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-xs font-bold transition"
                            title="Hapus Pencarian"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- KATEGORI PILLS (FILTER CEPAT RAMAH ANAK)                -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="selectedCategory = cat.id"
                    class="shrink-0 px-3.5 py-2 rounded-2xl text-xs font-extrabold border-2 border-b-4 transition-all duration-150 cursor-pointer flex items-center gap-1.5"
                    :class="selectedCategory === cat.id
                        ? 'bg-[#FF4D30] text-white border-orange-700 shadow-sm translate-y-0'
                        : 'bg-white text-gray-700 border-amber-200 hover:bg-amber-50 active:translate-y-0.5'"
                >
                    <span>{{ cat.icon }}</span>
                    <span>{{ cat.label }}</span>
                </button>
            </div>

            <!-- Counter Info -->
            <span class="text-xs font-extrabold text-amber-900 bg-amber-100 px-3 py-1 rounded-xl border border-amber-200 shrink-0">
                Menampilkan {{ filteredItems.length }} Kosakata
            </span>
        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- GRID KARTU KAMUS SUNDA                                  -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div v-if="filteredItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            <div
                v-for="item in filteredItems"
                :key="item.id"
                class="bg-white rounded-3xl p-5 border-3 border-amber-200 hover:border-orange-400 shadow-md hover:shadow-lg transition-all duration-200 flex flex-col justify-between group"
            >
                <div>
                    <!-- Top Card Badge & Emoji -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-12 h-12 rounded-2xl bg-amber-50 border-2 border-amber-200 flex items-center justify-center text-2xl shadow-xs group-hover:scale-110 transition-transform">
                            {{ item.emoji }}
                        </span>
                        
                        <button
                            @click="speak(item.sundaLatin)"
                            class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-extrabold text-xs flex items-center gap-1 shadow-xs active:translate-y-0.5 transition cursor-pointer"
                            title="Dengarkan Suara"
                        >
                            <span>🔊</span>
                            <span>Dengar</span>
                        </button>
                    </div>

                    <!-- Aksara Sunda Display -->
                    <div class="bg-amber-50/60 rounded-2xl p-3 border-2 border-dashed border-amber-200 text-center mb-3">
                        <span class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-wider">
                            {{ item.sundaAksara }}
                        </span>
                    </div>

                    <!-- Latin & Artinya -->
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-1.5">
                        <span>{{ item.sundaLatin }}</span>
                    </h3>
                    <p class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg inline-block mt-1 border border-emerald-200">
                        🇮🇩 {{ item.indonesia }}
                    </p>
                </div>

                <!-- Contoh Kalimat -->
                <div class="mt-4 pt-3 border-t border-amber-100 text-xs text-gray-600 font-medium">
                    <p class="font-bold text-gray-700 text-[11px] mb-0.5">Contoh Kalimat:</p>
                    <p class="italic text-gray-800 font-semibold leading-relaxed">"{{ item.contoh }}"</p>
                </div>
            </div>
        </div>

        <!-- Empty State Search -->
        <div v-else class="bg-white rounded-3xl p-8 border-3 border-dashed border-amber-300 text-center">
            <span class="text-5xl block mb-2">🔍</span>
            <h3 class="text-base font-extrabold text-gray-800">Kosakata Tidak Ditemukan</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Coba kata kunci lain atau pilih kategori yang berbeda ya!</p>
            <button
                @click="searchQuery = ''; selectedCategory = 'semua'"
                class="mt-4 px-4 py-2 bg-[#FF4D30] text-white text-xs font-extrabold rounded-xl border-b-2 border-orange-700 cursor-pointer"
            >
                Reset Pencarian
            </button>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
