<?php

namespace App\Http\Requests\API\Media;

use App\Models\Product;
use InfyOm\Generator\Request\APIRequest;

class BuildingUploadRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit-building');
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
