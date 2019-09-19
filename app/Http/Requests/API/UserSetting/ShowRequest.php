<?php

namespace App\Http\Requests\API\UserSettings;

use App\Models\UserSettings;
use InfyOm\Generator\Request\APIRequest;

class ShowRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('view-user_setting');
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
