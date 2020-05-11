<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountHistory extends Model
{
    protected $fillable = [
        'value',
        'original_value',
        'exchange_rate_id',
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
