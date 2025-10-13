<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapelData = [
            [
                'nama_matapelajaran' => 'Matematika',
                'kode_mapel' => 'MTK',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang angka, pola, dan logika',
            ],
            [
                'nama_matapelajaran' => 'Bahasa Indonesia',
                'kode_mapel' => 'IND',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang bahasa dan sastra Indonesia',
            ],
            [
                'nama_matapelajaran' => 'Bahasa Inggris',
                'kode_mapel' => 'ING',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang bahasa Inggris',
            ],
            [
                'nama_matapelajaran' => 'Ilmu Pengetahuan Alam',
                'kode_mapel' => 'IPA',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang fenomena alam dan lingkungan',
            ],
            [
                'nama_matapelajaran' => 'Ilmu Pengetahuan Sosial',
                'kode_mapel' => 'IPS',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang masyarakat dan lingkungan sosial',
            ],
            [
                'nama_matapelajaran' => 'Pendidikan Agama Islam',
                'kode_mapel' => 'PAI',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang ajaran agama Islam',
            ],
            [
                'nama_matapelajaran' => 'Pendidikan Kewarganegaraan',
                'kode_mapel' => 'PKN',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang kewarganegaraan dan kehidupan berbangsa',
            ],
            [
                'nama_matapelajaran' => 'Seni Budaya',
                'kode_mapel' => 'SBK',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang seni dan budaya',
            ],
            [
                'nama_matapelajaran' => 'Pendidikan Jasmani',
                'kode_mapel' => 'PJOK',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang olahraga dan kesehatan',
            ],
            [
                'nama_matapelajaran' => 'Prakarya',
                'kode_mapel' => 'PKY',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang keterampilan dan kreativitas',
            ],
            [
                'nama_matapelajaran' => 'Teknologi Informasi',
                'kode_mapel' => 'TIK',
                'deskripsi' => 'Mata pelajaran yang mempelajari tentang komputer dan teknologi informasi',
            ],
        ];

        foreach ($mapelData as $data) {
            Mapel::create($data);
        }
    }
}
