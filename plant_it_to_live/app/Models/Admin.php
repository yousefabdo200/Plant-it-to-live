<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Admin  extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    protected $table ='admins';
    protected $fillable= ['name','password','email','access_Key'];
    protected $hidden=['password','access_Key','created_at','updated_at'];
    //public $timestamps=false ;
    public function plants ()
    {
        return $this->hasMany('App\Models\Plant','admin_id','id');
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
    public function getJWTCustomClaims()
    {
        return ['guard'=>'admin'];
    }
}
