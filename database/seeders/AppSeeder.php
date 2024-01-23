<?php

namespace Database\Seeders;

use App\Models\Applications;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Applications::create([
            'password'=>'$2y$12$uEQlnjlR.c96fWnCsxo6e.uWJPe0V3pch5.o0pgHshsbdl6WjrRxW',
            'kid_id'=>1,
            'class_id'=>1,
            'unlock'=>1,
            'exam_points'=>Null,
            'certificate_points'=>Null,
            'bonus_points'=>Null,
            'language_id'=>1,
            'priority'=>1,
            'status_id'=>2,
            'info1'=>1,
            'info2'=>0,
            'info3'=>0,
        ]);
        Applications::create([
            'password'=>'$2y$12$uEQlnjlR.c96fWnCsxo6e.uWJPe0V3pch5.o0pgHshsbdl6WjrRxW',
            'kid_id'=>2,
            'class_id'=>1,
            'unlock'=>1,
            'exam_points'=>Null,
            'certificate_points'=>Null,
            'bonus_points'=>Null,
            'language_id'=>3,
            'priority'=>0,
            'status_id'=>2,
            'info1'=>1,
            'info2'=>0,
            'info3'=>0,
        ]);
    }
}
