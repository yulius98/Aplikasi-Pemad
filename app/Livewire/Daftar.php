<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Daftar extends Component
{
    use WithPagination;

    public $name, $email,$password, $role;
    public $paginationTheme = 'tailwind';
    public $updatedata = false;
    public $user_id;
    public $showDeleteModal = false;
    public $cari;
    public $sortcolom ='name';
    public $sortdirection = 'asc';

    public function show_detail($id)
    {
        $user = User:: where('id', $id)->first();
            
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->updatedata = true;
        $this->user_id = $id;    
        
    }

    public function simpan()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];
        $messages = [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
        ];

        $validate = $this->validate($rules, $messages);

        //Simpan data ke tblpembeliandarisupplier
        User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
        ]);
                
        session()->flash('message', "Pendaftaran berhasil.");
        $this->clear();
        
    }

     public function edit($id)
    {
        $user = User::where('id', $id)
            ->first();

        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role ?? null;
        $this->updatedata = true;
        $this->user_id = $id;
    }    
        
    public function update()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];
        $messages = [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
        ];

        $validate = $this->validate($rules, $messages);

        //Update data ke tblpembeliandarisupplier
        User::find($this->user_idd)->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
        ]);
                
        session()->flash('message', "Data berhasil diupdate.");
        $this->clear();
    }

    public function hapus()
    {
        $id = $this->user_id;

        $user = User::find($id);
        if($user){
            $user->delete();
            session()->flash('message', "Data berhasil dihapus.");
        }else{
            session()->flash('message', "Data tidak ditemukan.");
        }
        $this->clear();
        $this->showDeleteModal = false;
    }

    public function konfimasihapus($id)
    {
        $this->user_id = $id;
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
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->user_id = '';
        $this->cari = '';
        $this->sortcolom = 'name';
        $this->sortdirection = 'asc';
        $this->updatedata = false;
    }

    public function render()
    {
         if(!empty($this->cari)) {
            $users = User::where('name', 'like', '%' . $this->cari . '%')
                ->orWhere('email', 'like', '%' . $this->cari . '%')
                ->orWhere('role', 'like', '%' . $this->cari . '%')
                ->orderBy($this->sortcolom, $this->sortdirection)
                ->paginate(10);
        } else {
            $users = User::orderBy($this->sortcolom, $this->sortdirection)
                ->paginate(10);
        }
        

        return view('livewire.daftar',[
            'users' => $users,
        ]);
    }
}
