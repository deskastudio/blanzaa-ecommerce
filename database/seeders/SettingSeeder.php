<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'ElectroShop',
                'type' => 'string',
                'description' => 'Website name',
            ],
            [
                'key' => 'site_description',
                'value' => 'Toko Elektronik Terpercaya',
                'type' => 'string',
                'description' => 'Website description',
            ],
            [
                'key' => 'currency',
                'value' => 'IDR',
                'type' => 'string',
                'description' => 'Default currency',
            ],
            [
                'key' => 'tax_rate',
                'value' => '10',
                'type' => 'number',
                'description' => 'Tax rate percentage',
            ],
            [
                'key' => 'shipping_cost',
                'value' => '15000',
                'type' => 'number',
                'description' => 'Default shipping cost',
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@electroshop.com',
                'type' => 'string',
                'description' => 'Contact email',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62-21-1234567',
                'type' => 'string',
                'description' => 'Contact phone',
            ],
            [
                'key' => 'bank_account',
                'value' => 'BCA: 1234567890 a.n. ElectroShop',
                'type' => 'text',
                'description' => 'Bank account for payment',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}