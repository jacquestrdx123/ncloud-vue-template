<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\RainfallData;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RainfallSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default customer
        $customer = Customer::firstOrCreate(
            ['email' => 'default@example.com'],
            ['name' => 'Default Customer']
        );

        $csvFile = public_path('rain.csv');
        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found at: $csvFile");
            return;
        }

        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data); // Remove header row

        // Header: Date;Rain mm;Eto mm
        // The CSV uses semi-colons as separators based on the preview

        $lines = file($csvFile);
        // Remove header
        array_shift($lines);

        foreach ($lines as $line) {
            $row = str_getcsv($line, ';');
            
            if (count($row) < 2) continue;

            $dateStr = $row[0];
            $rainStr = $row[1] ?? null;
            $etoStr = $row[2] ?? null;

            if (empty($dateStr)) continue;

            try {
                // Handle mixed date formats: 01-Jan-24 and 11 Jun 2024
                $date = null;
                if (preg_match('/^\d{2}-[A-Za-z]{3}-\d{2}$/', $dateStr)) {
                    $date = Carbon::createFromFormat('d-M-y', $dateStr);
                } else {
                    // Try other formats or fallback to standard parsing
                    try {
                        $date = Carbon::parse($dateStr);
                    } catch (\Exception $e) {
                         $this->command->warn("Could not parse date: $dateStr");
                         continue;
                    }
                }

                if (!$date) continue;

                RainfallData::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'date' => $date->format('Y-m-d'),
                    ],
                    [
                        'rain_mm' => $this->parseDecimal($rainStr),
                        'eto_mm' => $this->parseDecimal($etoStr),
                    ]
                );

            } catch (\Exception $e) {
                $this->command->error("Error processing row: " . json_encode($row) . " - " . $e->getMessage());
            }
        }
    }

    private function parseDecimal($value)
    {
        if (empty($value)) return 0;
        // Replace comma with dot
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }
}
