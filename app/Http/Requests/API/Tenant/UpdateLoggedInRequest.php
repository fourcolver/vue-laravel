<?php

namespace App\Http\Requests\API\Tenant;

use App\Models\Tenant;
use InfyOm\Generator\Request\APIRequest;

class UpdateLoggedInRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Tenant::$rules;
    }
}
