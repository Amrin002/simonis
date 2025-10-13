<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->string('jenis_pelanggaran');
            $table->enum('kategori', ['Ringan', 'Sedang', 'Berat']);
            $table->date('tanggal');
            $table->foreignId('wali_kelas_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Indexes untuk performa query
            $table->index(['siswa_id', 'tanggal']);
            $table->index('wali_kelas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggarans');
    }
};
