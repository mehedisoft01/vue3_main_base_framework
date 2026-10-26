<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();

        $users = [
            [
                'key'=>'app_name',
                'type'=>'text',
                'setting_type'=>'general_setting',
                'value'=>'VUE 3 MAIN BASE',
                'is_visible'=>1,
            ],
            [
                'key'=>'app_logo',
                'type'=>'file',
                'setting_type'=>'general_setting',
                'value'=>null,
                'is_visible'=>1,
            ],
            [
                'key'=>'notify_per_minuit',
                'type'=>'number',
                'setting_type'=>'notification_setting',
                'value'=>1,
                'is_visible'=>1,
            ],
            [
                'key' => 'company_name',
                'type' => 'text',
                'setting_type' => 'company_information',
                'value' => 'TMSS ICT LTD.',
                'is_visible' => 1,
            ],
            [
                'key' => 'company_address',
                'type' => 'text',
                'setting_type' => 'company_information',
                'value' => 'West Kazipara,Mirpur-10 Dhaka-1216, Bangladesh.',
                'is_visible' => 1,
            ],
            [
                'key' => 'company_city',
                'type' => 'text',
                'setting_type' => 'company_information',
                'value' => 'Dhaka-1216',
                'is_visible' => 1,
            ],
            [
                'key'=>'company_phone',
                'type'=>'text',
                'setting_type'=>'company_information',
                'value' => '01324-246691, 01324-246692, 01324-246693',
                'is_visible'=>1,
            ],
            [
                'key' => 'company_email',
                'type' => 'text',
                'setting_type' => 'company_information',
                'value' => 'gengzz@163.com',
                'is_visible' => 1,
            ],
            [
                'key' => 'company_website',
                'type' => 'text',
                'setting_type' => 'company_information',
                'value' => 'www.tmss-ict.com',
                'is_visible' => 1,
            ],

        ];

        Setting::insert($users);
    }
}
