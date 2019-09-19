<?php

namespace App\Http\Requests\API\ServiceProvider;

use App\Models\ServiceProvider;
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
        return $this->user()->can('edit-provider');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ServiceProvider::$rulesUpdate;
    }
}
