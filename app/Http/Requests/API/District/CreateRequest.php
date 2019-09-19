<?php

namespace App\Http\Requests\API\District;

use App\Models\District;
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
        return $this->user()->can('post-district');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return District::$rules;
    }
}
