#!/usr/bin/env python3
"""
Generator Audio Pre-rendered Bahasa Sunda menggunakan Microsoft Edge-TTS
Voice: su-ID-JajangNeural (atau su-ID-TutiNeural)
Hasil file disimpan di folder: public/audio/sunda/{slug}.mp3
"""

import asyncio
import os
import re
import edge_tts

# Konfigurasi default
DEFAULT_VOICE = "su-ID-JajangNeural"  # Logat Sunda Asli (Pria - Ramah & Positif)
OUTPUT_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "public", "audio", "sunda")

# Daftar Kosakata Kamus Sunda, Aksara Dasar, & Frasa Umum
AUDIO_ENTRIES = [
    # ── 1. Sasatoan (Hewan) ──
    {"text": "Ucing", "slug": "ucing"},
    {"text": "Hayam", "slug": "hayam"},
    {"text": "Maung", "slug": "maung"},
    {"text": "Monyét", "slug": "monyet"},
    {"text": "Lauk", "slug": "lauk"},
    {"text": "Manuk", "slug": "manuk"},
    {"text": "Kuya", "slug": "kuya"},
    {"text": "Domba", "slug": "domba"},
    {"text": "Munding", "slug": "munding"},

    # ── 2. Awak (Anggota Tubuh) ──
    {"text": "Panon", "slug": "panon"},
    {"text": "Leungeun", "slug": "leungeun"},
    {"text": "Ceuli", "slug": "ceuli"},
    {"text": "Irung", "slug": "irung"},
    {"text": "Biwir", "slug": "biwir"},
    {"text": "Suku", "slug": "suku"},
    {"text": "Buuk", "slug": "buuk"},
    {"text": "Huntu", "slug": "huntu"},

    # ── 3. Alam & Sabudeureun ──
    {"text": "Cai", "slug": "cai"},
    {"text": "Gunung", "slug": "gunung"},
    {"text": "Panonpoé", "slug": "panonpoe"},
    {"text": "Bulan", "slug": "bulan"},
    {"text": "Bentang", "slug": "bentang"},
    {"text": "Tangkal", "slug": "tangkal"},
    {"text": "Kembang", "slug": "kembang"},
    {"text": "Sawah", "slug": "sawah"},
    {"text": "Hujan", "slug": "hujan"},

    # ── 4. Dahareun & Inuman ──
    {"text": "Sangu", "slug": "sangu"},
    {"text": "Cau", "slug": "cau"},
    {"text": "Sampeu", "slug": "sampeu"},
    {"text": "Amis", "slug": "amis"},

    # ── 5. Kulawarga ──
    {"text": "Bapa", "slug": "bapa"},
    {"text": "Indung", "slug": "indung"},
    {"text": "Lanceuk", "slug": "lanceuk"},
    {"text": "Adi", "slug": "adi"},
    {"text": "Aki", "slug": "aki"},
    {"text": "Nini", "slug": "nini"},

    # ── 6. Sakola & Parabot ──
    {"text": "Buku", "slug": "buku"},
    {"text": "Patlot", "slug": "patlot"},
    {"text": "Imah", "slug": "imah"},
    {"text": "Sakola", "slug": "sakola"},
    {"text": "Korsi", "slug": "korsi"},
    {"text": "Méja", "slug": "meja"},

    # ── 7. Bagea & Sopan Santun ──
    {"text": "Sampurasun", "slug": "sampurasun"},
    {"text": "Rampés", "slug": "rampes"},
    {"text": "Hatur Nuhun", "slug": "hatur-nuhun"},
    {"text": "Punten", "slug": "punten"},
    {"text": "Mangga", "slug": "mangga"},
    {"text": "Wilujeng Sumping", "slug": "wilujeng-sumping"},

    # ── 8. Wilangan (Angka) ──
    {"text": "Hiji", "slug": "hiji"},
    {"text": "Dua", "slug": "dua"},
    {"text": "Tilu", "slug": "tilu"},
    {"text": "Opat", "slug": "opat"},
    {"text": "Lima", "slug": "lima"},
    {"text": "Genep", "slug": "genep"},
    {"text": "Tujuh", "slug": "tujuh"},
    {"text": "Dalapan", "slug": "dalapan"},
    {"text": "Salapan", "slug": "salapan"},
    {"text": "Sapuluh", "slug": "sapuluh"},

    # ── 9. Aksara Swara & Ngalagena Dasar (Huruf) ──
    {"text": "A", "slug": "a"},
    {"text": "I", "slug": "i"},
    {"text": "U", "slug": "u"},
    {"text": "É", "slug": "e-taling"},
    {"text": "O", "slug": "o"},
    {"text": "Eu", "slug": "eu"},
    {"text": "Ka", "slug": "ka"},
    {"text": "Ga", "slug": "ga"},
    {"text": "Nga", "slug": "nga"},
    {"text": "Ca", "slug": "ca"},
    {"text": "Ja", "slug": "ja"},
    {"text": "Nya", "slug": "nya"},
    {"text": "Ta", "slug": "ta"},
    {"text": "Da", "slug": "da"},
    {"text": "Na", "slug": "na"},
    {"text": "Pa", "slug": "pa"},
    {"text": "Ba", "slug": "ba"},
    {"text": "Ma", "slug": "ma"},
    {"text": "Sa", "slug": "sa"},
    {"text": "Ha", "slug": "ha"},
    {"text": "Ya", "slug": "ya"},
    {"text": "Ra", "slug": "ra"},
    {"text": "La", "slug": "la"},
    {"text": "Wa", "slug": "wa"},
    {"text": "Fa", "slug": "fa"},
    {"text": "Va", "slug": "va"},
    {"text": "Za", "slug": "za"},

    # ── 10. Contoh Suku Kata Gabungan Vokal (Rarangkén) ──
    {"text": "Ki", "slug": "ki"},
    {"text": "Ku", "slug": "ku"},
    {"text": "Ké", "slug": "ke-taling"},
    {"text": "Ko", "slug": "ko"},
    {"text": "Ke", "slug": "ke"},
    {"text": "Keu", "slug": "keu"},
    {"text": "Sunda", "slug": "sunda"},
]


