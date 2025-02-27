<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['nama_menu', 'kategori', 'harga', 'foto', 'deskripsi'];

    public function transactionItem()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
