<?php

namespace App\Repositories;

use App;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetRequest;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version January 11, 2019, 12:27 pm UTC
 *
 * @method PasswordReset findWithoutFail($id, $columns = ['*'])
 * @method PasswordReset find($id, $columns = ['*'])
 * @method PasswordReset first($columns = ['*'])
 */
class PasswordResetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'email',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PasswordReset::class;
    }

    /**
     * @param array $attributes
     * @param App\Models\User $user
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createToken(array $attributes, App\Models\User $user)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = parent::updateOrCreate(['email' => $attributes['email']], $attributes);

        $this->skipPresenter($temporarySkipPresenter);

        $model->notify(
            new PasswordResetRequest($user, $model)
        );

        return $this->parserResult($model);
    }

    public function findToken(string $token)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = parent::findWhere(['token' => $token])->first();

        $this->skipPresenter($temporarySkipPresenter);

        return $this->parserResult($model);
    }
}
