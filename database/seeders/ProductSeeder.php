<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    //Seeding Command
    // php artisan db:seed --class=ProductSeeder
    public function run()
    {
        DB::Table('products')->insert([
            'name' => Str::random(10),
            'stock' => 20
        ]);
    }
}
