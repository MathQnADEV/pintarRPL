<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f2f2f2] font-sans text-[#4b4b4b] min-h-screen flex items-center justify-center">

    <div class="bg-white p-10 rounded-3xl border-2 border-b-8 border-gray-200 w-full max-w-md shadow-sm">
        <div class="flex justify-center mb-6">
            <div class="bg-[#e5e5e5] text-[#afafaf] font-bold text-2xl py-4 px-8 rounded-2xl border-2 border-b-4 border-[#cecece]">
                Logo
            </div>
        </div>

        <h1 class="text-3xl font-extrabold text-center mb-2 text-gray-800">Masuk ke PINTAR</h1>
        <p class="text-center text-gray-500 font-medium mb-8">Gunakan email dan password terdaftar</p>

        <form action="#" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Email</label>
                <input type="email" placeholder="nama@kampus.ac.id" class="w-full p-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]">
            </div>
            
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Password</label>
                <input type="password" placeholder="••••••••" class="w-full p-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]">
            </div>

            <div class="pt-4">
                <button  type="submit" class="w-full bg-[#111827] text-white font-bold text-lg py-4 rounded-2xl border-b-8 border-gray-900 hover:bg-gray-800 active:border-b-0 active:translate-y-2 transition-all">
                    <a href="/mahasiswa">Login</a>
                </button>
            </div>
        </form>

        <div class="flex justify-between mt-8 text-sm font-bold text-[#1cb0f6]">
            <a href="#" class="hover:underline opacity-70 hover:opacity-100 transition">Lupa password?</a>
            <a href="#" class="hover:underline opacity-70 hover:opacity-100 transition">Belum punya akun? Daftar</a>
        </div>
    </div>

</body>
</html>