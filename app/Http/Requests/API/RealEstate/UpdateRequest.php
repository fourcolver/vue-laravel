<?php

namespace App\Http\Requests\API\RealEstate;

use App\Models\RealEstate;
use InfyOm\Generator\Request\APIRequest;

class UpdateRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit-real_estate');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return RealEstate::rules();
    }
}
