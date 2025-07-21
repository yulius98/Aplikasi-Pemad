<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'), // password
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('12345678'), // password
            'role' => 'user',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tbl_kategoris')->insert([
            'nama_kategori' => 'Elektronik',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('tbl_kategoris')->insert([
                'nama_kategori' => 'Pakaian',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        DB::table('tbl_kategoris')->insert([
                'nama_kategori' => 'Makanan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        DB::table('tbl_kategoris')->insert([
            'nama_kategori' => 'Peralatan Rumah Tangga',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = [];
        for ($i = 0; $i < 150000; $i++) {
            $data[] = [
                'kategori_id' => rand(1, 4),
                'nama_produk' => 'Produk_' . Str::random(8),
                'deskripsi_produk' => 'Deskripsi produk ' . Str::random(20),
                'harga_produk' => rand(50, 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert in chunks of 1000 to avoid memory overload
            if (count($data) === 1000) {
                DB::table('tbl_produks')->insert($data);
                $data = [];
            }
        }

        // Insert remaining data if any
        if (!empty($data)) {
            DB::table('tbl_produks')->insert($data);
        }
    }
}
