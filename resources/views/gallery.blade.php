<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unduh Foto - Photobooth TeFa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-6">

    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-blue-600 p-6 text-white flex justify-between items-center">
            <h1 class="text-2xl font-bold">📂 Album Foto Kamu</h1>
            <button onclick="bagikanLink()" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-bold hover:bg-blue-50 transition">🔗 Bagikan Link</button>
        </div>

        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-600">Pilih foto yang ingin diunduh:</p>
                <button onclick="selectAll()" class="text-blue-600 font-semibold hover:underline">Pilih Semua</button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="gallery-container">
                <div class="relative group border rounded-xl overflow-hidden">
                    <input type="checkbox" class="foto-checkbox absolute top-3 left-3 w-6 h-6 z-10 cursor-pointer" value="/uploads/sessions/{{ $kode_sesi }}/framed.jpg">
                    <img src="/uploads/sessions/{{ $kode_sesi }}/framed.jpg" class="w-full aspect-[3/4] object-cover">
                    <div class="bg-gray-800 text-white text-xs text-center py-2">Hasil Akhir (Berbingkai)</div>
                </div>

                @for ($i = 1; $i <= 3; $i++)
                <div class="relative group border rounded-xl overflow-hidden">
                    <input type="checkbox" class="foto-checkbox absolute top-3 left-3 w-6 h-6 z-10 cursor-pointer" value="/uploads/sessions/{{ $kode_sesi }}/raw_{{ $i }}.jpg">
                    <img src="/uploads/sessions/{{ $kode_sesi }}/raw_{{ $i }}.jpg" class="w-full aspect-[4/3] object-cover transform scale-x-[-1]">
                    <div class="bg-gray-800 text-white text-xs text-center py-2">Polosan {{ $i }}</div>
                </div>
                @endfor
            </div>

            <button onclick="downloadTerpilih()" class="mt-8 w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl text-xl transition">
                ⬇️ Unduh Foto Terpilih
            </button>
        </div>
    </div>

    <script>
        function selectAll() {
            const checkboxes = document.querySelectorAll('.foto-checkbox');
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => c.checked = !allChecked);
        }

        async function downloadTerpilih() {
            const terpilih = document.querySelectorAll('.foto-checkbox:checked');
            if(terpilih.length === 0) return alert("Pilih minimal 1 foto dulu!");

            // Unduh satu per satu dengan jeda agar browser tidak memblokir
            for (let i = 0; i < terpilih.length; i++) {
                const link = document.createElement('a');
                link.href = terpilih[i].value;
                link.download = `Photobooth_TeFa_${i+1}.jpg`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                await new Promise(r => setTimeout(r, 500)); // Jeda 0.5 detik
            }
        }

        function bagikanLink() {
            if (navigator.share) {
                navigator.share({
                    title: 'Foto Photobooth TeFa',
                    text: 'Ini hasil foto kita tadi, yuk lihat dan download!',
                    url: window.location.href,
                })
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert("Link berhasil disalin ke clipboard!");
            }
        }
    </script>
</body>
</html>