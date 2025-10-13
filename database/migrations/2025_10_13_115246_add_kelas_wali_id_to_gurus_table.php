<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->foreignId('kelas_wali_id')
                ->nullable()
                ->after('is_wali_kelas')
                ->constrained('kelas')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropForeign(['kelas_wali_id']);
            $table->dropColumn('kelas_wali_id');
        });
    }
};
