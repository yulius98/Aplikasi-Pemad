@php use Illuminate\Support\Facades\Storage; @endphp


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if ($errors->any())
        <div class="pt-3">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>    
            </div>
        </div>
    @endif
    @if (session()->has('message'))
        <div class="pt-3">
            <div id="flash-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('message') }}
            </div>
        </div>
        
    @endif


    <!-- START FORM -->
    <div class="my-3 p-6 bg-white rounded-lg shadow">
        
        <form>
            <div class="flex flex-wrap -mx-3">
                <!-- Kolom Pertama -->
                <div class="w-full md:w-1/2 px-3">
                    <div class="mb-4 flex items-center">
                        <label for="nama_produk" class="w-1/3 text-gray-700 font-semibold">Nama Produk</label>
                        <input type="text" id="nama_produk" wire:model="nama_produk" class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    
                    <div class="mb-4 flex items-center">
                        <label for="kategori_id" class="w-1/3 text-gray-700 font-semibold">Kategori</label>
                        <select id="kategori_id" wire:model="kategori_id" class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($dtkategori as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4 flex items-center">
                        <label for="harga_produk" class="w-1/3 text-gray-700 font-semibold">Harga Produk</label>
                        <input type="number" id="harga_produk" wire:model="harga_produk" class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="mb-4 flex items-center">
                        <label for="deskripsi_produk" class="w-1/3 text-gray-700 font-semibold">Keterangan</label>
                        <input type="text" id="deskripsi_produk" wire:model="deskripsi_produk" class="w-2/3 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Tombol SIMPAN -->
            <div class="mb-4 flex justify-end space-x-2">
                @if ($updatedata == false)
                    <button type="button" wire:click="simpan()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">SIMPAN</button>
                @else
                    <button type="button" wire:click="update()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">UPDATE</button>    
                @endif
                <button type="button" wire:click="clear()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded">Clear</button>    
            </div>
        </form>
    </div>
    
    <!-- AKHIR FORM -->

    <!-- START DATA -->
    
    <div class="my-3 p-6 bg-white rounded-lg shadow">
        <h4 class="text-lg font-semibold mb-4">Data Barang</h4>
        <div class="pb-3 pt-3">
            <input type="text" placeholder="Search Produk/Kategori..." wire:model.live="cari" class="w-1/4 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="pb-3 pt-3">
            <label class="block text-gray-700 font-semibold mb-1">Filter Harga</label>
            <div class="text-sm text-gray-500 mb-2">Masukkan rentang harga untuk filter</div>
            <input type="text" placeholder="Search harga..." wire:model.live="cari_harga1" class="w-1/4 border border-gray-300 rounded px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input type="text" placeholder="Search harga..." wire:model.live="cari_harga2" class="w-1/4 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        @if ($dtproduk)
            {{ $dtproduk->links() }}
        @endif
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 cursor-pointer" @if ($sortcolom == 'nama_barang') {{ $sortdirection }} @endif wire:click="sort('nama_barang')">No</th>
                    <th class="border border-gray-300 px-3 py-2 cursor-pointer" @if ($sortcolom == 'nama_barang') {{ $sortdirection }} @endif wire:click="sort('nama_barang')">Nama Barang</th>
                    <th class="border border-gray-300 px-3 py-2 cursor-pointer" @if ($sortcolom == 'nama_kategori') {{ $sortdirection }} @endif wire:click="sort('nama_kategori')">Kategori</th>
                    <th class="border border-gray-300 px-3 py-2 cursor-pointer">Harga</th>
                    <th class="border border-gray-300 px-3 py-2 cursor-pointer">Keterangan</th>
                    <th class="border border-gray-300 px-3 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($dtproduk)
                    @foreach ($dtproduk as $key => $value)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-3 py-2">{{ $dtproduk->firstItem() + $key }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $value->nama_produk }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $value->nama_kategori }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $value->harga_produk }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $value->deskripsi_produk }}</td>
                        <td class="border border-gray-300 px-3 py-2">
                            <div class="flex gap-1">
                                <button wire:click="show_detail({{ $value->id }})" class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded">Detail</button>
                                <button wire:click="edit({{ $value->id }})" class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded">Edit</button>
                                <button wire:click="konfimasihapus({{ $value->id }})" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-2 py-1 rounded">Del</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- AKHIR DATA -->

    @if($showDeleteModal)
    <div wire:click.self="$set('showDeleteModal', false)" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold" id="exampleModalLabel">Konfirmasi Hapus Data</h1>
                <button type="button" class="text-gray-600 hover:text-gray-900" wire:click="$set('showDeleteModal', false)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded" wire:click="$set('showDeleteModal', false)">Tidak</button>
                <button type="button" wire:click="hapus()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">YA</button>
            </div>
        </div>
    </div>
    @endif
</div>

