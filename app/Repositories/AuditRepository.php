<?php

namespace App\Repositories;

use OwenIt\Auditing\Models\Audit;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AuditRepository
 * @package App\Repositories
 * @version March 08, 2019, 9:44 pm UTC
 *
 * @method Country findWithoutFail($id, $columns = ['*'])
 * @method Country find($id, $columns = ['*'])
 * @method Country first($columns = ['*'])
*/
class AuditRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'auditable_type',
        'auditable_id',
        'id_address',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Audit::class;
    }
}
