<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Hasil Post-test {{ ucfirst($review['level']) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">
    <style>
        .question-body p, .explanation-body p { margin-bottom: 0.45rem; }
        .question-body ul, .explanation-body ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body ol, .explanation-body ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body strong, .explanation-body strong { font-weight: 700; }
        .question-body pre, .explanation-body pre {
            position: relative; margin: 0.75rem 0; border-radius: 0.75rem; overflow: hidden;
        }
        .question-body pre::before, .explanation-body pre::before {
            content: attr(data-lang); display: block;
            padding: 0.35rem 1rem; background: #1a1d23; color: #6b7280;
            font-size: 0.65rem; font-family: monospace; font-weight: 600;
            letter-spacing: 0.06em; text-transform: uppercase; border-bottom: 1px solid #2d3139;
        }
        .question-body pre code.hljs, .explanation-body pre code.hljs {
            border-radius: 0; padding: 1rem 1.25rem; font-size: 0.82rem; line-height: 1.65;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
        }
        .copy-code-btn {
            position: absolute; top: 0.25rem; right: 0.5rem;
            background: transparent; border: 1px solid #374151; color: #9ca3af;
            padding: 0.15rem 0.5rem; border-radius: 0.3rem; font-size: 0.65rem; cursor: pointer;
        }
        .copy-code-btn:hover { background: #374151; color: #f3f4f6; }
        .copy-code-btn.copied { border-color: #4ade80; color: #4ade80; }

        /* ── Tile untuk review code_arrange ─────────────────────── */
        .review-tile {
            display: inline-flex; align-items: center;
            padding: 0.3rem 0.65rem; border-radius: 0.5rem;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.75rem; line-height: 1.4; white-space: pre;
            border: 1.5px solid; margin: 0.2rem 0.15rem;
        }
        .tile-correct   { background: #e9f9d4; border-color: #58cc02; color: #2e5800; }
        .tile-wrong     { background: #fde8e8; border-color: #ff4b4b; color: #8b1a1a; }
        .tile-neutral   { background: #282c34; border-color: #3d4455; color: #abb2bf; }
        .tile-numbadge  {
            display: inline-flex; align-items: center; justify-content: center;
            width: 1.2rem; height: 1.2rem; border-radius: 0.25rem;
            font-size: 0.6rem; font-weight: 800; margin-right: 0.3rem;
            background: rgba(0,0,0,0.2); color: inherit;
        }
    </style>
</head>
<body class="bg-[#f7f7f7] font-sans text-gray-800 min-h-screen">

    <nav class="bg-white border-b-2 border-gray-100 px-6 py-4">
        <div class="max-w-3xl mx-auto flex items-center justify-between">
            <span class="font-extrabold text-[#4c3fb5] text-xl tracking-tight">PINTAR</span>
            <span class="text-sm text-gray-400 font-semibold">Post-test {{ ucfirst($review['level']) }}</span>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-8 space-y-6">

        @php $passed = $review['passed']; $score = $review['score']; @endphp

        {{-- ─── Score card ──────────────────────────────────────────────────── --}}
        <div class="rounded-3xl border-2 border-b-4 overflow-hidden
                    {{ $passed ? 'border-[#58cc02]' : 'border-[#ff4b4b]' }}">
            <div class="px-8 py-10 text-center {{ $passed ? 'bg-[#58cc02]' : 'bg-[#ff4b4b]' }}">
                <div class="text-5xl mb-3">{{ $passed ? '🎓' : '😓' }}</div>
                <h1 class="text-white font-extrabold text-2xl tracking-tight mb-1">
                    {{ $passed ? 'Post-test Lulus!' : 'Belum Lulus — Coba Lagi!' }}
                </h1>
                <p class="text-white/80 text-sm">
                    {{ $passed
                        ? 'Selamat! Kamu memenuhi syarat untuk naik ke level berikutnya.'
                        : 'Nilai minimum kelulusan adalah 60. Pelajari kembali materinya, kamu pasti bisa!' }}
                </p>
            </div>
            <div class="bg-white px-8 py-6 flex items-center justify-around gap-4">
                <div class="text-center">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-1">Nilai</p>
                    <p class="font-extrabold text-4xl {{ $passed ? 'text-[#58cc02]' : 'text-[#ff4b4b]' }}">
                        {{ $score }}
                    </p>
                </div>
                <div class="w-px h-12 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-1">Benar</p>
                    <p class="font-extrabold text-4xl text-[#58cc02]">{{ $review['correct'] }}</p>
                </div>
                <div class="w-px h-12 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-1">Salah</p>
                    <p class="font-extrabold text-4xl text-[#ff4b4b]">{{ $review['total'] - $review['correct'] }}</p>
                </div>
                <div class="w-px h-12 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-1">Soal</p>
                    <p class="font-extrabold text-4xl text-gray-600">{{ $review['total'] }}</p>
                </div>
            </div>
        </div>

        {{-- ─── CTA ─────────────────────────────────────────────────────────── --}}
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('mahasiswa.posttest') }}"
               class="flex-1 min-w-[130px] text-center bg-white text-[#ff9600] font-extrabold py-4 px-4 rounded-2xl
                      border-2 border-b-4 border-[#ff9600] uppercase tracking-wide text-xs
                      hover:bg-[#fff7ed] active:translate-y-1 active:border-b-2 transition-all">
                🔄 Ulangi Post-test
            </a>
            <a href="{{ route('mahasiswa.progres') }}"
               class="flex-1 min-w-[130px] text-center bg-white text-gray-600 font-extrabold py-4 px-4 rounded-2xl
                      border-2 border-b-4 border-gray-300 uppercase tracking-wide text-xs
                      hover:bg-gray-50 active:translate-y-1 active:border-b-2 transition-all">
                📊 Progres
            </a>
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="flex-1 min-w-[130px] text-center bg-[#4c3fb5] text-white font-extrabold py-4 px-4 rounded-2xl
                      border-2 border-b-4 border-[#3d3291] uppercase tracking-wide text-xs
                      hover:bg-[#3d3291] active:translate-y-1 active:border-b-2 transition-all">
                🏠 Dashboard
            </a>
        </div>

        {{-- ─── Review soal ─────────────────────────────────────────────────── --}}
        <h2 class="font-extrabold text-lg text-gray-700 pt-2">📋 Review Jawaban</h2>

        @foreach ($review['questions'] as $qi => $q)
            <div class="bg-white rounded-3xl border-2 border-b-4 overflow-hidden
                        {{ $q['is_correct'] ? 'border-[#58cc02]' : 'border-[#ff4b4b]' }}">

                {{-- Header soal --}}
                <div class="px-6 pt-5 pb-3 flex items-start gap-3">
                    <span class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center
                                 font-extrabold text-sm
                                 {{ $q['is_correct'] ? 'bg-[#d7f0bc] text-[#4a8c00]' : 'bg-[#fde8e8] text-[#cc2929]' }}">
                        {{ $q['is_correct'] ? '✓' : '✗' }}
                    </span>
                    <div class="question-body flex-1 text-gray-800 text-sm leading-relaxed font-semibold">
                        <span class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mr-2">
                            Soal {{ $qi + 1 }}
                            @if ($q['type'] === 'code_arrange')
                                · <span class="text-[#b45300]">🧩 Susun Kode</span>
                            @endif
                        </span>
                        {!! $q['text'] !!}
                    </div>
                </div>

                {{-- ── Review berdasarkan tipe ─────────────────────────────── --}}
                @if ($q['type'] === 'code_arrange')
                    @php
                        $tiles        = $q['tiles'];         // [id => text]
                        $correctOrder = $q['correct_order']; // [id, id, ...]
                        $userOrder    = $q['user_order'];
                    @endphp

                    <div class="px-6 pb-5 space-y-4">
                        {{-- Jawaban mahasiswa --}}
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest
                                       {{ $q['is_correct'] ? 'text-[#3a7200]' : 'text-[#cc2929]' }} mb-2">
                                Susunanmu
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($userOrder as $idx => $uid)
                                    @php
                                        $isRightSpot = isset($correctOrder[$idx]) && $correctOrder[$idx] == $uid;
                                    @endphp
                                    <span class="review-tile {{ $isRightSpot ? 'tile-correct' : 'tile-wrong' }}">
                                        <span class="tile-numbadge">{{ $idx + 1 }}</span>
                                        {{ $tiles[$uid] ?? '?' }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-400 italic">Tidak dijawab</span>
                                @endforelse
                            </div>
                        </div>

                        @if (! $q['is_correct'])
                            {{-- Urutan benar --}}
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-widest text-[#3a7200] mb-2">
                                    Urutan Benar
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($correctOrder as $idx => $cid)
                                        <span class="review-tile tile-correct">
                                            <span class="tile-numbadge">{{ $idx + 1 }}</span>
                                            {{ $tiles[$cid] ?? '?' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                @else
                    {{-- Multiple choice --}}
                    <div class="px-6 pb-4 space-y-2">
                        @foreach ($q['options'] as $opt)
                            @php
                                $isOptCorrect  = $opt['is_correct'];
                                $isOptSelected = $opt['id'] === ($q['selected_id'] ?? null);
                                $isWrongPick   = $isOptSelected && ! $isOptCorrect;

                                if ($isOptCorrect) {
                                    $optBg = 'bg-[#e9f9d4] border-[#58cc02]'; $optText = 'text-[#3a7200]';
                                    $optIcon = '✓'; $iconColor = 'bg-[#58cc02] text-white';
                                } elseif ($isWrongPick) {
                                    $optBg = 'bg-[#fde8e8] border-[#ff4b4b]'; $optText = 'text-[#cc2929]';
                                    $optIcon = '✗'; $iconColor = 'bg-[#ff4b4b] text-white';
                                } else {
                                    $optBg = 'bg-gray-50 border-gray-200'; $optText = 'text-gray-500';
                                    $optIcon = chr(65 + $loop->index);
                                    $iconColor = 'bg-gray-100 text-gray-400 border border-gray-200';
                                }
                            @endphp
                            <div class="flex items-center gap-3 p-3 rounded-xl border-2 {{ $optBg }}">
                                <span class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                                             font-extrabold text-xs {{ $iconColor }}">
                                    {{ $optIcon }}
                                </span>
                                <span class="text-sm font-semibold {{ $optText }} flex-1">{{ $opt['text'] }}</span>
                                @if ($isOptSelected && ! $isOptCorrect)
                                    <span class="text-xs font-bold text-[#ff4b4b]">Jawabanmu</span>
                                @elseif ($isOptSelected && $isOptCorrect)
                                    <span class="text-xs font-bold text-[#58cc02]">Jawabanmu ✓</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Penjelasan --}}
                @if (! empty($q['explanation']))
                    <div class="mx-6 mb-5 p-4 bg-[#fffbeb] border-l-4 border-[#f59e0b] rounded-xl">
                        <p class="text-xs font-extrabold uppercase tracking-widest text-[#b45309] mb-2">
                            💡 Penjelasan
                        </p>
                        <div class="explanation-body text-sm text-[#78350f] leading-relaxed">
                            {!! $q['explanation'] !!}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- ─── CTA bawah ───────────────────────────────────────────────────── --}}
        <div class="flex gap-3 flex-wrap pb-8">
            <a href="{{ route('mahasiswa.posttest') }}"
               class="flex-1 min-w-[130px] text-center bg-white text-[#ff9600] font-extrabold py-4 px-4 rounded-2xl
                      border-2 border-b-4 border-[#ff9600] uppercase tracking-wide text-xs
                      hover:bg-[#fff7ed] active:translate-y-1 active:border-b-2 transition-all">
                🔄 Ulangi Post-test
            </a>
            <a href="{{ route('mahasiswa.progres') }}"
               class="flex-1 min-w-[130px] text-center bg-white text-gray-600 font-extrabold py-4 px-4 rounded-2xl
                      border-2 border-b-4 border-gray-300 uppercase tracking-wide text-xs
                      hover:bg-gray-50 active:translate-y-1 active:border-b-2 transition-all">
                📊 Progres
            </a>
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="flex-1 min-w-[130px] text-center bg-[#4c3fb5] text-white font-extrabold py-4 px-4 rounded-2xl
                      border-2 border-b-4 border-[#3d3291] uppercase tracking-wide text-xs
                      hover:bg-[#3d3291] active:translate-y-1 active:border-b-2 transition-all">
                🏠 Dashboard
            </a>
        </div>

    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.question-body pre, .explanation-body pre').forEach(function (pre) {
                if (!pre.querySelector('code')) {
                    const code = document.createElement('code');
                    code.innerHTML = pre.innerHTML; pre.innerHTML = ''; pre.appendChild(code);
                }
            });
            document.querySelectorAll('.question-body pre code, .explanation-body pre code').forEach(function (block) {
                hljs.highlightElement(block);
                block.closest('pre').setAttribute('data-lang', block.result?.language ?? 'kode');
            });
            document.querySelectorAll('.question-body pre, .explanation-body pre').forEach(function (pre) {
                const btn = document.createElement('button');
                btn.textContent = 'Salin'; btn.className = 'copy-code-btn'; btn.type = 'button';
                btn.addEventListener('click', function () {
                    navigator.clipboard.writeText(pre.querySelector('code')?.innerText ?? '').then(function () {
                        btn.textContent = '✓'; btn.classList.add('copied');
                        setTimeout(function () { btn.textContent = 'Salin'; btn.classList.remove('copied'); }, 2000);
                    });
                });
                pre.appendChild(btn);
            });
        });
    </script>

</body>
</html>
