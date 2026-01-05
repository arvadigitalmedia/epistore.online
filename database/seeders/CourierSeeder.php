<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couriers = [
            ['code' => 'jne', 'name' => 'JNE'],
            ['code' => 'pos', 'name' => 'POS Indonesia'],
            ['code' => 'tiki', 'name' => 'TIKI'],
            ['code' => 'rpx', 'name' => 'RPX Holding'],
            ['code' => 'pandu', 'name' => 'Pandu Logistics'],
            ['code' => 'wahana', 'name' => 'Wahana Prestasi Logistik'],
            ['code' => 'sicepat', 'name' => 'SiCepat Ekspres'],
            ['code' => 'jnt', 'name' => 'J&T Express'],
            ['code' => 'pahala', 'name' => 'Pahala Kencana Express'],
            ['code' => 'sap', 'name' => 'SAP Express'],
            ['code' => 'jet', 'name' => 'JET Express'],
            ['code' => 'indah', 'name' => 'Indah Logistik'],
            ['code' => 'dse', 'name' => '21 Express'],
            ['code' => 'slis', 'name' => 'Solusi Ekspres'],
            ['code' => 'first', 'name' => 'First Logistics'],
            ['code' => 'ncs', 'name' => 'NCS'],
            ['code' => 'star', 'name' => 'Star Cargo'],
            ['code' => 'lion', 'name' => 'Lion Parcel'],
            ['code' => 'ninja', 'name' => 'Ninja Xpress'],
            ['code' => 'idl', 'name' => 'IDL Cargo'],
            ['code' => 'rex', 'name' => 'Royal Express Indonesia'],
            ['code' => 'ide', 'name' => 'ID Express'],
            ['code' => 'sentral', 'name' => 'Sentral Cargo'],
            ['code' => 'anteraja', 'name' => 'AnterAja'],
        ];

        foreach ($couriers as $index => $courier) {
            Courier::updateOrCreate(
                ['code' => $courier['code']],
                [
                    'name' => $courier['name'],
                    'priority' => $index + 1,
                    'is_active' => true, // Default active, admin can disable
                ]
            );
        }
    }
}
