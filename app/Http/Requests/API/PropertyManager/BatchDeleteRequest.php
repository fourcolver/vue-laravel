<?php

namespace App\Http\Requests\API\PropertyManager;

use InfyOm\Generator\Request\APIRequest;

class BatchDeleteRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('delete-property_manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'managerIds' => 'required|array',
            'managerIds.*' => 'integer',
            'assignee' => 'sometimes|integer',
        ];
    }
}
