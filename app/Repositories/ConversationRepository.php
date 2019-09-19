<?php

namespace App\Repositories;

use App\Models\Conversation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ConversationRepository
 * @package App\Repositories
 * @version February 11, 2019, 6:22 pm UTC
 *
 * @method Comment findWithoutFail($id, $columns = ['*'])
 * @method Comment find($id, $columns = ['*'])
 * @method Comment first($columns = ['*'])
*/
class ConversationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Conversation::class;
    }
}
