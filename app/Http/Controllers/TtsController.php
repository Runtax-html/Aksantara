<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class TtsController extends Controller
{
    /**
     * Synthesize audio on-the-fly using Microsoft Edge-TTS with Sundanese voice (su-ID-JajangNeural).
     * Hasil disimpan di public/audio/sunda/cache/{hash}.mp3 agar cepat dan hemat bandwidth.
     */
    public function synthesize(Request $request)
    {
        $text = trim($request->input('text', ''));
        if (empty($text)) {
            return response()->json(['error' => 'Teks tidak boleh kosong'], 400);
        }

        // Batasi panjang teks maksimal 300 karakter untuk keamanan
        $text = mb_substr($text, 0, 300);

        $voice = $request->input('voice', 'su-ID-JajangNeural');
        $hash = md5($voice . '_' . mb_strtolower($text));
        
        $cacheDir = public_path('audio/sunda/cache');
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $fileName = $hash . '.mp3';
        $filePath = $cacheDir . DIRECTORY_SEPARATOR . $fileName;
        $relativeUrl = '/audio/sunda/cache/' . $fileName;

        // Jika file audio sudah pernah di-generate, langsung kembalikan URL-nya
        if (file_exists($filePath) && filesize($filePath) > 0) {
            return response()->json([
                'url' => $relativeUrl,
                'cached' => true,
                'text' => $text,
            ]);
        }

        // Jalankan Python script untuk sintesis Edge-TTS
        $pythonScript = sprintf(
            'import asyncio, edge_tts; asyncio.run(edge_tts.Communicate(%s, %s, rate="-5%%", pitch="+2Hz").save(%s))',
            json_encode($text, JSON_UNESCAPED_UNICODE),
            json_encode($voice),
            json_encode($filePath)
        );

        $process = new Process(['python', '-c', $pythonScript]);
        $process->setTimeout(15);
        $process->run();

        if ($process->isSuccessful() && file_exists($filePath) && filesize($filePath) > 0) {
            return response()->json([
                'url' => $relativeUrl,
                'cached' => false,
                'text' => $text,
            ]);
        }

        return response()->json([
            'error' => 'Gagal menghasilkan audio Edge-TTS',
            'details' => $process->getErrorOutput(),
        ], 500);
    }
}
