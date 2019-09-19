<?php

use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ServiceRequestsTableSeeder extends Seeder
{
    var $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();
        if (App::environment('local')) {
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'super_admin');
            })->get();
            $serviceRequests = factory(App\Models\ServiceRequest::class, 50)->create();
            $user = App\Models\User::where('email', 'tenant@example.com')->first();
            foreach ($serviceRequests as $key => $serviceRequest) {
                $this->addRequestComments($serviceRequest);
                if ($key < 3) {
                    continue;
                }

                $serviceRequest->tenant_id = $user->tenant->id;
                $serviceRequest->unit_id = $user->tenant->unit_id;
                $serviceRequest->status = array_rand(ServiceRequest::Status);
                $serviceRequest->save();
                $providers = ServiceProvider::inRandomOrder()->take(2)->get();
                $serviceRequest->providers()->sync($providers);
                foreach ($providers as $prov) {
                    foreach ($admins as $admin) {
                        $c = $serviceRequest->conversationFor($admin, $prov->user);
                        $c->commentAsUser($admin, "Knock Knock!");
                        usleep(1000);
                        $c->commentAsUser($prov->user, "Who's there?");
                    }
                }
            }
        }
    }

    private function addRequestComments(ServiceRequest $serviceRequest)
    {
        $totalComments = $this->faker->numberBetween(2, 20);
        $users = [
            $serviceRequest->tenant->user,
        ];

        if ($serviceRequest->agent) {
            $users [] = $serviceRequest->agent;
        }

        for ($i = 0; $i < $totalComments; $i++) {
            $user = $users[rand(0, count($users) - 1)];
            $serviceRequest->commentAsUser($user, $this->faker->sentence(3), null);
        }

        DB::statement("UPDATE comments SET created_at = NOW() + INTERVAL -1 week + INTERVAL id second, updated_at = NOW() + INTERVAL -1 week + INTERVAL id second;");
    }
}
