<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'nomor_invoice',
        'total_pembayaran',
        'total_pajak',
        'payment_method',
        'user_id',
        'payment_status',
        'transaction_status',
        'snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'notes',
        'cash_amount',
        'cash_change',
        'nama_konsumen',
        'no_table'
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function canBeModified()
    {
        return $this->transaction_status === 'unpaid';
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'transaction_id');
    }
}
