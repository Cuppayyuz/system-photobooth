<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiFoto;
use App\Models\ItemFoto;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimFotoTefa;

class PhotoboothController extends Controller
{
    public function simpanFoto(Request $request)
    {
        try {
            // --- 1. PROSES STITCHING (Intervention V4) ---
            $manager = ImageManager::usingDriver(Driver::class);

            $images = $request->input('images');
            $frameName = $request->input('frame');
            $namaPelanggan = $request->input('nama_pelanggan');

            $pathFrame = public_path('frames/' . $frameName);
            $templateFrame = $manager->decode($pathFrame);

            // ... kode bagian atas tetap ...
            $lebarFrame = $templateFrame->width();
            $tinggiFrame = $templateFrame->height();

            // --- KONFIGURASI BINGKAI (SILAKAN UBAH ANGKA INI SESUAI TEMPLATE-MU) ---
            $marginKiri = 30;  // Jarak foto dari tepi kiri frame
            $marginAtas = 40;  // Jarak foto pertama dari ujung atas frame
            $jarakAntarFoto = 20; // Celah (gap) antara foto 1, 2, dan 3

            // Menghitung ukuran foto aktual di dalam bingkai
            $lebarFotoAktual = $lebarFrame - ($marginKiri * 2);
            // Menghitung tinggi tiap foto (Tinggi total dikurangi margin atas, bawah, dan celah)
            $tinggiFotoAktual = ($tinggiFrame - ($marginAtas * 2) - ($jarakAntarFoto * 2)) / 3;

            $canvas = $manager->createImage($lebarFrame, $tinggiFrame);

            foreach ($images as $index => $base64) {
                $imageParts = explode(";base64,", $base64);
                $decoded = base64_decode($imageParts[1]);

                $foto = $manager->decode($decoded);
                // Menggunakan cover() agar foto otomatis terpotong rapi tanpa gepeng
                $foto->cover((int)$lebarFotoAktual, (int)$tinggiFotoAktual);

                // Menghitung posisi Y (turun ke bawah) untuk masing-masing foto
                $posisiY = $marginAtas + ($index * ($tinggiFotoAktual + $jarakAntarFoto));

                // Masukkan foto dengan kordinat X (margin kiri) dan Y yang sudah dihitung
                $canvas->insert($foto, $marginKiri, (int)$posisiY);
            }

            // Terakhir, timpa dengan Frame PNG (yang tengahnya bolong/transparan)
            $canvas->insert($templateFrame, 0, 0);
            // ... kode save ke lokal tetap ...

            // --- 2. PENYIMPANAN LOKAL DENGAN NAMA UNIK (PRIVATE) ---
            // Membuat string acak agar link tidak bisa ditebak orang lain
            // ... bagian atas (try, $manager, dll) tetap sama ...

            // --- 1. BUAT FOLDER KHUSUS UNTUK SESI INI ---
            $kodeAcak = bin2hex(random_bytes(6)); // Menghasilkan kode seperti 'a1b2c3d4e5f6'
            $folderPath = public_path('uploads/sessions/' . $kodeAcak);
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $canvas = $manager->createImage($lebarFrame, $tinggiFrame);

            // --- 2. PROSES FOTO MENTAH & STITCHING ---
            foreach ($images as $index => $base64) {
                $imageParts = explode(";base64,", $base64);
                $decoded = base64_decode($imageParts[1]);

                $foto = $manager->decode($decoded);

                // SIMPAN FOTO MENTAH (Tanpa Frame)
                $foto->save($folderPath . '/raw_' . ($index + 1) . '.jpg', 90);

                // Proses untuk masuk ke Frame
                $foto->cover((int)$lebarFotoAktual, (int)$tinggiFotoAktual);
                $posisiY = $marginAtas + ($index * ($tinggiFotoAktual + $jarakAntarFoto));
                $canvas->insert($foto, $marginKiri, (int)$posisiY);
            }

            $canvas->insert($templateFrame, 0, 0);

            // SIMPAN FOTO BERBINGKAI
            $canvas->save($folderPath . '/framed.jpg', 90);

            // --- 3. KONFIGURASI LINK NGROK KE HALAMAN GALLERY ---
            // Ganti dengan link Ngrok kamu
            $urlNgrok = "https://masukkan-id-ngrok-kamu.ngrok-free.app";
            $linkDownload = $urlNgrok . '/gallery/' . $kodeAcak; // Link mengarah ke halaman ala GDrive

            // ... kode simpan database, email, dan response JSON tetap sama ...

            // --- 4. SIMPAN KE DATABASE ---
            $sesi = SesiFoto::create([
                'nama_pelanggan' => $namaPelanggan,
                'status_cetak' => 'menunggu',
                'tautan_gdrive' => $linkDownload // Kita simpan link Ngrok di sini
            ]);

            ItemFoto::create([
                'sesi_foto_id' => $sesi->id,
                'jalur_foto_asli' => 'multiple',
                'jalur_foto_frame' => 'uploads/framed/' . $fileName,
            ]);

            // --- 5. KIRIM EMAIL KE TEFA ---
            try {
                $emailTefa = env('MAIL_TO_TEFA');
                Mail::to($emailTefa)->send(new KirimFotoTefa($namaPelanggan, $savePath));
            } catch (\Exception $e) {
                \Log::error("Email Gagal: " . $e->getMessage());
            }

            // --- 6. RESPONSE UNTUK QR CODE ---
            return response()->json([
                'success' => true,
                'link_gdrive' => $linkDownload
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
