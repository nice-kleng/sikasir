<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['nomor_invoice', 'total_pembayaran', 'total_pajak', 'payment_method', 'user_id', 'payment_status', 'snap_token', 'midtrans_transaction_id', 'midtrans_payment_type', 'notes', 'cash_amount', 'cash_change'];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
