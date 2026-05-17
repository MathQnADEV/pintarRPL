<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR · Hasil Pre-test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f9fa] font-sans text-gray-800 min-h-screen pb-16">

    {{-- ── Nav ── --}}
    <nav class="bg-white border-b-2 border-gray-200 flex items-center justify-between px-6 lg:px-10 h-16 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#4c3fb5] rounded-lg"></div>
            <span class="text-xl font-extrabold text-[#111827]">PINTAR</span>
        </div>
        <span class="text-sm font-semibold text-gray-500">{{ Auth::user()->name }}</span>
    </nav>

    @php
        $levelConfig = [
            'pemula'   => ['emoji' => '🌱', 'label' => 'Pemula',   'num' => 1, 'bg' => 'bg-[#f0fff0]',  'border' => 'border-[#58cc02]', 'text' => 'text-[#2d6601]',  'badge' => 'bg-[#58cc02]'],
            'menengah' => ['emoji' => '📘', 'label' => 'Menengah', 'num' => 2, 'bg' => 'bg-[#e8f6fd]',  'border' => 'border-[#1cb0f6]', 'text' => 'text-[#0a5a7a]',  'badge' => 'bg-[#1cb0f6]'],
            'lanjut'   => ['emoji' => '🚀', 'label' => 'Lanjut',   'num' => 3, 'bg' => 'bg-[#f5f0ff]',  'border' => 'border-[#4c3fb5]', 'text' => 'text-[#4c3fb5]',  'badge' => 'bg-[#4c3fb5]'],
        ];
        $cfg = $levelConfig[$level] ?? $levelConfig['pemula'];
        $levelOrder = \App\Models\PretestResult::LEVELS;
    @endphp

    <div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

        {{-- ── Level result banner ── --}}
        <div class="{{ $cfg['bg'] }} rounded-3xl border-2 border-b-8 {{ $cfg['border'] }} p-8 text-center">
            <div class="text-6xl mb-4">{{ $cfg['emoji'] }}</div>
            <p class="text-sm font-extrabold uppercase tracking-widest {{ $cfg['text'] }} mb-1">Level kamu ditetapkan</p>
            <h1 class="text-4xl font-extrabold {{ $cfg['text'] }} mb-2">
                Level {{ $cfg['num'] }} · {{ $cfg['label'] }}
            </h1>
            <p class="text-gray-500 font-semibold text-sm">
                Skor total: <strong class="{{ $cfg['text'] }}">{{ $score }}/100</strong>
                &nbsp;·&nbsp; {{ $correct }}/{{ $total }} soal benar
            </p>
        </div>

        {{-- ── Rule-based placement explanation ── --}}
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <h2 class="font-extrabold text-gray-800 mb-4 flex items-center gap-2">
                ⚖️ Cara penentuan level (rule-based)
            </h2>
            <p class="text-sm text-gray-500 font-medium mb-5">
                Sistem memeriksa performa kamu dari level tertinggi ke terendah.
                Level tertinggi yang kamu kuasai (≥ 70% soal benar) menjadi level penempatan.
            </p>

            <div class="space-y-3">
                @foreach (array_reverse($levelOrder) as $lvl)
                    @php
                        $c      = $levelConfig[$lvl];
                        $stat   = $level_stats[$lvl] ?? null;
                        $hasStat = $stat && $stat['total'] > 0;
                        $pct    = $hasStat ? (int) round(($stat['correct'] / $stat['total']) * 100) : null;
                        $passed = $hasStat && $pct >= 70;

                        // Is this the deciding level?
                        $isDeciding = $lvl === $level && $hasStat;
                    @endphp
                    <div class="flex items-center gap-4 p-4 rounded-xl border-2
                        {{ $isDeciding ? $c['border'] . ' ' . $c['bg'] : 'border-gray-100 bg-gray-50' }}">

                        {{-- Status icon --}}
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-sm font-extrabold
                            {{ $passed ? $c['badge'] . ' text-white' : ($hasStat ? 'bg-red-100 text-red-500' : 'bg-gray-200 text-gray-400') }}">
                            {{ $passed ? '✓' : ($hasStat ? '✗' : '—') }}
                        </div>

                        {{-- Level label + stats --}}
                        <div class="flex-1">
                            <p class="font-extrabold text-sm {{ $isDeciding ? $c['text'] : 'text-gray-600' }}">
                                {{ $c['emoji'] }} Soal Level {{ $c['label'] }}
                                @if ($isDeciding)
                                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $c['badge'] }} text-white">Penempatan</span>
                                @endif
                            </p>
                            @if ($hasStat)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $stat['correct'] }}/{{ $stat['total'] }} soal benar
                                    · {{ $pct }}%
                                    @if ($passed)
                                        — <span class="text-[#58cc02] font-bold">melewati threshold 70%</span>
                                    @else
                                        — <span class="text-red-400 font-bold">di bawah threshold 70%</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-0.5">Tidak ada soal level ini pada sesi ini</p>
                            @endif
                        </div>

                        {{-- Percentage bar --}}
                        @if ($hasStat)
                            <div class="w-20 shrink-0 text-right">
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $passed ? $c['badge'] : 'bg-red-300' }}"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs font-extrabold {{ $passed ? $c['text'] : 'text-red-400' }}">
                                    {{ $pct }}%
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Per-question review ── --}}
        <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-extrabold text-gray-800">📋 Rincian Jawaban</h2>
                <div class="flex gap-3 text-xs font-bold">
                    <span class="text-[#58cc02]">✅ {{ $correct }} benar</span>
                    <span class="text-red-400">❌ {{ $total - $correct }} salah</span>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($questions as $i => $q)
                    @php
                        $qCfg   = $levelConfig[$q['level']] ?? null;
                        $lvlBadge = $qCfg
                            ? '<span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full ' . $qCfg['badge'] . ' text-white ml-2">' . $qCfg['label'] . '</span>'
                            : '';
                    @endphp
                    <div class="p-5 {{ $q['is_correct'] ? '' : 'bg-red-50/40' }}">

                        {{-- Question header --}}
                        <div class="flex items-start gap-3 mb-3">
                            <span class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-extrabold text-sm
                                {{ $q['is_correct'] ? 'bg-green-100 text-[#3a9e01]' : 'bg-red-100 text-red-500' }}">
                                {{ $q['is_correct'] ? '✓' : '✗' }}
                            </span>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 text-sm leading-snug">
                                    {{ $i + 1 }}. {{ $q['text'] }}
                                    @if ($qCfg)
                                        <span class="inline-block text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $qCfg['badge'] }} text-white ml-1 align-middle">
                                            {{ $qCfg['emoji'] }} {{ $qCfg['label'] }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Answer rows --}}
                        <div class="ml-11 space-y-2">
                            {{-- Selected answer --}}
                            <div class="flex items-start gap-2 p-3 rounded-xl text-sm font-semibold
                                {{ $q['is_correct']
                                    ? 'bg-green-50 border border-green-200 text-[#2d6601]'
                                    : 'bg-red-50 border border-red-200 text-red-700' }}">
                                <span class="shrink-0 font-extrabold">
                                    {{ $q['is_correct'] ? '✅' : '❌' }}
                                </span>
                                <span>
                                    <span class="opacity-60 text-xs uppercase tracking-wide mr-1">Jawabanmu:</span>
                                    {{ $q['selected_text'] }}
                                </span>
                            </div>

                            {{-- Correct answer (only when wrong) --}}
                            @if (! $q['is_correct'])
                                <div class="flex items-start gap-2 p-3 rounded-xl text-sm font-semibold bg-green-50 border border-green-200 text-[#2d6601]">
                                    <span class="shrink-0">💡</span>
                                    <span>
                                        <span class="opacity-60 text-xs uppercase tracking-wide mr-1">Jawaban benar:</span>
                                        {{ $q['correct_text'] }}
                                    </span>
                                </div>
                            @endif

                            {{-- Explanation if available --}}
                            @if (! empty($q['explanation']))
                                <div class="flex items-start gap-2 p-3 rounded-xl text-xs font-medium bg-blue-50 border border-blue-100 text-blue-700">
                                    <span class="shrink-0">📖</span>
                                    <span>{{ $q['explanation'] }}</span>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── CTA ── --}}
        <div class="text-center pt-2">
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="inline-block px-10 py-4 bg-[#4c3fb5] text-white font-extrabold text-lg rounded-2xl
                      border-b-8 border-[#3d3291] hover:bg-[#3d3291] active:border-b-0 active:translate-y-1 transition-all">
                Mulai Belajar →
            </a>
            <p class="text-xs text-gray-400 mt-3">Kamu akan mulai dari materi <strong>Level {{ $cfg['label'] }}</strong></p>
        </div>

    </div>

</body>
</html>
