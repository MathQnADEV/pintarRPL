<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Kuis: {{ $topic->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Highlight.js — untuk blok kode di teks soal --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">
    <style>
        /* Blok kode di dalam soal */
        .question-body pre {
            position: relative;
            margin: 1rem 0;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .question-body pre::before {
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
        .question-body pre code.hljs {
            border-radius: 0;
            padding: 1rem 1.25rem;
            font-size: 0.82rem;
            line-height: 1.65;
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
        }
        /* Salin button di soal */
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

        /* Prose ringan untuk teks soal (bold, italic, list) */
        .question-body p   { margin-bottom: 0.5rem; }
        .question-body ul  { list-style: disc; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body ol  { list-style: decimal; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body strong { font-weight: 700; }
    </style>
</head>
<body class="bg-white font-sans text-gray-800 min-h-screen flex flex-col">

    {{-- ─── Top bar: close + progress bar + counter ───────────────────────── --}}
    <div class="p-6 max-w-4xl mx-auto w-full flex items-center gap-6">
        <a href="{{ route('mahasiswa.dashboard') }}"
           class="text-gray-400 text-2xl hover:text-gray-600 transition leading-none"
           title="Keluar dari kuis">✕</a>

        <div class="flex-1 h-4 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-[#58cc02] rounded-full transition-all duration-500"
                 style="width: {{ $progress }}%"></div>
        </div>

        <span class="text-gray-500 font-bold text-sm whitespace-nowrap">
            {{ $current }} / {{ $total }}
        </span>
    </div>

    {{-- ─── Flash error ─────────────────────────────────────────────────────── --}}
    @if (session('error'))
        <div class="max-w-2xl mx-auto px-6 mb-2">
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm font-semibold px-4 py-3 rounded-xl">
                ⚠️ {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ─── Question ────────────────────────────────────────────────────────── --}}
    <main class="flex-1 max-w-2xl mx-auto w-full pt-6 px-6 pb-4">

        <p class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">
            {{ $topic->title }} · Soal {{ $current }}
        </p>

        {{-- Teks soal — render HTML dari RichEditor agar kode tampil dengan benar --}}
        <div class="question-body text-gray-800 leading-relaxed mb-8 text-base">
            {!! $question->question_text !!}
        </div>

        <form action="{{ route('mahasiswa.kuis.answer', $topic) }}" method="POST" id="kuis-form">
            @csrf

            @if ($question->type === 'code_arrange')
                {{-- ── Tile-picker untuk soal susun kode ───────────────────── --}}
                @include('partials.code-arrange-picker', ['question' => $question, 'formId' => 'kuis-form'])
            @else
                {{-- ── Pilihan ganda biasa ──────────────────────────────────── --}}
                <div class="grid gap-4">
                    @foreach ($question->options->shuffle() as $option)
                        <label
                            class="p-4 border-2 border-gray-200 border-b-4 rounded-2xl flex items-center gap-4 cursor-pointer
                                   hover:border-gray-300 hover:bg-gray-50 transition-all duration-150
                                   has-[:checked]:border-[#1cb0f6] has-[:checked]:bg-[#e6f7ff] has-[:checked]:border-b-4">
                            <span class="w-9 h-9 rounded-xl border-2 border-gray-200 flex items-center justify-center
                                         font-extrabold text-gray-400 shrink-0 text-sm">
                                {{ chr(65 + $loop->index) }}
                            </span>
                            <span class="text-base font-semibold flex-1 leading-snug">
                                {{ $option->option_text }}
                            </span>
                            <input type="radio" name="answer" value="{{ $option->id }}" class="sr-only" required>
                        </label>
                    @endforeach
                </div>
            @endif
        </form>

    </main>

    {{-- ─── Submit footer ───────────────────────────────────────────────────── --}}
    <div class="p-6 border-t-2 border-gray-100 bg-white sticky bottom-0">
        <div class="max-w-4xl mx-auto">
            <button
                form="kuis-form"
                type="submit"
                id="submit-btn"
                disabled
                class="w-full bg-[#4c3fb5] text-white font-extrabold py-4 px-12 rounded-2xl
                       border-b-4 border-[#3d3291] uppercase tracking-widest
                       hover:bg-[#3d3291] active:translate-y-1 active:border-b-0 transition-all
                       disabled:opacity-40 disabled:cursor-not-allowed disabled:translate-y-0 disabled:border-b-4"
            >
                @if ($current < $total)
                    Jawab &amp; Lanjut →
                @else
                    Selesai &amp; Lihat Hasil 🏆
                @endif
            </button>
        </div>
    </div>

    {{-- Highlight.js init --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script>
        // ── Syntax highlighting untuk blok kode di teks soal ─────────────────
        document.addEventListener('DOMContentLoaded', function () {
            // Trix menyimpan code block sebagai <pre> tanpa <code> di dalamnya
            document.querySelectorAll('.question-body pre').forEach(function (pre) {
                if (!pre.querySelector('code')) {
                    const code = document.createElement('code');
                    code.innerHTML = pre.innerHTML;
                    pre.innerHTML = '';
                    pre.appendChild(code);
                }
            });

            document.querySelectorAll('.question-body pre code').forEach(function (block) {
                hljs.highlightElement(block);
                const lang = block.result?.language ?? '';
                block.closest('pre').setAttribute('data-lang', lang || 'kode');
            });

            // Tombol salin per blok kode
            document.querySelectorAll('.question-body pre').forEach(function (pre) {
                const btn = document.createElement('button');
                btn.textContent = 'Salin';
                btn.className = 'copy-code-btn';
                btn.type = 'button'; // jangan trigger form submit
                btn.addEventListener('click', function () {
                    const text = pre.querySelector('code')?.innerText ?? '';
                    navigator.clipboard.writeText(text).then(function () {
                        btn.textContent = '✓';
                        btn.classList.add('copied');
                        setTimeout(function () {
                            btn.textContent = 'Salin';
                            btn.classList.remove('copied');
                        }, 2000);
                    });
                });
                pre.appendChild(btn);
            });
        });

        // ── Enable submit hanya setelah pilih jawaban (MC saja — code_arrange
        //    diatur oleh tile-picker partial) ──────────────────────────────────
        const radios = document.querySelectorAll('input[type="radio"][name="answer"]');
        const btn    = document.getElementById('submit-btn');
        if (radios.length > 0) {
            radios.forEach(r => r.addEventListener('change', () => btn.disabled = false));
        }
        // code_arrange: submit diaktifkan via tile-picker script saat semua tile dipasang
    </script>

</body>
</html>
