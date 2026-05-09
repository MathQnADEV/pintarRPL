<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Panel Dosen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f9fa] font-sans text-gray-700 h-screen overflow-hidden">

    <header class="bg-white border-b-2 border-gray-200 h-16 flex items-center justify-between px-8 z-10 relative shadow-sm">
        <div class="font-extrabold text-xl flex items-center gap-2">
            <span class="bg-gray-200 text-gray-500 px-3 py-1 rounded-lg border-b-2 border-gray-300 text-sm">Logo</span> PINTAR - Panel Dosen
        </div>
        <div class="flex items-center gap-4 font-bold text-sm">
            <span class="bg-[#f0e6d2] text-[#8a7346] px-3 py-1 rounded-full border-2 border-[#d8c39e]">4 kelas</span>
            <span class="bg-[#f0e6d2] text-[#8a7346] px-3 py-1 rounded-full border-2 border-[#d8c39e]">158 mahasiswa</span>
            <span class="text-gray-500">Pagi, Dosen_... ✦</span>
            <a href="#" class="text-red-500 hover:underline">Logout</a>
        </div>
    </header>

    <div class="flex h-[calc(100vh-4rem)]">
        <aside class="w-64 bg-white border-r-2 border-gray-200 p-6 flex flex-col gap-4 overflow-y-auto">
            <div class="text-xs font-extrabold text-gray-400 tracking-wider">MENU</div>
            <nav class="flex flex-col gap-2">
                <a href="#" class="flex items-center gap-3 p-3 bg-gray-100 rounded-xl border-2 border-gray-900 font-bold text-gray-900"><span class="w-2 h-2 bg-black rounded-full"></span> Kelas Diampu</a>
                <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition">○ Kelola Materi</a>
                <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition">○ Kelola Kuis</a>
                <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition">○ Laporan Nilai</a>
                <a href="#" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition">○ Pengaturan</a>
            </nav>
            <div class="text-xs font-extrabold text-gray-400 tracking-wider mt-4">MATA KULIAH</div>
            <a href="#" class="flex items-center gap-3 p-3 bg-white rounded-xl border-2 border-gray-900 font-bold text-gray-900"><span class="w-2 h-2 bg-black rounded-full"></span> Struktur Data</a>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            
            <div class="flex justify-between items-end mb-6">
                <div>
                    <div class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1">HOME > KELAS DIAMPU</div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Kelas yang Diampu</h1>
                    <p class="text-sm font-semibold text-gray-500 mt-1">Pilih kelas untuk lihat statistik, daftar mahasiswa, dan akses manajemen konten.</p>
                </div>
                <div class="flex gap-3">
                    <input type="text" placeholder="🔎 Cari kelas / mahasiswa..." class="p-3 border-2 border-gray-300 rounded-xl font-semibold focus:border-gray-900 outline-none w-64 text-sm">
                    <button class="bg-white border-2 border-b-4 border-gray-900 text-gray-900 font-bold px-6 rounded-xl hover:bg-gray-50">⬇ Unduh Laporan</button>
                </div>
            </div>

            <div class="bg-[#f0e6d2] p-5 rounded-2xl border-2 border-[#d8c39e] mb-6 flex justify-between items-center">
                <div>
                    <div class="text-xs font-extrabold text-[#8a7346] tracking-wider mb-1">MATA KULIAH - WAJIB</div>
                    <h2 class="text-2xl font-extrabold text-gray-900">Struktur Data</h2>
                    <p class="text-sm font-semibold text-[#8a7346]">Semester 2 - 2025/2026 · 4 SKS · Kurikulum 2025</p>
                </div>
                <div class="flex gap-2">
                    <span class="bg-[#111827] text-white font-bold text-sm px-4 py-2 rounded-full border-b-2 border-black">4 kelas aktif</span>
                    <span class="bg-white text-gray-800 font-bold text-sm px-4 py-2 rounded-full border-2 border-[#d8c39e]">158 mahasiswa</span>
                    <span class="bg-white text-gray-800 font-bold text-sm px-4 py-2 rounded-full border-2 border-[#d8c39e]">rata-rata 76</span>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl border-2 border-b-8 border-gray-900 cursor-pointer relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-[#111827] text-white text-xs font-bold px-3 py-1 rounded-full">dipilih</div>
                    <div class="w-10 h-10 bg-[#111827] text-white rounded-xl flex items-center justify-center font-extrabold text-xl mb-3">A</div>
                    <h3 class="font-extrabold text-gray-900 mb-1">Struktur Data — TEKOM A</h3>
                    <p class="text-xs font-semibold text-gray-400 mb-4">Teknik Komputer · Senin 08.00</p>
                    <div class="space-y-2 text-sm font-bold text-gray-600">
                        <div class="flex justify-between"><span>Mahasiswa</span><span class="text-gray-900">42</span></div>
                        <div class="flex justify-between"><span>Rata-rata</span><span class="text-gray-900">78</span></div>
                        <div class="flex justify-between"><span>Post-test lulus</span><span class="text-[#58cc02]">18/42</span></div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border-2 border-b-4 border-gray-200 hover:border-gray-400 cursor-pointer transition">
                    <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl border-2 border-gray-200 flex items-center justify-center font-extrabold text-xl mb-3">B</div>
                    <h3 class="font-extrabold text-gray-900 mb-1">Struktur Data — TEKOM B</h3>
                    <p class="text-xs font-semibold text-gray-400 mb-4">Teknik Komputer · Senin 13.00</p>
                    <div class="space-y-2 text-sm font-bold text-gray-600">
                        <div class="flex justify-between"><span>Mahasiswa</span><span class="text-gray-900">40</span></div>
                        <div class="flex justify-between"><span>Rata-rata</span><span class="text-gray-900">74</span></div>
                        <div class="flex justify-between"><span>Post-test lulus</span><span class="text-[#58cc02]">14/40</span></div>
                    </div>
                </div>
                </div>

            <div class="bg-white rounded-3xl border-2 border-gray-200 overflow-hidden">
                <div class="p-6 border-b-2 border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900">Struktur Data — TEKOM A · ringkasan</h3>
                        <p class="text-xs font-semibold text-gray-400">42 mahasiswa · Senin 08.00</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-white border-2 border-b-4 border-gray-300 text-gray-700 font-bold px-4 py-2 rounded-xl text-sm">✎ Kelola Materi</button>
                        <button class="bg-[#111827] text-white font-bold px-4 py-2 rounded-xl border-b-4 border-black text-sm">Laporan CSV/PDF</button>
                    </div>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#f0e6d2] text-[#8a7346] text-xs font-extrabold uppercase tracking-widest border-b-2 border-[#d8c39e]">
                            <th class="p-4 pl-6">Nama Mahasiswa</th>
                            <th class="p-4 text-center">Level</th>
                            <th class="p-4 text-center">Sub-Bahasan</th>
                            <th class="p-4 text-center">Rata Kuis</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="font-semibold text-sm text-gray-600">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="p-4 pl-6">Mahasiswa 1 - 240210500000</td>
                            <td class="p-4 text-center"><span class="border-2 border-gray-300 rounded-lg px-2 py-1 font-bold">Lv 2</span></td>
                            <td class="p-4 text-center">4 / 10</td>
                            <td class="p-4 text-center text-gray-900 font-bold">82</td>
                            <td class="p-4 text-center"><span class="bg-[#e6f7ed] text-[#46a302] border-2 border-[#a6deb0] rounded-full px-3 py-1 text-xs font-extrabold">on-track</span></td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="p-4 pl-6">Mahasiswa 2 - 240210500001</td>
                            <td class="p-4 text-center"><span class="border-2 border-gray-300 rounded-lg px-2 py-1 font-bold">Lv 2</span></td>
                            <td class="p-4 text-center">6 / 10</td>
                            <td class="p-4 text-center text-gray-900 font-bold">74</td>
                            <td class="p-4 text-center"><span class="bg-[#e6f7ed] text-[#46a302] border-2 border-[#a6deb0] rounded-full px-3 py-1 text-xs font-extrabold">on-track</span></td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 bg-orange-50">
                            <td class="p-4 pl-6 text-orange-800">Mahasiswa 3 - 240210500002</td>
                            <td class="p-4 text-center"><span class="border-2 border-orange-300 rounded-lg px-2 py-1 font-bold text-orange-600">Lv 1</span></td>
                            <td class="p-4 text-center">2 / 10</td>
                            <td class="p-4 text-center text-gray-900 font-bold">61</td>
                            <td class="p-4 text-center"><span class="bg-[#fcecd9] text-[#d97706] border-2 border-[#f6cda3] rounded-full px-3 py-1 text-xs font-extrabold">perhatian</span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-4 text-center text-xs font-bold text-gray-400 border-t-2 border-gray-200 hover:bg-gray-50 cursor-pointer">
                    menampilkan 3 dari 42 · lihat semua
                </div>
            </div>

        </main>
    </div>
</body>
</html>