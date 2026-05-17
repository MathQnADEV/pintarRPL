{{--
  Tile-picker untuk soal tipe code_arrange.
  Variabel:
    $question  — Question model (options sudah di-load)
    $formId    — ID form HTML

  Logika order:
    order >= 1  → tile bagian jawaban benar (harus dipilih + diurutkan)
    order == 0  → tile pengecoh (muncul di bank, BUKAN bagian jawaban)
    order null  → diabaikan / dianggap pengecoh
--}}

@php
    $correctCount    = $question->options->where('order', '>', 0)->count();
    $shuffledOptions = $question->options->shuffle();
@endphp

<style>
.tile-zone-label {
    font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em;
    text-transform: uppercase; color: #9ca3af; margin-bottom: 0.5rem;
}
.answer-zone {
    min-height: 3.5rem; padding: 0.75rem; background: #f8f9ff;
    border: 2px dashed #c4b9f8; border-radius: 1rem;
    display: flex; flex-wrap: wrap; gap: 0.5rem; align-content: flex-start;
    transition: border-color 0.2s;
}
.answer-zone.ready { border-style: solid; border-color: #4c3fb5; background: #f0effe; }
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
/* Hint jumlah slot yang diharapkan */
.slot-hint {
    display: inline-flex; align-items: center; gap: 0.25rem;
    font-size: 0.7rem; font-weight: 700; color: #a78bfa;
}
</style>

{{-- ─── Area jawaban ──────────────────────────────────────────────────────── --}}
<div class="mb-4">
    <div class="flex items-center justify-between mb-2">
        <p class="tile-zone-label mb-0">Susunanmu</p>
        {{-- Tampilkan berapa tile yang harus dipilih (tanpa bocorkan mana pengecoh) --}}
        <span class="slot-hint">
            🧩 Pilih {{ $correctCount }} baris yang benar
        </span>
    </div>
    <div id="answer-zone" class="answer-zone">
        <p class="answer-zone-empty" id="empty-hint">Klik tile kode di bawah untuk menyusun…</p>
    </div>
    <div class="flex items-center justify-between mt-2 px-1">
        <span id="tile-counter" class="tile-counter">
            0 / {{ $correctCount }} tile dipilih
        </span>
        <button type="button" id="btn-reset-tiles"
                class="text-xs font-bold text-gray-400 hover:text-red-400 transition">
            ↺ Reset
        </button>
    </div>
</div>

{{-- ─── Bank tile ─────────────────────────────────────────────────────────── --}}
<div class="mb-6">
    <p class="tile-zone-label">Pilih baris kode (termasuk pengecoh)</p>
    <div id="bank-zone" class="bank-zone">
        @foreach ($shuffledOptions as $opt)
            <button type="button" class="code-tile bank-tile"
                    data-id="{{ $opt->id }}">
                {{ $opt->option_text }}
            </button>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
(function () {
    const tileText = {};
    @foreach ($shuffledOptions as $opt)
    tileText[{{ $opt->id }}] = @json($opt->option_text);
    @endforeach

    // Jumlah tile yang HARUS ada di area jawaban = tile dengan order >= 1
    const correctCount = {{ $correctCount }};

    let bankIds   = @json($shuffledOptions->pluck('id')->toArray());
    let answerIds = [];

    const answerZone = document.getElementById('answer-zone');
    const bankZone   = document.getElementById('bank-zone');
    const emptyHint  = document.getElementById('empty-hint');
    const counter    = document.getElementById('tile-counter');
    const submitBtn  = document.getElementById('submit-btn');
    const formEl     = document.getElementById('{{ $formId }}');

    function render() {
        // ── Answer zone ───────────────────────────────────────────
        answerZone.innerHTML = '';
        if (answerIds.length === 0) {
            answerZone.appendChild(emptyHint);
        } else {
            answerIds.forEach(id => {
                const tile = document.createElement('button');
                tile.type      = 'button';
                tile.className = 'code-tile in-answer';
                tile.innerHTML = escHtml(tileText[id]) + ' <span class="tile-remove">✕</span>';
                tile.addEventListener('click', () => returnToBank(id));
                answerZone.appendChild(tile);
            });
        }

        // ── Bank zone ─────────────────────────────────────────────
        bankZone.innerHTML = '';
        bankIds.forEach(id => {
            const tile = document.createElement('button');
            tile.type        = 'button';
            tile.className   = 'code-tile bank-tile';
            tile.textContent = tileText[id];
            tile.addEventListener('click', () => pickTile(id));
            bankZone.appendChild(tile);
        });

        // ── Counter: X / correctCount ─────────────────────────────
        const placed = answerIds.length;
        counter.textContent = placed + ' / ' + correctCount + ' tile dipilih';
        counter.classList.toggle('done', placed === correctCount);
        answerZone.classList.toggle('ready', placed === correctCount);

        // Submit aktif HANYA jika tepat correctCount tile dipilih
        if (submitBtn) submitBtn.disabled = (placed !== correctCount);

        // ── Hidden inputs ─────────────────────────────────────────
        formEl.querySelectorAll('input[name="answer[]"]').forEach(el => el.remove());
        answerIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'answer[]';
            inp.value = id;
            formEl.appendChild(inp);
        });
    }

    function pickTile(id) {
        // Jika sudah penuh (correctCount tile), tolak penambahan
        if (answerIds.length >= correctCount) return;
        bankIds   = bankIds.filter(x => x !== id);
        answerIds = [...answerIds, id];
        render();
    }

    function returnToBank(id) {
        answerIds = answerIds.filter(x => x !== id);
        bankIds   = [...bankIds, id];
        render();
    }

    document.getElementById('btn-reset-tiles').addEventListener('click', () => {
        bankIds   = bankIds.concat(answerIds);
        answerIds = [];
        render();
    });

    function escHtml(str) {
        return str
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    render();
})();
}); // DOMContentLoaded
</script>
