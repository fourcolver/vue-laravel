<?php

namespace App\Http\Requests\API\Building;

use App\Models\Building;
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
        if ($this->user()->can('view-building')) {
            return true;
        }

        // permit tenants to see their building
        $b = Building::where('id', $this->route('id'))->first();
        $t = $this->user()->tenant ?? null;

        return $t && $t->building_id == $b->id;
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
