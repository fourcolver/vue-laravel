<?php

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;


/**
 * @SWG\Definition(
 *      definition="Permission",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="Unique name for the permission, used for looking up permission information in the application layer.",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="display_name",
 *          description="Human readable name for the permission.",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="description",
 *          description="A more detailed explanation of the Permission.",
 *          type="string"
 *      ),
 * )
 */
class Permission extends EntrustPermission
{
    protected $hidden = ['created_at', 'pivot', 'updated_at'];
}
