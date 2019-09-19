<?php

use App\Repositories\ProductRepository;
use Illuminate\Database\Seeder;
use App\Models\Unit;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pRepo = new ProductRepository(app());
        if (App::environment('local')) {
            $products = factory(App\Models\Product::class, 10)->create();
            foreach ($products as $product) {
                $pRepo->notify($product);
            }
        }
    }
}
