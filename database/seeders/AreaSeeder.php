<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('area')->insert([
            [
                'nama_area' => 'Kebun Hidroponik',
                'lokasi' => 'Jl. Titi Bumi Asri, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55293',
                'keterangan' => 'Lantai 3',
            ],
        ]);
    }
}
