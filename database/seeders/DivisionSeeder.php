<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['id' => 1, 'division_name' => 'Dhaka',      'division_code' => '3'],
            ['id' => 2, 'division_name' => 'Rajshahi',   'division_code' => '5'],
            ['id' => 3, 'division_name' => 'Rangpur',    'division_code' => '6'],
            ['id' => 4, 'division_name' => 'Khulna',     'division_code' => '4'],
            ['id' => 5, 'division_name' => 'Sylhet',     'division_code' => '7'],
            ['id' => 6, 'division_name' => 'Barishal',   'division_code' => '1'],
            ['id' => 7, 'division_name' => 'Chattogram', 'division_code' => '2'],
            ['id' => 8, 'division_name' => 'Mymensingh', 'division_code' => '8'],
        ];

        foreach ($divisions as $division) {
            DB::table('divisions')->updateOrInsert(
                ['id' => $division['id']],
                [
                    'division_name' => $division['division_name'],
                    'division_code' => $division['division_code'],
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
