<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mahasiswa — {{ $kelas->mata_kuliah }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #fff;
            padding: 32px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #d97706;
        }
        .header-title h1 {
            font-size: 20px;
            font-weight: 700;
            color: #d97706;
        }
        .header-title p {
            margin-top: 4px;
            color: #555;
            font-size: 12px;
        }
        .header-meta {
            text-align: right;
            font-size: 12px;
            color: #555;
        }
        .header-meta strong {
            display: block;
            color: #1a1a1a;
        }

        /* ── Info Kelas ── */
        .kelas-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
            padding: 14px 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
        }
        .kelas-info dt {
            font-size: 11px;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }
        .kelas-info dd {
            font-weight: 600;
            color: #1a1a1a;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        thead tr {
            background: #d97706;
            color: #fff;
        }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        thead th.center { text-align: center; }

        tbody tr:nth-child(odd)  { background: #fff; }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody tr:hover           { background: #fffbeb; }

        tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        tbody td.center { text-align: center; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-lulus     { background: #dcfce7; color: #166534; }
        .badge-tidak     { background: #fee2e2; color: #991b1b; }
        .badge-belum     { background: #f3f4f6; color: #6b7280; }
        .badge-pemula    { background: #dbeafe; color: #1e40af; }
        .badge-menengah  { background: #fef9c3; color: #854d0e; }
        .badge-lanjut    { background: #fee2e2; color: #991b1b; }
        .badge-default   { background: #f3f4f6; color: #6b7280; }

        /* ── Footer ── */
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #9ca3af;
        }

        /* ── Print button (hidden when printing) ── */
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #d97706;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .print-btn:hover { background: #b45309; }

        @media print {
            body { padding: 16px; }
            .print-btn { display: none !important; }
            .kelas-info { break-inside: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">
        🖨 Cetak / Simpan sebagai PDF
    </button>

    {{-- Header --}}
    <div class="header">
        <div class="header-title">
            <h1>PINTAR — Laporan Mahasiswa</h1>
            <p>{{ $kelas->mata_kuliah }} &bull; {{ $kelas->name }} &bull; {{ $kelas->program_studi }}</p>
        </div>
        <div class="header-meta">
            <strong>Tanggal Cetak</strong>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- Info Kelas --}}
    <dl class="kelas-info">
        <div>
            <dt>Mata Kuliah</dt>
            <dd>{{ $kelas->mata_kuliah }}</dd>
        </div>
        <div>
            <dt>Kelas</dt>
            <dd>{{ $kelas->name }}</dd>
        </div>
        <div>
            <dt>Program Studi</dt>
            <dd>{{ $kelas->program_studi }}</dd>
        </div>
        <div>
            <dt>Tahun Akademik</dt>
            <dd>{{ $kelas->academic_year }}</dd>
        </div>
        <div>
            <dt>Total Mahasiswa</dt>
            <dd>{{ count($rows) }} orang</dd>
        </div>
        <div>
            <dt>Lulus Post-test</dt>
            <dd>{{ collect($rows)->where('status', 'Lulus')->count() }} orang</dd>
        </div>
    </dl>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th>Nama Mahasiswa</th>
                <th>NIM</th>
                <th class="center">Level</th>
                <th class="center">Sub Bahasan</th>
                <th class="center">Rata Kuis</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $row['name'] }}</strong></td>
                    <td>{{ $row['nim'] }}</td>
                    <td class="center">
                        @php
                            $lvl = strtolower($row['level']);
                            $badgeClass = match($lvl) {
                                'pemula'   => 'badge-pemula',
                                'menengah' => 'badge-menengah',
                                'lanjut'   => 'badge-lanjut',
                                default    => 'badge-default',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $row['level'] }}</span>
                    </td>
                    <td class="center">{{ $row['sub_bahasan'] }}</td>
                    <td class="center">{{ $row['rata_kuis'] }}</td>
                    <td class="center">
                        @php
                            $statusClass = match($row['status']) {
                                'Lulus'       => 'badge-lulus',
                                'Tidak Lulus' => 'badge-tidak',
                                default       => 'badge-belum',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $row['status'] }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:24px; color:#9ca3af;">
                        Belum ada data mahasiswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <span>Dicetak dari sistem PINTAR &bull; {{ config('app.url') }}</span>
        <span>{{ now()->format('d/m/Y H:i') }}</span>
    </div>

</body>
</html>
