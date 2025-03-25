<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'transaction_id',
        'refund_number',
        'total_refund',
        'refund_method',
        'refund_status',
        'midtrans_refund_id',
        'alasan',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
