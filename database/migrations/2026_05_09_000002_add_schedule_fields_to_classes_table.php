<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom jadwal & informasi akademik ke tabel classes.
 *
 * Kolom-kolom ini dibutuhkan untuk tampilan kartu kelas di dashboard dosen
 * sesuai SRS: "Senin 08.00 · AE-201" dan "Semester 2 · 4 SKS".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('schedule', 60)->nullable()->after('academic_year');   // contoh: Senin, 08.00-09.40
            $table->string('ruangan', 50)->nullable()->after('schedule');         // contoh: AE-201
            $table->unsignedTinyInteger('semester')->nullable()->after('ruangan'); // contoh: 2
            $table->unsignedTinyInteger('sks')->nullable()->after('semester');    // contoh: 4
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['schedule', 'ruangan', 'semester', 'sks']);
        });
    }
};
