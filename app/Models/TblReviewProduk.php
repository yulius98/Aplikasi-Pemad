<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblReviewProduk extends Model
{
    protected $table = 'tbl_review_produks';

    protected $fillable = [
        'produk_id',
        'review',
    ];

    public function produk()
    {
        return $this->belongsTo(TblProduk::class, 'produk_id');
    }
}
