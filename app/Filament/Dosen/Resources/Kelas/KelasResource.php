<?php

namespace App\Filament\Dosen\Resources\Kelas;

use App\Filament\Dosen\Resources\Kelas\Pages\CreateKelas;
use App\Filament\Dosen\Resources\Kelas\Pages\EditKelas;
use App\Filament\Dosen\Resources\Kelas\Pages\ListKelas;
use App\Filament\Dosen\Resources\Kelas\Pages\ViewKelas;
use App\Filament\Dosen\Resources\Kelas\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Dosen\Resources\Kelas\Schemas\KelasForm;
use App\Filament\Dosen\Resources\Kelas\Schemas\KelasInfolist;
use App\Filament\Dosen\Resources\Kelas\Tables\KelasTable;
use App\Models\Kelas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Kelola Kelas';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Daftar Kelas';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    // Dosen hanya melihat kelas miliknya sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('dosen_id', Auth::id())
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Schema $schema): Schema
    {
        return KelasForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KelasInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KelasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKelas::route('/'),
            'create' => CreateKelas::route('/create'),
            'view'   => ViewKelas::route('/{record}'),
            'edit'   => EditKelas::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
