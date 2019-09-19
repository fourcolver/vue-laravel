<?php

namespace App\Http\Requests\API\ServiceRequest;

use InfyOm\Generator\Request\APIRequest;

class NotifyProviderRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('notify-provider');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'provider_id' => 'required|exists:service_providers,id',
            'assignee_ids' => 'array',
            'title' => 'string|required',
            'body' => 'string|required',
            'to' => 'nullable|email',
            'cc.*' => 'email',
            'bcc.*' => 'email',
        ];
    }
}
