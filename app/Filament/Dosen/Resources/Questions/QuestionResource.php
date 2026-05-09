<?php

namespace App\Filament\Dosen\Resources\Questions;

use App\Filament\Dosen\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Dosen\Resources\Questions\Pages\EditQuestion;
use App\Filament\Dosen\Resources\Questions\Pages\ListQuestions;
use App\Filament\Dosen\Resources\Questions\Pages\ViewQuestion;
use App\Filament\Dosen\Resources\Questions\RelationManagers\OptionsRelationManager;
use App\Filament\Dosen\Resources\Questions\Schemas\QuestionForm;
use App\Filament\Dosen\Resources\Questions\Schemas\QuestionInfolist;
use App\Filament\Dosen\Resources\Questions\Tables\QuestionsTable;
use App\Models\Question;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QuestionMarkCircle;

    protected static ?string $navigationLabel = 'Bank Soal';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Pembelajaran';

    protected static ?string $modelLabel = 'Soal';

    protected static ?string $pluralModelLabel = 'Bank Soal';

    protected static ?string $recordTitleAttribute = 'question_text';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return QuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'view' => ViewQuestion::route('/{record}'),
            'edit' => EditQuestion::route('/{record}/edit'),
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
