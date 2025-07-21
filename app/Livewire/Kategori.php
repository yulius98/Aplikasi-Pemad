<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TblKategori;


class Kategori extends Component
{
    use WithPagination;
    public $nama_kategori;
    public $paginationTheme = 'tailwind';
    public $updatedata = false;
    public $kategori_id;
    public $showDeleteModal = false;
    
    public $cari;
    public $sortcolom ='nama_kategori';
    public $sortdirection = 'asc';

    public function show_detail($id)
    {
        $kategori = TblKategori::where('tbl_kategoris.id', $id)->first();
            
        $this->nama_kategori = $kategori->nama_kategori;
        $this->updatedata = true;
        
        $this->kategori_id = $id;
    }

    public function simpan()
    {
        $rules = [
            'nama_kategori' => 'required',
        ];
        $messages = [
            'nama_kategori.required' => 'Kategori tidak boleh kosong',
        ];

        $validate = $this->validate($rules, $messages);

        //Simpan data ke tblpembeliandarisupplier
        TblKategori::create([
                'nama_kategori' => $this->nama_kategori,
                
        ]);
        session()->flash('message', "Kategori berhasil disimpan.");
        $this->clear();
        
    }

    public function edit($id)
    {
        $kategori = TblKategori::where('tbl_kategoris.id', $id)
            ->first();

        $this->kategori_id = $id;
        $this->nama_kategori = $kategori->nama_kategori;
        $this->updatedata = true;
    }

    public function update()
    {
        $rules = [
            'nama_kategori' => 'required',
        ];
        $messages = [
            'nama_kategori.required' => 'Kategori tidak boleh kosong',
        ];

        $validate = $this->validate($rules, $messages);

        //Update data ke tblpembeliandarisupplier
        TblKategori::find($this->kategori_id)->update([
                
                'nama_kategori' => $this->nama_kategori,
                
        ]);
                
        session()->flash('message', "Kategori berhasil diupdate.");
        $this->clear();
    }

    public function hapus()
    {
        $id = $this->kategori_id;

        $kategori = TblKategori::find($id);
        if($kategori){
            $kategori->delete();
            session()->flash('message', "Kategori berhasil dihapus.");
        }else{
            session()->flash('message', "Kategori tidak ditemukan.");
        }
        $this->clear();
        $this->showDeleteModal = false;
    }

    public function konfimasihapus($id)
    {
        $this->kategori_id = $id;
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
        
        $this->cari = '';
        
        
        $this->updatedata = false;
    }
    
    public function render()
    {  
        if ($this->cari != null) {
            $dtkategori = TblKategori::where('nama_kategori', 'like', '%' . $this->cari . '%')
                ->orderBy($this->sortcolom, $this->sortdirection)
                ->paginate(5);
        } else {
            $dtkategori = TblKategori::orderBy($this->sortcolom, $this->sortdirection)
                ->paginate(5);
        }
          
        return view('livewire.kategori',[
            'dtkategori' => $dtkategori,
        ]);
    }
}
