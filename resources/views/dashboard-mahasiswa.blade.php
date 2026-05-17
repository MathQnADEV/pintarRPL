@extends('layouts.mahasiswa')
@section('title', 'Dashboard')

{{-- Dashboard: 2 kolom (main scroll kiri + sidebar kanan) di desktop, stack di mobile --}}
@section('content-class', 'flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden pb-14 lg:pb-0')

@section('content')

    {{-- ── Main content ── --}}
    <main class="flex-1 min-w-0 p-4 lg:p-6 lg:overflow-y-auto">

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200">
                <h3 class="text-xl lg:text-2xl font-extrabold text-gray-800">{{ ucfirst($level) }}</h3>
                <p class="text-xs text-gray-400 font-semibold">Level saat ini</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200">
                <h3 class="text-xl lg:text-2xl font-extrabold text-[#58cc02]">
                    {{ $completedCount }} / {{ $totalCount }}
                    <span class="text-sm float-right">✔</span>
                </h3>
                <p class="text-xs text-gray-400 font-semibold">Sub-bahasan selesai</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200">
                <h3 class="text-xl lg:text-2xl font-extrabold text-[#1cb0f6]">
                    {{ $avgQuiz }} <span class="text-sm float-right">★</span>
                </h3>
                <p class="text-xs text-gray-400 font-semibold">Rata-rata nilai kuis</p>
            </div>
            @if ($postTestUnlocked)
                <a href="{{ route('mahasiswa.posttest') }}"
                   class="bg-white p-4 rounded-2xl border-2 border-b-4
                          {{ $postTestResult?->passed ? 'border-[#58cc02]' : 'border-[#ff9600]' }}
                          hover:scale-105 transition-transform block">
                    @if ($postTestResult?->passed)
                        <h3 class="text-lg lg:text-xl font-extrabold text-[#58cc02]">Lulus <span class="text-sm float-right">🏆</span></h3>
                    @else
                        <h3 class="text-lg lg:text-xl font-extrabold text-[#ff9600]">Tersedia <span class="text-sm float-right">📝</span></h3>
                    @endif
                    <p class="text-xs text-gray-400 font-semibold">Post-test {{ ucfirst($level) }}</p>
                </a>
            @else
                <div class="bg-white p-4 rounded-2xl border-2 border-b-4 border-gray-200 opacity-60">
                    <h3 class="text-lg lg:text-xl font-extrabold text-gray-400">Terkunci <span class="text-sm float-right">🔒</span></h3>
                    <p class="text-xs text-gray-400 font-semibold">Post-test {{ ucfirst($level) }}</p>
                </div>
            @endif
        </div>

        {{-- ── Peta Belajar: semua level, gaya Duolingo ── --}}
        <div class="bg-white p-5 lg:p-8 rounded-3xl border-2 border-b-8 border-gray-200 relative">
            <div class="flex flex-wrap justify-between items-start gap-3 mb-8 lg:mb-10">
                <div>
                    <h2 class="text-xl lg:text-2xl font-extrabold text-gray-800">Peta Belajar</h2>
                    <p class="text-xs lg:text-sm font-semibold text-gray-500 mt-1">
                        Kerjakan secara berurutan untuk naik level · topik selesai bisa di-review kapan saja.
                    </p>
                </div>
                <div class="bg-[#111827] text-white px-3 lg:px-4 py-2 rounded-xl border-b-4 border-gray-900 font-bold text-xs lg:text-sm">
                    ▶ Level {{ ucfirst($level) }}
                </div>
            </div>

            @php
                $levelOrder      = \App\Models\PretestResult::LEVELS;
                $currentLevelIdx = array_search($level, $levelOrder, true);
                $levelEmojis     = ['pemula' => '🌱', 'menengah' => '📘', 'lanjut' => '🚀'];
                // sm: prefix agar zigzag hanya muncul di layar ≥ 640px
                $offsets         = ['sm:translate-x-12', 'sm:translate-x-20', 'sm:translate-x-6', 'sm:-translate-x-4', 'sm:-translate-x-10'];
                $n               = 0;
            @endphp

            <div class="text-center text-gray-300 font-extrabold tracking-[0.2em] mb-8 text-xs lg:text-sm">------ MULAI DI SINI ------</div>

            <div class="flex flex-col items-center gap-8 lg:gap-10 overflow-x-hidden">

                @foreach ($levelOrder as $lvlIdx => $lvl)
                    @php
                        $lvlTopics     = $allLevelTopics[$lvl] ?? collect();
                        $isPast        = $lvlIdx < $currentLevelIdx;
                        $isCurrent     = $lvlIdx === $currentLevelIdx;
                        $isFuture      = $lvlIdx > $currentLevelIdx;
                        $lvlPT         = $allPostTestResults[$lvl] ?? null;
                        $lvlPTUnlocked = $isPast || ($isCurrent && $postTestUnlocked);
                    @endphp

                    {{-- Level header --}}
                    <div class="w-full flex items-center gap-3
                        {{ $lvlIdx > 0 ? 'mt-2 pt-6 border-t-2 border-dashed border-gray-100' : '' }}">
                        <span class="text-xl lg:text-2xl">{{ $levelEmojis[$lvl] }}</span>
                        <span class="font-extrabold tracking-[0.12em] text-xs lg:text-sm
                            {{ $isPast ? 'text-[#46a302]' : ($isCurrent ? 'text-gray-700' : 'text-gray-300') }}">
                            LEVEL {{ strtoupper($lvl) }}
                        </span>
                        @if ($isPast)
                            <span class="text-xs font-extrabold bg-[#58cc02] text-white px-2 py-0.5 rounded-full">✓ Selesai</span>
                        @elseif ($isCurrent)
                            <span class="text-xs font-extrabold bg-[#1cb0f6] text-white px-2 py-0.5 rounded-full animate-pulse">▶ Saat ini</span>
                        @else
                            <span class="text-xs font-extrabold bg-gray-200 text-gray-400 px-2 py-0.5 rounded-full">🔒 Terkunci</span>
                        @endif
                    </div>

                    {{-- Zigzag topics --}}
                    @if ($lvlTopics->isEmpty() && $isCurrent)
                        <p class="text-gray-400 font-semibold text-sm">Belum ada materi untuk level ini. Hubungi dosen Anda.</p>
                    @else
                        @foreach ($lvlTopics as $t)
                            @php
                                $tp      = $allProgressMap->get($t->id);
                                $tq      = $allQuizMap->get($t->id);
                                $tDone   = $tp?->status === 'completed';
                                $tActive = $isCurrent && ($t->id === $currentTopicId);
                                // {{-- Topik dari level lampau selalu bisa diakses untuk review, meski belum pernah dikunjungi (tidak ada learning_progress) --}}
                                $tReview = $isPast && !$tDone;
                                $off     = $offsets[$n % count($offsets)];
                                $n++;
                            @endphp

                            @if ($tDone)
                                {{-- ✅ Sudah selesai: hijau, bisa diklik --}}
                                <div class="flex items-center gap-3 {{ $off }}">
                                    <a href="{{ route('mahasiswa.materi', $t) }}"
                                       class="flex items-center gap-3 lg:gap-4 hover:scale-105 transition">
                                        <div class="w-14 h-14 lg:w-16 lg:h-16 bg-[#58cc02] rounded-full border-b-8 border-[#46a302] flex items-center justify-center text-white text-xl lg:text-2xl font-bold shrink-0">✔</div>
                                        <span class="font-extrabold text-[#58cc02] text-sm lg:text-base">
                                            {{ $t->title }}
                                            @if ($tq)
                                                <span class="text-xs lg:text-sm opacity-70 ml-1">· {{ $tq->score }}</span>
                                            @endif
                                        </span>
                                    </a>
                                    <a href="{{ route('mahasiswa.review', $t) }}"
                                       title="Review soal"
                                       class="w-8 h-8 lg:w-9 lg:h-9 bg-white rounded-xl border-2 border-gray-200 flex items-center justify-center text-xs lg:text-sm text-gray-400 hover:border-[#1cb0f6] hover:text-[#1cb0f6] transition shrink-0">
                                        📋
                                    </a>
                                </div>

                            @elseif ($tActive)
                                {{-- ▶ Topik aktif saat ini: biru bouncing --}}
                                <a href="{{ route('mahasiswa.materi', $t) }}"
                                   class="flex items-center gap-3 lg:gap-4 {{ $off }} hover:scale-105 transition-transform w-full max-w-xs sm:max-w-none sm:w-auto">
                                    <div class="w-16 h-16 lg:w-20 lg:h-20 bg-[#1cb0f6] rounded-full border-b-8 border-[#1899d6] flex items-center justify-center text-white text-2xl lg:text-3xl font-bold animate-bounce shadow-xl shrink-0">▶</div>
                                    <div class="bg-white p-3 lg:p-4 border-2 border-b-4 border-[#1cb0f6] rounded-2xl shadow-lg flex-1 sm:w-56 sm:flex-none">
                                        <h4 class="font-extrabold text-gray-800 text-sm leading-snug mb-1">{{ $t->title }}</h4>
                                        @if ($t->description)
                                            <p class="text-xs text-gray-500 font-semibold mb-3 leading-snug hidden sm:block">{{ $t->description }}</p>
                                        @endif
                                        <span class="block w-full bg-[#111827] text-white py-2 rounded-xl border-b-4 border-gray-900 font-bold text-xs text-center">
                                            ▶ Mulai / Lanjutkan
                                        </span>
                                    </div>
                                </a>

                            @elseif ($tReview)
                                {{-- 📖 Level lampau, belum dikunjungi: tetap bisa dibuka untuk review --}}
                                <a href="{{ route('mahasiswa.materi', $t) }}"
                                   class="flex items-center gap-3 lg:gap-4 {{ $off }} hover:scale-105 transition-transform">
                                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-gray-100 rounded-full border-b-8 border-gray-200 flex items-center justify-center text-gray-400 text-xl lg:text-2xl shrink-0 hover:bg-gray-200 transition">📖</div>
                                    <span class="font-bold text-gray-500 text-sm lg:text-base hover:text-gray-700 transition">
                                        {{ $t->title }}
                                        <span class="block text-xs text-gray-400 font-semibold mt-0.5">Buka untuk belajar</span>
                                    </span>
                                </a>

                            @else
                                {{-- 🔒 Terkunci: level masa depan atau belum urutan di level saat ini --}}
                                <div class="flex items-center gap-3 lg:gap-4 {{ $off }} opacity-50">
                                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-[#e5e5e5] rounded-full border-b-8 border-[#cecece] flex items-center justify-center text-gray-400 text-xl lg:text-2xl">🔒</div>
                                    <span class="font-extrabold text-gray-400 text-sm lg:text-base">{{ $t->title }}</span>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    {{-- Post-test --}}
                    @php
                        $ptOff = $offsets[$n % count($offsets)];
                        $n++;
                    @endphp
                    <div class="text-center text-gray-300 font-extrabold tracking-[0.2em] w-full text-xs lg:text-sm">
                        ------ POST-TEST {{ strtoupper($lvl) }} ------
                    </div>
                    <div class="{{ $ptOff }}">
                        @if ($lvlPT?->passed)
                            @if ($isCurrent)
                                <a href="{{ route('mahasiswa.posttest') }}"
                                   class="inline-flex items-center gap-2 bg-[#58cc02] text-white font-extrabold px-6 lg:px-8 py-2.5 lg:py-3 rounded-2xl border-b-4 border-[#46a302] hover:bg-[#46a302] transition text-xs lg:text-sm uppercase tracking-wide">
                                    🏆 Post-test Lulus · Ulangi
                                </a>
                            @else
                                <span class="inline-flex items-center gap-2 bg-[#58cc02] text-white font-extrabold px-6 lg:px-8 py-2.5 lg:py-3 rounded-2xl border-b-4 border-[#46a302] text-xs lg:text-sm uppercase tracking-wide">
                                    🏆 Post-test Lulus
                                </span>
                            @endif
                        @elseif ($isFuture)
                            <span class="inline-flex items-center gap-2 bg-gray-200 text-gray-400 font-extrabold px-6 lg:px-8 py-2.5 lg:py-3 rounded-2xl border-b-4 border-gray-300 text-xs lg:text-sm uppercase tracking-wide cursor-not-allowed opacity-50">
                                🔒 Terkunci
                            </span>
                        @elseif (! $lvlPTUnlocked)
                            <span class="inline-flex items-center gap-2 bg-gray-200 text-gray-400 font-extrabold px-6 lg:px-8 py-2.5 lg:py-3 rounded-2xl border-b-4 border-gray-300 text-xs lg:text-sm uppercase tracking-wide cursor-not-allowed opacity-60">
                                🔒 Selesaikan semua materi dulu
                            </span>
                        @else
                            <a href="{{ route('mahasiswa.posttest') }}"
                               class="inline-flex items-center gap-2 bg-[#ff9600] text-white font-extrabold px-6 lg:px-8 py-2.5 lg:py-3 rounded-2xl border-b-4 border-[#cc7800] hover:bg-[#e08800] active:translate-y-1 active:border-b-2 transition text-xs lg:text-sm uppercase tracking-wide shadow-lg animate-bounce">
                                📝 Mulai Post-test →
                            </a>
                        @endif
                    </div>

                @endforeach

                <div class="text-center text-gray-300 font-extrabold tracking-[0.2em] w-full pb-4 text-xs lg:text-sm">
                    ------ 🎓 SELESAI ------
                </div>

            </div>
        </div>
    </main>

    {{-- ── Sidebar kanan ── --}}
    @php $currentTopic = $topics->firstWhere('id', $currentTopicId); @endphp
    <aside class="w-full lg:w-80 lg:shrink-0 p-4 lg:p-6 flex flex-col gap-4 lg:overflow-y-auto bg-gray-50 border-t-2 lg:border-t-0 lg:border-l-2 border-gray-200">

        {{-- Salam --}}
        <div class="bg-[#111827] p-5 rounded-2xl border-b-8 border-gray-900 text-white">
            <h3 class="text-base lg:text-lg font-extrabold mb-1">👋 Halo, {{ $user->name }}</h3>
            @if ($currentTopic)
                <p class="text-xs text-gray-400 font-semibold mb-4">
                    Lanjutkan: <strong class="text-white">{{ $currentTopic->title }}</strong>
                </p>
                <a href="{{ route('mahasiswa.materi', $currentTopic) }}"
                   class="block w-full bg-white text-gray-900 py-2 rounded-xl font-bold text-sm border-b-4 border-gray-300 hover:bg-gray-100 text-center">
                    ▶ Lanjutkan Materi
                </a>
            @else
                <p class="text-xs text-gray-400 font-semibold">Semua materi level ini telah selesai. 🎉</p>
            @endif
        </div>

        {{-- Progres level --}}
        <div class="bg-white p-5 rounded-2xl border-2 border-b-4 border-gray-200">
            <h3 class="font-extrabold text-gray-800 text-base lg:text-lg mb-4">Progres Level {{ ucfirst($level) }}</h3>
            <div class="space-y-3 text-sm font-semibold text-gray-600">
                <div class="flex justify-between items-center">
                    <span>Sub-bahasan</span>
                    <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full">
                        <div class="h-full bg-[#111827] rounded-full"
                             style="width: {{ $totalCount > 0 ? round(($completedCount/$totalCount)*100) : 0 }}%">
                        </div>
                    </div>
                    <span>{{ $completedCount }}/{{ $totalCount }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Rata-rata kuis</span>
                    <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full">
                        <div class="h-full bg-[#111827] rounded-full" style="width: {{ $avgQuiz }}%"></div>
                    </div>
                    <span>{{ $avgQuiz }}</span>
                </div>
            </div>
            @if ($totalCount > 0 && $completedCount < $totalCount)
                <p class="text-xs text-gray-500 mt-4 leading-relaxed font-semibold">
                    Selesaikan <strong class="text-gray-800">{{ $totalCount - $completedCount }} sub-bahasan lagi</strong>
                    untuk membuka post-test.
                </p>
            @endif
        </div>

        {{-- Riwayat nilai kuis --}}
        @if ($quizMap->isNotEmpty())
            <div class="bg-white p-5 rounded-2xl border-2 border-b-4 border-gray-200">
                <h3 class="font-extrabold text-gray-800 text-base lg:text-lg mb-4">Riwayat Nilai Kuis</h3>
                <div class="space-y-3 text-sm font-bold">
                    @foreach ($topics as $topic)
                        @php $qr = $quizMap->get($topic->id); @endphp
                        <div class="flex justify-between items-center text-gray-600">
                            <span class="truncate max-w-40">{{ $topic->title }}</span>
                            @if ($qr)
                                <span class="w-10 h-8 rounded-full border-2 {{ $qr->score >= 60 ? 'border-[#58cc02] text-[#58cc02]' : 'border-red-400 text-red-400' }} flex items-center justify-center text-xs shrink-0">
                                    {{ $qr->score }}
                                </span>
                            @else
                                <span class="w-14 h-8 rounded-full border-2 border-gray-200 text-gray-400 flex items-center justify-center text-xs shrink-0">
                                    — belum
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </aside>

@endsection
