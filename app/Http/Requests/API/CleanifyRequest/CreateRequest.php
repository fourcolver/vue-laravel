<?php

namespace App\Http\Requests\API\CleanifyRequest;

use App\Models\CleanifyRequest;
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
        return $this->user()->can('post-cleanify_request');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ];
    }
}
