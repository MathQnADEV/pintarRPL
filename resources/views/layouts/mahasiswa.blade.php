<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR · @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f8f9fa] font-sans text-gray-700 h-screen overflow-hidden">

    @php
        $__user = auth()->user();
        $__level = isset($level) ? $level : \App\Models\PretestResult::effectiveLevel($__user->id);
        $__kelas = isset($kelas) ? $kelas : $__user->kelas()->first();

        $__navItems = [
            ['route' => 'mahasiswa.dashboard', 'label' => 'Dashboard', 'dot' => true],
            ['route' => 'mahasiswa.progres', 'label' => 'Progres', 'dot' => true],
            ['route' => 'mahasiswa.profil', 'label' => 'Profil', 'dot' => true],
        ];
    @endphp

    <div class="flex h-full">

        {{-- ── Sidebar kiri (desktop: always visible, mobile: hidden — pakai bottom nav) ── --}}
        <aside class="hidden lg:flex flex-col w-64 bg-white border-r-2 border-gray-200 overflow-y-auto shrink-0">

            {{-- Logo --}}
            <div class="h-16 border-b-2 border-gray-200 shrink-0 flex items-start justify-start p-4">
                <img src="{{ asset('img/logo.png') }}" alt="logo" class="h-full w-auto object-contain px-3">
            </div>

            <div class="p-6 flex flex-col gap-6 flex-1">

                <div class="text-xs font-extrabold text-gray-400 tracking-wider">MENU</div>
                <nav class="flex flex-col gap-2">
                    @foreach ($__navItems as $item)
                        @php $__active = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 p-3 rounded-xl font-bold text-sm transition
                              {{ $__active
                                  ? 'bg-gray-100 border-2 border-gray-900 text-gray-900'
                                  : 'hover:bg-gray-50 border-2 border-transparent text-gray-500' }}">
                            <span
                                class="w-2 h-2 rounded-full shrink-0
                                     {{ $__active ? 'bg-black' : 'border-2 border-gray-400' }}"></span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                @if ($__kelas)
                    <div class="text-xs font-extrabold text-gray-400 tracking-wider mt-2">MATA KULIAH</div>
                    <div class="p-3 bg-white rounded-xl border-2 border-gray-900 font-bold text-gray-900 text-sm">
                        {{ $__kelas->mata_kuliah }}
                        <p class="text-xs text-gray-400 font-semibold mt-1">
                            {{ $__kelas->name }} · {{ $__kelas->academic_year }}
                        </p>
                    </div>
                @endif

                {{-- Logout (di bawah sidebar) --}}
                <div class="mt-auto pt-4 border-t border-gray-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 p-3 rounded-xl font-bold text-sm text-red-400
                                   hover:bg-red-50 border-2 border-transparent hover:border-red-200 transition text-left">
                            <span class="w-2 h-2 rounded-full border-2 border-red-300 shrink-0"></span>
                            Keluar
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        {{-- ── Area kanan: header + konten ── --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- Header --}}
            <header
                class="h-16 bg-white border-b-2 border-gray-200 flex items-center justify-between px-4 lg:px-8 shrink-0 shadow-sm z-10">
                {{-- Kiri: logo (mobile) / level badge (desktop) --}}
                <div class="flex items-center gap-3">
                    <span class="lg:hidden font-extrabold text-xl">PINTAR</span>
                    <span
                        class="hidden lg:inline bg-gray-100 px-3 py-1 rounded-full border-2 border-gray-200 text-sm font-extrabold text-gray-700">
                        {{ ucfirst($__level) }}
                        @if (isset($completedCount, $totalCount))
                            — {{ $completedCount }}/{{ $totalCount }}
                        @endif
                    </span>
                </div>

                {{-- Kanan: badges + nama --}}
                <div class="flex items-center gap-2 lg:gap-4 font-bold text-sm">
                    {{-- Level (mobile) --}}
                    <span
                        class="lg:hidden bg-gray-100 px-2 py-1 rounded-full border border-gray-200 text-xs font-extrabold">
                        {{ ucfirst($__level) }}
                    </span>
                    @if ($__kelas)
                        <span
                            class="hidden sm:inline bg-[#ffc800] text-white px-3 py-1 rounded-full border-b-2 border-yellow-500 text-xs font-extrabold">
                            {{ $__kelas->mata_kuliah }}
                        </span>
                    @endif
                    <span class="text-gray-500 hidden md:inline text-sm">Halo, {{ $__user->name }} ✦</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1 text-red-500 hover:text-red-700 font-bold transition">
                            <span class="text-base leading-none lg:hidden" title="Keluar">🚪</span>
                            <span class="hidden lg:inline text-sm">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Flash notifications --}}
            <div class="fixed top-20 right-4 z-50 flex flex-col gap-2 max-w-xs w-full pointer-events-none">

                @if (session('quiz_done'))
                    <div
                        class="bg-white p-4 rounded-2xl border-2 border-b-4 shadow-lg text-sm font-semibold pointer-events-auto
                    {{ session('quiz_status') === 'lulus' ? 'border-green-400' : 'border-red-400' }}">
                        @if (session('quiz_status') === 'lulus')
                            <p class="text-green-700">✅ Kuis <strong>{{ session('quiz_topic') }}</strong> selesai!</p>
                            <p class="text-gray-500 mt-1">Skor: {{ session('quiz_score') }} — Topik ditandai selesai.
                            </p>
                        @else
                            <p class="text-red-700">❌ Kuis <strong>{{ session('quiz_topic') }}</strong> belum lulus.
                            </p>
                            <p class="text-gray-500 mt-1">Skor: {{ session('quiz_score') }} — Coba lagi.</p>
                        @endif
                    </div>
                @endif

                @if (session('pretest_done'))
                    <div
                        class="bg-white p-4 rounded-2xl border-2 border-b-4 border-blue-400 shadow-lg text-sm font-semibold pointer-events-auto">
                        <p class="text-blue-700">🎯 Pre-test selesai!</p>
                        <p class="text-gray-500 mt-1">Skor: {{ session('pretest_score') }} — Level:
                            <strong>{{ ucfirst(session('pretest_level')) }}</strong></p>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="bg-white p-4 rounded-2xl border-2 border-b-4 border-red-400 shadow-lg text-sm font-semibold pointer-events-auto">
                        <p class="text-red-700">⚠️ {{ session('error') }}</p>
                    </div>
                @endif

                @if (session('info'))
                    <div
                        class="bg-white p-4 rounded-2xl border-2 border-b-4 border-blue-300 shadow-lg text-sm font-semibold pointer-events-auto">
                        <p class="text-blue-700">ℹ️ {{ session('info') }}</p>
                    </div>
                @endif

            </div>

            {{-- Konten halaman --}}
            <div class="flex-1 min-h-0 @yield('content-class', 'overflow-y-auto pb-14 lg:pb-0')">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- ── Bottom navigation (mobile & tablet only) ── --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 z-30 lg:hidden safe-bottom">
        <div class="flex">
            @foreach ([['route' => 'mahasiswa.dashboard', 'icon' => '🏠', 'label' => 'Dashboard'], ['route' => 'mahasiswa.progres', 'icon' => '📊', 'label' => 'Progres'], ['route' => 'mahasiswa.profil', 'icon' => '👤', 'label' => 'Profil']] as $__nav)
                @php $__active = request()->routeIs($__nav['route']); @endphp
                <a href="{{ route($__nav['route']) }}"
                    class="flex-1 flex flex-col items-center py-2 text-xs font-bold transition gap-0.5
                      {{ $__active ? 'text-[#4c3fb5]' : 'text-gray-400' }}">
                    <span class="text-xl leading-tight">{{ $__nav['icon'] }}</span>
                    {{ $__nav['label'] }}
                    @if ($__active)
                        <span class="w-1 h-1 rounded-full bg-[#4c3fb5]"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>

</body>

</html>
