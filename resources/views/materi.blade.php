<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Materi Queue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f9fa] font-sans text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-72 bg-white border-r-2 border-gray-200 p-6 overflow-y-auto">
            <h2 class="font-extrabold text-[#4c3fb5] text-lg mb-6 tracking-tight">📖 Unit 1: Linear</h2>
            <nav class="space-y-3">
                <a href="#" class="block p-3 rounded-xl border-2 border-gray-100 font-bold text-gray-400">Array</a>
                <a href="#" class="block p-3 rounded-xl border-2 border-gray-900 bg-gray-100 font-bold text-gray-900 flex justify-between">Queue (Antrian) <span class="text-[#58cc02]">▶</span></a>
                <a href="#" class="block p-3 rounded-xl border-2 border-gray-100 font-bold text-gray-400">Linked List</a>
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-10">
            <div class="max-w-5xl mx-auto">
                <div class="bg-[#58cc02] p-4 rounded-t-2xl text-white font-extrabold flex justify-between">
                    <span>Level 2: Menengah — Queue (Antrian) [cite: 804]</span>
                    <span class="opacity-80">Skor: 82 → Level 3</span>
                </div>
                
                <div class="bg-white p-8 rounded-b-2xl border-2 border-b-8 border-gray-200 shadow-sm">
                    <h1 class="text-3xl font-extrabold mb-4">Queue (Antrian) C++ [cite: 805]</h1>
                    <p class="text-gray-500 font-semibold mb-6">Queue: FIFO (First In First Out). Operasi enqueue (push), dequeue (pop). [cite: 806]</p>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="bg-[#1e1e1e] p-6 rounded-2xl font-mono text-sm text-gray-300 leading-relaxed shadow-inner">
                            <p class="text-blue-400">#include <iostream></p>
                            <p class="text-blue-400">using namespace std;</p>
                            <br>
                            <p class="text-green-400">// [cite: 807-813]</p>
                            <p>int main() {</p>
                            <p class="pl-4">queue q;</p>
                            <p class="pl-4 text-gray-500">q.push("Ali");</p>
                            <p class="pl-4 text-gray-500">q.push("Budi");</p>
                            <p class="pl-4">cout << q.front(); <span class="text-gray-500">// Output: Ali</span></p>
                            <p class="pl-4">q.pop();</p>
                            <p>}</p>
                        </div>
                        <div class="bg-[#f0f9ff] border-2 border-dashed border-[#1cb0f6] rounded-2xl flex flex-col items-center justify-center p-6">
                            <div class="flex gap-2 mb-8">
                                <div class="w-12 h-12 bg-white border-2 border-[#1cb0f6] rounded-lg flex items-center justify-center font-bold shadow-sm">Ali</div>
                                <div class="w-12 h-12 bg-white border-2 border-[#1cb0f6] rounded-lg flex items-center justify-center font-bold shadow-sm">Budi</div>
                                <div class="w-12 h-12 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg"></div>
                            </div>
                            <p class="text-xs font-extrabold text-[#1cb0f6] uppercase">Animasi: Orang datang di belakang, keluar dari depan. [cite: 814]</p>
                            <div class="mt-6 flex gap-2">
                                <button class="px-4 py-2 bg-white border-2 border-b-4 border-gray-300 rounded-xl font-bold text-sm">◀ Step</button>
                                <button class="px-6 py-2 bg-[#1cb0f6] text-white border-b-4 border-[#1899d6] rounded-xl font-bold text-sm">Play ▶</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 bg-white p-10 rounded-2xl border-2 border-b-8 border-gray-200 text-center">
                    <div class="text-5xl mb-4">🏆</div>
                    <h2 class="text-2xl font-extrabold mb-2">Selamat! Anda telah lulus evaluasi Queue (Antrian) [cite: 815]</h2>
                    <p class="text-gray-500 font-semibold mb-6">Silakan lanjutkan ke materi berikutnya. [cite: 816]</p>
                    <button class="bg-[#4c3fb5] text-white font-bold py-3 px-10 rounded-xl border-b-4 border-[#3d3291] uppercase tracking-widest hover:bg-[#3d3291] transition">Lanjut ke Linked List → [cite: 817]</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>