<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TemplateCategoriesTableSeeder::class);
        $this->call(TemplateTableSeeder::class);

        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(AddressesTableSeeder::class);
        $this->call(DistrictsTableSeeder::class);
        $this->call(RealEstateTableSeeder::class);

        $this->call(BuildingsTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(TenantsTableSeeder::class);

        $this->call(PostsTableSeeder::class);

        $this->call(CommentsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);

        $this->call(ServiceProvidersTableSeeder::class);
        $this->call(PropertyManagerTableSeeder::class);
        $this->call(ServiceRequestCategoriesTableSeeder::class);
        $this->call(ServiceRequestsTableSeeder::class);

        $this->call(AuditsTableSeeder::class);
    }
}
