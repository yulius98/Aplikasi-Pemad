<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblKategori extends Model
{
    protected $table = 'tbl_kategoris';

    protected $fillable = [
        'nama_kategori',
    ];

    public function produks()
    {
        return $this->hasMany(TblProduk::class, 'kategori_id');
    }
}
