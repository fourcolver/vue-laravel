<?php

namespace App\Criteria\Posts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Post;

/**
 * Class FilterByLocationCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByLocationCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param         Builder|Model     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $u = \Auth::user();
        if ($u->can('list-post')) {
            return $model;
        }

        if ($this->request->get('all', null)) {
            return $model;
        }

        $t = $u->tenant;
        if (!$t) {
            return $model;
        }

        $conds = [
            "posts.user_id = ?",
            "posts.visibility = ?",
            "building_post.building_id = ?",
        ];
        $args = [
            \Auth::id(),
            Post::VisibilityAll,
            $t->building_id,
        ];
        // If the tenant building is in a district, show the pinned posts from that district
        if ($t->building && $t->building->district_id) {
            $conds[] = "district_post.district_id = ?";
            $args[] = $t->building->district_id;
        }

        // It's raw, Melissa, because  https://github.com/laravel/framework/issues/23957
        return $model->select('posts.*')->distinct()
            ->leftJoin("building_post", "building_post.post_id", "=", "posts.id")
            ->leftJoin("district_post", "district_post.post_id", "=", "posts.id")
            ->whereRaw("(" . implode(" or ", $conds) . ")", $args);
    }
}
