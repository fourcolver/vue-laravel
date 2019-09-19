<?php

namespace App\Http\Requests\API\Post;

use App\Models\Post;
use InfyOm\Generator\Request\APIRequest;

class DeleteRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u = \Auth::user();
        if ($u->can('delete-post')) {
            return true;
        }
        $p = Post::where('id', $this->route('post'))
            ->where('user_id', $u->id)->first();
        if (!$p) {
            return false;
        }

        return $p->status == Post::StatusNew;
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
