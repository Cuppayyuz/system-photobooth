<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Photobooth Expo - TeFa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #kamera-preview {
            transform: scaleX(-1);
        }

        /* Mirror effect */
    </style>
</head>

<body class="bg-gray-900 text-white h-screen overflow-hidden font-sans select-none">

    <div id="halaman-awal" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-50">
        <h1 class="text-6xl font-bold mb-4 text-blue-400">Photobooth Expo</h1>

        <div class="bg-gray-800 p-8 rounded-2xl shadow-xl w-full max-w-md mb-8 border border-gray-700">
            <label class="block text-sm font-semibold mb-3 text-gray-400 uppercase tracking-wider">Sumber Kamera:</label>
            <select id="kamera-selector" class="w-full bg-gray-900 text-white p-4 rounded-xl border border-gray-600 focus:border-blue-500 outline-none text-lg">
                <option value="">Mencari Kamera...</option>
            </select>
        </div>

        <button onclick="mulaiSesiBaru()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-6 px-12 rounded-2xl text-3xl shadow-lg transition transform hover:scale-105">
            📸 Tambah Photobooth Baru
        </button>
    </div>

    <div id="halaman-frame" class="absolute inset-0 flex flex-col p-10 bg-gray-900 z-40 hidden">
        <h2 class="text-4xl font-bold mb-8 text-center">Pilih Bingkai Foto</h2>
        <div class="flex-grow grid grid-cols-3 gap-8 content-start max-w-5xl mx-auto w-full" id="container-frame">
            <div onclick="pilihFrame('frame1.png')" id="card-frame1.png" class="frame-card cursor-pointer border-4 border-gray-700 rounded-xl p-2 bg-gray-800"><img src="/frames/frame1.png" class="w-full h-auto"></div>
            <div onclick="pilihFrame('frame2.png')" id="card-frame2.png" class="frame-card cursor-pointer border-4 border-gray-700 rounded-xl p-2 bg-gray-800"><img src="/frames/frame2.png" class="w-full h-auto"></div>
            <div onclick="pilihFrame('frame3.png')" id="card-frame3.png" class="frame-card cursor-pointer border-4 border-gray-700 rounded-xl p-2 bg-gray-800"><img src="/frames/frame3.png" class="w-full h-auto"></div>
            <div onclick="pilihFrame('frame4.png')" id="card-frame4.png" class="frame-card cursor-pointer border-4 border-gray-700 rounded-xl p-2 bg-gray-800"><img src="/frames/frame4.png" class="w-full h-auto"></div>
        </div>
        <div class="mt-8 flex justify-between max-w-5xl mx-auto w-full">
            <button onclick="pindahHalaman('halaman-awal')" class="text-gray-400 font-bold text-xl hover:text-white transition">⬅ Kembali</button>
            <button onclick="lanjutKeKamera()" id="btn-lanjut-kamera" class="bg-green-600 py-4 px-10 rounded-xl text-xl font-bold opacity-50 transition" disabled>Lanjut ke Pemotretan ➡</button>
        </div>
    </div>

    <div id="halaman-kamera" class="absolute inset-0 flex bg-black z-30 hidden">
        <div class="w-3/4 h-full relative flex items-center justify-center p-6 bg-black">
            <video id="kamera-preview" class="w-full h-full object-cover rounded-3xl" autoplay playsinline></video>

            <div class="absolute top-10 right-10 w-32 border-2 border-blue-500 rounded-lg overflow-hidden shadow-2xl bg-black">
                <p class="text-[10px] text-center bg-blue-500 text-white p-1 font-bold">FRAME AKTIF</p>
                <img id="mini-frame-preview" src="" class="w-full h-auto bg-gray-800">
            </div>

            <div id="teks-timer" class="absolute inset-0 flex items-center justify-center text-white text-[18rem] font-bold drop-shadow-[0_10px_10px_rgba(0,0,0,0.8)] z-20 pointer-events-none"></div>
        </div>

        <div class="w-1/4 bg-gray-800 p-6 flex flex-col border-l border-gray-700 h-full">
            <div class="flex-grow flex flex-col gap-4 overflow-y-auto" id="slot-container"></div>

            <div class="mt-auto space-y-3 pt-4">
                <button id="btn-jepret" onclick="mulaiHitungMundur()" class="w-full bg-blue-600 hover:bg-blue-500 py-6 rounded-2xl text-2xl font-bold shadow-lg transition">📸 JEPRET</button>

                <button id="btn-tampil-raw" onclick="tampilkanSemuaFoto()" class="w-full bg-purple-600 hover:bg-purple-500 py-5 rounded-2xl text-xl font-bold shadow-lg hidden transition">1️⃣ Tampilkan Semua Foto</button>

                <button id="btn-preview-frame" onclick="previewFrame()" class="w-full bg-indigo-600 hover:bg-indigo-500 py-5 rounded-2xl text-xl font-bold shadow-lg hidden transition">2️⃣ Lihat Hasil Frame</button>

                <div id="grup-final" class="hidden flex gap-2 w-full">
                    <button onclick="bukaPilihFrameLagi()" class="bg-gray-600 hover:bg-gray-500 px-4 rounded-2xl text-sm font-bold shadow-lg transition">🔄 Ganti<br>Frame</button>
                    <button id="btn-proses" onclick="prosesDanUpload()" class="flex-1 bg-green-600 hover:bg-green-500 py-5 rounded-2xl text-xl font-bold shadow-lg transition">3️⃣ CETAK & UPLOAD</button>
                </div>

                <button onclick="batalSesi()" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl text-lg transition">Batalkan Sesi</button>
            </div>
        </div>
    </div>

    <div id="popup-qr" class="absolute inset-0 bg-black/95 z-[60] flex flex-col items-center justify-center hidden">
        <h2 class="text-4xl font-bold mb-4 text-blue-400 text-center">Foto Kamu Sudah Jadi!</h2>
        <p class="text-gray-400 mb-8 text-xl">Scan QR di bawah ini untuk mengambil foto di HP:</p>

        <div id="qrcode-container" class="p-6 bg-white rounded-3xl shadow-[0_0_50px_rgba(255,255,255,0.2)] mb-8"></div>

        <button onclick="tutupSesi()" class="bg-gray-700 hover:bg-gray-600 px-10 py-4 rounded-xl font-bold text-xl transition">
            Selesai / Sesi Baru
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        // --- STATE MANAGEMENT ---
        let frameAktif = '';
        let daftarFoto = [];
        const maxFoto = 3;

        // Elemen DOM
        const channel = new BroadcastChannel('photobooth_sync');
        const videoElement = document.getElementById('kamera-preview');
        const teksTimer = document.getElementById('teks-timer');
        const btnJepret = document.getElementById('btn-jepret');
        const btnProses = document.getElementById('btn-proses');

        // --- INIT: Deteksi Kamera Saat Halaman Dimuat ---
        window.addEventListener('load', async () => {
            await deteksiKamera();
        });

        async function deteksiKamera() {
            try {
                // Minta izin dasar agar nama kamera terbaca
                await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });
                const devices = await navigator.mediaDevices.enumerateDevices();
                const selector = document.getElementById('kamera-selector');
                selector.innerHTML = '';

                devices.filter(d => d.kind === 'videoinput').forEach((d, index) => {
                    const opt = document.createElement('option');
                    opt.value = d.deviceId;
                    opt.text = d.label || `Kamera ${index + 1}`;
                    selector.appendChild(opt);
                });
            } catch (error) {
                console.error("Gagal mendeteksi kamera:", error);
                document.getElementById('kamera-selector').innerHTML = '<option value="">Izinkan akses kamera di browser!</option>';
            }
        }

        // --- NAVIGASI HALAMAN ---
        function pindahHalaman(idHalaman) {
            document.getElementById('halaman-awal').classList.add('hidden');
            document.getElementById('halaman-frame').classList.add('hidden');
            document.getElementById('halaman-kamera').classList.add('hidden');
            document.getElementById(idHalaman).classList.remove('hidden');
        }

        // --- LOGIKA FRAME ---
        function pilihFrame(namaFile) {
            frameAktif = namaFile;
            document.querySelectorAll('.frame-card').forEach(card => card.classList.replace('border-blue-500', 'border-gray-700'));
            document.getElementById('card-' + namaFile).classList.replace('border-gray-700', 'border-blue-500');

            const btnLanjut = document.getElementById('btn-lanjut-kamera');
            btnLanjut.classList.remove('opacity-50', 'cursor-not-allowed');
            btnLanjut.disabled = false;
        }

        function lanjutKeKamera() {
            document.getElementById('mini-frame-preview').src = '/frames/' + frameAktif;
            pindahHalaman('halaman-kamera');

            if (sedangGantiFrame) {
                // JIKA SEDANG GANTI FRAME DI TENGAH JALAN
                sedangGantiFrame = false;
                
                // 1. Render ulang foto agar layar kiri operator tidak kosong
                renderSlotFoto(); 
                
                // 2. Langsung panggil previewFrame() untuk memunculkan tombol "Cetak & Upload"
                //    dan menyuruh monitor merakit foto dengan frame yang baru dipilih.
                previewFrame(); 

            } else {
                // JIKA SESI BARU
                nyalakanKamera(document.getElementById('kamera-selector').value);
                renderSlotFoto();
                channel.postMessage({
                    aksi: 'GANTI_FRAME',
                    frame: frameAktif
                });
            }
        }

        // --- LOGIKA KAMERA ---
        async function nyalakanKamera(deviceId) {
            matikanKamera(); // Matikan yang lama dulu jika ada

            const constraints = {
                video: {
                    width: 1280,
                    height: 720
                }
            };
            if (deviceId) constraints.video.deviceId = {
                exact: deviceId
            };

            try {
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                videoElement.srcObject = stream;
            } catch (error) {
                alert("Kamera gagal diakses! Pastikan tidak dipakai aplikasi lain.");
            }
        }

        function matikanKamera() {
            if (videoElement.srcObject) {
                videoElement.srcObject.getTracks().forEach(track => track.stop());
                videoElement.srcObject = null;
            }
        }

        // --- LOGIKA JEPRET ---
        function mulaiHitungMundur() {
            if (daftarFoto.length >= maxFoto) return;
            btnJepret.disabled = true;
            btnJepret.classList.replace('bg-blue-600', 'bg-gray-500');
            let waktu = 3;
            teksTimer.innerText = waktu;

            // --- TAMBAHAN BARU: Sinkronkan angka awal ---
            channel.postMessage({
                aksi: 'TIMER',
                waktu: waktu
            });

            const interval = setInterval(() => {
                waktu--;
                if (waktu > 0) {
                    teksTimer.innerText = waktu;
                    // --- TAMBAHAN BARU: Sinkronkan detik ---
                    channel.postMessage({
                        aksi: 'TIMER',
                        waktu: waktu
                    });
                } else {
                    clearInterval(interval);
                    teksTimer.innerText = "";
                    // --- TAMBAHAN BARU: Hapus angka & picu flash ---
                    channel.postMessage({
                        aksi: 'TIMER',
                        waktu: ""
                    });
                    ambilGambar();
                }
            }, 1000);
        }

        function ambilGambar() {
            // Efek Flash
            const flash = document.createElement('div');
            flash.className = "absolute inset-0 bg-white z-50 opacity-80 transition-opacity duration-200";
            document.getElementById('halaman-kamera').appendChild(flash);
            setTimeout(() => flash.remove(), 200);

            // Capture Canvas
            const canvas = document.createElement('canvas');
            canvas.width = 1280;
            canvas.height = 720;
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

            const base64Image = canvas.toDataURL('image/jpeg', 0.9);
            daftarFoto.push(base64Image);

            // --- KIRIM GAMBAR KE MONITOR CID ---
            channel.postMessage({
                aksi: 'TAMPILKAN_HASIL',
                gambar: base64Image
            });

            // Langsung render slot (tombol otomatis diatur di dalam sini)
            renderSlotFoto();
        }

        function renderSlotFoto() {
            const container = document.getElementById('slot-container');
            container.innerHTML = '';

            for (let i = 0; i < maxFoto; i++) {
                const slot = document.createElement('div');
                slot.className = "relative w-full aspect-[4/3] bg-gray-700 rounded-lg overflow-hidden border-2 border-gray-600 flex items-center justify-center";

                if (daftarFoto[i]) {
                    slot.innerHTML = `
                        <img src="${daftarFoto[i]}" class="w-full h-full object-cover transform scale-x-[-1]" alt="Slot ${i+1}">
                        <button onclick="hapusFoto(${i})" class="absolute top-2 right-2 bg-red-600 p-2 rounded-full hover:bg-red-500 transition shadow-lg text-sm font-bold">🗑️ Ulang</button>
                    `;
                    slot.classList.replace('border-gray-600', 'border-blue-500');
                } else {
                    slot.innerHTML = `<span class="text-gray-500 font-bold text-xl">Foto ${i+1}</span>`;
                }
                container.appendChild(slot);
            }

            // --- LOGIKA KEMUNCULAN TOMBOL YANG BENAR ---
            if (daftarFoto.length >= maxFoto) {
                // Jika foto sudah 3, matikan tombol jepret
                btnJepret.classList.add('hidden');

                // Munculkan HANYA tombol 1 (Tampilkan Semua Foto)
                document.getElementById('btn-tampil-raw')?.classList.remove('hidden');

                // Pastikan tombol tahap selanjutnya sembunyi
                document.getElementById('btn-preview-frame')?.classList.add('hidden');
                document.getElementById('grup-final')?.classList.add('hidden');
            } else {
                // Jika foto belum 3 (atau ada yang dihapus)
                btnJepret.classList.remove('hidden');
                btnJepret.disabled = false;
                btnJepret.classList.replace('bg-gray-500', 'bg-blue-600');

                // Sembunyikan semua tombol langkah akhir
                document.getElementById('btn-tampil-raw')?.classList.add('hidden');
                document.getElementById('btn-preview-frame')?.classList.add('hidden');
                document.getElementById('grup-final')?.classList.add('hidden');
            }
        }

        // LOGIKA KEMUNCULAN TOMBOL
        if (daftarFoto.length >= maxFoto) {
            // Jika foto sudah 3, matikan tombol jepret, munculkan tombol Review
            btnJepret.classList.add('hidden');
            document.getElementById('btn-lanjut').classList.add('hidden');

            // Munculkan HANYA tombol langkah 1
            document.getElementById('btn-tampil-raw').classList.remove('hidden');
            document.getElementById('grup-btn-jadikan-frame').classList.add('hidden');
            document.getElementById('btn-proses').classList.add('hidden');
        } else {
            // Jika foto belum 3 (atau habis di-retake)
            btnJepret.classList.remove('hidden');
            btnJepret.disabled = false;
            btnJepret.classList.replace('bg-gray-500', 'bg-blue-600');

            // Sembunyikan semua tombol langkah akhir
            document.getElementById('btn-tampil-raw')?.classList.add('hidden');
            document.getElementById('grup-btn-jadikan-frame')?.classList.add('hidden');
            document.getElementById('btn-proses')?.classList.add('hidden');
        }
        // --- LOGIKA REVIEW & PREVIEW ---

        function kembaliKeLive() {
            // Kembalikan tombol Jepret
            btnJepret.classList.remove('hidden');
            btnJepret.disabled = false;
            btnJepret.classList.replace('bg-gray-500', 'bg-blue-600');

            // Suruh monitor Cid kembali menampilkan kamera
            channel.postMessage({
                aksi: 'LANJUT_LIVE'
            });
        }

        function hapusFoto(index) {
            // Hapus foto dari array berdasarkan urutannya
            daftarFoto.splice(index, 1);

            // Render ulang slot (otomatis mengembalikan tombol jepret karena foto < 3)
            renderSlotFoto();

            // PENTING: Sembunyikan layar Review di monitor, kembalikan ke kamera live
            kembaliKeLive();
        }

        function batalSesi() {
            if (confirm("Yakin ingin membatalkan sesi ini? Semua foto akan hilang.")) {
                daftarFoto = [];
                frameAktif = '';
                matikanKamera();
                document.getElementById('btn-lanjut-kamera').classList.add('opacity-50', 'cursor-not-allowed');
                document.getElementById('btn-lanjut-kamera').disabled = true;
                pindahHalaman('halaman-awal');
                deteksiKamera();

                // Pastikan monitor kembali bersih
                channel.postMessage({
                    aksi: 'BATAL_SESI'
                });
                channel.postMessage({
                    aksi: 'LANJUT_LIVE'
                });
            }
        }
        // Fungsi baru untuk mensinkronkan halaman pilih frame ke monitor
        function mulaiSesiBaru() {
            pindahHalaman('halaman-frame');

            // Reset tulisan tombol untuk sesi baru
            document.getElementById('btn-lanjut-kamera').innerText = "Lanjut ke Pemotretan ➡";

            channel.postMessage({
                aksi: 'TAMPILKAN_PILIHAN_FRAME'
            });
        }

        // Fungsi baru untuk menutup QR di monitor sebelum halaman operator di-refresh
        function tutupSesi() {
            // Kirim sinyal ke monitor untuk menutup QR dan kembali ke mode standby
            channel.postMessage({
                aksi: 'TUTUP_QR_SELESAI'
            });

            // Beri jeda 100 milidetik agar pesan terkirim sebelum browser merefresh halaman
            setTimeout(() => {
                location.reload();
            }, 100);
        }
        // --- UPLOAD & QR CODE ---
        function tampilkanQR(url) {
            document.getElementById('popup-qr').classList.remove('hidden');
            document.getElementById("qrcode-container").innerHTML = ""; // Bersihkan QR lama jika ada
            new QRCode(document.getElementById("qrcode-container"), {
                text: url,
                width: 300,
                height: 300
            });
        }

        async function prosesDanUpload() {
            btnProses.innerText = "⏳ Memproses & Upload...";
            btnProses.disabled = true;
            btnProses.classList.replace('bg-green-600', 'bg-gray-500');

            try {
                const response = await fetch('/api/simpan-foto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        images: daftarFoto,
                        frame: frameAktif,
                        nama_pelanggan: 'Cid'
                    })
                });

                const result = await response.json();
                if (result.success) {
                    tampilkanQR(result.link_gdrive || "https://google.com"); // Fallback URL sementara jika backend belum siap
                    channel.postMessage({
                        aksi: 'TAMPILKAN_QR',
                        link: result.link_gdrive
                    });
                } else {
                    alert("Gagal memproses: " + result.message);
                    btnProses.disabled = false;
                    btnProses.innerText = "✨ CETAK & UPLOAD";
                    btnProses.classList.replace('bg-gray-500', 'bg-green-600');
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Terjadi kesalahan koneksi jaringan.");
                btnProses.disabled = false;
                btnProses.innerText = "✨ CETAK & UPLOAD";
                btnProses.classList.replace('bg-gray-500', 'bg-green-600');
            }

        }

        function tampilkanSemuaFoto() {
            document.getElementById('btn-tampil-raw').classList.add('hidden');
            document.getElementById('btn-preview-frame').classList.remove('hidden'); // Munculkan tombol ke-2

            channel.postMessage({
                aksi: 'REVIEW_RAW',
                gambar: daftarFoto
            });
        }

        // 2. Tampilkan Preview di Dalam Bingkai
        function previewFrame() {
            // Sembunyikan semua tombol fase 1 & 2 dengan paksa
            document.getElementById('btn-tampil-raw')?.classList.add('hidden');
            document.getElementById('btn-preview-frame')?.classList.add('hidden');
            
            // Munculkan grup tombol final & Cetak
            document.getElementById('grup-final').classList.remove('hidden'); 
            document.getElementById('btn-proses').classList.remove('hidden'); 

            channel.postMessage({
                aksi: 'REVIEW_FRAMED',
                frame: frameAktif,
                gambar: daftarFoto // Kirim fotonya agar monitor bisa merakitnya
            });
        }

        let sedangGantiFrame = false;

        function bukaPilihFrameLagi() {
            sedangGantiFrame = true;
            pindahHalaman('halaman-frame');

            // Ubah tulisan tombol menjadi Implementasi Frame
            document.getElementById('btn-lanjut-kamera').innerText = "✨ Implementasi Frame";

            channel.postMessage({
                aksi: 'TAMPILKAN_PILIHAN_FRAME'
            });
        }

        function jadikanFrame() {
            // Sembunyikan tombol Jadikan Frame, munculkan Cetak
            document.getElementById('grup-btn-jadikan-frame').classList.add('hidden');
            document.getElementById('btn-proses').classList.remove('hidden');

            // Beritahu monitor untuk merakit foto mentah ke dalam bingkai (Preview Statis)
            channel.postMessage({
                aksi: 'REVIEW_FRAMED',
                frame: frameAktif
            });
        }
    </script>
</body>

</html>