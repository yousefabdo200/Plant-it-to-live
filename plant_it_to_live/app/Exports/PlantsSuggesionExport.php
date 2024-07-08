<?php

namespace App\Exports;

use App\Models\Suggested_plant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantsSuggesionExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $plant= Suggested_plant::all();
        $plant->makeHidden(['created_at','updated_at','id','admin_id','plant_id','user_id','approved']);
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
