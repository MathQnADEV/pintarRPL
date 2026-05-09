<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Progres Belajar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f2f2f2] font-sans text-gray-700 min-h-screen p-10">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-extrabold mb-8 flex items-center gap-4 text-gray-800">📊 Progres Belajar <span class="text-sm font-normal text-gray-400">Mahasiswa 1 [cite: 163]</span></h1>
        
        <div class="grid grid-cols-2 gap-6 mb-10">
            <div class="bg-white p-8 rounded-3xl border-2 border-b-8 border-gray-200">
                <p class="text-gray-400 font-extrabold text-xs uppercase mb-2">Level Aktif [cite: 801]</p>
                <h3 class="text-3xl font-extrabold text-gray-800">Level 2: Menengah</h3>
                <p class="text-sm text-gray-500 font-semibold mt-2">Skor pretest: 85/100 [cite: 793, 858]</p>
            </div>
            <div class="bg-white p-8 rounded-3xl border-2 border-b-8 border-gray-200">
                <p class="text-gray-400 font-extrabold text-xs uppercase mb-2">Penyelesaian Materi [cite: 163]</p>
                <h3 class="text-3xl font-extrabold text-[#58cc02]">40% <span class="text-sm text-gray-300 font-normal">/ 10 sub</span></h3>
                <div class="w-full h-3 bg-gray-100 rounded-full mt-4"><div class="w-[40%] h-full bg-[#58cc02] rounded-full"></div></div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border-2 border-gray-200 overflow-hidden shadow-sm">
            <div class="p-6 bg-gray-50 border-b-2 border-gray-200">
                <h2 class="font-extrabold text-lg text-gray-800">Riwayat Nilai Kuis [cite: 163]</h2>
            </div>
            <table class="w-full text-left font-semibold">
                <tr class="border-b border-gray-100"><td class="p-4 pl-8">Array</td><td class="p-4 text-right pr-8 text-[#58cc02]">90</td></tr>
                <tr class="border-b border-gray-100"><td class="p-4 pl-8">Linked List</td><td class="p-4 text-right pr-8 text-[#58cc02]">85</td></tr>
                <tr class="border-b border-gray-100 bg-gray-50 opacity-50"><td class="p-4 pl-8">Tree</td><td class="p-4 text-right pr-8">Belum</td></tr>
            </table>
        </div>
    </div>
</body>
</html>