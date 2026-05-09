<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\LearningProgress;
use App\Models\PostTestResult;
use App\Models\PretestResult;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    // ── CSV (Excel-compatible, UTF-8 BOM) ───────────────────────────

    public function csv(Request $request): StreamedResponse
    {
        $kelas = $this->resolveKelas($request);

        $rows     = $this->buildRows($kelas);
        $filename = 'laporan_' . \Str::slug($kelas->name) . '_' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // BOM supaya Excel baca UTF-8 dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Nama', 'NIM', 'Level', 'Sub Bahasan', 'Rata Kuis', 'Status']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['nim'],
                    $row['level'],
                    $row['sub_bahasan'],
                    $row['rata_kuis'],
                    $row['status'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ── Printable HTML (Ctrl+P → Save as PDF) ───────────────────────

    public function pdf(Request $request)
    {
        $kelas = $this->resolveKelas($request);
        $rows  = $this->buildRows($kelas);

        return view('exports.mahasiswa-pdf', compact('kelas', 'rows'));
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function resolveKelas(Request $request): Kelas
    {
        $kelasId = (int) $request->query('kelas_id');

        $kelas = Kelas::where('id', $kelasId)
            ->where('dosen_id', Auth::id())
            ->firstOrFail();

        return $kelas;
    }

    private function buildRows(Kelas $kelas): array
    {
        $mahasiswa = $kelas->mahasiswa()->orderBy('name')->get();
        $rows = [];

        foreach ($mahasiswa as $mhs) {
            $progress  = LearningProgress::where('user_id', $mhs->id)->get();
            $completed = $progress->where('status', 'completed')->count();
            $total     = $progress->count();

            $avgQuiz      = round(QuizResult::where('user_id', $mhs->id)->avg('score') ?? 0);
            $lastPostTest = PostTestResult::where('user_id', $mhs->id)
                ->orderByDesc('completed_at')
                ->first();
            $level = PretestResult::where('user_id', $mhs->id)
                ->latest()
                ->value('level') ?? '-';

            $rows[] = [
                'name'        => $mhs->name,
                'nim'         => $mhs->nim ?? '-',
                'level'       => ucfirst($level),
                'sub_bahasan' => "{$completed}/{$total}",
                'rata_kuis'   => $avgQuiz,
                'status'      => $lastPostTest?->passed
                    ? 'Lulus'
                    : ($lastPostTest ? 'Tidak Lulus' : 'Belum'),
            ];
        }

        return $rows;
    }
}
