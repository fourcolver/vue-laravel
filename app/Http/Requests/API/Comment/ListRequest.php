<?php

namespace App\Http\Requests\API\Comment;

use App\Models\Post;
use App\Models\Conversation;
use InfyOm\Generator\Request\APIRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

class ListRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // users can only see comments from own conversations
        if (Relation::$morphMap[$this->commentable] == Conversation::class) {
            return Conversation::where('id', $this->id)->ofLoggedInUser()->exists();
        }

        // and comments from all other models
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'commentable' => [
                'required',
                'string',
                Rule::in(array_keys(Relation::$morphMap)),
            ],
        ];
    }
}
