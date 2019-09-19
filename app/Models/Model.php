<?php

namespace App\Models;

use Eloquent;

class Model extends Eloquent
{
    // relationExists returns whether the relation named $key exists, is loaded
    // and is not null
    public function relationExists($key)
    {
        return parent::relationLoaded($key) && isset($this->$key);
    }
}
