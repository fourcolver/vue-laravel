<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return;
        }
        $us = [
            User::where('deleted_at', null)->first(),
            User::where('email', 'tenant@example.com')->first(),
        ];

        $ps = Post::all();
        foreach ($ps as $p) {
            for ($i = 1; $i <= 20; $i++) {
                $u = $us[rand(0, 1)];
                $p->commentAsUser($u, $i, null);
            }
        }

        // Comments are sorted by created_at, when retrieved to the client
        // created_at is the same for all the rows generated with the seed
        // Update comments so that each comment has a different created_at
        DB::statement("update comments set created_at = NOW() + INTERVAL -1 week + INTERVAL id second, updated_at = NOW() + INTERVAL -1 week + INTERVAL id second;");
    }
}
