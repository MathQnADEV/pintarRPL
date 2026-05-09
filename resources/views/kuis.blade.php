<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Kuis</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-gray-800 h-screen flex flex-col">
    <div class="p-8 max-w-4xl mx-auto w-full flex items-center gap-6">
        <button class="text-gray-400 text-2xl">✕</button>
        <div class="flex-1 h-4 bg-gray-100 rounded-full">
            <div class="w-1/2 h-full bg-[#58cc02] rounded-full"></div>
        </div>
        <div class="text-[#58cc02] font-extrabold text-xl">❤️ 3</div>
    </div>

    <main class="flex-1 max-w-2xl mx-auto w-full pt-10 px-6">
        <h2 class="text-3xl font-extrabold mb-8 text-gray-800">2. Kompleksitas insert node di awal linked list adalah? [cite: 843]</h2>
        <div class="grid gap-4">
            <label class="p-4 border-2 border-gray-200 border-b-4 rounded-2xl flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition active:translate-y-1 active:border-b-0">
                <span class="w-8 h-8 rounded-lg border-2 border-gray-200 flex items-center justify-center font-bold text-gray-400">1</span>
                <span class="text-xl font-bold">O(1) [cite: 845]</span>
                <input type="radio" name="ans" class="hidden">
            </label>
            <label class="p-4 border-2 border-[#1cb0f6] border-b-4 bg-[#e6f7ff] rounded-2xl flex items-center gap-4 cursor-pointer">
                <span class="w-8 h-8 rounded-lg border-2 border-[#1cb0f6] flex items-center justify-center font-bold text-[#1cb0f6]">2</span>
                <span class="text-xl font-bold text-[#1cb0f6]">O(n) [cite: 844]</span>
                <input type="radio" name="ans" class="hidden" checked>
            </label>
        </div>
    </main>

    <div class="p-8 border-t-2 border-gray-100 bg-white">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <button class="bg-[#1cb0f6] text-white font-extrabold py-4 px-12 rounded-2xl border-b-4 border-[#1899d6] uppercase tracking-widest hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all">Kirim Jawaban [cite: 847]</button>
        </div>
    </div>
</body>
</html>