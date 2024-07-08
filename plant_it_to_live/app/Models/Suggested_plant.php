<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Suggested_plant extends Authenticatable implements JWTSubject
{
    use HasFactory;
    public $table='Suggested_plants';
    protected $fillable=['common_name','scientific_name','watering','fertilizer','sunlight','pruning','img','water_amount','fertilizer_amount','sun_per_day','soil_salinty','appropriate_season','approved','user_id','admin_id','plant_id'];
    public $timestamps = false;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    public function plant()
    {
        return $this->belongsTo('App\Models\Plant','plant_id');
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
