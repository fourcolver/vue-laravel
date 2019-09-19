<?php

namespace App\Traits;

use BeyondCode\Comments\Traits\HasComments as OriginalHasTraits;
use BeyondCode\Comments\Contracts\Commentator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UniqueIDFormat
{
    /**
     * @param $id
     * @return mixed
     */
    public function getUniqueIDFormat($id)
    {
        $format = $this->getTable();
        $format = Str::singular($format);
        $format = strtoupper($format);
        $format .= '_FORMAT';
        $format = env($format, 'TE-ID');

        $len = strlen($id);
        if ($len < 6) {
            for ($i = 0; $i < (6 - $len); $i++) {
                $id = '0' . $id;
            }
        }

        return str_replace('ID', $id, $format);
    }
}
