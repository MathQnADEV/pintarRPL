<?php

namespace App\Filament\Dosen\Pages;

use App\Filament\Dosen\Widgets\KelasCardWidget;
use App\Filament\Dosen\Widgets\KelasStatsWidget;
use App\Filament\Dosen\Widgets\MahasiswaTableWidget;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class MonitoringDashboard extends Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?string $navigationLabel = 'Dashboard Monitoring';

    protected static ?string $title = 'Dashboard Monitoring';

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            KelasCardWidget::class,    // Kartu kelas — klik untuk pilih
            KelasStatsWidget::class,   // Ringkasan kelas terpilih + action buttons
            MahasiswaTableWidget::class, // Tabel ringkasan mahasiswa
        ];
    }

    public function getColumns(): int
    {
        return 1;
    }
}
