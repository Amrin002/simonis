<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orangTuaData = [
            [
                'nama_orang_tua' => 'Bambang Suryanto',
                'alamat' => 'Jl. Merdeka No. 12, Jakarta Pusat',
                'nomor_tlp' => '081234567801',
            ],
            [
                'nama_orang_tua' => 'Sri Wahyuni',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta Selatan',
                'nomor_tlp' => '081234567802',
            ],
            [
                'nama_orang_tua' => 'Agus Salim',
                'alamat' => 'Jl. Gatot Subroto No. 78, Jakarta Barat',
                'nomor_tlp' => '081234567803',
            ],
            [
                'nama_orang_tua' => 'Ratna Sari',
                'alamat' => 'Jl. Ahmad Yani No. 23, Jakarta Timur',
                'nomor_tlp' => '081234567804',
            ],
            [
                'nama_orang_tua' => 'Dedi Hermawan',
                'alamat' => 'Jl. Diponegoro No. 56, Jakarta Utara',
                'nomor_tlp' => '081234567805',
            ],
            [
                'nama_orang_tua' => 'Lina Marlina',
                'alamat' => 'Jl. Hayam Wuruk No. 89, Jakarta Pusat',
                'nomor_tlp' => '081234567806',
            ],
            [
                'nama_orang_tua' => 'Hendra Wijaya',
                'alamat' => 'Jl. Thamrin No. 34, Jakarta Pusat',
                'nomor_tlp' => '081234567807',
            ],
            [
                'nama_orang_tua' => 'Maya Anggraini',
                'alamat' => 'Jl. Kuningan No. 67, Jakarta Selatan',
                'nomor_tlp' => '081234567808',
            ],
            [
                'nama_orang_tua' => 'Rudi Hartono',
                'alamat' => 'Jl. Mangga Dua No. 90, Jakarta Utara',
                'nomor_tlp' => '081234567809',
            ],
            [
                'nama_orang_tua' => 'Endang Susilowati',
                'alamat' => 'Jl. Kebon Jeruk No. 15, Jakarta Barat',
                'nomor_tlp' => '081234567810',
            ],
            [
                'nama_orang_tua' => 'Yanto Prasetyo',
                'alamat' => 'Jl. Cempaka Putih No. 28, Jakarta Pusat',
                'nomor_tlp' => '081234567811',
            ],
            [
                'nama_orang_tua' => 'Nurul Hidayah',
                'alamat' => 'Jl. Tebet Raya No. 41, Jakarta Selatan',
                'nomor_tlp' => '081234567812',
            ],
            [
                'nama_orang_tua' => 'Tono Sugiarto',
                'alamat' => 'Jl. Kalimalang No. 73, Jakarta Timur',
                'nomor_tlp' => '081234567813',
            ],
            [
                'nama_orang_tua' => 'Ani Yulianti',
                'alamat' => 'Jl. Pademangan No. 52, Jakarta Utara',
                'nomor_tlp' => '081234567814',
            ],
            [
                'nama_orang_tua' => 'Bambang Irawan',
                'alamat' => 'Jl. Kebayoran No. 36, Jakarta Selatan',
                'nomor_tlp' => '081234567815',
            ],
        ];

        foreach ($orangTuaData as $data) {
            $user = User::where('name', $data['nama_orang_tua'])->first();

            OrangTua::create([
                'nama_orang_tua' => $data['nama_orang_tua'],
                'alamat' => $data['alamat'],
                'nomor_tlp' => $data['nomor_tlp'],
                'user_id' => $user ? $user->id : null,
            ]);
        }
    }
}
