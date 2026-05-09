<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Pre-test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f9fa] font-sans text-gray-800 min-h-screen pb-10">
    
    <nav class="bg-white p-4 border-b-2 border-gray-200 flex justify-between items-center mb-8 px-10 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#4c3fb5] rounded-md"></div>
            <h1 class="text-xl font-extrabold text-[#111827]">PINTAR | <span class="text-[#4c3fb5] font-semibold">Adaptive Learning</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <span class="font-semibold text-gray-700 flex items-center gap-2">
                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs">👤</div> 
                Mahasiswa
            </span>
            <button class="text-red-500 font-semibold px-4 py-1 border border-red-500 rounded-md hover:bg-red-50 transition">Reset Semua</button>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto w-full bg-white p-10 rounded-2xl shadow-sm border border-gray-100">
        
        <div class="mb-8 border-b pb-6">
            <h2 class="text-2xl font-bold mb-2">Pre-test Kemampuan (10 Soal)</h2>
            <p class="text-gray-500">Tentukan level adaptif Anda (Level 1/2/3) berdasarkan skor akhir.</p>
        </div>

        <form action="#" method="POST" class="space-y-8">
            @csrf
            
            <div>
                <p class="font-bold text-lg mb-4">1. Manakah struktur data yang menerapkan prinsip LIFO? [cite: 763]</p>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="q1" value="Queue" class="mr-3 w-4 h-4 text-[#4c3fb5]"> <span class="text-gray-700">Queue [cite: 764]</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="q1" value="Stack" class="mr-3 w-4 h-4 text-[#4c3fb5]"> <span class="text-gray-700">Stack [cite: 766]</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="q1" value="Array" class="mr-3 w-4 h-4 text-[#4c3fb5]"> <span class="text-gray-700">Array [cite: 767]</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="q1" value="Linked List" class="mr-3 w-4 h-4 text-[#4c3fb5]"> <span class="text-gray-700">Linked List [cite: 768]</span>
                    </label>
                </div>
            </div>

            <div>
                <p class="font-bold text-lg mb-4">2. Kompleksitas akses acak pada array? [cite: 769]</p>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q2" value="O(1)" class="mr-3 w-4 h-4"> O(1) [cite: 770]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q2" value="O(n)" class="mr-3 w-4 h-4"> O(n) [cite: 771]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q2" value="O(log n)" class="mr-3 w-4 h-4"> O(log n) [cite: 772]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q2" value="O(n^2)" class="mr-3 w-4 h-4"> O(n) [cite: 773]</label>
                </div>
            </div>

            <div>
                <p class="font-bold text-lg mb-4">3. Kelemahan utama linked list dibanding array? [cite: 774]</p>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q3" value="Akses acak lambat" class="mr-3 w-4 h-4"> Akses acak lambat [cite: 775]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q3" value="Memori lebih besar" class="mr-3 w-4 h-4"> Memori lebih besar [cite: 776]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q3" value="Tidak bisa di-resize" class="mr-3 w-4 h-4"> Tidak bisa di-resize [cite: 777]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q3" value="Sulit dihapus" class="mr-3 w-4 h-4"> Sulit dihapus [cite: 778]</label>
                </div>
            </div>

            <div>
                <p class="font-bold text-lg mb-4">4. Queue menggunakan prinsip? [cite: 779]</p>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q4" value="FIFO" class="mr-3 w-4 h-4"> FIFO [cite: 780]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q4" value="LIFO" class="mr-3 w-4 h-4"> LIFO [cite: 781]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q4" value="LILO" class="mr-3 w-4 h-4"> LILO [cite: 782]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q4" value="FILO" class="mr-3 w-4 h-4"> FILO [cite: 784]</label>
                </div>
            </div>

            <div>
                <p class="font-bold text-lg mb-4">5. Operasi push/pop stack memiliki kompleksitas? [cite: 785]</p>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q5" value="O(1)" class="mr-3 w-4 h-4"> O(1) [cite: 786]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q5" value="O(n)" class="mr-3 w-4 h-4"> O(n) [cite: 788]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q5" value="O(log n)" class="mr-3 w-4 h-4"> O(log n) [cite: 789]</label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"><input type="radio" name="q5" value="O(2n)" class="mr-3 w-4 h-4"> O(2n) [cite: 790]</label>
                </div>
            </div>

            <div class="mt-8">
                <button type="button" class="w-full bg-[#4c3fb5] text-white font-semibold py-3 rounded-lg hover:bg-[#3d3291] transition duration-200">
                    Submit & Tentukan Level [cite: 791]
                </button>
            </div>
        </form>
    </div>
</body>
</html>