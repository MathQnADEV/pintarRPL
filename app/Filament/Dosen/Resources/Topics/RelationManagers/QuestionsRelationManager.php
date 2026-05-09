<?php

namespace App\Filament\Dosen\Resources\Topics\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Soal Kuis Sub Bahasan Ini';

    /**
     * Hanya tampilkan soal kuis (bukan pretest/posttest) untuk topik ini.
     */
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // Soal yang ditambah dari sini selalu merupakan soal kuis
                Hidden::make('is_pretest')->default(false),
                Hidden::make('is_posttest')->default(false),

                Textarea::make('question_text')
                    ->label('Teks Pertanyaan')
                    ->placeholder('Tuliskan pertanyaan di sini...')
                    ->required()
                    ->rows(3),

                Textarea::make('explanation')
                    ->label('Penjelasan Jawaban')
                    ->placeholder('Jelaskan mengapa jawaban tersebut benar (ditampilkan setelah mahasiswa menjawab)')
                    ->rows(2),

                Repeater::make('options')
                    ->label('Pilihan Jawaban')
                    ->relationship('options')
                    ->schema([
                        TextInput::make('option_text')
                            ->label('Teks Pilihan')
                            ->placeholder('contoh: O(n log n)')
                            ->required(),
                        Toggle::make('is_correct')
                            ->label('Jawaban Benar'),
                    ])
                    ->columns(2)
                    ->minItems(2)
                    ->maxItems(5)
                    ->defaultItems(4)
                    ->addActionLabel('+ Tambah Pilihan')
                    ->helperText('Tandai tepat satu pilihan sebagai jawaban benar.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // Hanya tampilkan soal kuis (bukan pretest maupun posttest)
            ->modifyQueryUsing(
                fn ($query) => $query->where('is_pretest', false)->where('is_posttest', false)
            )
            ->columns([
                TextColumn::make('question_text')
                    ->label('Pertanyaan')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('options_count')
                    ->label('Pilihan')
                    ->counts('options')
                    ->badge()
                    ->color('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Soal Kuis')
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
