<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class plant_user extends Pivot
{
    use HasFactory;
    protected $table = 'user_plant';

    protected $fillable = [
        'plant_id',
        'user_id',
    ];
}
