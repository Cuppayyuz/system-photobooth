<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unduh Foto - Spooky Photobooth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-horror { font-family: 'Creepster', cursive; letter-spacing: 2px; }
        .font-sans { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-gray-200 font-sans min-h-screen p-4 md:p-8 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]">

    <div class="max-w-5xl mx-auto bg-slate-800 border-4 border-purple-600 rounded-3xl shadow-[0_0_30px_rgba(139,92,246,0.3)] overflow-hidden">
        
        <div class="bg-purple-900 border-b-4 border-purple-600 p-8 text-center">
            <h1 class="text-5xl font-horror text-green-400 drop-shadow-[0_4px_4px_rgba(0,0,0,0.8)]">🎃 Spooky Album 🎃</h1>
            <p class="text-purple-200 mt-2 text-lg font-bold">Koleksi fotomu sudah siap!</p>
            <button onclick="bagikanLink()" class="mt-4 bg-orange-500 hover:bg-orange-400 text-slate-900 px-6 py-2 rounded-full font-bold shadow-lg transition hover:scale-105 flex items-center justify-center mx-auto gap-2">
                🔗 Bagikan Link ke Teman
            </button>
        </div>

        <div class="p-6 md:p-8">
            <div class="text-center mb-8 bg-slate-900 p-4 rounded-xl border border-slate-700">
                <p class="text-xl text-gray-300 font-bold">Klik tombol <span class="text-green-400">UNDUH</span> di bawah gambar untuk menyimpan foto.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" id="gallery-container">
                
                <div class="card-foto bg-slate-900 p-4 rounded-2xl border-2 border-slate-700 flex flex-col">
                    <div class="relative overflow-hidden rounded-xl bg-black">
                        <img src="/uploads/sessions/{{ $kode_sesi }}/framed.jpg?v={{ time() }}" class="w-full aspect-[397/1123] object-cover">
                    </div>
                    <div class="mt-3 text-center text-green-400 font-bold font-horror text-xl tracking-widest">HASIL AKHIR</div>
                    
                    <button onclick="unduhSatuFoto('/uploads/sessions/{{ $kode_sesi }}/framed.jpg', 'Hasil_Akhir_Photobooth.jpg', this)" 
                            class="mt-4 w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition flex justify-center items-center gap-2 shadow-lg">
                        <span>📥</span> <span class="teks-tombol">Unduh Foto Ini</span>
                    </button>
                </div>

                @for ($i = 1; $i <= 3; $i++)
                <div class="card-foto bg-slate-900 p-4 rounded-2xl border-2 border-slate-700 flex flex-col">
                    <div class="relative flex-grow flex items-center justify-center overflow-hidden rounded-xl bg-black">
                        <img src="/uploads/sessions/{{ $kode_sesi }}/raw_{{ $i }}.jpg?v={{ time() }}" class="w-full aspect-[4/3] object-cover transform scale-x-[-1]">
                    </div>
                    <div class="mt-3 text-center text-purple-300 font-bold">Polosan {{ $i }}</div>
                    
                    <button onclick="unduhSatuFoto('/uploads/sessions/{{ $kode_sesi }}/raw_{{ $i }}.jpg', 'Polosan_{{ $i }}.jpg', this)" 
                            class="mt-4 w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 rounded-xl transition flex justify-center items-center gap-2 shadow-lg">
                        <span>📥</span> <span class="teks-tombol">Unduh Polosan</span>
                    </button>
                </div>
                @endfor

            </div>
        </div>
    </div>

    <script>
        async function unduhSatuFoto(urlGambar, namaFile, tombolElement) {
            const teksAsli = tombolElement.innerHTML;
            
            tombolElement.disabled = true;
            tombolElement.classList.add('opacity-70', 'cursor-not-allowed');
            tombolElement.innerHTML = '<span class="animate-spin">⏳</span> <span>Mengunduh...</span>';

            try {
                const response = await fetch(urlGambar);
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = namaFile;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(blobUrl);
                
                tombolElement.classList.replace('bg-green-600', 'bg-blue-600');
                tombolElement.classList.replace('bg-purple-600', 'bg-blue-600');
                tombolElement.innerHTML = '<span>✅</span> <span>Tersimpan!</span>';
                
            } catch (error) {
                tombolElement.classList.replace('bg-green-600', 'bg-red-600');
                tombolElement.classList.replace('bg-purple-600', 'bg-red-600');
                tombolElement.innerHTML = '<span>❌</span> <span>Gagal</span>';
            }

            setTimeout(() => {
                tombolElement.disabled = false;
                tombolElement.classList.remove('opacity-70', 'cursor-not-allowed');
                
                if(teksAsli.includes('Polosan')) {
                    tombolElement.className = "mt-4 w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 rounded-xl transition flex justify-center items-center gap-2 shadow-lg";
                } else {
                    tombolElement.className = "mt-4 w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition flex justify-center items-center gap-2 shadow-lg";
                }
                tombolElement.innerHTML = teksAsli;
            }, 3000);
        }

        function bagikanLink() {
            if (navigator.share) {
                navigator.share({ title: 'Spooky Photobooth TeFa', url: window.location.href })
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert("Link sudah disalin, silakan paste ke chat!");
            }
        }
    </script>
</body>
</html>