<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiFoto;
use App\Models\ItemFoto;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimFotoTefa;
use Illuminate\Support\Facades\Log;

class PhotoboothController extends Controller
{
    public function simpanFoto(Request $request)
    {
        try {
            // 1. INISIALISASI & INPUT
            $manager = ImageManager::usingDriver(Driver::class);
            $images = $request->input('images');
            $frameName = $request->input('frame');
            $namaPelanggan = $request->input('nama_pelanggan', 'Guest');

            // 2. TENTUKAN KODE UNIK & FOLDER SESI
            $kodeAcak = bin2hex(random_bytes(6));
            $folderRelativePath = 'uploads/sessions/' . $kodeAcak;
            $folderPath = public_path($folderRelativePath);

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            // 3. LOAD FRAME & ATUR KOORDINAT
            $pathFrame = public_path('frames/' . $frameName);
            if (!file_exists($pathFrame)) {
                throw new \Exception("File frame tidak ditemukan di: " . $pathFrame);
            }

            $templateFrame = $manager->decode($pathFrame);
            $lebarFrame = $templateFrame->width();
            $tinggiFrame = $templateFrame->height();

            // 1. DEFAULT: Rumus Frame Musik / Anything You Want (frame1)
            $pKiri = 0.1159;
            $pAtas = 0.0980;
            $pGap = 0.0410;
            $pLebar = 0.8010;
            $pTinggi = 0.1879;

            // 2. DETEKSI: Jika pakai Frame Keranjang Makanan (frame4)
            // Tambahkan else if lagi di sini nanti untuk Frame Halloween (frame2), dll.

            // 3. Eksekusi Rumus Persentase
            $marginKiri     = (int) round($lebarFrame * $pKiri);
            $marginAtas     = (int) round($tinggiFrame * $pAtas);
            $jarakAntarFoto = (int) round($tinggiFrame * $pGap);
            $lebarFotoAktual  = (int) round($lebarFrame * $pLebar);
            $tinggiFotoAktual = (int) round($tinggiFrame * $pTinggi);

            $canvas = $manager->createImage($lebarFrame, $tinggiFrame);

            foreach ($images as $index => $base64) {
                $imageParts = explode(";base64,", $base64);
                $decoded = base64_decode($imageParts[1]);
                $foto = $manager->decode($decoded);

                $rawFileName = 'raw_' . ($index + 1) . '.jpg';
                $foto->save($folderPath . '/' . $rawFileName, 90);

                // Resize & Tempel!
                $foto->cover($lebarFotoAktual, $tinggiFotoAktual);
                $posisiY = $marginAtas + ($index * ($tinggiFotoAktual + $jarakAntarFoto));

                $canvas->insert($foto, $marginKiri, $posisiY);
            } // <-- ini penutup foreach

            $canvas->insert($templateFrame, 0, 0);

            // Simpan Hasil Akhir
            $framedFileName = 'framed.jpg';
            $canvas->save($folderPath . '/' . $framedFileName, 90);

            // 6. RAKIT LINK NGROK
            $urlNgrok = "https://unconverged-paragraphistical-gemma.ngrok-free.dev"; // Update tiap nyalakan Ngrok
            $linkGallery = $urlNgrok . '/gallery/' . $kodeAcak;

            // 7. SIMPAN KE DATABASE
            $sesi = SesiFoto::create([
                'nama_pelanggan' => $namaPelanggan,
                'status_cetak' => 'menunggu',
                'tautan_gdrive' => $linkGallery
            ]);

            ItemFoto::create([
                'sesi_foto_id' => $sesi->id,
                'jalur_foto_asli' => $folderRelativePath, // Simpan path foldernya
                'jalur_foto_frame' => $folderRelativePath . '/' . $framedFileName,
            ]);

            // 8. KIRIM EMAIL KE TEFA
            try {
                $emailTefa = env('MAIL_TO_TEFA');
                Mail::to($emailTefa)->send(new KirimFotoTefa($namaPelanggan, $savePath));
            } catch (\Exception $e) {
                Log::error("Email Gagal: " . $e->getMessage());
            }

            // 9. RESPONSE UNTUK FRONTEND
            return response()->json([
                'success' => true,
                'link_gdrive' => $linkGallery
            ]);
        } catch (\Exception $e) {
            Log::error("Photobooth Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
