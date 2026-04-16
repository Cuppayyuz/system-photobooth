<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Mirror effect untuk live video dan hasil foto */
        #kamera-preview,
        #preview-hasil {
            transform: scaleX(-1);
        }
    </style>
</head>

<body class="bg-black text-white h-screen overflow-hidden font-sans cursor-none relative">

    <div id="layar-standby" class="absolute inset-0 flex flex-col items-center justify-center z-40 bg-gray-900">
        <h1 class="text-7xl font-bold text-blue-400 mb-8">Photobooth TeFa</h1>
        <p class="text-4xl text-gray-300 animate-pulse">Menyiapkan sesi baru...</p>
    </div>

    <div id="layar-pilih-frame" class="absolute inset-0 flex flex-col p-10 bg-gray-900 z-40 hidden">
        <h2 class="text-6xl font-bold mb-12 text-center text-white">Pilih Bingkai Foto Favoritmu!</h2>
        <div class="flex-grow grid grid-cols-3 gap-10 content-start max-w-6xl mx-auto w-full">
            <div class="border-4 border-gray-700 rounded-2xl p-4 bg-gray-800 shadow-2xl"><img src="/frames/frame1.png" class="w-full h-auto"></div>
            <div class="border-4 border-gray-700 rounded-2xl p-4 bg-gray-800 shadow-2xl"><img src="/frames/frame2.png" class="w-full h-auto"></div>
            <div class="border-4 border-gray-700 rounded-2xl p-4 bg-gray-800 shadow-2xl"><img src="/frames/frame3.png" class="w-full h-auto"></div>
            <div class="border-4 border-gray-700 rounded-2xl p-4 bg-gray-800 shadow-2xl"><img src="/frames/frame4.png" class="w-full h-auto"></div>
        </div>
        <p class="text-center text-2xl text-gray-400 mt-10 animate-pulse">Beri tahu operator bingkai mana yang kamu inginkan...</p>
    </div>

    <div id="layar-kamera" class="absolute inset-0 z-30 hidden">
        <video id="kamera-preview" class="absolute w-full h-full object-cover z-0" autoplay playsinline></video>

        <div id="teks-timer" class="absolute inset-0 flex items-center justify-center text-white text-[30rem] font-bold drop-shadow-[0_20px_20px_rgba(0,0,0,1)] z-30 pointer-events-none"></div>
        <div id="flash-effect" class="absolute inset-0 bg-white z-50 opacity-0 pointer-events-none transition-opacity duration-100 hidden"></div>
    </div>

    <div id="layar-review-raw" class="absolute inset-0 bg-gray-900 z-[35] p-10 flex flex-col items-center justify-center hidden">
        <h2 class="text-4xl font-bold text-white mb-8">Apakah ada foto yang ingin diulang?</h2>
        <div class="grid grid-cols-3 gap-6 w-full max-w-7xl" id="grid-raw-container">
        </div>
    </div>

    <div id="layar-review-framed" class="absolute inset-0 bg-black z-[38] flex items-center justify-center hidden">

        <div class="relative h-[95vh] aspect-[600/1800] bg-transparent overflow-hidden shadow-[0_0_50px_rgba(255,255,255,0.1)]" id="preview-frame-container">

            <img id="frame-overlay-review" src="" class="absolute inset-0 w-full h-full object-cover z-20 pointer-events-none">

        </div>

    </div>

    <div id="popup-qr" class="absolute inset-0 bg-black/95 z-[60] flex flex-col items-center justify-center hidden">
        <h2 class="text-6xl font-bold mb-6 text-blue-400 text-center">Terima Kasih!</h2>
        <p class="text-gray-300 mb-10 text-3xl">Scan QR di bawah ini untuk mengambil foto di HP Anda:</p>
        <div id="qrcode-container" class="p-8 bg-white rounded-[3rem] mb-8 shadow-[0_0_50px_rgba(255,255,255,0.2)]"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const videoElement = document.getElementById('kamera-preview');
        const teksTimer = document.getElementById('teks-timer');
        const flashEffect = document.getElementById('flash-effect');
        const popupQr = document.getElementById('popup-qr');
        let fotoSesiIni = []; // Menyimpan foto mentah

        // Setup Kamera...
        window.addEventListener('load', async () => {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: 1280,
                    height: 720
                }
            });
            videoElement.srcObject = stream;
        });

        const channel = new BroadcastChannel('photobooth_sync');

        channel.onmessage = (event) => {
            const data = event.data;

            if (data.aksi === 'TAMPILKAN_PILIHAN_FRAME') {
                document.getElementById('layar-standby').classList.add('hidden');
                document.getElementById('layar-pilih-frame').classList.remove('hidden');
            }

            if (data.aksi === 'GANTI_FRAME') {
                document.getElementById('layar-pilih-frame').classList.add('hidden');
                document.getElementById('layar-kamera').classList.remove('hidden');

                // Jika sedang di mode REVIEW FRAMED (Ganti frame di tengah jalan)
                document.getElementById('frame-overlay').src = '/frames/' + data.frame;
            }

            if (data.aksi === 'TIMER') {
                teksTimer.innerText = data.waktu;
                if (data.waktu === "") {
                    flashEffect.classList.remove('hidden');
                    flashEffect.style.opacity = '1';
                    setTimeout(() => flashEffect.style.opacity = '0', 100);
                }
            }

            // ALUR 1: TAMPILKAN 3 FOTO MENTAH
            if (data.aksi === 'REVIEW_RAW') {
                fotoSesiIni = data.gambar; // Simpan array foto
                document.getElementById('layar-kamera').classList.add('hidden');
                document.getElementById('layar-review-raw').classList.remove('hidden');

                const grid = document.getElementById('grid-raw-container');
                grid.innerHTML = '';
                fotoSesiIni.forEach((imgSrc, idx) => {
                    grid.innerHTML += `
                        <div class="border-4 border-gray-700 rounded-2xl overflow-hidden aspect-[4/3]">
                            <img src="${imgSrc}" class="w-full h-full object-cover transform scale-x-[-1]">
                            <p class="text-center bg-gray-800 p-2 font-bold text-xl">Foto ${idx + 1}</p>
                        </div>`;
                });
            }

            // KEMBALI KE LIVE (JIKA ADA RETAKE)
            if (data.aksi === 'LANJUT_LIVE') {
                document.getElementById('layar-review-raw').classList.add('hidden');
                document.getElementById('layar-kamera').classList.remove('hidden');
            }

            // ALUR 2: PREVIEW FRAME
            // ALUR 2: PREVIEW FRAME (HASIL AKHIR)
            // ALUR 2: PREVIEW FRAME (HASIL AKHIR)
            // ALUR 2: PREVIEW FRAME (HASIL AKHIR)
            // monitor.blade.php

            // ALUR 2: PREVIEW FRAME (HASIL AKHIR)
            if (data.aksi === 'REVIEW_FRAMED') {
                // 1. Sembunyikan layar lain & tampilkan layar hasil
                document.getElementById('layar-pilih-frame').classList.add('hidden');
                document.getElementById('layar-review-raw').classList.add('hidden');
                document.getElementById('layar-kamera').classList.add('hidden');
                document.getElementById('layar-review-framed').classList.remove('hidden');

                // 2. Tangkap foto jika operator mengirimnya ulang
                if (data.gambar) {
                    fotoSesiIni = data.gambar;
                }

                // 3. Update Gambar Bingkai (Frame)
                const overlayReview = document.getElementById('frame-overlay-review');
                if (overlayReview) {
                    overlayReview.src = '/frames/' + data.frame + '?t=' + Date.now();
                }

                // 4. Bersihkan kontainer foto lama
                const container = document.getElementById('preview-frame-container');
                container.querySelectorAll('.foto-insert').forEach(el => el.remove());

                // 5. RUMUS PRESISI (BERDASARKAN UKURAN 397 x 1123 px)
                // Margin Atas: 9.80%, Lebar: 80.10%, Kiri: 11.59%, Tinggi: 18.79%, Jarak: 4.10%
                const pTinggi = 18.79;
                const pJarak = 4.10;
                const pAtasDasar = 9.80;

                fotoSesiIni.forEach((imgSrc, idx) => {
                    // Hitung posisi top otomatis untuk foto 1, 2, dan 3
                    const topPos = pAtasDasar + (idx * (pTinggi + pJarak));

                    // Membuat elemen gambar dengan CSS yang benar-benar rapi
                    const imgElement = document.createElement('img');
                    imgElement.src = imgSrc;
                    imgElement.className = "foto-insert absolute object-cover transform scale-x-[-1] z-10";

                    // Set gaya CSS secara manual agar tidak bentrok dengan sisa teks
                    imgElement.style.width = "80.10%";
                    imgElement.style.left = "11.59%";
                    imgElement.style.height = "18.79%";
                    imgElement.style.top = topPos + "%";

                    container.appendChild(imgElement);
                });
            }
            if (data.aksi === 'TAMPILKAN_QR') {
                popupQr.classList.remove('hidden');
                document.getElementById("qrcode-container").innerHTML = "";
                new QRCode(document.getElementById("qrcode-container"), {
                    text: data.link,
                    width: 400,
                    height: 400
                });
            }

            if (data.aksi === 'TUTUP_QR_SELESAI' || data.aksi === 'BATAL_SESI') {
                popupQr.classList.add('hidden');
                document.getElementById('layar-kamera').classList.add('hidden');
                document.getElementById('layar-review-raw').classList.add('hidden');
                document.getElementById('layar-review-framed').classList.add('hidden');
                document.getElementById('layar-standby').classList.remove('hidden');

                // Reset State
                document.getElementById('btn-tampil-raw')?.classList.add('hidden');
                document.getElementById('grup-btn-jadikan-frame')?.classList.add('hidden');
                document.getElementById('btn-proses')?.classList.add('hidden');
            }
        };
    </script>
</body>

</html>