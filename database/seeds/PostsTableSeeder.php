<?php

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pRepo = new PostRepository(app());
        if (App::environment('local')) {
            $posts = factory(App\Models\Post::class, 10)->create();
            foreach ($posts as $post) {
                $u = $post->user;
                if ($u->tenant && $u->tenant->building) {
                    $post->buildings()->sync($u->tenant->building->id);
                    if ($u->tenant->building->district_id) {
                        $post->districts()->sync($u->tenant->building->district_id);
                    }
                }
                $pRepo->setStatus($post->id, Post::StatusPublished, now());
            }
        }
    }
}
