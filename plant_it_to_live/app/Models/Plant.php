<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
class Plant extends Authenticatable implements JWTSubject
{
    public  $table="plants";
    protected $fillable=['common_name','scientific_name','watering','fertilizer','sunlight','pruning','img','water_amount','fertilizer_amount','sun_per_day','soil_salinty','appropriate_season','admin_id'];
    public $timestamps = false;
    use  HasFactory, Notifiable  ;
    public function admin ():BelongsTo
    {
        return $this->belongsTo('App\Models\Admin','admin_id');
    }
    public function suggestedplant()
    {
        return $this->hasOne('App\Models\Suggested_plant','plant_id');
    }
    public function users()
    {
        return $this ->belongsToMany('App\Models\User','user_plant');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims():array
    {
        return [];
    }
}
