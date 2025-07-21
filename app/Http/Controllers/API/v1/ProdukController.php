<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblProduk;
use Illuminate\Support\Facades\Log;

class ProdukController extends Controller
{
    /**
     * Search products by name.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([
                'error' => 'Query parameter "q" is required.'
            ], 400);
        }

        $products = TblProduk::join('tbl_kategoris', 'tbl_produks.kategori_id', '=', 'tbl_kategoris.id')
            ->select('tbl_produks.*', 'tbl_kategoris.nama_kategori')
            ->where('nama_produk', 'LIKE', '%' . $query . '%')
            ->get();

        return response()->json($products);
    }

    public function category(Request $request)
    {
        $query = $request->query('q');
        $limit = $request->query('limit', 10);

        if (!$query) {
            return response()->json([
                'error' => 'Query parameter "q" is required.'
            ], 400);
        }

        $products = TblProduk::join('tbl_kategoris', 'tbl_produks.kategori_id', '=', 'tbl_kategoris.id')
                ->select('tbl_produks.*', 'tbl_kategoris.nama_kategori')
                ->where('nama_kategori', 'LIKE', '%' . $query . '%')
                ->paginate($limit);

        return response()->json($products);
        
    }

    /**
     * Filter products by price range.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rentangharga(Request $request)
    {
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');

        $query = TblProduk::join('tbl_kategoris', 'tbl_produks.kategori_id', '=', 'tbl_kategoris.id')
            ->select('tbl_produks.*', 'tbl_kategoris.nama_kategori');

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('tbl_produks.harga_produk', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $query->where('tbl_produks.harga_produk', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $query->where('tbl_produks.harga_produk', '<=', $maxPrice);
        }

        $products = $query->paginate(10);

        return response()->json($products);
    }

    /**
     * Display a paginated list of products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $products = TblProduk::join('tbl_kategoris', 'tbl_produks.kategori_id', '=', 'tbl_kategoris.id')
            ->select('tbl_produks.*', 'tbl_kategoris.nama_kategori')
            ->paginate($limit);

        return response()->json($products);
    }
}
