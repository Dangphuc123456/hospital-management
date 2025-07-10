<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class admintable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('admin')->insert([
        ['name' => 'Sản phẩm 1', 'price' => 100.00, 'description' => 'Mô tả sản phẩm 1'],
        ['name' => 'Sản phẩm 2', 'price' => 200.00, 'description' => 'Mô tả sản phẩm 2'],
        // Thêm dữ liệu mẫu khác...
    ]);
    }
}
