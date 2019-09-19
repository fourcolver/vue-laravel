<?php

namespace App\Repositories;

use App;
use App\Jobs\NewAdminNotification;
use App\Models\User;
use App\Models\UserSettings;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Str;
use InfyOm\Generator\Common\BaseRepository;
use Intervention\Image\ImageManager as Image;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version January 11, 2019, 12:27 pm UTC
 *
 * @method User findWithoutFail($id, $columns = ['*'])
 * @method User find($id, $columns = ['*'])
 * @method User first($columns = ['*'])
 */
class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'email' => 'like',
        'phone' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $attributes['password'] = bcrypt($attributes['password']);

        $model = parent::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        //add user role
        if (!isset($attributes['role'])) {
            $attributes['role'] = 'registered';
        }
        $role = (new RoleRepository(app()))->getRoleByName($attributes['role']);

        $model->attachRole($role);

        //add user settings
        $model->settings()->save(new UserSettings());

        if (in_array($role->name, ['administrator'])) {
            dispatch(new NewAdminNotification($model));
        }

        return $this->parserResult($model);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $attributes, $id)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        if (isset($attributes['password']) && !empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        //change user role
        if (isset($attributes['role'])) {
            $role = (new RoleRepository(app()))->getRoleByName($attributes['role']);
            if ($role) {
                $model->detachRoles();
                $model->attachRole($role);
            }
        }

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }

    // TODO: move function to media repository

    /**
     * @param string $fileData
     * @param User $user
     * @return string
     */
    public function uploadImage(string $fileData, User $user)
    {
        $avatar = Str::slug(sprintf('%s-%d', $user->name, $user->id)) . '.png';
        $imgPath = storage_path(sprintf('app/public/avatar/%s', $avatar));

        (new Image)->make($fileData)->encode('png', 100)->fit(800, 800)->save($imgPath);

        return sprintf('storage/avatar/%s', $avatar);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function resetPassword(array $attributes, $id)
    {
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $attributes['password'] = bcrypt($attributes['password']);

        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model->notify(new PasswordResetSuccess($model));

        return $this->parserResult($model);
    }
}
