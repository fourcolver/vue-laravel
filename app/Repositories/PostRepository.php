<?php

namespace App\Repositories;

use App\Models\Building;
use App\Models\District;
use App\Models\Post;
use App\Models\Tenant;
use App\Models\RealEstate;
use App\Models\User;
use App\Notifications\NewTenantInNeighbour;
use App\Notifications\NewTenantPost;
use App\Notifications\PinnedPostPublished;
use App\Notifications\PostPublished;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InfyOm\Generator\Common\BaseRepository;
use Notification;

/**
 * Class PostRepository
 * @package App\Repositories
 * @version February 11, 2019, 6:22 pm UTC
 *
 * @method Post findWithoutFail($id, $columns = ['*'])
 * @method Post find($id, $columns = ['*'])
 * @method Post first($columns = ['*'])
*/
class PostRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'content' => 'like',
    ];

    /**
     * @var array
     */
    protected $mimeToExtension = [
        "image/jpeg" =>  "jpg",
        "image/png" =>  "png",
    ];


    /**
     * Configure the Model
     **/
    public function model()
    {
        return Post::class;
    }

    /**
     * @param array $atts
     * @return Post|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $atts)
    {
        $atts['building_ids'] = $atts['building_ids'] ?? [];
        $atts['district_ids'] = $atts['district_ids'] ?? [];
        $u = \Auth::user();
        if ($u->can('post-post') && !($u->can('post-located-post'))) {
            if ($u->tenant()->exists()) {
                if ($u->tenant->homeless()) {
                    throw new \Exception("Your tenant account does not belong to any unit");
                }

                $atts['building_ids'] = [$u->tenant->building->id];
                if ($u->tenant->building->district_id) {
                    $atts['district_ids'] = [$u->tenant->building->district_id];
                }
            }
        }

        $model = parent::create($atts);
        $model->districts()->sync($atts['district_ids']);
        $model->buildings()->sync($atts['building_ids']);
        if (!$atts['needs_approval']) {
            return $this->setStatus($model->id, Post::StatusPublished, Carbon::now());
        }

        return $model;
    }

    /**
     * @param array $atts
     * @param $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $atts, $id)
    {
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::update($atts, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $atts);
        $model->save();

        $post = $this->parserResult($model);
        return $post;
    }

    /**
     * @param int $id
     * @param $status
     * @param $publish_at
     * @return Post
     */
    public function setStatus(int $id, $status, $publish_at)
    {
        $post = $this->find($id);
        if ($post->status != $status && $status == Post::StatusPublished) {
            $post->status = $status;
            $post->published_at = $publish_at ?? Carbon::now();

            $post->save();
            $this->notify($post);
            return $post;
        }
        $post->status = $status;
        $post->save();
        return $post;
    }

    /**
     * @param Post $post
     */
    public function notify(Post $post)
    {
        if (!$post->notify_email) {
            return;
        }
        $usersToNotify = new Collection();
        if ($post->visibility == Post::VisibilityAll) {
            $users = User::has('tenant')->where('id', '!=', $post->user_id)->get();
            $usersToNotify = $usersToNotify->merge($users);
        }
        if ($post->visibility == Post::VisibilityDistrict) {
            $district_ids = $post->districts()->pluck('id')->toArray();
            $users = User::select('users.*')
                ->join('tenants', 'tenants.user_id', '=', 'users.id')
                ->join('buildings', 'tenants.building_id', '=', 'buildings.id')
                ->where('tenants.deleted_at', null)
                ->whereIn('buildings.district_id', $district_ids)
                ->get();
            $usersToNotify = $usersToNotify->merge($users);
        }
        if ($post->visibility == Post::VisibilityAddress) {
            $building_ids = $post->buildings()->pluck('id')->toArray();
            $users = User::select('users.*')
                ->join('tenants', 'tenants.user_id', '=', 'users.id')
                ->where('tenants.deleted_at', null)
                ->whereIn('tenants.building_id', $building_ids)
                ->get();
            $usersToNotify = $usersToNotify->merge($users);
        }
        if ($post->pinned) {
            $building_ids = $post->buildings()->pluck('id')->toArray();
            $district_ids = $post->districts()->pluck('id')->toArray();
            $bUsers = User::select('users.*')
                ->join('tenants', 'tenants.user_id', '=', 'users.id')
                ->where('tenants.deleted_at', null)
                ->whereIn('tenants.building_id', $building_ids)
                ->get();
            $dUsers = User::select('users.*')
                ->join('tenants', 'tenants.user_id', '=', 'users.id')
                ->join('buildings', 'tenants.building_id', '=', 'buildings.id')
                ->where('tenants.deleted_at', null)
                ->whereIn('buildings.district_id', $district_ids)
                ->get();
            $usersToNotify = $usersToNotify->merge($bUsers);
            $usersToNotify = $usersToNotify->merge($dUsers);
        }
        $usersToNotify = $usersToNotify->unique();
        $usersToNotify->load('settings');

        $i = 0;
        foreach ($usersToNotify as $u) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $u->redirect = '/news';
            if ($u->settings->admin_notification && $post->pinned) {
                $u->notify((new PinnedPostPublished($post))
                    ->delay(now()->addSeconds($delay)));
                continue;
            }
            if ($u->settings->news_notification && !$post->pinned) {
                if ($post->type == Post::TypeArticle) {
                    $u->notify(new PostPublished($post));
                }
                if ($post->type == Post::TypeNewNeighbour) {
                    $u->notify((new NewTenantInNeighbour($post))->delay($post->published_at));
                }
            }
        }
    }

    /**
     * @param Post $post
     */
    public function notifyAdmins(Post $post)
    {
        $re = RealEstate::firstOrFail();
        $tRepo = new TemplateRepository(app());
        if ($post->user->tenant) {
            $admins = User::whereIn('id', $re->news_receiver_ids)->get();
            $i = 0;
            foreach ($admins as $admin) {
                $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
                $admin->redirect = '/admin/posts';

                $notif = (new NewTenantPost($post, $admin))->delay(now()->addSeconds($delay));
                $admin->notify($notif);
            }
        }
    }

    /**
     * @param string $collectionName
     * @param string $dataBase64
     * @param Post $model
     * @return bool|\Spatie\MediaLibrary\Models\Media
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\InvalidBase64Data
     */
    public function uploadFile(string $collectionName, string $dataBase64, Post $model)
    {
        if (!$data = base64_decode($dataBase64)) {
            return false;
        }

        $file  = finfo_open();
        $mimeType  = finfo_buffer($file, $data, FILEINFO_MIME_TYPE);
        finfo_close($file);

        if (!isset($this->mimeToExtension[$mimeType])){
            return false;
        }
        $extension = $this->mimeToExtension[$mimeType];

        $diskName = sprintf("posts_%s", $collectionName);

        $media = $model->addMediaFromBase64($dataBase64)
            ->sanitizingFileName(function ($fileName) use ($extension) {
                return sprintf('%s.%s', str_slug($fileName), $extension);
            })
            ->toMediaCollection($collectionName, $diskName);

        return $media;
    }

    /**
     * @param Tenant $tenant
     * @return Post|bool|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function newTenantPost(Tenant $tenant)
    {
        if ($tenant->homeless()) {
            return false;
        }
        $post = $this->create([
            'visibility' => Post::VisibilityAddress,
            'status' => Post::StatusNew,
            'type' => Post::TypeNewNeighbour,
            'content' => "New neighbour",
            'user_id' => $tenant->user->id,
            'building_ids' => [$tenant->building->id],
            'needs_approval' => false,
            'notify_email' => true,
        ]);

        $publishStart = $tenant->rent_start ?? Carbon::now();
        if ($publishStart->isBefore(Carbon::now())) {
            $publishStart = Carbon::now();
        }
        $this->setStatus($post->id, Post::StatusPublished, $publishStart);
        return $post;
    }

    /**
     * @param Post $p
     * @return mixed
     */
    public function locations(Post $p)
    {
        // Cannot use $p->buildings() and $p->districts() because of a bug
        // related to different number of columns in union
        $pbs = Building::select(\DB::raw('id, name, "building" as type'))
            ->join('building_post', 'building_post.building_id', '=', 'id')
            ->where('building_post.post_id', $p->id);
        $pds = District::select(\DB::raw('id, name, "district" as type'))
            ->join('district_post', 'district_post.district_id', '=', 'id')
            ->where('district_post.post_id', $p->id);

        return $pbs->union($pds);
    }
}
