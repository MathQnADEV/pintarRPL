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
            <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-24 h-24">
        </div>

        <h1 class="text-3xl font-extrabold text-center mb-2 text-gray-800">Masuk ke PINTAR</h1>
        <p class="text-center text-gray-500 font-medium mb-8">Gunakan email dan password terdaftar</p>

        {{-- Error messages --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-2xl text-red-700 text-sm font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-2xl text-blue-700 text-sm font-semibold">
                {{ session('info') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@kampus.ac.id"
                    required
                    class="w-full p-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9] @error('email') border-red-400 @enderror"
                >
            </div>

            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    class="w-full p-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]"
                >
            </div>

            <div class="pt-4">
                <button
                    type="submit"
                    class="w-full bg-[#111827] text-white font-bold text-lg py-4 rounded-2xl border-b-8 border-gray-900 hover:bg-gray-800 active:border-b-0 active:translate-y-2 transition-all"
                >
                    Masuk
                </button>
            </div>
        </form>

        <div class="flex justify-between mt-8 text-sm font-bold text-[#1cb0f6]">
            <a href="{{ route('register') }}" class="hover:underline transition">Belum punya akun? Daftar</a>
        </div>
    </div>

</body>
</html>