async def generate_single_audio(text: str, slug: str, voice: str = DEFAULT_VOICE, overwrite: bool = False):
    """Menghasilkan satu file audio .mp3 dari teks menggunakan edge-tts."""
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    output_path = os.path.join(OUTPUT_DIR, f"{slug}.mp3")

    if os.path.exists(output_path) and not overwrite:
        print(f" [SKIP] {slug}.mp3 sudah ada.")
        return True

    try:
        # Rate sedikit santai (-5%) dan pitch sedikit lebih ramah anak (+2Hz)
        communicate = edge_tts.Communicate(text, voice, rate="-5%", pitch="+2Hz")
        await communicate.save(output_path)
        print(f" [OK] '{text}' -> {slug}.mp3")
        return True
    except Exception as e:
        print(f" [ERROR] Gagal mengenerate '{text}' ({slug}): {e}")
        return False


async def main():
    print("=" * 60)
    print("  AKSANARA - Generator Audio Pre-rendered Edge-TTS Sunda")
    print(f"  Voice     : {DEFAULT_VOICE}")
    print(f"  Output Dir: {OUTPUT_DIR}")
    print(f"  Total Kata: {len(AUDIO_ENTRIES)}")
    print("=" * 60)

    success_count = 0
    for entry in AUDIO_ENTRIES:
        ok = await generate_single_audio(entry["text"], entry["slug"])
        if ok:
            success_count += 1
        # Delay sejenak agar request stabil
        await asyncio.sleep(0.15)

    print("\n" + "=" * 60)
    print(f" Selesai! Berhasil memproses {success_count}/{len(AUDIO_ENTRIES)} file audio.")
    print(" File tersimpan di: public/audio/sunda/")
    print("=" * 60)


if __name__ == "__main__":
    asyncio.run(main())
