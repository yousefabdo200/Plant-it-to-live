<?php

namespace App\Exports;

use App\Models\Plant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantsExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        $plant= Plant::all();
        $plant->makeHidden(['created_at','updated_at','id','admin_id']);
        return $plant;
    }
    public function headings(): array
    {

        return [
            'common_name',
            'scientific_name',
            'watering',
            'fertilizer',
            'sunlight',
            'pruning',
            'img',
            'water_amount',
            'fertilizer_amount',
            'sun_per_day',
            'soil_salinty',
            'appropriate_season',

            // Add more headings as needed
        ];
    }
}
