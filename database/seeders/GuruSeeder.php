<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guruData = [
            [
                'nama_guru' => 'Budi Santoso',
                'nip' => '196501011990031001',
                'is_guru_mapel' => true,
                'is_wali_kelas' => true,
            ],
            [
                'nama_guru' => 'Siti Nurhaliza',
                'nip' => '197203151995122001',
                'is_guru_mapel' => true,
                'is_wali_kelas' => true,
            ],
            [
                'nama_guru' => 'Ahmad Fauzi',
                'nip' => '198005102005011002',
                'is_guru_mapel' => true,
                'is_wali_kelas' => true,
            ],
            [
                'nama_guru' => 'Dewi Lestari',
                'nip' => '198307222008012003',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
            [
                'nama_guru' => 'Eko Prasetyo',
                'nip' => '197912152003121001',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
            [
                'nama_guru' => 'Fitri Handayani',
                'nip' => '198606302010012004',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
            [
                'nama_guru' => 'Gunawan Wijaya',
                'nip' => '197508201999031002',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
            [
                'nama_guru' => 'Hesti Rahayu',
                'nip' => '198209182006042001',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
            [
                'nama_guru' => 'Indra Kusuma',
                'nip' => '197711252002121001',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
            [
                'nama_guru' => 'Joko Susilo',
                'nip' => '198404122009011003',
                'is_guru_mapel' => true,
                'is_wali_kelas' => false,
            ],
        ];

        foreach ($guruData as $index => $data) {
            // Ambil user_id berdasarkan nama (skip admin, mulai dari index 2)
            $user = User::where('name', $data['nama_guru'])->first();

            Guru::create([
                'nama_guru' => $data['nama_guru'],
                'nip' => $data['nip'],
                'is_guru_mapel' => $data['is_guru_mapel'],
                'is_wali_kelas' => $data['is_wali_kelas'],
                'user_id' => $user ? $user->id : null,
            ]);
        }
    }
}
