<?php

namespace App\Http\Requests\API\Media;

use InfyOm\Generator\Request\APIRequest;

/**
 * Class TenantUploadRequest
 * @package App\Http\Requests\API\Media
 */
class TenantUploadRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit-tenant');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
