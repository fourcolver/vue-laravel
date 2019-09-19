<?php

namespace App\Http\Requests\API\Product;

use App\Models\Product;
use InfyOm\Generator\Request\APIRequest;

class ViewRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->can('view-product');
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
