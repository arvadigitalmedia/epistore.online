<?php

namespace Database\Seeders;

use App\Models\Distributor;
use Illuminate\Database\Seeder;

class JakartaDistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Distributor::where('subdomain', 'jakarta')->exists()) {
            Distributor::create([
                'name' => 'EPI Store Jakarta',
                'code' => 'JKT001',
                'subdomain' => 'jakarta',
                'address' => 'Jl. Jend. Sudirman No. 1, Jakarta Pusat',
                'phone' => '021-12345678',
                'email' => 'jakarta@epi.id',
                'status' => 'active',
                'config' => [
                    'theme_color' => 'blue',
                    'banner_text' => 'Welcome to EPI Jakarta Store!',
                ],
            ]);
            $this->command->info('Distributor "jakarta" created successfully.');
        } else {
            $this->command->info('Distributor "jakarta" already exists.');
        }
    }
}
