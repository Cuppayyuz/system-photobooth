<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiFoto;
use App\Models\ItemFoto;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoboothController extends Controller
{
    public function simpanFoto(Request $request)
    {
        try {
            $manager = new ImageManager(new Driver());
            $images = $request->input('images'); // Array dari 3 Base64
            $frameName = $request->input('frame');

            // 1. Baca File Frame Terlebih Dahulu (Sebagai Template Utama)
            $pathFrame = public_path('frames/' . $frameName);
            $templateFrame = $manager->read($pathFrame);

            // Ambil lebar dan tinggi frame asli (misal: 1200x3600)
            $lebarFrame = $templateFrame->width();
            $tinggiFrame = $templateFrame->height();

            // Hitung tinggi tiap slot (Tinggi total / 3)
            $tinggiPerFoto = $tinggiFrame / 3;

            // 2. Buat Kanvas Kosong sesuai ukuran frame
            $canvas = $manager->create($lebarFrame, $tinggiFrame);

            // 3. Masukkan 3 foto ke dalam kanvas (Stitching Vertikal)
            foreach ($images as $index => $base64) {
                $imageParts = explode(";base64,", $base64);
                $decoded = base64_decode($imageParts[1]);

                // Baca foto dan resize agar pas dengan lebar frame
                $foto = $manager->read($decoded);

                // Gunakan cover() agar foto memenuhi slot tanpa gepeng
                $foto->cover($lebarFrame, $tinggiPerFoto);

                // Tempelkan di posisi Y yang sesuai (0, 1/3, 2/3)
                $posisiY = $index * $tinggiPerFoto;
                $canvas->place($foto, 'top-left', 0, (int)$posisiY);
            }

            // 4. Tempelkan Frame (Overlay) di atas tumpukan 3 foto tadi
            $canvas->place($templateFrame, 'top-left');

            // 5. Simpan Hasil Akhir (Strip)
            $fileName = 'strip_' . time() . '.jpg';
            $savePath = public_path('uploads/framed/' . $fileName);
            $canvas->save($savePath);

            // 6. Simpan ke Database (Model SesiFoto)
            $sesi = SesiFoto::create([
                'nama_pelanggan' => $request->nama_pelanggan,
                'status_cetak' => 'menunggu'
            ]);

            ItemFoto::create([
                'sesi_foto_id' => $sesi->id,
                'jalur_foto_asli' => 'multiple',
                'jalur_foto_frame' => 'uploads/framed/' . $fileName,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
