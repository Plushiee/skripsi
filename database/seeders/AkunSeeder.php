<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('akun')->insert([
            [
                'email' => 'barasatyaradi@gmail.com',
                'password' => bcrypt('password'),
                'nama' => 'Nikolaus Pastika',
                'role' => 'admin-master',
                'hari' => json_encode(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']),
                'jam' => json_encode(['s' => '10:30', 'e' => '17:30']),
                'fakultas' => 'Teknologi Informasi',
                'prodi' => 'Sistem Informasi',
                'semester' => '8',
                'foto' => '',
                'nomor_telepon' => '81289934392',
            ]
        ]);
    }
}
