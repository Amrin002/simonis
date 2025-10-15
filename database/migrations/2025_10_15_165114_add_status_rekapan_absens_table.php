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
        Schema::table('absens', function (Blueprint $table) {
            // Status rekapan untuk workflow absensi
            $table->enum('status_rekapan', ['draft', 'dikirim', 'selesai'])
                ->default('draft')
                ->after('presentase_kehadiran');

            // Timestamp untuk tracking workflow
            $table->timestamp('dikirim_at')->nullable()->after('status_rekapan');
            $table->timestamp('diselesaikan_at')->nullable()->after('dikirim_at');

            // Guru yang menyelesaikan (wali kelas)
            $table->foreignId('diselesaikan_oleh')
                ->nullable()
                ->after('diselesaikan_at')
                ->constrained('gurus')
                ->onDelete('set null');

            // Index untuk performa query
            $table->index(['tanggal', 'status_rekapan']);
            $table->index('diselesaikan_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->dropForeign(['diselesaikan_oleh']);
            $table->dropIndex(['tanggal', 'status_rekapan']);
            $table->dropIndex(['diselesaikan_oleh']);

            $table->dropColumn([
                'status_rekapan',
                'dikirim_at',
                'diselesaikan_at',
                'diselesaikan_oleh'
            ]);
        });
    }
};
