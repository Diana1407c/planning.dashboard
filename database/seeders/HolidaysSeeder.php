<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        $endYear = 2099;

        $fixedholidays = [
            ['01-01', "New Year's Day"],
            ['01-07', "Orthodox Christmas"],
            ['01-08', "Orthodox Christmas Day 2"],
            ['03-08', "International Women's Day"],
            ['05-01', "Labour Day"],
            ['05-09', "Europe Day and Victory Day"],
            ['08-27', "Independence Holiday"],
            ['08-31', "National Language Day (Moldova)"],
            ['10-14', "Chisinau City Day"],
        ];

        foreach ($fixedholidays as $holidayData) {
            $date = $currentYear. '-' . $holidayData[0];
            $name = $holidayData[1];

            Holiday::updateOrCreate(
                ['date' => $date, 'name' => $name],
                ['every_year' => 1]
            );
        }

        for ($year = $currentYear; $year <= $endYear; $year++) {

            $easterTimestamp = orthodox_eastern($year);
            $easterDate = date('m-d', $easterTimestamp);
            $memorialTimestamp = memorial_orthodox_eastern($year);
            $memorialEasterDate = date('m-d', $memorialTimestamp);

            $dynamicHolidays = [
                [$easterDate, "Orthodox Easter"],
                [$memorialEasterDate, "Memorial Easter"],
            ];

            foreach ($dynamicHolidays as $holidayData) {
                $date = $year . '-' . $holidayData[0];
                $name = $holidayData[1];

                Holiday::updateOrCreate(
                    ['date' => $date, 'name' => $name],
                    ['every_year' => 0]
                );
            }
        }
    }
}
