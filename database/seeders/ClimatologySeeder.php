<?php

namespace Database\Seeders;

use App\Models\ClimatologyReference;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class ClimatologySeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::first();

        if (! $customer) {
            return;
        }

        $data = [
            ['month' => 1, 'average_rain' => 258.8, 'ari_wet_100yr' => 364.6, 'ari_dry_100yr' => 196.4],
            ['month' => 2, 'average_rain' => 266.2, 'ari_wet_100yr' => 359.4, 'ari_dry_100yr' => 126.2],
            ['month' => 3, 'average_rain' => 187.4, 'ari_wet_100yr' => 329.5, 'ari_dry_100yr' => 149.6],
            ['month' => 4, 'average_rain' => 81.2, 'ari_wet_100yr' => 156.0, 'ari_dry_100yr' => 20.2],
            ['month' => 5, 'average_rain' => 1.9, 'ari_wet_100yr' => 5.4, 'ari_dry_100yr' => 0.2],
            ['month' => 6, 'average_rain' => 0.4, 'ari_wet_100yr' => 0.0, 'ari_dry_100yr' => 0.0],
            ['month' => 7, 'average_rain' => 0.0, 'ari_wet_100yr' => 0.0, 'ari_dry_100yr' => 0.0],
            ['month' => 8, 'average_rain' => 5.9, 'ari_wet_100yr' => 11.8, 'ari_dry_100yr' => 11.8],
            ['month' => 9, 'average_rain' => 6.7, 'ari_wet_100yr' => 18.0, 'ari_dry_100yr' => 1.5],
            ['month' => 10, 'average_rain' => 21.6, 'ari_wet_100yr' => 52.2, 'ari_dry_100yr' => 3.8],
            ['month' => 11, 'average_rain' => 176.2, 'ari_wet_100yr' => 208.9, 'ari_dry_100yr' => 121.7],
            ['month' => 12, 'average_rain' => 211.9, 'ari_wet_100yr' => 326.4, 'ari_dry_100yr' => 148.3],
        ];

        foreach ($data as $row) {
            ClimatologyReference::updateOrCreate(
                [
                    'customer_id' => $customer->id,
                    'month' => $row['month'],
                ],
                $row
            );
        }
    }
}
