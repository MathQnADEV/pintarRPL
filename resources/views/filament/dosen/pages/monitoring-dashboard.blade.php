

<x-filament-panels::page>

    {{-- ── Filter Program Studi ────────────────────────────────────── --}}
    <div class="flex items-center gap-2 flex-wrap">
        <button
            wire:click="setFilterProdi('semua')"
            @class([
                'px-4 py-1.5 rounded-full text-sm font-medium border transition',
                'bg-primary-600 text-white border-primary-600' => $filterProdi === 'semua',
                'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600' => $filterProdi !== 'semua',
            ])
        >Semua</button>

        @foreach ($this->getUniqueProdi() as $prodi)
            <button
                wire:click="setFilterProdi('{{ $prodi }}')"
                @class([
                    'px-4 py-1.5 rounded-full text-sm font-medium border transition',
                    'bg-primary-600 text-white border-primary-600' => $filterProdi === $prodi,
                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600' => $filterProdi !== $prodi,
                ])
            >{{ $prodi }}</button>
        @endforeach
    </div>

    {{-- ── Kelas Cards Grid ─────────────────────────────────────────── --}}
    @php $kelasList = $this->getFilteredKelas(); @endphp

    @if ($kelasList->isEmpty())
        <div class="text-center py-12 text-gray-500">
            Belum ada kelas yang diampu.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($kelasList as $kelas)
                @php $cardData = $this->getKelasCardData($kelas); @endphp
                <button
                    wire:click="selectKelas({{ $kelas->id }})"
                    @class([
                        'text-left rounded-xl border p-4 transition shadow-sm',
                        'border-primary-500 ring-2 ring-primary-400 bg-primary-50 dark:bg-primary-900/20' => $selectedKelasId === $kelas->id,
                        'border-gray-200 bg-white hover:border-primary-300 dark:border-gray-700 dark:bg-gray-800' => $selectedKelasId !== $kelas->id,
                    ])
                >
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $kelas->mata_kuliah }}</p>
                            <p class="text-xs text-gray-500">{{ $kelas->name }} &mdash; {{ $kelas->program_studi }}</p>
                        </div>
                        @if ($kelas->is_active)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Aktif</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-1 mt-3 text-xs text-gray-600 dark:text-gray-300">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $cardData['total_mahasiswa'] }}</span>
                            <span class="block text-gray-400">Mahasiswa</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $cardData['avg_score'] }}</span>
                            <span class="block text-gray-400">Rata-rata</span>
                        </div>
                        <div class="col-span-2 mt-1">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $cardData['post_test_lulus'] }}/{{ $cardData['total_mahasiswa_post'] }}</span>
                            <span class="text-gray-400 ml-1">Lulus Post-test</span>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    @endif

    {{-- ── Detail Kelas Terpilih ────────────────────────────────────── --}}
    @if ($selectedKelasId && $selectedKelas = $this->getSelectedKelas())
        @php
            $stats   = $this->getStats();
            $rows    = $this->getMahasiswaRingkasan();
        @endphp

        <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">

            {{-- Header ringkasan --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $selectedKelas->mata_kuliah }} &mdash; {{ $selectedKelas->name }} &mdash; Ringkasan
                    </h2>
                    <p class="text-xs text-gray-400">{{ $selectedKelas->program_studi }} &bull; T.A. {{ $selectedKelas->academic_year }}</p>
                </div>
                <div class="flex gap-2">
                    <button
                        wire:click="exportCsv"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        Laporan CSV
                    </button>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-gray-100 dark:divide-gray-700">
                @php
                    $statItems = [
                        ['value' => $stats['total'],           'label' => 'Total Mahasiswa'],
                        ['value' => $stats['avg_quiz'],        'label' => 'Rata-rata Kuis'],
                        ['value' => $stats['post_test_lulus'] . '/' . $stats['total_post_test'], 'label' => 'Lulus Post-test'],
                        ['value' => $rows->where('status', 'Lulus')->count(), 'label' => 'Naik Level'],
                    ];
                @endphp
                @foreach ($statItems as $stat)
                    <div class="px-6 py-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Tabel mahasiswa --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            @foreach (['Nama Mahasiswa', 'NIM', 'Level', 'Sub Bahasan', 'Rata Kuis', 'Status'] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $row['nim'] }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700' => $row['level'] === 'pemula',
                                        'bg-yellow-100 text-yellow-700' => $row['level'] === 'menengah',
                                        'bg-red-100 text-red-700' => $row['level'] === 'lanjut',
                                        'bg-gray-100 text-gray-500' => $row['level'] === '-',
                                    ])>{{ ucfirst($row['level']) }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $row['sub_bahasan'] }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $row['rata_kuis'] }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700' => $row['status'] === 'Lulus',
                                        'bg-red-100 text-red-700' => $row['status'] === 'Tidak Lulus',
                                        'bg-gray-100 text-gray-500' => $row['status'] === 'Belum',
                                    ])>{{ $row['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    Belum ada mahasiswa terdaftar di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-filament-panels::page>
