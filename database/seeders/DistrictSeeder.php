<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [1, 1, 'GAZIPUR', ''],
            [2, 5, 'HABIGANJ', ''],
            [3, 7, 'NOAKHALI', ''],
            [4, 6, 'PATUAKHALI', ''],
            [5, 4, 'Jashore', ''],
            [6, 2, 'Joypurhat', ''],
            [7, 3, 'Lalmonirhat', ''],
            [8, 2, 'Bogura', ''],
            [9, 1, 'Dhaka', ''],
            [10, 1, 'Faridpur', ''],
            [11, 1, 'Gopalganj', ''],
            [12, 8, 'Jamalpur', '21'],
            [13, 1, 'Kishoreganj', ''],
            [14, 1, 'Madaripur', ''],
            [15, 1, 'Manikganj', ''],
            [16, 1, 'Munshiganj', ''],
            [17, 8, 'Mymensingh', '39'],
            [18, 1, 'Narayanganj', ''],
            [19, 1, 'Narsingdi', ''],
            [20, 8, 'Netrokona', ''],
            [21, 1, 'Rajbari', ''],
            [22, 1, 'Shariatpur', ''],
            [23, 8, 'Sherpur', '38'],
            [24, 1, 'Tangail', ''],
            [25, 2, 'Natore', ''],
            [26, 2, 'Naogaon', ''],
            [27, 2, 'Pabna', ''],
            [28, 2, 'Chapinawabganj', '1'],
            [29, 2, 'Sirajganj', ''],
            [30, 2, 'Rajshahi', ''],
            [31, 3, 'Dinajpur', ''],
            [32, 3, 'Gaibandha', ''],
            [33, 3, 'Kurigram', ''],
            [34, 3, 'Nilphamari', ''],
            [35, 3, 'Panchagarh', ''],
            [36, 3, 'Rangpur', ''],
            [37, 3, 'Thakurgaon', ''],
            [38, 6, 'Barguna', ''],
            [39, 6, 'Barishal', ''],
            [40, 6, 'Bhola', ''],
            [41, 6, 'Jhalokati', ''],
            [42, 6, 'Pirojpur', ''],
            [43, 7, 'Bandarban', ''],
            [44, 7, 'Brahmanbaria', ''],
            [45, 7, 'Chandpur', ''],
            [46, 7, 'Chattogram', ''],
            [47, 7, 'Cumilla', ''],
            [48, 7, 'Cox s Bazar', ''],
            [49, 7, 'Feni', ''],
            [50, 7, 'Khagrachari', ''],
            [51, 7, 'Lakshmipur', ''],
            [52, 7, 'Rangamati', ''],
            [53, 5, 'Maulvibazar', ''],
            [54, 5, 'Sunamganj', ''],
            [55, 5, 'Sylhet', ''],
            [56, 4, 'Bagerhat', ''],
            [57, 4, 'Jhenaidah', ''],
            [58, 4, 'Khulna', ''],
            [59, 4, 'Kushtia', ''],
            [60, 4, 'Magura', ''],
            [61, 4, 'Meherpur', ''],
            [62, 4, 'Narail', ''],
            [63, 4, 'Satkhira', ''],
            [64, 4, 'Chuadanga', ''],
        ];

        // =====================================================
        //  Insert Or Update District Records
        // =====================================================
        foreach ($districts as $district) {
            DB::table('districts')->updateOrInsert(
                ['id' => $district[0]],
                [
                    'division_id'  => $district[1],
                    'district_name' => $district[2],
                    'district_code' => $district[3],
                    'status'        => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
