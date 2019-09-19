<?php

use App\Models\ServiceRequestCategory;
use Illuminate\Database\Seeder;

class ServiceRequestCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/serviceRequestCategories.json');
        $data = json_decode($json);

        if (!$data) {
            return;
        }

        foreach ($data as $obj) {
            $category = [];
            $category['name'] = $obj->name;
            if ($obj->parent_id) {
                $category['parent_id'] = $obj->parent_id;
            }

            if ($obj->has_qualifications) {
                $category['has_qualifications'] = $obj->has_qualifications;
            }

            ServiceRequestCategory::create(
                $category
            );
        }
    }
}
