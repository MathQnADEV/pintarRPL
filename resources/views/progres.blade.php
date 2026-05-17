@extends('layouts.mahasiswa')
@section('title', 'Progres Belajar')

@section('content')
<div class="max-w-5xl mx-auto p-4 lg:p-10 space-y-6 lg:space-y-8">

        {{-- ── Judul + stat ringkasan ── --}}
        <div>
            <h1 class="text-3xl font-extrabold mb-2 text-gray-800">📊 Progres Belajar</h1>
            <p class="text-sm text-gray-400 font-semibold">{{ $user->name }} · Level saat ini:
                <span class="text-[#4c3fb5] font-extrabold">{{ ucfirst($level) }}</span>
            </p>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-2xl border-2 border-b-4 border-gray-200">
                <p class="text-xs font-extrabold text-gray-400 uppercase mb-1">Level Saat Ini</p>
                <h3 class="text-xl font-extrabold text-gray-800">{{ ucfirst($level) }}</h3>
                <p class="text-xs text-gray-400 mt-1">Pre-test: {{ $pretestResult->score ?? 0 }}/100</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border-2 border-b-4 border-gray-200">
                <p class="text-xs font-extrabold text-gray-400 uppercase mb-1">Selesai (Level Ini)</p>
                <h3 class="text-xl font-extrabold {{ $percentage >= 100 ? 'text-[#58cc02]' : 'text-[#4c3fb5]' }}">
                    {{ $percentage }}%
                    <span class="text-xs font-normal text-gray-300">{{ $completedCount }}/{{ $totalCount }}</span>
                </h3>
                <div class="w-full h-2 bg-gray-100 rounded-full mt-2">
                    <div class="h-full {{ $percentage >= 100 ? 'bg-[#58cc02]' : 'bg-[#4c3fb5]' }} rounded-full"
                         style="width: {{ $percentage }}%"></div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border-2 border-b-4 border-gray-200">
                <p class="text-xs font-extrabold text-gray-400 uppercase mb-1">Post-test (Level Ini)</p>
                @if ($postTestResult)
                    <h3 class="text-xl font-extrabold {{ $postTestResult->passed ? 'text-[#58cc02]' : 'text-red-400' }}">
                        {{ $postTestResult->score }}/100
                    </h3>
                    <p class="text-xs mt-1 {{ $postTestResult->passed ? 'text-[#58cc02]' : 'text-red-400' }} font-bold">
                        {{ $postTestResult->passed ? '🏆 Lulus' : '✗ Belum lulus' }}
                    </p>
                @else
                    <h3 class="text-xl font-extrabold text-gray-300">—</h3>
                    <p class="text-xs text-gray-400 mt-1">Belum dikerjakan</p>
                @endif
            </div>
        </div>

        {{-- ── Tiap level ── --}}
        @php
            $levelOrder      = \App\Models\PretestResult::LEVELS;
            $currentLevelIdx = array_search($level, $levelOrder, true);
            $levelConfig     = [
                'pemula'   => ['emoji' => '🌱', 'label' => 'Pemula',   'num' => 1,
                               'colors' => ['header_bg' => 'bg-[#f0fff0]', 'header_border' => 'border-[#58cc02]',
                                            'badge_bg' => 'bg-[#58cc02]', 'bar' => 'bg-[#58cc02]',
                                            'text' => 'text-[#2d6601]']],
                'menengah' => ['emoji' => '📘', 'label' => 'Menengah', 'num' => 2,
                               'colors' => ['header_bg' => 'bg-[#e8f6fd]', 'header_border' => 'border-[#1cb0f6]',
                                            'badge_bg' => 'bg-[#1cb0f6]', 'bar' => 'bg-[#1cb0f6]',
                                            'text' => 'text-[#0a5a7a]']],
                'lanjut'   => ['emoji' => '🚀', 'label' => 'Lanjut',   'num' => 3,
                               'colors' => ['header_bg' => 'bg-[#f5f0ff]', 'header_border' => 'border-[#4c3fb5]',
                                            'badge_bg' => 'bg-[#4c3fb5]', 'bar' => 'bg-[#4c3fb5]',
                                            'text' => 'text-[#4c3fb5]']],
            ];
        @endphp

        @foreach ($levelOrder as $lvlIdx => $lvl)
            @php
                $cfg        = $levelConfig[$lvl];
                $lvlTopics  = $allLevelTopics[$lvl] ?? collect();
                $isPast     = $lvlIdx < $currentLevelIdx;
                $isCurrent  = $lvlIdx === $currentLevelIdx;
                $isFuture   = $lvlIdx > $currentLevelIdx;
                $lvlPT      = $allPostTestResults[$lvl] ?? null;

                $lvlIds       = $lvlTopics->pluck('id')->all();
                $lvlProgress  = $allProgressMap->whereIn('topic_id', $lvlIds);
                $lvlQuiz      = $allQuizMap->whereIn('topic_id', $lvlIds);
                $lvlDone      = $lvlProgress->where('status', 'completed')->count();
                $lvlTotal     = $lvlTopics->count();
                $lvlPct       = $lvlTotal > 0 ? (int) round(($lvlDone / $lvlTotal) * 100) : 0;
                $lvlAvgQuiz   = $lvlQuiz->isNotEmpty() ? (int) round($lvlQuiz->avg('score')) : 0;
            @endphp

            <div class="bg-white rounded-3xl border-2 border-b-4 overflow-hidden
                {{ $isFuture ? 'border-gray-200 opacity-60' : $cfg['colors']['header_border'] }}">

                {{-- Level header --}}
                <div class="px-8 py-5 flex flex-wrap items-center justify-between gap-4
                    {{ $isFuture ? 'bg-gray-50' : $cfg['colors']['header_bg'] }}">

                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $cfg['emoji'] }}</span>
                        <div>
                            <h2 class="font-extrabold text-lg {{ $isFuture ? 'text-gray-400' : $cfg['colors']['text'] }}">
                                Level {{ $cfg['num'] }} · {{ $cfg['label'] }}
                            </h2>
                            <p class="text-xs font-bold
                                {{ $isPast ? 'text-[#46a302]' : ($isCurrent ? 'text-gray-500' : 'text-gray-400') }}">
                                @if ($isPast) ✓ Selesai
                                @elseif ($isCurrent) ▶ Level saat ini
                                @else 🔒 Belum terbuka
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Mini stats --}}
                    <div class="flex items-center gap-6 text-sm font-bold">
                        {{-- Progress bar --}}
                        <div class="flex items-center gap-2">
                            <div class="w-28 h-2 bg-gray-200 rounded-full">
                                <div class="h-full rounded-full {{ $isFuture ? 'bg-gray-300' : $cfg['colors']['bar'] }}"
                                     style="width: {{ $lvlPct }}%"></div>
                            </div>
                            <span class="{{ $isFuture ? 'text-gray-400' : $cfg['colors']['text'] }}">
                                {{ $lvlDone }}/{{ $lvlTotal }}
                            </span>
                        </div>

                        {{-- Avg quiz --}}
                        @if ($lvlAvgQuiz > 0)
                            <span class="text-gray-500">⌀ {{ $lvlAvgQuiz }}</span>
                        @endif

                        {{-- Post-test badge --}}
                        @if ($lvlPT?->passed)
                            <span class="bg-[#58cc02] text-white text-xs font-extrabold px-3 py-1 rounded-full">
                                🏆 Post-test Lulus
                            </span>
                        @elseif ($lvlPT && ! $lvlPT->passed)
                            <span class="bg-red-100 text-red-500 text-xs font-extrabold px-3 py-1 rounded-full">
                                ✗ Post-test {{ $lvlPT->score }}/100
                            </span>
                        @elseif ($isFuture)
                            <span class="bg-gray-100 text-gray-400 text-xs font-extrabold px-3 py-1 rounded-full">
                                🔒 Post-test
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Tabel topik --}}
                @if ($lvlTopics->isEmpty())
                    <div class="p-8 text-center text-gray-400 font-semibold text-sm">
                        Belum ada materi untuk level ini.
                    </div>
                @else
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="p-4 pl-8 text-xs font-extrabold text-gray-400 uppercase tracking-wide">Sub-bahasan</th>
                                <th class="p-4 text-xs font-extrabold text-gray-400 uppercase tracking-wide">Status</th>
                                <th class="p-4 text-xs font-extrabold text-gray-400 uppercase tracking-wide">Percobaan</th>
                                <th class="p-4 text-right text-xs font-extrabold text-gray-400 uppercase tracking-wide">Skor Terbaik</th>
                                <th class="p-4 pr-8 text-right text-xs font-extrabold text-gray-400 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lvlTopics as $t)
                                @php
                                    $tp     = $allProgressMap->get($t->id);
                                    $qr     = $allQuizMap->get($t->id);
                                    $status = $tp?->status ?? 'not_started';
                                    $isDone = $status === 'completed';
                                    $isOngoing = $status === 'in_progress';
                                    $canRead = ($isDone || $isOngoing) && ! $isFuture;
                                    $canReview = $canRead && ($tp?->quiz_attempts > 0 || $isDone);
                                @endphp
                                <tr class="border-b border-gray-100 last:border-0 font-semibold
                                    {{ ($status === 'not_started' || $isFuture) ? 'opacity-40' : '' }}">

                                    <td class="p-4 pl-8 text-gray-700">{{ $t->title }}</td>

                                    <td class="p-4">
                                        @if ($isDone)
                                            <span class="inline-flex items-center gap-1 text-xs font-extrabold text-[#3a9e01] bg-green-50 px-3 py-1 rounded-full">
                                                ✓ Selesai
                                            </span>
                                        @elseif ($isOngoing)
                                            <span class="inline-flex items-center gap-1 text-xs font-extrabold text-[#1cb0f6] bg-blue-50 px-3 py-1 rounded-full">
                                                ▶ Berlangsung
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-extrabold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                                                — Belum dimulai
                                            </span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-gray-500 text-sm">
                                        {{ $tp?->quiz_attempts ?? 0 }}×
                                    </td>

                                    <td class="p-4 text-right">
                                        @if ($tp?->best_score !== null)
                                            <span class="font-extrabold text-lg {{ $tp->best_score >= 60 ? 'text-[#58cc02]' : 'text-red-400' }}">
                                                {{ $tp->best_score }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                    <td class="p-4 pr-8">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($canRead)
                                                <a href="{{ route('mahasiswa.materi', $t) }}"
                                                   class="inline-flex items-center gap-1 text-xs font-extrabold
                                                          text-gray-600 bg-gray-100 px-3 py-1.5 rounded-lg
                                                          border border-gray-200 hover:bg-gray-200 transition">
                                                    📖 Baca
                                                </a>
                                            @endif
                                            @if ($canReview)
                                                <a href="{{ route('mahasiswa.review', $t) }}"
                                                   class="inline-flex items-center gap-1 text-xs font-extrabold
                                                          text-[#4c3fb5] bg-[#f0effe] px-3 py-1.5 rounded-lg
                                                          border border-[#c4b9f8] hover:bg-[#e0d8fd] transition">
                                                    📋 Review
                                                </a>
                                            @endif
                                            @if (! $canRead && ! $canReview)
                                                <span class="text-gray-200 text-xs">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Post-test row di bawah tabel --}}
                    <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">
                            Post-test Level {{ $cfg['label'] }}
                        </span>
                        @if ($lvlPT?->passed)
                            @if ($isCurrent)
                                <a href="{{ route('mahasiswa.posttest') }}"
                                   class="inline-flex items-center gap-1.5 bg-[#58cc02] text-white text-xs font-extrabold px-4 py-2 rounded-xl border-b-2 border-[#46a302] hover:bg-[#46a302] transition">
                                    🏆 Lulus · Ulangi
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-[#d7f5b0] text-[#2d6601] text-xs font-extrabold px-4 py-2 rounded-xl border border-[#58cc02]">
                                    🏆 Lulus ({{ $lvlPT->score }}/100)
                                </span>
                            @endif
                        @elseif ($lvlPT && ! $lvlPT->passed)
                            <a href="{{ route('mahasiswa.posttest') }}"
                               class="inline-flex items-center gap-1.5 bg-[#ff9600] text-white text-xs font-extrabold px-4 py-2 rounded-xl border-b-2 border-[#cc7800] hover:bg-[#e08800] transition">
                                📝 Coba Lagi ({{ $lvlPT->score }}/100)
                            </a>
                        @elseif ($isCurrent && $lvlDone >= $lvlTotal && $lvlTotal > 0)
                            <a href="{{ route('mahasiswa.posttest') }}"
                               class="inline-flex items-center gap-1.5 bg-[#ff9600] text-white text-xs font-extrabold px-4 py-2 rounded-xl border-b-2 border-[#cc7800] hover:bg-[#e08800] transition animate-bounce">
                                📝 Mulai Post-test →
                            </a>
                        @elseif ($isCurrent)
                            <span class="text-xs font-bold text-gray-400">
                                🔒 Selesaikan semua topik ({{ $lvlDone }}/{{ $lvlTotal }})
                            </span>
                        @else
                            <span class="text-xs font-bold text-gray-400">🔒 Terkunci</span>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        <div class="text-center">
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="inline-block px-8 py-3 bg-[#4c3fb5] text-white font-bold rounded-xl border-b-4 border-[#3d3291] hover:bg-[#3d3291] transition">
                ← Kembali ke Dashboard
            </a>
        </div>

</div>
@endsection
