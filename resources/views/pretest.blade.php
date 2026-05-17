<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - Pre-test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Highlight.js --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">

    <style>
        /* ── Blok kode di soal ──────────────────────────────────────── */
        .question-body pre {
            position: relative; margin: 1rem 0;
            border-radius: 0.75rem; overflow: hidden;
        }
        .question-body pre::before {
            content: attr(data-lang); display: block;
            padding: 0.35rem 1rem; background: #1a1d23; color: #6b7280;
            font-size: 0.65rem; font-family: monospace; font-weight: 600;
            letter-spacing: 0.06em; text-transform: uppercase;
            border-bottom: 1px solid #2d3139;
        }
        .question-body pre code.hljs {
            border-radius: 0; padding: 1rem 1.25rem;
            font-size: 0.82rem; line-height: 1.65;
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
        }

        /* ── Tombol salin ───────────────────────────────────────────── */
        .copy-code-btn {
            position: absolute; top: 0.25rem; right: 0.5rem;
            background: transparent; border: 1px solid #374151; color: #9ca3af;
            padding: 0.15rem 0.5rem; border-radius: 0.3rem; font-size: 0.65rem;
            cursor: pointer; transition: all 0.15s;
        }
        .copy-code-btn:hover  { background: #374151; color: #f3f4f6; }
        .copy-code-btn.copied { border-color: #4ade80; color: #4ade80; }

        /* ── Prose ringan untuk teks soal ──────────────────────────── */
        .question-body p      { margin-bottom: 0.5rem; }
        .question-body ul     { list-style: disc;    padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body ol     { list-style: decimal; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .question-body strong { font-weight: 700; }
        .question-body em     { font-style: italic; }
        .question-body :not(pre) > code {
            background: #f3f4f6; color: #4c3fb5;
            padding: 0.1em 0.4em; border-radius: 0.3rem;
            font-size: 0.875em;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            border: 1px solid #e5e7eb;
        }

        /* ══════════════════════════════════════════════════════════════
           TILE-PICKER  (Duolingo code-arrange style, multi-instance)
           ══════════════════════════════════════════════════════════════ */
        .tile-zone-label {
            font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em;
            text-transform: uppercase; color: #9ca3af; margin-bottom: 0.5rem;
        }
        .answer-zone {
            min-height: 3.5rem; padding: 0.75rem;
            background: #f8f9ff; border: 2px dashed #c4b9f8;
            border-radius: 1rem; display: flex; flex-wrap: wrap;
            gap: 0.5rem; align-content: flex-start; transition: border-color 0.2s;
        }
        .answer-zone.ready {
            border-style: solid; border-color: #4c3fb5; background: #f0effe;
        }
        .answer-zone-empty {
            width: 100%; text-align: center; color: #c4b9f8;
            font-size: 0.8rem; font-weight: 600; padding: 0.5rem 0;
        }
        .bank-zone {
            display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem;
            background: #1e1e2e; border-radius: 1rem;
            min-height: 3.5rem; align-content: flex-start;
        }
        .code-tile {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #282c34; color: #abb2bf;
            border: 1.5px solid #3d4455; border-bottom-width: 3px;
            border-radius: 0.5rem; padding: 0.35rem 0.75rem;
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
            font-size: 0.78rem; line-height: 1.4; cursor: pointer;
            user-select: none; transition: all 0.15s; white-space: pre;
        }
        .code-tile:hover { border-color: #4c3fb5; color: #c8d3f5; transform: translateY(-1px); }
        .code-tile.in-answer {
            background: #2d2b55; border-color: #7c6fff; color: #c8d3f5; border-bottom-width: 2px;
        }
        .code-tile .tile-remove { font-size: 0.65rem; color: #6272a4; margin-left: 0.1rem; font-weight: 800; }
        .code-tile:hover .tile-remove { color: #ff5555; }
        .tile-counter { font-size: 0.75rem; font-weight: 700; color: #6b7280; }
        .tile-counter.done { color: #4c3fb5; font-weight: 800; }
        .slot-hint {
            display: inline-flex; align-items: center; gap: 0.25rem;
            font-size: 0.7rem; font-weight: 700; color: #a78bfa;
        }
    </style>
</head>
<body class="bg-white font-sans text-gray-800 min-h-screen flex flex-col">

    {{-- ─── Top bar ─────────────────────────────────────────────────────────── --}}
    <div class="p-6 max-w-4xl mx-auto w-full flex items-center gap-6">
        <a href="{{ route('mahasiswa.dashboard') }}"
           class="text-gray-400 text-2xl hover:text-gray-600 transition leading-none"
           title="Keluar dari pre-test">✕</a>

        <div class="flex-1 h-4 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-[#4c3fb5] rounded-full transition-all duration-500"
                 id="progress-bar"
                 style="width: {{ round(1 / $questions->count() * 100) }}%"></div>
        </div>

        <span class="text-gray-500 font-bold text-sm whitespace-nowrap" id="counter">
            1 / {{ $questions->count() }}
        </span>
    </div>

    {{-- ─── Form (semua soal di DOM, JS atur yang tampil) ─────────────────── --}}
    <form action="{{ route('mahasiswa.pretest.submit') }}" method="POST" id="pretest-form">
        @csrf

        <main class="flex-1 max-w-2xl mx-auto w-full pt-6 px-6 pb-4">

            @foreach ($questions as $index => $question)
            @php $shuffledOpts = $question->options->shuffle(); @endphp

            <div class="question-slide {{ $index > 0 ? 'hidden' : '' }}"
                 data-index="{{ $index }}">

                {{-- Label soal --}}
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    Pre-test · Soal {{ $index + 1 }}
                    @if ($question->type === 'code_arrange')
                        <span class="bg-[#fff3e0] text-[#b45300] px-2 py-0.5 rounded-full normal-case font-bold text-xs">
                            🧩 Susun Kode
                        </span>
                    @endif
                </p>

                {{-- Teks soal — render HTML agar tag tidak tampil mentah --}}
                <div class="question-body text-gray-800 leading-relaxed mb-8 text-base">
                    {!! $question->question_text !!}
                </div>

                {{-- ── Pilihan: tile-picker ATAU pilihan ganda ── --}}
                @if ($question->type === 'code_arrange')
                    @php $correctCount = $question->options->where('order', '>', 0)->count(); @endphp

                    {{-- Area jawaban --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="tile-zone-label mb-0">Susunanmu</p>
                            <span class="slot-hint">🧩 Pilih {{ $correctCount }} baris yang benar</span>
                        </div>
                        <div id="answer-zone-{{ $question->id }}" class="answer-zone">
                            <p class="answer-zone-empty" id="empty-hint-{{ $question->id }}">
                                Klik tile kode di bawah untuk menyusun…
                            </p>
                        </div>
                        <div class="flex items-center justify-between mt-2 px-1">
                            <span id="tile-counter-{{ $question->id }}" class="tile-counter">
                                0 / {{ $correctCount }} tile dipilih
                            </span>
                            <button type="button" id="btn-reset-{{ $question->id }}"
                                    class="text-xs font-bold text-gray-400 hover:text-red-400 transition">
                                ↺ Reset
                            </button>
                        </div>
                    </div>

                    {{-- Bank tile --}}
                    <div class="mb-6">
                        <p class="tile-zone-label">Pilih baris kode (termasuk pengecoh)</p>
                        <div id="bank-zone-{{ $question->id }}" class="bank-zone">
                            @foreach ($shuffledOpts as $opt)
                                <button type="button" class="code-tile"
                                        data-id="{{ $opt->id }}">{{ $opt->option_text }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Hidden inputs — diisi oleh JS saat tile disusun --}}
                    <div id="hidden-inputs-{{ $question->id }}"></div>

                @else
                    {{-- Pilihan ganda biasa --}}
                    <div class="grid gap-4">
                        @foreach ($shuffledOpts as $option)
                        <label class="p-4 border-2 border-gray-200 border-b-4 rounded-2xl flex items-center gap-4 cursor-pointer
                                      hover:border-gray-300 hover:bg-gray-50 transition-all duration-150
                                      has-[:checked]:border-[#4c3fb5] has-[:checked]:bg-[#f0eeff] has-[:checked]:border-b-4">
                            <span class="w-9 h-9 rounded-xl border-2 border-gray-200 flex items-center justify-center
                                         font-extrabold text-gray-400 shrink-0 text-sm">
                                {{ chr(65 + $loop->index) }}
                            </span>
                            <span class="text-base font-semibold flex-1 leading-snug">
                                {{ $option->option_text }}
                            </span>
                            <input type="radio"
                                   name="answers[{{ $question->id }}]"
                                   value="{{ $option->id }}"
                                   class="sr-only option-radio"
                                   data-question-index="{{ $index }}">
                        </label>
                        @endforeach
                    </div>
                @endif

            </div>{{-- .question-slide --}}
            @endforeach

        </main>

        {{-- ─── Sticky footer ──────────────────────────────────────────────────── --}}
        <div class="p-6 border-t-2 border-gray-100 bg-white sticky bottom-0">
            <div class="max-w-4xl mx-auto">
                <button type="button" id="submit-btn" disabled
                        class="w-full bg-[#4c3fb5] text-white font-extrabold py-4 px-12 rounded-2xl
                               border-b-4 border-[#3d3291] uppercase tracking-widest
                               hover:bg-[#3d3291] active:translate-y-1 active:border-b-0 transition-all
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:translate-y-0 disabled:border-b-4">
                    Jawab &amp; Lanjut →
                </button>
            </div>
        </div>

    </form>

    {{-- Highlight.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ════════════════════════════════════════════════════════════════
           1. NAVIGASI SOAL (satu per satu)
           ════════════════════════════════════════════════════════════════ */
        const TOTAL      = {{ $questions->count() }};
        let   currentIdx = 0;
        const answered   = {};   // { questionIndex: true }

        const slides      = document.querySelectorAll('.question-slide');
        const progressBar = document.getElementById('progress-bar');
        const counter     = document.getElementById('counter');
        const btn         = document.getElementById('submit-btn');

        function updateProgress () {
            const pct = Math.round(((currentIdx + 1) / TOTAL) * 100);
            progressBar.style.width = pct + '%';
            counter.textContent     = (currentIdx + 1) + ' / ' + TOTAL;
        }

        function updateButton () {
            btn.disabled = !answered[currentIdx];
            if (currentIdx === TOTAL - 1) {
                btn.textContent = 'Selesai & Tentukan Level 🏆';
            } else {
                btn.innerHTML = 'Jawab &amp; Lanjut →';
            }
        }

        btn.addEventListener('click', function () {
            if (!answered[currentIdx]) return;
            if (currentIdx === TOTAL - 1) {
                document.getElementById('pretest-form').submit();
                return;
            }
            slides[currentIdx].classList.add('hidden');
            currentIdx++;
            slides[currentIdx].classList.remove('hidden');
            updateProgress();
            updateButton();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        /* ── Jawaban pilihan ganda ──────────────────────────────────── */
        document.querySelectorAll('.option-radio').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const qi = parseInt(this.dataset.questionIndex, 10);
                answered[qi] = true;
                if (qi === currentIdx) updateButton();
            });
        });

        /* ── Callback untuk tile-picker (code_arrange) ─────────────── */
        // Dipanggil oleh masing-masing tile-picker saat state berubah
        window.__pretestTileUpdate = function (questionIndex, isComplete) {
            answered[questionIndex] = isComplete;
            if (questionIndex === currentIdx) updateButton();
        };

        updateProgress();
        updateButton();

        /* ════════════════════════════════════════════════════════════════
           2. SYNTAX HIGHLIGHTING
           ════════════════════════════════════════════════════════════════ */
        document.querySelectorAll('.question-body pre').forEach(function (pre) {
            if (!pre.querySelector('code')) {
                const code     = document.createElement('code');
                code.innerHTML = pre.innerHTML;
                pre.innerHTML  = '';
                pre.appendChild(code);
            }
        });
        document.querySelectorAll('.question-body pre code').forEach(function (block) {
            hljs.highlightElement(block);
            const lang = block.result?.language ?? '';
            block.closest('pre').setAttribute('data-lang', lang || 'kode');
        });
        document.querySelectorAll('.question-body pre').forEach(function (pre) {
            const copyBtn       = document.createElement('button');
            copyBtn.textContent = 'Salin';
            copyBtn.className   = 'copy-code-btn';
            copyBtn.type        = 'button';
            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(pre.querySelector('code')?.innerText ?? '')
                    .then(function () {
                        copyBtn.textContent = '✓';
                        copyBtn.classList.add('copied');
                        setTimeout(function () {
                            copyBtn.textContent = 'Salin';
                            copyBtn.classList.remove('copied');
                        }, 2000);
                    });
            });
            pre.appendChild(copyBtn);
        });

        /* ════════════════════════════════════════════════════════════════
           3. TILE-PICKER — multi-instance (satu per soal code_arrange)
           Semua soal sudah ada di DOM; tiap picker punya namespace sendiri
           via question ID sehingga tidak ada konflik.
           ════════════════════════════════════════════════════════════════ */
        function initTilePicker (cfg) {
            /*  cfg: {
                  questionId    – ID soal (int)
                  questionIndex – posisi soal dalam pretest (0-based)
                  correctCount  – jumlah tile jawaban benar
                  tileData      – { id: text, ... }
                  initialOrder  – [id, id, ...] urutan awal di bank
                }
            */
            const qid      = cfg.questionId;
            const qi       = cfg.questionIndex;
            const correctN = cfg.correctCount;
            const tileText = cfg.tileData;

            const answerZone   = document.getElementById('answer-zone-'    + qid);
            const bankZone     = document.getElementById('bank-zone-'      + qid);
            const emptyHint    = document.getElementById('empty-hint-'     + qid);
            const tileCounter  = document.getElementById('tile-counter-'   + qid);
            const resetBtn     = document.getElementById('btn-reset-'      + qid);
            const hiddenInputs = document.getElementById('hidden-inputs-'  + qid);

            if (!answerZone || !bankZone) return;

            let bankIds   = cfg.initialOrder.slice();
            let answerIds = [];

            function escHtml (s) {
                return s.replace(/&/g,'&amp;').replace(/</g,'&lt;')
                        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            function render () {
                /* ── Answer zone ──────────────────────────────────── */
                answerZone.innerHTML = '';
                if (answerIds.length === 0) {
                    answerZone.appendChild(emptyHint);
                } else {
                    answerIds.forEach(function (id) {
                        const tile = document.createElement('button');
                        tile.type      = 'button';
                        tile.className = 'code-tile in-answer';
                        tile.innerHTML = escHtml(tileText[id])
                                       + ' <span class="tile-remove">✕</span>';
                        tile.addEventListener('click', function () { returnToBank(id); });
                        answerZone.appendChild(tile);
                    });
                }

                /* ── Bank zone ────────────────────────────────────── */
                bankZone.innerHTML = '';
                bankIds.forEach(function (id) {
                    const tile = document.createElement('button');
                    tile.type        = 'button';
                    tile.className   = 'code-tile';
                    tile.textContent = tileText[id];
                    tile.addEventListener('click', function () { pickTile(id); });
                    bankZone.appendChild(tile);
                });

                /* ── Counter ─────────────────────────────────────── */
                const placed = answerIds.length;
                tileCounter.textContent = placed + ' / ' + correctN + ' tile dipilih';
                tileCounter.classList.toggle('done', placed === correctN);
                answerZone.classList.toggle('ready', placed === correctN);

                /* ── Hidden inputs untuk form submit ─────────────── */
                hiddenInputs.innerHTML = '';
                answerIds.forEach(function (id) {
                    const inp  = document.createElement('input');
                    inp.type   = 'hidden';
                    inp.name   = 'answers_arranged[' + qid + '][]';
                    inp.value  = id;
                    hiddenInputs.appendChild(inp);
                });

                /* ── Beritahu navigasi pretest ───────────────────── */
                if (typeof window.__pretestTileUpdate === 'function') {
                    window.__pretestTileUpdate(qi, placed === correctN);
                }
            }

            function pickTile (id) {
                if (answerIds.length >= correctN) return;
                bankIds   = bankIds.filter(function (x) { return x !== id; });
                answerIds = answerIds.concat([id]);
                render();
            }

            function returnToBank (id) {
                answerIds = answerIds.filter(function (x) { return x !== id; });
                bankIds   = bankIds.concat([id]);
                render();
            }

            resetBtn.addEventListener('click', function () {
                bankIds   = bankIds.concat(answerIds);
                answerIds = [];
                render();
            });

            render(); // init
        }

        /* ── Inisialisasi satu picker per soal code_arrange ─────────── */
        @foreach ($questions as $index => $question)
        @if ($question->type === 'code_arrange')
        initTilePicker({
            questionId:    {{ $question->id }},
            questionIndex: {{ $index }},
            correctCount:  {{ $question->options->where('order', '>', 0)->count() }},
            tileData:      @json($question->options->pluck('option_text', 'id')),
            initialOrder:  @json($question->options->shuffle()->pluck('id')->toArray()),
        });
        @endif
        @endforeach

    }); // DOMContentLoaded
    </script>

</body>
</html>
