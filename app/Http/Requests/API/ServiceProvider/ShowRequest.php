<?php

namespace App\Http\Requests\API\ServiceProvider;

use App\Models\ServiceProvider;
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
        $u = $this->user();
        if ($u->can('view-provider')) {
            return true;
        }
        $p = ServiceProvider::where('id', $this->route('id'))->first();
        if (!$p) {
            return false;
        }

        return $p->user_id == $u->id;
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
