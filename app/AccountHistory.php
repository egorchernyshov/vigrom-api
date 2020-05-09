<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountHistory extends Model
{
    protected $fillable = [
        'value',
        'transaction_type',
        'currency',
        'change_reason',
        'account_number',
    ];

    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
