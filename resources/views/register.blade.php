<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Daftar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f2f2f2] font-sans text-[#4b4b4b] min-h-screen flex items-center justify-center py-10">

    <div class="bg-white p-10 rounded-3xl border-2 border-b-8 border-gray-200 w-full max-w-md shadow-sm">

        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-24 h-24">
        </div>

        <h1 class="text-3xl font-extrabold text-center mb-2 text-gray-800">Daftar ke PINTAR</h1>
        <p class="text-center text-gray-500 font-medium mb-8">Buat akun mahasiswa baru</p>

        {{-- Error messages --}}
        @if ($errors->any())
            <div
                class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-2xl text-red-700 text-sm font-semibold space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama sesuai KTP / KTM"
                    required
                    class="w-full p-4 border-2 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]
                           @error('name') border-red-400 @else border-gray-300 @enderror">
            </div>

            {{-- Email --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@kampus.ac.id"
                    required
                    class="w-full p-4 border-2 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]
                           @error('email') border-red-400 @else border-gray-300 @enderror">
            </div>

            {{-- NIM --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">NIM</label>
                <input type="text" name="nim" value="{{ old('nim') }}" placeholder="Nomor Induk Mahasiswa"
                    required
                    class="w-full p-4 border-2 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]
                           @error('nim') border-red-400 @else border-gray-300 @enderror">
            </div>

            {{-- No. Telepon (opsional) --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">
                    No. Telepon
                    <span class="text-gray-400 font-medium">(opsional)</span>
                </label>
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                    class="w-full p-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]">
            </div>

            {{-- Kelas --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Kelas</label>
                @if ($kelasList->isEmpty())
                    <div
                        class="p-4 bg-yellow-50 border-2 border-yellow-200 rounded-2xl text-yellow-700 text-sm font-semibold">
                        ⚠️ Belum ada kelas aktif. Hubungi dosen atau admin untuk pendaftaran.
                    </div>
                    <input type="hidden" name="kelas_id" value="">
                @else
                    <select name="kelas_id" required
                        class="w-full p-4 border-2 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9] appearance-none
                               @error('kelas_id') border-red-400 @else border-gray-300 @enderror">
                        <option value="" disabled {{ old('kelas_id') ? '' : 'selected' }}>— Pilih kelas —</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->name }} · {{ $kelas->mata_kuliah }}
                                @if ($kelas->academic_year)
                                    ({{ $kelas->academic_year }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Password --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Password</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" required
                    class="w-full p-4 border-2 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]
                           @error('password') border-red-400 @else border-gray-300 @enderror">
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block font-bold text-sm mb-2 text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" required
                    class="w-full p-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-[#1cb0f6] transition bg-[#f9f9f9]">
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-[#111827] text-white font-bold text-lg py-4 rounded-2xl border-b-8 border-gray-900 hover:bg-gray-800 active:border-b-0 active:translate-y-2 transition-all">
                    Buat Akun
                </button>
            </div>
        </form>

        <div class="mt-8 text-center text-sm font-semibold text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-[#1cb0f6] font-bold hover:underline ml-1">Masuk</a>
        </div>

    </div>

</body>

</html>
