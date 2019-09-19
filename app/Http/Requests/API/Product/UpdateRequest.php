<?php

namespace App\Http\Requests\API\Product;

use InfyOm\Generator\Request\APIRequest;
use App\Models\Product;

class UpdateRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u = \Auth::user();
        if ($u->can('edit-product')) {
            return true;
        }
        return Product::where('id', $this->route('product'))
            ->where('user_id', $u->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Product::rules();
    }
}
