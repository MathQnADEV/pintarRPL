@extends('layouts.mahasiswa')
@section('title', 'Profil')

@section('content')

    {{-- ─── Profile card ────────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-center p-4 lg:p-10">
        <div class="bg-white w-full max-w-lg rounded-3xl border-2 border-b-8 border-gray-200 overflow-hidden">

            {{-- Cover banner --}}
            <div class="h-32 bg-gradient-to-r from-[#1cb0f6] to-[#4c3fb5] relative">
                <div class="w-24 h-24 bg-white rounded-full absolute -bottom-12 left-10 border-4 border-white shadow-md flex items-center justify-center text-4xl">
                    👨‍🎓
                </div>
            </div>

            {{-- Info --}}
            <div class="pt-16 pb-10 px-10">

                <h1 class="text-3xl font-extrabold text-gray-800">{{ $user->name }}</h1>
                <p class="font-bold text-gray-400 mb-6">
                    {{ $user->nim ?? '—' }} · Mahasiswa
                </p>

                <div class="space-y-4">

                    {{-- Email --}}
                    <div class="p-4 bg-gray-50 rounded-2xl border-2 border-gray-100">
                        <p class="text-xs font-extrabold text-gray-400 uppercase mb-1">Email</p>
                        <p class="font-bold text-gray-700">{{ $user->email }}</p>
                    </div>

                    {{-- Phone --}}
                    @if ($user->phone)
                        <div class="p-4 bg-gray-50 rounded-2xl border-2 border-gray-100">
                            <p class="text-xs font-extrabold text-gray-400 uppercase mb-1">No. Telepon</p>
                            <p class="font-bold text-gray-700">{{ $user->phone }}</p>
                        </div>
                    @endif

                    {{-- Active class --}}
                    @if ($kelas)
                        <div class="p-4 bg-gray-50 rounded-2xl border-2 border-gray-100">
                            <p class="text-xs font-extrabold text-gray-400 uppercase mb-1">Kelas Aktif</p>
                            <p class="font-bold text-gray-700">{{ $kelas->name }}</p>
                            @if ($kelas->schedule || $kelas->ruangan)
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ implode(' · ', array_filter([$kelas->schedule, $kelas->ruangan])) }}
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Learning level --}}
                    @if ($pretestResult)
                        @php
                            $levelNum = ['pemula' => 1, 'menengah' => 2, 'lanjut' => 3];
                            $levelLabel = match($level) {
                                'pemula'   => 'Level 1 · Pemula',
                                'menengah' => 'Level 2 · Menengah',
                                'lanjut'   => 'Level 3 · Lanjut',
                                default    => ucfirst($level),
                            };
                            $pretestLabel = match($pretestResult->level) {
                                'pemula'   => 'Pemula',
                                'menengah' => 'Menengah',
                                'lanjut'   => 'Lanjut',
                                default    => ucfirst($pretestResult->level),
                            };
                            $naik = $pretestResult->level !== $level;
                        @endphp
                        <div class="p-4 bg-[#f0eeff] rounded-2xl border-2 border-[#ddd5ff]">
                            <p class="text-xs font-extrabold text-[#6c5ce7] uppercase mb-1">Level Saat Ini</p>
                            <p class="font-extrabold text-[#4c3fb5] text-lg">{{ $levelLabel }}</p>
                            @if ($naik)
                                <p class="text-xs text-[#58cc02] font-bold mt-1">
                                    🏆 Naik dari {{ $pretestLabel }} setelah lulus post-test
                                </p>
                            @endif
                            <p class="text-xs text-[#9381ff] mt-1">
                                Pre-test awal: <strong>{{ $pretestLabel }}</strong> · Skor {{ $pretestResult->score }}/100 ·
                                {{ $pretestResult->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    @endif

                </div>

                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="block w-full mt-8 text-center bg-white border-2 border-b-4 border-gray-300
                          text-gray-600 font-extrabold py-3 rounded-xl
                          hover:bg-gray-50 active:translate-y-1 active:border-b-0 transition-all">
                    ← Kembali ke Dashboard
                </a>

            </div>
        </div>
    </div>

@endsection
