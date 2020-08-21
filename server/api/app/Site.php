<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;


use Illuminate\Database\Eloquent\Model;



class Site extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'country_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    

    public function quotes() {
        return $this->hasMany(Quote::class);
    }

    public function clicks() {
        return $this->hasManyThrough(Click::class, Quote::class);
    }
}
