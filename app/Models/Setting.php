<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = [
        'nama_usaha',
        'nama_pemilik',
        'nama_aplikasi',
        'deskripsi',
        'alamat',
        'telepon',
        'logo',
        'favicon',
        'email',
        'midtrans_merchant_id',
        'midtrans_client_key',
        'midtrans_server_key',
        'midtrans_environment'
    ];
}
