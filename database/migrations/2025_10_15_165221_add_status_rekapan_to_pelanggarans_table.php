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
        Schema::table('pelanggarans', function (Blueprint $table) {
            // Status rekapan untuk pelanggaran
            $table->enum('status_rekapan', ['draft', 'selesai'])
                ->default('draft')
                ->after('keterangan');

            // Timestamp saat diselesaikan
            $table->timestamp('diselesaikan_at')
                ->nullable()
                ->after('status_rekapan');

            // Index untuk validasi: 1 pelanggaran selesai per siswa per hari
            $table->index(['siswa_id', 'tanggal', 'status_rekapan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            $table->dropIndex(['siswa_id', 'tanggal', 'status_rekapan']);

            $table->dropColumn([
                'status_rekapan',
                'diselesaikan_at'
            ]);
        });
    }
};
