<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'status',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELASI USER
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->hasMany(User::class);
    }


    /*
    |--------------------------------------------------------------------------
    | RELASI TRANSAKSI
    |--------------------------------------------------------------------------
    */

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }


    /*
    |--------------------------------------------------------------------------
    | RELASI JURNAL
    |--------------------------------------------------------------------------
    */

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class);
    }
}