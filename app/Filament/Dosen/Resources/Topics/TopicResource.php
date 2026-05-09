<?php

namespace App\Filament\Dosen\Resources\Topics;

use App\Filament\Dosen\Resources\Topics\Pages\CreateTopic;
use App\Filament\Dosen\Resources\Topics\Pages\EditTopic;
use App\Filament\Dosen\Resources\Topics\Pages\ListTopics;
use App\Filament\Dosen\Resources\Topics\Pages\ViewTopic;
use App\Filament\Dosen\Resources\Topics\RelationManagers\QuestionsRelationManager;
use App\Filament\Dosen\Resources\Topics\Schemas\TopicForm;
use App\Filament\Dosen\Resources\Topics\Schemas\TopicInfolist;
use App\Filament\Dosen\Resources\Topics\Tables\TopicsTable;
use App\Models\Topic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TopicResource extends Resource
{
    protected static ?string $model = Topic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?string $navigationLabel = 'Kelola Materi';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Pembelajaran';

    protected static ?string $modelLabel = 'Materi';

    protected static ?string $pluralModelLabel = 'Daftar Materi';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return TopicForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TopicInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TopicsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTopics::route('/'),
            'create' => CreateTopic::route('/create'),
            'view' => ViewTopic::route('/{record}'),
            'edit' => EditTopic::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
