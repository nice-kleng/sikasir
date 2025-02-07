<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'product_id', 'jumlah', 'harga', 'subtotal', 'discount', 'notes'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
