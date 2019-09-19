<?php

namespace App\Http\Requests\API\Post;

use App\Models\Post;
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
        $u = \Auth::user();
        if ($u->can('view-post')) {
            return true;
        }
        $p = Post::where('id', $this->route('post'))->first();
        if (!$p) {
            return false;
        }

        if ($p->user_id == $u->id) {
            return true;
        }

        return $p->status == Post::StatusPublished;
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
