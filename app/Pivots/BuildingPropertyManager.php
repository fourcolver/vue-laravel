<?php

namespace App\Pivots;

use App\Models\Building;
use App\Models\PropertyManager;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BuildingPropertyManager extends Pivot
{

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function propertyManager()
    {
        return $this->belongsTo(PropertyManager::class);
    }

    public function districts()
    {
        return $this->hasManyThrough(Building::class, PropertyManager::class);
    }
}
