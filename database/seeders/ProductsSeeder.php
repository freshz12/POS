<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 20; $i++) {         
        \App\Models\Products::create([
            'product_name' => "barang $i",
            "sku" => $i,
            "description" => "ini $i description",
            "picture_path" => "",
            "purchase_price" => $i+1 . "0000",
            "selling_price" => $i+2 . "0000",
            "quantity" => "5",
            "unit_of_measurement" => "pcs",
            "created_by" => 0,
            "updated_by" => 0,
        ]);
    }
    }
}
