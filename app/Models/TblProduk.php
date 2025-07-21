<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblProduk extends Model
{
    protected $table = 'tbl_produks';

    protected $fillable = [
        'kategori_id',
        'nama_produk',
        'deskripsi_produk',
        'harga_produk',
    ];

    public function kategori()
    {
        return $this->belongsTo(TblKategori::class, 'kategori_id');
    }

    public function reviews()
    {
        return $this->hasMany(TblReviewProduk::class, 'produk_id');
    }
}
