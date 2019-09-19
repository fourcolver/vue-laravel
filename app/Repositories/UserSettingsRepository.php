<?php

namespace App\Repositories;

use App\Models\UserSettings;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserSettingsRepository
 * @package App\Repositories
 * @version January 17, 2019, 1:08 pm UTC
 *
 * @method UserSettings findWithoutFail($id, $columns = ['*'])
 * @method UserSettings find($id, $columns = ['*'])
 * @method UserSettings first($columns = ['*'])
*/
class UserSettingsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'language',
        'summary',
        'admin_notification',
        'news_notification',
        'marketplace_notification',
        'service_notification'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserSettings::class;
    }
}
