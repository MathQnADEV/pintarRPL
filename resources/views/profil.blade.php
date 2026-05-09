<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Profil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f2f2f2] font-sans text-gray-700 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-lg rounded-3xl border-2 border-b-8 border-gray-200 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-[#1cb0f6] to-[#4c3fb5] flex items-end px-10 pb-6 relative">
            <div class="w-24 h-24 bg-white rounded-full absolute -bottom-12 left-10 border-4 border-white shadow-md flex items-center justify-center text-4xl">👨‍🎓</div>
        </div>
        <div class="pt-16 pb-10 px-10">
            <h1 class="text-3xl font-extrabold text-gray-800">Shaquille Rashaun [cite: 9]</h1>
            <p class="font-bold text-gray-400 mb-6">240210500030 · Mahasiswa [cite: 107]</p>
            
            <div class="space-y-4 font-bold">
                <div class="p-4 bg-gray-50 rounded-2xl border-2 border-gray-100">
                    <p class="text-xs text-gray-400 uppercase">Email Kampus </p>
                    <p class="text-gray-700">shaquille@kampus.ac.id</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl border-2 border-gray-100">
                    <p class="text-xs text-gray-400 uppercase">Mata Kuliah Aktif [cite: 31]</p>
                    <p class="text-gray-700">Struktur Data - TEKOM A</p>
                </div>
            </div>
            
            <button class="w-full mt-8 bg-white border-2 border-b-4 border-gray-300 text-gray-500 font-extrabold py-3 rounded-xl hover:bg-gray-50 active:translate-y-1 active:border-b-0 transition-all">Edit Profil</button>
        </div>
    </div>
</body>
</html>