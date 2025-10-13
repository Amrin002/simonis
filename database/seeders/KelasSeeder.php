<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil guru yang bisa jadi wali kelas
        $waliKelasGurus = Guru::where('is_wali_kelas', true)->get();

        $kelasData = [
            ['nama' => '7A', 'wali_guru_id' => $waliKelasGurus[0]->id ?? null],
            ['nama' => '7B', 'wali_guru_id' => $waliKelasGurus[1]->id ?? null],
            ['nama' => '8A', 'wali_guru_id' => $waliKelasGurus[2]->id ?? null],
            ['nama' => '8B', 'wali_guru_id' => null],
            ['nama' => '9A', 'wali_guru_id' => null],
            ['nama' => '9B', 'wali_guru_id' => null],
        ];

        foreach ($kelasData as $data) {
            Kelas::create($data);
        }
    }
}
