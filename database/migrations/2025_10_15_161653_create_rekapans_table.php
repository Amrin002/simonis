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
        Schema::create('rekapans', function (Blueprint $table) {
            $table->id();

            // Foreign key ke siswa
            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->onDelete('cascade');

            // BARU: Tanggal rekapan (untuk rekapan harian)
            $table->date('tanggal');


            $table->text('kehadiran')->nullable();
            $table->text('perilaku')->nullable();

            // BARU: Status kirim ke orang tua
            $table->enum('status_kirim', ['belum_dikirim', 'dikirim', 'gagal'])
                ->default('belum_dikirim');
            $table->timestamp('dikirim_at')->nullable();
            $table->text('catatan_pengiriman')->nullable();
            $table->text('wa_link')->nullable();

            $table->timestamps();
            $table->unique(['siswa_id', 'tanggal']);
            $table->index(['tanggal', 'status_kirim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekapans');
    }
};
