<?php

namespace App\Helpers;

use InvalidArgumentException;

class TranslationHelper
{
    public static function tr($en = null, $id = null) 
    {
        $translate['en'] = $en ?? $id;
        $translate['id'] = $id ?? $en;
        return json_encode($translate);
    }
}
