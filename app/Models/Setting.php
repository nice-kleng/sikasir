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
        'midtrans_environment',
        'permission_front_menu',
    ];

    // public static function getSettings()
    // {
    //     return self::first();
    // }

    // public function isProductionEnvironment()
    // {
    //     return $this->midtrans_environment === 'production';
    // }

    // public function getSnapUrl()
    // {
    //     return $this->isProductionEnvironment()
    //         ? 'https://app.midtrans.com/snap/snap.js'
    //         : 'https://app.sandbox.midtrans.com/snap/snap.js';
    // }
}
