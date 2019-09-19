<?php

namespace App\Http\Requests\API\PropertyManager;

use App\Models\PropertyManager;
use InfyOm\Generator\Request\APIRequest;

class CreateRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('post-property_manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return PropertyManager::$rules;
    }
}
