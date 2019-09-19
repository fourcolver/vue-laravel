<?php

namespace App\Http\Requests\API\Post;

use App\Models\Post;
use InfyOm\Generator\Request\APIRequest;

class ListViewsRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->can('list_views-post');
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
