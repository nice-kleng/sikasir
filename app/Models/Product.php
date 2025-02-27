<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_menu',
        'harga',
        'deskripsi',
        'foto'
    ];

    public function transactionItem()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
