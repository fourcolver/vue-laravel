<?php

namespace App\Http\Requests\API\Audit;

use InfyOm\Generator\Request\APIRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use App\Models\ServiceRequest;

class ListRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $u = $this->user();
        if ($u->can('list-audit')) {
            return true;
        }

        if ($u->tenant && Relation::$morphMap[$this->auditable_type] == ServiceRequest::class) {
            return ServiceRequest::where('id', $this->auditable_id)
                ->where('tenant_id', $u->tenant->id)->exists();
        }

        return false;
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
