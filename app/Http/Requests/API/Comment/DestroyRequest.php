<?php

namespace App\Http\Requests\API\Comment;

use App\Models\Comment;
use InfyOm\Generator\Request\APIRequest;

class DestroyRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->can('edit-comment')) {
            return true;
        }
        return Comment::where('id', $this->route('id'))
            ->where('user_id', \Auth::id())->exists();
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
