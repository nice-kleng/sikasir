<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['nama_menu', 'kategori_id', 'harga', 'foto', 'deskripsi', 'status'];

    public function transactionItem()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
