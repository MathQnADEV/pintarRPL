<?php

namespace App\Filament\Dosen\Resources\Questions\Schemas;

use App\Models\Question;
use App\Models\Topic;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                // ── Step 1: Pilih tipe soal ──────────────────────────────────

                ToggleButtons::make('question_category')
                    ->label('Tipe Soal')
                    ->helperText(
                        'Pre-test: menentukan level awal mahasiswa · '.
                        'Kuis: latihan per sub bahasan · '.
                        'Post-test: syarat naik ke level berikutnya'
                    )
                    ->dehydrated(false)   // virtual — tidak disimpan ke DB
                    ->options([
                        'pretest'  => 'Soal Pre-test',
                        'kuis'     => 'Soal Kuis',
                        'posttest' => 'Soal Post-test',
                    ])
                    ->icons([
                        'pretest'  => 'heroicon-o-academic-cap',
                        'kuis'     => 'heroicon-o-pencil-square',
                        'posttest' => 'heroicon-o-trophy',
                    ])
                    ->colors([
                        'pretest'  => 'warning',
                        'kuis'     => 'info',
                        'posttest' => 'success',
                    ])
                    ->inline()
                    ->grouped()
                    ->required()
                    ->live()
                    ->default('kuis')
                    // Saat record dimuat, turunkan kategori dari flag boolean di DB
                    ->afterStateHydrated(function (ToggleButtons $component, ?Question $record): void {
                        if (! $record) {
                            $component->state('kuis');
                            return;
                        }
                        $component->state($record->question_category);
                    })
                    // Saat kategori berubah, setel flag boolean & reset field terkait
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('is_pretest', $state === 'pretest');
                        $set('is_posttest', $state === 'posttest');

                        if ($state !== 'kuis') {
                            $set('topic_id', null);
                        }
                        if ($state !== 'posttest') {
                            $set('level', null);
                        }
                    })
                    ->columnSpanFull(),

                // ── Hidden flags (persisted ke DB) ───────────────────────────

                Hidden::make('is_pretest')->default(false),
                Hidden::make('is_posttest')->default(false),

                // ── Step 2a: Kuis → pilih sub bahasan ───────────────────────

                Select::make('topic_id')
                    ->label('Sub Bahasan / Topik')
                    ->relationship('topic', 'title')
                    ->getOptionLabelFromRecordUsing(
                        fn (Topic $record): string =>
                            "[{$record->level}] {$record->title}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(fn (Get $get): bool => $get('question_category') === 'kuis')
                    ->visible(fn (Get $get): bool => $get('question_category') === 'kuis')
                    ->helperText('Soal kuis wajib dikaitkan ke sub bahasan tertentu.')
                    ->columnSpanFull(),

                // ── Step 2b: Post-test → pilih level ────────────────────────

                Select::make('level')
                    ->label('Level Post-test')
                    ->options([
                        'pemula'   => 'Pemula',
                        'menengah' => 'Menengah',
                        'lanjut'   => 'Lanjut',
                    ])
                    ->native(false)
                    ->required(fn (Get $get): bool => $get('question_category') === 'posttest')
                    ->visible(fn (Get $get): bool => $get('question_category') === 'posttest')
                    ->helperText('Soal post-test digunakan untuk menentukan apakah mahasiswa layak naik dari level ini.')
                    ->columnSpanFull(),

                // ── Step 3: Konten soal ──────────────────────────────────────

                Select::make('type')
                    ->label('Tipe Jawaban')
                    ->options(['multiple_choice' => 'Pilihan Ganda'])
                    ->default('multiple_choice')
                    ->required()
                    ->native(false),

                // spacer untuk kolom kanan agar tipe tidak melebar penuh
                Textarea::make('question_text')
                    ->label('Teks Pertanyaan')
                    ->placeholder('Tuliskan pertanyaan di sini...')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('explanation')
                    ->label('Penjelasan Jawaban')
                    ->placeholder('Jelaskan mengapa jawaban tersebut benar — ditampilkan ke mahasiswa setelah menjawab.')
                    ->rows(3)
                    ->columnSpanFull(),

                // ── Step 4: Pilihan jawaban ──────────────────────────────────

                Repeater::make('options')
                    ->label('Pilihan Jawaban')
                    ->relationship('options')
                    ->schema([
                        TextInput::make('option_text')
                            ->label('Teks Pilihan')
                            ->placeholder('contoh: O(n²)')
                            ->required(),
                        Toggle::make('is_correct')
                            ->label('Jawaban Benar'),
                    ])
                    ->columns(2)
                    ->minItems(2)
                    ->maxItems(5)
                    ->defaultItems(4)
                    ->addActionLabel('+ Tambah Pilihan')
                    ->helperText('Tandai tepat satu pilihan sebagai jawaban benar.')
                    ->columnSpanFull(),
            ]);
    }
}
