<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'kode_akun',
    'nama_akun',
    'tipe_akun',
    'kelompok_akun',
    'status'
])]
class Akun extends Model
{
    use HasFactory;
}