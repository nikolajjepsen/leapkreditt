<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfirmCode extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quote_id', 'code',
    ];

    protected $table = 'confirm_codes';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
