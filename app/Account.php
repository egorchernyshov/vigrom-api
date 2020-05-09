<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'number';

    protected $fillable = [
        'number',
        'balance',
    ];

    public function accountHistories()
    {
        return $this->hasMany(AccountHistory::class);
    }
}
