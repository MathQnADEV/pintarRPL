<?php

namespace App\Filament\Dosen\Resources\Questions\Schemas;

use App\Models\Question;
use App\Models\Topic;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                    ->dehydrated(false)
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
                    ->afterStateHydrated(function (ToggleButtons $component, ?Question $record): void {
                        if (! $record) {
                            $component->state('kuis');
                            return;
                        }
                        $component->state($record->question_category);
                    })
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

                // ── Hidden flags ─────────────────────────────────────────────

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

                // ── Step 3: Tipe jawaban ─────────────────────────────────────

                Select::make('type')
                    ->label('Format Jawaban')
                    ->options([
                        'multiple_choice' => '🔘 Pilihan Ganda',
                        'code_arrange'    => '🧩 Susun Kode (Duolingo-style)',
                    ])
                    ->default('multiple_choice')
                    ->required()
                    ->native(false)
                    ->live()
                    ->helperText(
                        'Pilihan Ganda: mahasiswa memilih satu opsi yang benar. · '.
                        'Susun Kode: mahasiswa menyusun baris/token kode ke urutan yang tepat.'
                    ),

                // ── Step 4: Teks pertanyaan ──────────────────────────────────

                RichEditor::make('question_text')
                    ->label('Teks Pertanyaan')
                    ->helperText('Gunakan tombol <> untuk menyisipkan blok kode sebagai konteks soal.')
                    ->required()
                    ->toolbarButtons([
                        'bold', 'italic',
                        'bulletList', 'orderedList',
                        'codeBlock',
                    ])
                    ->columnSpanFull(),

                RichEditor::make('explanation')
                    ->label('Penjelasan Jawaban (opsional)')
                    ->helperText('Ditampilkan setelah mahasiswa selesai kuis/post-test.')
                    ->toolbarButtons(['bold', 'italic', 'codeBlock'])
                    ->columnSpanFull(),

                // ── Step 5: Opsi jawaban / baris kode ───────────────────────

                Repeater::make('options')
                    ->label(fn (Get $get): string =>
                        $get('type') === 'code_arrange'
                            ? '🧩 Baris / Token Kode (isi setiap baris + nomor urut yang benar)'
                            : '🔘 Pilihan Jawaban'
                    )
                    ->relationship('options')
                    ->schema([
                        TextInput::make('option_text')
                            ->label(fn (Get $get): string =>
                                $get('../../type') === 'code_arrange' ? 'Baris / Token Kode' : 'Teks Pilihan'
                            )
                            ->placeholder(fn (Get $get): string =>
                                $get('../../type') === 'code_arrange'
                                    ? 'contoh: cout << "Hello, World!";'
                                    : 'contoh: O(n²)'
                            )
                            ->required()
                            ->columnSpanFull(),

                        // ── Hanya untuk multiple_choice ──────────────────────
                        Toggle::make('is_correct')
                            ->label('Jawaban Benar')
                            ->visible(fn (Get $get): bool => $get('../../type') !== 'code_arrange'),

                        // ── Hanya untuk code_arrange ─────────────────────────
                        TextInput::make('order')
                            ->label('Nomor Urut')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0, 1, 2, 3 …')
                            ->helperText('0 = pengecoh (muncul tapi bukan jawaban) · 1, 2, 3… = urutan benar')
                            ->visible(fn (Get $get): bool => $get('../../type') === 'code_arrange'),
                    ])
                    ->columns(2)
                    ->minItems(2)
                    ->maxItems(10)
                    ->defaultItems(fn (Get $get): int => $get('type') === 'code_arrange' ? 4 : 4)
                    ->addActionLabel(fn (Get $get): string =>
                        $get('type') === 'code_arrange' ? '+ Tambah Baris Kode' : '+ Tambah Pilihan'
                    )
                    ->helperText(fn (Get $get): string =>
                        $get('type') === 'code_arrange'
                            ? 'Masukkan setiap baris/token kode lalu beri nomor urut yang benar (1, 2, 3…). Mahasiswa akan menyusunnya secara acak.'
                            : 'Tandai tepat satu pilihan sebagai jawaban benar.'
                    )
                    ->columnSpanFull(),
            ]);
    }
}
