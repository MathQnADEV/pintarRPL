<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Review Soal: {{ $topic->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">

    <style>
        /* ── Prose untuk teks soal & penjelasan ───────────────────────── */
        .question-body p,
        .explanation-body p   { margin-bottom: 0.45rem; }
        .question-body ul,
        .explanation-body ul  { list-style: disc; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body ol,
        .explanation-body ol  { list-style: decimal; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body strong,
        .explanation-body strong { font-weight: 700; }
        .question-body em,
        .explanation-body em  { font-style: italic; }

        /* ── Blok kode ─────────────────────────────────────────────────── */
        .question-body pre,
        .explanation-body pre {
            position: relative;
            margin: 0.75rem 0;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .question-body pre::before,
        .explanation-body pre::before {
            content: attr(data-lang);
            display: block;
            padding: 0.35rem 1rem;
            background: #1a1d23;
            color: #6b7280;
            font-size: 0.65rem;
            font-family: monospace;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-bottom: 1px solid #2d3139;
        }
        .question-body pre code.hljs,
        .explanation-body pre code.hljs {
            border-radius: 0;
            padding: 1rem 1.25rem;
            font-size: 0.82rem;
            line-height: 1.65;
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
        }
        .copy-code-btn {
            position: absolute;
            top: 0.25rem;
            right: 0.5rem;
            background: transparent;
            border: 1px solid #374151;
            color: #9ca3af;
            padding: 0.15rem 0.5rem;
            border-radius: 0.3rem;
            font-size: 0.65rem;
            cursor: pointer;
            transition: all 0.15s;
        }
        .copy-code-btn:hover { background: #374151; color: #f3f4f6; }
        .copy-code-btn.copied { border-color: #4ade80; color: #4ade80; }

        /* ── Pilihan jawaban tersembunyi / terungkap ────────────────────── */
        .option-item { transition: all 0.25s ease; }
        .option-item.revealed-correct {
            background-color: #e9f9d4;
            border-color: #58cc02;
        }
        .option-item.revealed-wrong {
            background-color: #f9fafb;
            border-color: #e5e7eb;
            opacity: 0.55;
        }
        .option-item .reveal-icon { display: none; }
        .option-item.revealed-correct .reveal-icon,
        .option-item.revealed-wrong  .reveal-icon { display: flex; }

        /* ── Penjelasan tersembunyi sampai revealed ────────────────────── */
        .explanation-box { display: none; }
        .explanation-box.visible { display: block; }
    </style>
</head>
<body class="bg-[#f7f7f7] font-sans text-gray-800 min-h-screen">

    {{-- ─── Navbar ──────────────────────────────────────────────────────────── --}}
    <nav class="bg-white border-b-2 border-gray-200 px-6 py-4 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('mahasiswa.progres') }}"
                   class="text-gray-400 hover:text-gray-600 transition font-bold text-lg leading-none"
                   title="Kembali ke Progres">←</a>
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">Review Soal</p>
                    <p class="font-extrabold text-gray-700 text-sm leading-tight">{{ $topic->title }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Skor terbaik --}}
                @if ($progress->best_score !== null)
                    <div class="text-center hidden sm:block">
                        <p class="text-xs font-bold text-gray-400">Skor Terbaik</p>
                        <p class="font-extrabold text-lg {{ $progress->best_score >= 60 ? 'text-[#58cc02]' : 'text-red-400' }}">
                            {{ $progress->best_score }}
                        </p>
                    </div>
                @endif

                <a href="{{ route('mahasiswa.kuis', $topic) }}"
                   class="bg-[#4c3fb5] text-white font-extrabold px-4 py-2 rounded-xl
                          border-b-2 border-[#3d3291] hover:bg-[#3d3291] transition text-xs uppercase tracking-wide">
                    Kuis Lagi
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-8">

        {{-- ─── Info header ─────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-3xl border-2 border-b-4 border-gray-200 px-6 py-5 mb-6
                    flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-xl text-gray-800">📚 Bank Soal Kuis</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $questions->count() }} soal ·
                    @if ($progress->status === 'completed')
                        <span class="text-[#58cc02] font-bold">✓ Topik Selesai</span>
                    @else
                        <span class="text-[#1cb0f6] font-bold">▶ Sedang Berlangsung</span>
                    @endif
                </p>
            </div>
            <div class="flex gap-3 text-center text-sm">
                <div class="bg-gray-50 px-4 py-2 rounded-xl border border-gray-200">
                    <p class="text-xs text-gray-400 font-bold uppercase">Percobaan</p>
                    <p class="font-extrabold text-gray-700 text-lg">{{ $progress->quiz_attempts }}×</p>
                </div>
                @if ($progress->best_score !== null)
                    <div class="bg-gray-50 px-4 py-2 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-400 font-bold uppercase">Skor Terbaik</p>
                        <p class="font-extrabold text-lg {{ $progress->best_score >= 60 ? 'text-[#58cc02]' : 'text-red-400' }}">
                            {{ $progress->best_score }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── Kontrol global ──────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between mb-5">
            <p class="text-xs font-extrabold text-gray-400 uppercase tracking-widest">
                Klik soal untuk melihat jawaban
            </p>
            <div class="flex gap-2">
                <button id="btn-reveal-all"
                        class="text-xs font-bold px-4 py-2 rounded-xl border-2 border-b-4 border-[#58cc02]
                               text-[#3a7200] bg-[#e9f9d4] hover:bg-[#d4f5ab] transition">
                    Tampilkan Semua
                </button>
                <button id="btn-hide-all"
                        class="text-xs font-bold px-4 py-2 rounded-xl border-2 border-b-4 border-gray-200
                               text-gray-500 bg-white hover:bg-gray-50 transition">
                    Sembunyikan Semua
                </button>
            </div>
        </div>

        {{-- ─── Daftar soal ─────────────────────────────────────────────────── --}}
        <div class="space-y-4" id="questions-list">
            @foreach ($questions as $qi => $question)
                <div class="question-card bg-white rounded-3xl border-2 border-b-4 border-gray-200 overflow-hidden
                            hover:border-gray-300 transition-colors"
                     data-revealed="false">

                    {{-- Header soal (klik untuk toggle) --}}
                    <button type="button"
                            class="toggle-btn w-full text-left px-6 pt-5 pb-4 flex items-start gap-3 focus:outline-none">
                        <span class="question-num w-9 h-9 rounded-xl shrink-0 flex items-center justify-center
                                     font-extrabold text-sm bg-gray-100 text-gray-500 transition-all">
                            {{ $qi + 1 }}
                        </span>
                        <div class="question-body flex-1 text-gray-800 text-sm leading-relaxed font-semibold text-left">
                            {!! $question->question_text !!}
                        </div>
                        <span class="chevron text-gray-300 text-lg ml-2 shrink-0 transition-transform pt-0.5">▼</span>
                    </button>

                    {{-- Pilihan jawaban --}}
                    <div class="options-area px-6 pb-4 space-y-2">
                        @foreach ($question->options as $opt)
                            <div class="option-item flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 bg-gray-50"
                                 data-correct="{{ $opt->is_correct ? 'true' : 'false' }}">
                                {{-- Label huruf --}}
                                <span class="option-letter w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                                             font-extrabold text-xs bg-white border border-gray-200 text-gray-400 transition-all">
                                    {{ chr(65 + $loop->index) }}
                                </span>
                                {{-- Teks --}}
                                <span class="text-sm font-semibold text-gray-600 flex-1">
                                    {{ $opt->option_text }}
                                </span>
                                {{-- Ikon ✓ (muncul saat revealed) --}}
                                <span class="reveal-icon w-6 h-6 rounded-full bg-[#58cc02] text-white items-center justify-center text-xs font-extrabold">
                                    ✓
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Penjelasan (tersembunyi sampai revealed) --}}
                    @if (! empty($question->explanation))
                        <div class="explanation-box mx-6 mb-5 p-4 bg-[#fffbeb] border-l-4 border-[#f59e0b] rounded-xl">
                            <p class="text-xs font-extrabold uppercase tracking-widest text-[#b45309] mb-2">
                                💡 Penjelasan
                            </p>
                            <div class="explanation-body text-sm text-[#78350f] leading-relaxed">
                                {!! $question->explanation !!}
                            </div>
                        </div>
                    @endif

                    {{-- Footer: tombol Lihat / Sembunyikan Jawaban --}}
                    <div class="border-t border-gray-100 px-6 py-3 flex items-center justify-between">
                        <span class="answer-status text-xs font-bold text-gray-400">— Jawaban tersembunyi</span>
                        <button type="button"
                                class="toggle-btn text-xs font-extrabold px-4 py-1.5 rounded-lg border-2 border-b-2
                                       border-[#4c3fb5] text-[#4c3fb5] hover:bg-[#f0effe] transition">
                            Lihat Jawaban
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- ─── CTA bawah ───────────────────────────────────────────────────── --}}
        <div class="flex gap-4 mt-8 pb-8">
            <a href="{{ route('mahasiswa.progres') }}"
               class="flex-1 text-center bg-white text-gray-600 font-extrabold py-4 px-6 rounded-2xl
                      border-2 border-b-4 border-gray-300 uppercase tracking-widest text-sm
                      hover:bg-gray-50 active:translate-y-1 active:border-b-2 transition-all">
                ← Progres
            </a>
            <a href="{{ route('mahasiswa.materi', $topic) }}"
               class="flex-1 text-center bg-white text-[#1cb0f6] font-extrabold py-4 px-6 rounded-2xl
                      border-2 border-b-4 border-[#1cb0f6] uppercase tracking-widest text-sm
                      hover:bg-[#e6f7ff] active:translate-y-1 active:border-b-2 transition-all">
                📖 Baca Materi
            </a>
            <a href="{{ route('mahasiswa.kuis', $topic) }}"
               class="flex-1 text-center bg-[#4c3fb5] text-white font-extrabold py-4 px-6 rounded-2xl
                      border-2 border-b-4 border-[#3d3291] uppercase tracking-widest text-sm
                      hover:bg-[#3d3291] active:translate-y-1 active:border-b-2 transition-all">
                🎯 Kuis Lagi
            </a>
        </div>

    </main>

    {{-- ─── Highlight.js ─────────────────────────────────────────────────────── --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Syntax highlighting ─────────────────────────────────────────────
        document.querySelectorAll('.question-body pre, .explanation-body pre').forEach(function (pre) {
            if (!pre.querySelector('code')) {
                const code = document.createElement('code');
                code.innerHTML = pre.innerHTML;
                pre.innerHTML  = '';
                pre.appendChild(code);
            }
        });
        document.querySelectorAll('.question-body pre code, .explanation-body pre code').forEach(function (block) {
            hljs.highlightElement(block);
            const lang = block.result?.language ?? '';
            block.closest('pre').setAttribute('data-lang', lang || 'kode');
        });
        document.querySelectorAll('.question-body pre, .explanation-body pre').forEach(function (pre) {
            const btn = document.createElement('button');
            btn.textContent = 'Salin';
            btn.className   = 'copy-code-btn';
            btn.type        = 'button';
            btn.addEventListener('click', function () {
                navigator.clipboard.writeText(pre.querySelector('code')?.innerText ?? '').then(function () {
                    btn.textContent = '✓';
                    btn.classList.add('copied');
                    setTimeout(function () { btn.textContent = 'Salin'; btn.classList.remove('copied'); }, 2000);
                });
            });
            pre.appendChild(btn);
        });

        // ── Per-card reveal / hide ──────────────────────────────────────────
        function revealCard(card) {
            card.dataset.revealed = 'true';

            // Warnai pilihan
            card.querySelectorAll('.option-item').forEach(function (opt) {
                if (opt.dataset.correct === 'true') {
                    opt.classList.add('revealed-correct');
                    opt.classList.remove('revealed-wrong');
                    opt.querySelector('.option-letter').classList.add('bg-[#58cc02]', 'text-white', 'border-[#58cc02]');
                    opt.querySelector('.option-letter').classList.remove('bg-white', 'text-gray-400', 'border-gray-200');
                } else {
                    opt.classList.add('revealed-wrong');
                    opt.classList.remove('revealed-correct');
                }
            });

            // Nomor soal jadi hijau
            card.querySelector('.question-num').classList.add('bg-[#e9f9d4]', 'text-[#3a7200]');
            card.querySelector('.question-num').classList.remove('bg-gray-100', 'text-gray-500');

            // Card border hijau
            card.classList.add('border-[#58cc02]');
            card.classList.remove('border-gray-200');

            // Penjelasan tampil
            const expl = card.querySelector('.explanation-box');
            if (expl) expl.classList.add('visible');

            // Footer status
            card.querySelector('.answer-status').textContent = '✓ Jawaban ditampilkan';
            card.querySelector('.answer-status').classList.add('text-[#3a7200]');
            card.querySelector('.answer-status').classList.remove('text-gray-400');

            // Footer tombol
            const footerBtn = card.querySelector('[class*="toggle-btn"]:not(.toggle-btn.w-full)') ??
                              card.querySelectorAll('.toggle-btn')[1];
            if (footerBtn) footerBtn.textContent = 'Sembunyikan';

            // Chevron rotate
            card.querySelector('.chevron').style.transform = 'rotate(180deg)';
        }

        function hideCard(card) {
            card.dataset.revealed = 'false';

            card.querySelectorAll('.option-item').forEach(function (opt) {
                opt.classList.remove('revealed-correct', 'revealed-wrong');
                opt.querySelector('.option-letter').classList.remove('bg-[#58cc02]', 'text-white', 'border-[#58cc02]');
                opt.querySelector('.option-letter').classList.add('bg-white', 'text-gray-400', 'border-gray-200');
            });

            card.querySelector('.question-num').classList.remove('bg-[#e9f9d4]', 'text-[#3a7200]');
            card.querySelector('.question-num').classList.add('bg-gray-100', 'text-gray-500');

            card.classList.remove('border-[#58cc02]');
            card.classList.add('border-gray-200');

            const expl = card.querySelector('.explanation-box');
            if (expl) expl.classList.remove('visible');

            card.querySelector('.answer-status').textContent = '— Jawaban tersembunyi';
            card.querySelector('.answer-status').classList.remove('text-[#3a7200]');
            card.querySelector('.answer-status').classList.add('text-gray-400');

            const footerBtn = card.querySelectorAll('.toggle-btn')[1];
            if (footerBtn) footerBtn.textContent = 'Lihat Jawaban';

            card.querySelector('.chevron').style.transform = '';
        }

        function toggleCard(card) {
            if (card.dataset.revealed === 'true') {
                hideCard(card);
            } else {
                revealCard(card);
            }
        }

        // Pasang event listener ke semua toggle-btn di tiap card
        document.querySelectorAll('.question-card').forEach(function (card) {
            card.querySelectorAll('.toggle-btn').forEach(function (btn) {
                btn.addEventListener('click', function () { toggleCard(card); });
            });
        });

        // ── Reveal all / Hide all ───────────────────────────────────────────
        document.getElementById('btn-reveal-all').addEventListener('click', function () {
            document.querySelectorAll('.question-card').forEach(revealCard);
        });
        document.getElementById('btn-hide-all').addEventListener('click', function () {
            document.querySelectorAll('.question-card').forEach(hideCard);
        });

    });
    </script>

</body>
</html>
