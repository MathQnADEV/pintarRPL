<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTAR - {{ $topic->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Highlight.js — syntax highlighting untuk blok kode di konten materi --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">
    <style>
        /* ═══════════════════════════════════════════════════════════════════
           TYPOGRAPHY — styling semua elemen HTML dari RichEditor / Trix
           (Tailwind v4 tidak punya prose tanpa plugin, jadi kita tulis sendiri)
           ═══════════════════════════════════════════════════════════════════ */

        .materi-content {
            color: #374151;
            line-height: 1.8;
            font-size: 0.975rem;
        }

        /* — Headings — */
        .materi-content h1 {
            font-size: 1.875rem; font-weight: 800;
            color: #111827; margin: 2rem 0 1rem;
            padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;
        }
        .materi-content h2 {
            font-size: 1.5rem; font-weight: 700;
            color: #111827; margin: 1.75rem 0 0.75rem;
        }
        .materi-content h3 {
            font-size: 1.2rem; font-weight: 700;
            color: #1f2937; margin: 1.5rem 0 0.5rem;
        }
        .materi-content h4 {
            font-size: 1.05rem; font-weight: 700;
            color: #1f2937; margin: 1.25rem 0 0.4rem;
        }

        /* — Paragraphs — */
        .materi-content p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        /* — Lists — */
        .materi-content ul {
            list-style: disc;
            padding-left: 1.75rem;
            margin-bottom: 1rem;
        }
        .materi-content ol {
            list-style: decimal;
            padding-left: 1.75rem;
            margin-bottom: 1rem;
        }
        .materi-content li {
            margin-bottom: 0.35rem;
            line-height: 1.75;
        }
        .materi-content li > ul,
        .materi-content li > ol {
            margin-top: 0.35rem;
            margin-bottom: 0;
        }

        /* — Links — */
        .materi-content a {
            color: #4c3fb5;
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        .materi-content a:hover { color: #3d3291; }

        /* — Inline emphasis — */
        .materi-content strong { font-weight: 700; color: #111827; }
        .materi-content em     { font-style: italic; }
        .materi-content u      { text-decoration: underline; text-underline-offset: 2px; }
        .materi-content s      { text-decoration: line-through; color: #9ca3af; }

        /* — Inline code (bukan blok) — */
        .materi-content :not(pre) > code {
            background: #f3f4f6;
            color: #4c3fb5;
            padding: 0.1em 0.4em;
            border-radius: 0.3rem;
            font-size: 0.875em;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            border: 1px solid #e5e7eb;
        }

        /* — Blockquote — */
        .materi-content blockquote {
            border-left: 4px solid #4c3fb5;
            background: #f5f3ff;
            padding: 0.85rem 1.25rem;
            margin: 1.25rem 0;
            border-radius: 0 0.5rem 0.5rem 0;
            color: #4c3fb5;
        }
        .materi-content blockquote p { margin-bottom: 0; }

        /* — Horizontal rule — */
        .materi-content hr {
            border: none;
            border-top: 2px solid #e5e7eb;
            margin: 1.75rem 0;
        }

        /* — Images — */
        .materi-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }

        /* — Table — */
        .materi-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0 0 1px #e5e7eb;
        }
        .materi-content thead {
            background: #f3f4f6;
        }
        .materi-content th {
            padding: 0.7rem 1rem;
            text-align: left;
            font-weight: 700;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }
        .materi-content th:last-child { border-right: none; }
        .materi-content td {
            padding: 0.65rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .materi-content td:last-child { border-right: none; }
        .materi-content tr:last-child td { border-bottom: none; }
        .materi-content tbody tr:nth-child(even) { background: #f9fafb; }
        .materi-content tbody tr:hover { background: #f0eeff; }

        /* ═══════════════════════════════════════════════════════════════════
           CODE BLOCKS
           ═══════════════════════════════════════════════════════════════════ */

        .materi-content pre {
            position: relative;
            margin: 1.5rem 0;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
        }
        .materi-content pre::before {
            content: attr(data-lang);
            display: block;
            padding: 0.4rem 1rem;
            background: #1a1d23;
            color: #6b7280;
            font-size: 0.68rem;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-bottom: 1px solid #2d3139;
        }
        .materi-content pre code.hljs {
            border-radius: 0;
            padding: 1.25rem 1.5rem;
            font-size: 0.875rem;
            line-height: 1.7;
            font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;
        }

        /* ═══════════════════════════════════════════════════════════════════
           VIDEO YOUTUBE EMBED
           ═══════════════════════════════════════════════════════════════════ */

        .video-embed-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* rasio 16:9 */
            height: 0;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        .video-embed-wrapper iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            border: none;
        }

        /* ═══════════════════════════════════════════════════════════════════
           GIF — pastikan animasi tampil dengan baik
           ═══════════════════════════════════════════════════════════════════ */

        .materi-content img[src$=".gif"] {
            display: block;
            margin: 1.25rem auto;
            border-radius: 0.75rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
        }

        /* Tombol salin */
        .copy-code-btn {
            position: absolute;
            top: 0.3rem;
            right: 0.6rem;
            background: transparent;
            border: 1px solid #374151;
            color: #9ca3af;
            padding: 0.2rem 0.6rem;
            border-radius: 0.35rem;
            font-size: 0.7rem;
            font-family: sans-serif;
            cursor: pointer;
            transition: all 0.15s;
            z-index: 10;
        }
        .copy-code-btn:hover  { background: #374151; color: #f3f4f6; }
        .copy-code-btn.copied { border-color: #4ade80; color: #4ade80; }
    </style>
</head>
<body class="bg-[#f8f9fa] font-sans text-gray-800">
<div class="flex h-screen overflow-hidden">

    {{-- ─── Sidebar ──────────────────────────────────────────────────────────── --}}
    <aside class="w-72 bg-white border-r-2 border-gray-200 p-6 overflow-y-auto flex flex-col gap-6">

        <div>
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="flex items-center gap-1 text-sm font-bold text-gray-400 hover:text-[#4c3fb5] transition mb-5">
                ← Dashboard
            </a>
            @php
            // dd($topic);
                $levelLabel = match($topic->level) {
                    'pemula'   => 'Level 1: Pemula',
                    'menengah' => 'Level 2: Menengah',
                    'lanjut'   => 'Level 3: Lanjut',
                    default    => ucfirst($topic->level),
                };
            @endphp
            <h2 class="font-extrabold text-[#4c3fb5] text-base tracking-tight">📖 {{ $levelLabel }}</h2>
            <p class="text-xs text-gray-400 mt-1">{{ $topicsInLevel->count() }} sub-bahasan</p>
        </div>

        <nav class="space-y-2">
            @foreach ($topicsInLevel as $t)
                @php
                    $idx          = $loop->index;
                    $tp           = $progressMap[$t->id] ?? null;
                    $isActive     = $t->id === $topic->id;
                    $isDone       = $tp && $tp->status === 'completed';
                    $prevTopic    = $idx > 0 ? $topicsInLevel->get($idx - 1) : null;
                    $isAccessible = $idx === 0
                        || ($prevTopic && ($progressMap[$prevTopic->id] ?? null)?->status === 'completed');
                @endphp

                @if ($isActive)
                    <div class="p-3 rounded-xl border-2 border-[#4c3fb5] bg-[#f0eeff] font-bold text-[#4c3fb5] flex justify-between items-center">
                        <span class="truncate text-sm">{{ $t->title }}</span>
                        <span class="ml-2 shrink-0">▶</span>
                    </div>
                @elseif ($isDone)
                    <a href="{{ route('mahasiswa.materi', $t) }}"
                       class="flex p-3 rounded-xl border-2 border-[#58cc02] bg-[#f0fff4] font-bold text-[#3a9e01] justify-between items-center hover:bg-green-50 transition text-sm">
                        <span class="truncate">{{ $t->title }}</span>
                        <span class="ml-2 shrink-0">✓</span>
                    </a>
                @elseif ($isAccessible)
                    <a href="{{ route('mahasiswa.materi', $t) }}"
                       class="block p-3 rounded-xl border-2 border-gray-200 font-semibold text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition text-sm truncate">
                        {{ $t->title }}
                    </a>
                @else
                    <div class="p-3 rounded-xl border-2 border-gray-100 font-semibold text-gray-300 flex justify-between items-center cursor-not-allowed text-sm">
                        <span class="truncate">{{ $t->title }}</span>
                        <span class="ml-2 shrink-0">🔒</span>
                    </div>
                @endif
            @endforeach
        </nav>

    </aside>

    {{-- ─── Main Content ────────────────────────────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto p-10">
        <div class="max-w-4xl mx-auto">

            @php
                $levelColor = match($pretestResult->level) {
                    'pemula'   => '#58cc02',
                    'menengah' => '#1cb0f6',
                    'lanjut'   => '#ff9600',
                    default    => '#4c3fb5',
                };
            @endphp

            {{-- Banner --}}
            <div class="p-4 rounded-t-2xl text-white font-extrabold flex justify-between items-center"
                 style="background-color: {{ $levelColor }}">
                <span>{{ $levelLabel }} — {{ $topic->title }}</span>
                @if ($progress->status === 'completed')
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-semibold">
                        ✓ Selesai · Skor terbaik: {{ $progress->best_score ?? 0 }}
                    </span>
                @else
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-semibold">
                        📖 Sedang dipelajari
                    </span>
                @endif
            </div>

            {{-- Session flash alerts --}}
            @if (session('error'))
                <div class="mt-3 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-700 text-sm font-semibold flex items-start gap-2">
                    <span>⚠️</span><span>{{ session('error') }}</span>
                </div>
            @endif
            @if (session('info'))
                <div class="mt-3 p-4 bg-blue-50 border-2 border-blue-200 rounded-xl text-blue-700 text-sm font-semibold flex items-start gap-2">
                    <span>ℹ️</span><span>{{ session('info') }}</span>
                </div>
            @endif

            {{-- Content card --}}
            <div class="bg-white p-8 rounded-b-2xl border-2 border-t-0 border-b-8 border-gray-200 shadow-sm">
                <h1 class="text-3xl font-extrabold mb-2">{{ $topic->title }}</h1>

                @if ($topic->description)
                    <p class="text-gray-500 font-semibold mb-6">{{ $topic->description }}</p>
                @endif

                <hr class="border-gray-100 mb-6">

                {{-- ─── Video YouTube ─────────────────────────────────────────────── --}}
                @php $embedUrl = $topic->getYoutubeEmbedUrl(); @endphp
                @if ($embedUrl)
                    <div class="mb-8">
                        <h2 class="text-base font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs">▶</span>
                            Video Pembelajaran
                        </h2>
                        <div class="video-embed-wrapper">
                            <iframe
                                src="{{ $embedUrl }}?rel=0&modestbranding=1"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                loading="lazy"
                                title="Video: {{ $topic->title }}">
                            </iframe>
                        </div>
                    </div>
                    <hr class="border-gray-100 mb-6">
                @endif

                @if ($topic->content)
                    <h2 class="text-base font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 text-[#4c3fb5] text-xs">📖</span>
                        Materi &amp; Animasi
                    </h2>
                    {{-- Render HTML dari rich-text editor (termasuk GIF yang di-embed via attachFiles) --}}
                    <div class="materi-content">
                        {!! $topic->content !!}
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400">
                        <div class="text-5xl mb-4">📄</div>
                        <p class="font-semibold">Konten materi belum tersedia.</p>
                        <p class="text-sm mt-1">Silakan hubungi dosen Anda.</p>
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex gap-4">
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="px-5 py-3 bg-white text-gray-600 font-bold rounded-xl border-2 border-b-4 border-gray-200 hover:bg-gray-50 transition text-sm whitespace-nowrap">
                    ← Dashboard
                </a>

                @if ($progress->status === 'completed')
                    @if ($hasKuis)
                        <a href="{{ route('mahasiswa.kuis', $topic) }}"
                           class="px-5 py-3 bg-white text-[#4c3fb5] font-bold rounded-xl border-2 border-b-4 border-[#4c3fb5] hover:bg-[#f0eeff] transition text-sm whitespace-nowrap">
                            Ulangi Kuis
                        </a>
                    @endif
                    @if ($nextTopic)
                        <a href="{{ route('mahasiswa.materi', $nextTopic) }}"
                           class="flex-1 text-center py-3 px-6 bg-[#4c3fb5] text-white font-bold rounded-xl border-b-4 border-[#3d3291] hover:bg-[#3d3291] transition">
                            Lanjut ke {{ $nextTopic->title }} →
                        </a>
                    @else
                        <a href="{{ route('mahasiswa.dashboard') }}"
                           class="flex-1 text-center py-3 px-6 bg-[#58cc02] text-white font-bold rounded-xl border-b-4 border-[#46a802] hover:bg-[#46a802] transition">
                            🏆 Lihat Dashboard
                        </a>
                    @endif
                @else
                    @if ($hasKuis)
                        <a href="{{ route('mahasiswa.kuis', $topic) }}"
                           class="flex-1 text-center py-3 px-6 bg-[#4c3fb5] text-white font-bold rounded-xl border-b-4 border-[#3d3291] hover:bg-[#3d3291] transition">
                            Mulai Kuis →
                        </a>
                    @else
                        <span class="flex-1 text-center py-3 px-6 bg-gray-200 text-gray-400 font-bold rounded-xl border-b-4 border-gray-300 cursor-not-allowed text-sm"
                              title="Soal kuis belum tersedia">
                            🔒 Kuis Belum Tersedia
                        </span>
                    @endif
                @endif
            </div>

        </div>
    </main>

</div>

{{-- Highlight.js init --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Filament RichEditor (Trix) menyimpan code block sebagai <pre>teks</pre>
    // tanpa <code> di dalamnya. Wrap dulu agar hljs bisa memprosesnya.
    document.querySelectorAll('.materi-content pre').forEach(function (pre) {
        if (!pre.querySelector('code')) {
            const code = document.createElement('code');
            code.innerHTML = pre.innerHTML;
            pre.innerHTML = '';
            pre.appendChild(code);
        }
    });

    // Jalankan syntax highlighting
    document.querySelectorAll('.materi-content pre code').forEach(function (block) {
        hljs.highlightElement(block);

        // Tulis label bahasa yang terdeteksi ke attr data-lang pada <pre>
        const lang = block.result?.language ?? '';
        block.closest('pre').setAttribute(
            'data-lang',
            lang || 'kode'
        );
    });

    // Tambahkan tombol "Salin" ke setiap blok
    document.querySelectorAll('.materi-content pre').forEach(function (pre) {
        const btn = document.createElement('button');
        btn.textContent = 'Salin';
        btn.className = 'copy-code-btn';

        btn.addEventListener('click', function () {
            const code = pre.querySelector('code');
            navigator.clipboard.writeText(code.innerText ?? '').then(function () {
                btn.textContent = '✓ Disalin!';
                btn.classList.add('copied');
                setTimeout(function () {
                    btn.textContent = 'Salin';
                    btn.classList.remove('copied');
                }, 2000);
            }).catch(function () {
                // fallback untuk browser lama
                const ta = document.createElement('textarea');
                ta.value = code.innerText;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                btn.textContent = '✓ Disalin!';
                setTimeout(function () { btn.textContent = 'Salin'; }, 2000);
            });
        });

        pre.appendChild(btn);
    });
});
</script>
</body>
</html>
