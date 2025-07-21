<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TblProduk;
use App\Models\TblKategori;


class Produk extends Component
{
    use WithPagination;
    public $kategori_id, $nama_kategori,$nama_produk, $deskripsi_produk, $harga_produk;
    public $paginationTheme = 'tailwind';
    public $updatedata = false;
    public $produk_id;
    public $showDeleteModal = false;
    
    public $cari;
    public $cari_harga1;
    public $cari_harga2;
    public $sortcolom ='nama_produk';
    public $sortdirection = 'asc';

    public function show_detail($id)
    {
        $produk = TblProduk::join('tbl_kategoris as k', 'tbl_produks.kategori_id', '=', 'k.id')
            ->select('k.*','tbl_produks.*')
            ->where('tbl_produks.id', $id)->first();
            
        $this->produk_id = $produk->id;
        $this->kategori_id = $produk->kategori_id;
        $this->nama_kategori = $produk->nama_kategori;
        $this->nama_produk = $produk->nama_produk;
        $this->deskripsi_produk = $produk->deskripsi_produk;
        $this->harga_produk = $produk->harga_produk;
        $this->updatedata = true;
        $this->produk_id = $id;
    }
        
    public function simpan()
    {
        $rules = [
            'nama_produk' => 'required',
        ];
        $messages = [
            'nama_produk.required' => 'Nama Produk tidak boleh kosong',
        ];

        $validate = $this->validate($rules, $messages);

        //Simpan data ke tblpembeliandarisupplier
        TblProduk::create([
                'kategori_id' => $this->kategori_id,
                'nama_produk' => $this->nama_produk,
                'deskripsi_produk' => $this->deskripsi_produk,
                'harga_produk' => $this->harga_produk,
        ]);
        session()->flash('message', "Data Produk berhasil disimpan.");
        $this->clear();
        
    }
        
    public function edit($id)
    {
        $produk = TblProduk::join('tbl_kategoris as k', 'tbl_produks.kategori_id', '=', 'k.id')
            ->select('tbl_produks.*', 'k.nama_kategori')
            ->where('tbl_produks.id', $id)
            ->first();
        $this->produk_id = $produk->id;
        $this->kategori_id = $produk->kategori_id;
        $this->nama_kategori = $produk->nama_kategori ?? null;
        $this->nama_produk = $produk->nama_produk ?? null;
        $this->deskripsi_produk = $produk->deskripsi_produk ?? null;
        $this->harga_produk = $produk->harga_produk ?? null;
        $this->updatedata = true;
        $this->produk_id = $id;

    }

    public function update()
    {
        $rules = [
            'nama_produk' => 'required',
        ];
        $messages = [
            'nama_produk.required' => 'Nama Produk tidak boleh kosong',
        ];

        $validate = $this->validate($rules, $messages);

        //Update data ke tblpembeliandarisupplier
        TblProduk::find($this->produk_id)->update([
                'kategori_id' => $this->kategori_id,
                'nama_produk' => $this->nama_produk,
                'deskripsi_produk' => $this->deskripsi_produk,
                'harga_produk' => $this->harga_produk,
        ]);
                
        session()->flash('message', "Data Produk berhasil diupdate.");
        $this->clear();
    }

    public function hapus()
    {
        $id = $this->produk_id;

        $produk = TblProduk::find($id);
        if($produk){
            $produk->delete();
            session()->flash('message', "Data Produk berhasil dihapus.");
        }else{
            session()->flash('message', "Data Produk tidak ditemukan.");
        }
        $this->clear();
        $this->showDeleteModal = false;
    }
    
    public function konfimasihapus($id)
    {
        $this->produk_id = $id;
        $this->showDeleteModal = true;
    }

    public function sort($colomname)
    {
        if ($this->sortcolom == $colomname) {
            $this->sortdirection = $this->sortdirection == 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortcolom = $colomname;
            $this->sortdirection = 'asc';
        }
    }

    public function clear()
    {
        $this->kategori_id = '';
        $this->nama_kategori = '';
        $this->nama_produk = '';
        $this->deskripsi_produk = '';
        $this->harga_produk = '';
        $this->produk_id = '';
        $this->cari = '';
        $this->cari_harga1 = '';
        $this->cari_harga2 = '';
        
        $this->updatedata = false;
    }

    public function selectKategori($id)
    {
        $dtkategori = TblKategori::find($id);
        if ($dtkategori) {
            $this->kategori_id = $dtkategori->id;
            $this->nama_kategori = $dtkategori->nama_kategori;
        }
    }

    public function render()
    {
        $query = TblProduk::join('tbl_kategoris as k', 'tbl_produks.kategori_id', '=', 'k.id')
            ->select('k.*', 'tbl_produks.*');

        if (trim($this->cari) !== '') {
            $search = '%' . $this->cari . '%';
            $query->where(function ($q) use ($search) {
                $q->where('tbl_produks.nama_produk', 'like', $search)
                  ->orWhere('k.nama_kategori', 'like', $search);
            });
        }

        $harga1 = is_numeric($this->cari_harga1) ? $this->cari_harga1 : null;
        $harga2 = is_numeric($this->cari_harga2) ? $this->cari_harga2 : null;

        if ($harga1 !== null && $harga2 !== null) {
            if ($harga1 > $harga2) {
                // Swap if harga1 is greater than harga2
                [$harga1, $harga2] = [$harga2, $harga1];
            }
            $query->whereBetween('tbl_produks.harga_produk', [$harga1, $harga2]);
        } elseif ($harga1 !== null) {
            $query->where('tbl_produks.harga_produk', '>=', $harga1);
        } elseif ($harga2 !== null) {
            $query->where('tbl_produks.harga_produk', '<=', $harga2);
        }

        $dtproduk = $query->orderBy($this->sortcolom, $this->sortdirection)
            ->paginate(20);

        $dtkategori = TblKategori::all();

        return view('livewire.produk', ['dtproduk' => $dtproduk, 'dtkategori' => $dtkategori]);
    }
}
