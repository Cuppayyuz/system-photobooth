<!DOCTYPE html>
<html>
<head>
    <title>Hasil Foto Photobooth</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #4A90E2;">Halo Tim TeFa,</h2>
        <p>Ada kiriman foto baru dari aplikasi Photobooth Expo!</p>
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 150px;"><strong>Nama Pelanggan:</strong></td>
                <td>{{ $namaPelanggan }}</td>
            </tr>
            <tr>
                <td><strong>Waktu:</strong></td>
                <td>{{ now()->format('d M Y H:i') }} WIB</td>
            </tr>
        </table>

        <p>Hasil foto <i>strip</i> sudah kami lampirkan di email ini. Silakan dicek kembali untuk keperluan arsip atau cetak ulang jika diperlukan.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #888; text-align: center;">
            Sistem Photobooth otomatis - SMKN 1 Surabaya
        </p>
    </div>
</body>
</html>