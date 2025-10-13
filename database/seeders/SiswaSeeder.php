<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = Kelas::all();
        $orangTua = OrangTua::all();

        $siswaData = [
            // Kelas 7A
            ['nama' => 'Ahmad Rizki', 'nis' => '2024001', 'kelas_id' => $kelas[0]->id ?? null, 'orang_tua_id' => $orangTua[0]->id ?? null],
            ['nama' => 'Siti Aisyah', 'nis' => '2024002', 'kelas_id' => $kelas[0]->id ?? null, 'orang_tua_id' => $orangTua[1]->id ?? null],
            ['nama' => 'Budi Setiawan', 'nis' => '2024003', 'kelas_id' => $kelas[0]->id ?? null, 'orang_tua_id' => $orangTua[2]->id ?? null],
            ['nama' => 'Rina Wijaya', 'nis' => '2024004', 'kelas_id' => $kelas[0]->id ?? null, 'orang_tua_id' => $orangTua[3]->id ?? null],
            ['nama' => 'Doni Prasetyo', 'nis' => '2024005', 'kelas_id' => $kelas[0]->id ?? null, 'orang_tua_id' => $orangTua[4]->id ?? null],

            // Kelas 7B
            ['nama' => 'Lina Kartika', 'nis' => '2024006', 'kelas_id' => $kelas[1]->id ?? null, 'orang_tua_id' => $orangTua[5]->id ?? null],
            ['nama' => 'Hendra Gunawan', 'nis' => '2024007', 'kelas_id' => $kelas[1]->id ?? null, 'orang_tua_id' => $orangTua[6]->id ?? null],
            ['nama' => 'Maya Putri', 'nis' => '2024008', 'kelas_id' => $kelas[1]->id ?? null, 'orang_tua_id' => $orangTua[7]->id ?? null],
            ['nama' => 'Rudi Hermawan', 'nis' => '2024009', 'kelas_id' => $kelas[1]->id ?? null, 'orang_tua_id' => $orangTua[8]->id ?? null],
            ['nama' => 'Endang Lestari', 'nis' => '2024010', 'kelas_id' => $kelas[1]->id ?? null, 'orang_tua_id' => $orangTua[9]->id ?? null],

            // Kelas 8A
            ['nama' => 'Yanto Kusuma', 'nis' => '2023001', 'kelas_id' => $kelas[2]->id ?? null, 'orang_tua_id' => $orangTua[10]->id ?? null],
            ['nama' => 'Nurul Fadilah', 'nis' => '2023002', 'kelas_id' => $kelas[2]->id ?? null, 'orang_tua_id' => $orangTua[11]->id ?? null],
            ['nama' => 'Tono Santoso', 'nis' => '2023003', 'kelas_id' => $kelas[2]->id ?? null, 'orang_tua_id' => $orangTua[12]->id ?? null],
            ['nama' => 'Ani Rahayu', 'nis' => '2023004', 'kelas_id' => $kelas[2]->id ?? null, 'orang_tua_id' => $orangTua[13]->id ?? null],
            ['nama' => 'Bambang Tri', 'nis' => '2023005', 'kelas_id' => $kelas[2]->id ?? null, 'orang_tua_id' => $orangTua[14]->id ?? null],

            // Kelas 8B
            ['nama' => 'Citra Dewi', 'nis' => '2023006', 'kelas_id' => $kelas[3]->id ?? null, 'orang_tua_id' => $orangTua[0]->id ?? null],
            ['nama' => 'Dimas Adi', 'nis' => '2023007', 'kelas_id' => $kelas[3]->id ?? null, 'orang_tua_id' => $orangTua[1]->id ?? null],
            ['nama' => 'Eka Putri', 'nis' => '2023008', 'kelas_id' => $kelas[3]->id ?? null, 'orang_tua_id' => $orangTua[2]->id ?? null],
            ['nama' => 'Fajar Nugroho', 'nis' => '2023009', 'kelas_id' => $kelas[3]->id ?? null, 'orang_tua_id' => $orangTua[3]->id ?? null],
            ['nama' => 'Gita Sari', 'nis' => '2023010', 'kelas_id' => $kelas[3]->id ?? null, 'orang_tua_id' => $orangTua[4]->id ?? null],

            // Kelas 9A
            ['nama' => 'Hadi Wijaya', 'nis' => '2022001', 'kelas_id' => $kelas[4]->id ?? null, 'orang_tua_id' => $orangTua[5]->id ?? null],
            ['nama' => 'Indah Permata', 'nis' => '2022002', 'kelas_id' => $kelas[4]->id ?? null, 'orang_tua_id' => $orangTua[6]->id ?? null],
            ['nama' => 'Joko Purnomo', 'nis' => '2022003', 'kelas_id' => $kelas[4]->id ?? null, 'orang_tua_id' => $orangTua[7]->id ?? null],
            ['nama' => 'Kartika Sari', 'nis' => '2022004', 'kelas_id' => $kelas[4]->id ?? null, 'orang_tua_id' => $orangTua[8]->id ?? null],
            ['nama' => 'Lukman Hakim', 'nis' => '2022005', 'kelas_id' => $kelas[4]->id ?? null, 'orang_tua_id' => $orangTua[9]->id ?? null],

            // Kelas 9B
            ['nama' => 'Mega Wati', 'nis' => '2022006', 'kelas_id' => $kelas[5]->id ?? null, 'orang_tua_id' => $orangTua[10]->id ?? null],
            ['nama' => 'Nando Pratama', 'nis' => '2022007', 'kelas_id' => $kelas[5]->id ?? null, 'orang_tua_id' => $orangTua[11]->id ?? null],
            ['nama' => 'Oktavia Ningsih', 'nis' => '2022008', 'kelas_id' => $kelas[5]->id ?? null, 'orang_tua_id' => $orangTua[12]->id ?? null],
            ['nama' => 'Putra Ramadhan', 'nis' => '2022009', 'kelas_id' => $kelas[5]->id ?? null, 'orang_tua_id' => $orangTua[13]->id ?? null],
            ['nama' => 'Qori Amalia', 'nis' => '2022010', 'kelas_id' => $kelas[5]->id ?? null, 'orang_tua_id' => $orangTua[14]->id ?? null],
        ];

        foreach ($siswaData as $data) {
            Siswa::create($data);
        }
    }
}
