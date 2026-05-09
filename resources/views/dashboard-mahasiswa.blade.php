<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Dashboard Mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f9fa] font-sans text-gray-700 h-screen overflow-hidden">


    <header class="bg-white border-b-2 border-gray-200 h-16 flex items-center justify-between px-8 z-10 relative shadow-sm">
        <div class="font-extrabold text-xl flex items-center gap-2">
            <span class="bg-gray-200 text-gray-500 px-3 py-1 rounded-lg border-b-2 border-gray-300 text-sm">Logo</span> PINTAR
        </div>
        <div class="flex items-center gap-4 font-bold text-sm">
            <span class="bg-gray-100 px-3 py-1 rounded-full border-2 border-gray-200">Level 2 - 4/10</span>
            <span class="bg-[#ffc800] text-white px-3 py-1 rounded-full border-b-2 border-yellow-500">Struktur Data</span>
            <span class="text-gray-500">Halo, User ✦</span>
            <a href="#" class="text-red-500 hover:underline">Logout</a>
        </div>
    </header>

    <div class="flex h-[calc(100vh-4rem)]">
        <aside class="w-64 bg-white border-r-2 border-gray-200 p-6 flex flex-col gap-6 overflow-y-auto">
            <div class="text-xs font-extrabold text-gray-400 tracking-wider">MENU</div>
            <nav class="flex flex-col gap-2">
                <a href="/mahasiswa" class="flex items-center gap-3 p-3 bg-gray-100 rounded-xl border-2 border-gray-900 font-bold text-gray-900"><span class="w-2 h-2 bg-black rounded-full"></span> Dashboard</a>
                <a href="/materi" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition"><span class="w-2 h-2 border-2 border-gray-400 rounded-full"></span> Materi</a>
                <a href="/kuis" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition"><span class="w-2 h-2 border-2 border-gray-400 rounded-full"></span> Kuis</a>
                <a href="/progres" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition"><span class="w-2 h-2 border-2 border-gray-400 rounded-full"></span> Progres</a>
                <a href="/profil" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl font-bold text-gray-500 transition"><span class="w-2 h-2 border-2 border-gray-400 rounded-full"></span> Profil</a>
            </nav>
            <div class="text-xs font-extrabold text-gray-400 tracking-wider mt-4">MATA KULIAH</div>
            <a href="#" class="flex items-center gap-3 p-3 bg-white rounded-xl border-2 border-gray-900 font-bold text-gray-900"><span class="w-2 h-2 bg-black rounded-full"></span> Struktur Data</a>
            <p class="text-xs text-gray-400 font-semibold px-3">Semester 2 - 2025/2026</p>
        </aside>

        <main class="flex-1 p-6 overflow-y-auto">
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200">
                    <h3 class="text-2xl font-extrabold text-gray-800">Level 2</h3>
                    <p class="text-xs text-gray-400 font-semibold">Level saat ini</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200">
                    <h3 class="text-2xl font-extrabold text-[#58cc02]">4 / 10 <span class="text-sm float-right">✔</span></h3>
                    <p class="text-xs text-gray-400 font-semibold">Sub-bahasan selesai</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200">
                    <h3 class="text-2xl font-extrabold text-[#1cb0f6]">82 <span class="text-sm float-right">★</span></h3>
                    <p class="text-xs text-gray-400 font-semibold">Rata-rata nilai kuis</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200 opacity-70">
                    <h3 class="text-xl font-extrabold text-gray-600">Terkunci <span class="text-sm float-right">🔒</span></h3>
                    <p class="text-xs text-gray-400 font-semibold">Status post-test level 2</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border-2 border-b-8 border-gray-200 relative min-h-[600px]">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-800">Peta Belajar</h2>
                        <p class="text-sm font-semibold text-gray-500">Kerjakan sub-bahasan secara berurutan.</p>
                    </div>
                    <div class="bg-[#f0e6d2] px-4 py-2 rounded-xl border-2 border-b-4 border-[#d8c39e] font-bold text-[#8a7346]">
                        Unit 1 - Linear Structures
                    </div>
                </div>

                <div class="text-center text-gray-300 font-extrabold tracking-[0.2em] mb-8">------ MULAI DI SINI ------</div>

                <div class="flex flex-col items-center gap-10">
                    <div class="flex items-center gap-4 translate-x-12 cursor-pointer hover:scale-105 transition">
                        <div class="w-16 h-16 bg-[#58cc02] rounded-full border-b-8 border-[#46a302] flex items-center justify-center text-white text-2xl font-bold">✔</div>
                        <span class="font-extrabold text-[#58cc02]">Array · 90</span>
                    </div>
                    <div class="flex items-center gap-4 translate-x-24 cursor-pointer hover:scale-105 transition">
                        <div class="w-16 h-16 bg-[#58cc02] rounded-full border-b-8 border-[#46a302] flex items-center justify-center text-white text-2xl font-bold">✔</div>
                        <span class="font-extrabold text-[#58cc02]">Linked List · 85</span>
                    </div>
                    
                    <div class="flex items-center gap-4 translate-x-8 relative group cursor-pointer">
                        <div class="w-20 h-20 bg-[#1cb0f6] rounded-full border-b-8 border-[#1899d6] flex items-center justify-center text-white text-3xl font-bold animate-bounce shadow-xl">▶</div>
                        
                        <div class="absolute left-full ml-4 top-1/2 -translate-y-1/2 w-64 bg-white p-4 border-2 border-b-4 border-gray-200 rounded-2xl z-20 hidden group-hover:block">
                            <h4 class="font-extrabold text-gray-800">Tree</h4>
                            <p class="text-xs text-gray-500 font-semibold mb-3">Sub-bahasan 5 · Struktur Hierarkis. Pelajari traversal, bst, dll.</p>
                            <button class="w-full bg-[#111827] text-white py-2 rounded-xl border-b-4 border-gray-900 font-bold text-sm">▶ Lanjutkan</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 -translate-x-12 opacity-50">
                        <div class="w-16 h-16 bg-[#e5e5e5] rounded-full border-b-8 border-[#cecece] flex items-center justify-center text-gray-400 text-2xl">🔒</div>
                        <span class="font-extrabold text-gray-400">Graph</span>
                    </div>
                </div>
                
                <div class="text-center text-gray-300 font-extrabold tracking-[0.2em] mt-16">------ POST-TEST LEVEL 2 ------</div>
            </div>
        </main>

        <aside class="w-80 p-6 flex flex-col gap-4 overflow-y-auto bg-gray-50 border-l-2 border-gray-200">
            <div class="bg-[#111827] p-5 rounded-2xl border-b-8 border-gray-900 text-white">
                <h3 class="text-lg font-extrabold mb-1">👋 Selamat datang kembali, Mahasiswa</h3>
                <p class="text-xs text-gray-400 font-semibold mb-4">Terakhir belajar 2 hari lalu - Queue selesai, saatnya Tree.</p>
                <button class="w-full bg-white text-gray-900 py-2 rounded-xl font-bold text-sm border-b-4 border-gray-300 hover:bg-gray-100">▶ Lanjutkan di Tree</button>
            </div>

            <div class="bg-white p-5 rounded-2xl border-2 border-b-4 border-gray-200">
                <h3 class="font-extrabold text-gray-800 text-lg mb-4">Progres Level 2</h3>
                <div class="space-y-3 text-sm font-semibold text-gray-600">
                    <div class="flex justify-between items-center">
                        <span>Sub-bahasan</span>
                        <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full"><div class="w-2/5 h-full bg-[#111827] rounded-full"></div></div>
                        <span>4/10</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Rata-rata kuis</span>
                        <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full"><div class="w-[82%] h-full bg-[#111827] rounded-full"></div></div>
                        <span>82</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4 leading-relaxed font-semibold">Selesaikan <strong class="text-gray-800">6 sub-bahasan lagi</strong> untuk membuka post-test dan naik ke Level 3.</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border-2 border-b-4 border-gray-200">
                <h3 class="font-extrabold text-gray-800 text-lg mb-4">Riwayat Nilai Kuis</h3>
                <div class="space-y-3 text-sm font-bold">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Array</span>
                        <span class="w-8 h-8 rounded-full border-2 border-[#58cc02] text-[#58cc02] flex items-center justify-center">90</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Linked List</span>
                        <span class="w-8 h-8 rounded-full border-2 border-[#58cc02] text-[#58cc02] flex items-center justify-center">85</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Tree</span>
                        <span class="w-12 h-8 rounded-full border-2 border-gray-200 text-gray-400 flex items-center justify-center text-xs">- belum</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</body>
</html>