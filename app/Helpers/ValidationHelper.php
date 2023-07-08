<?php

namespace App\Helpers;

use InvalidArgumentException;

class ValidationHelper
{
    public static function errMobile($data) 
    {
        $errMessage = $data[0];
        return $errMessage;
    }
    
    /**
     * For experiment development
     */
    public static function experimental($data)
    {
        $errors = [];
        foreach ($data as $value) {
            $key = explode(' ', $value)[1];
            $errors[] = [
                'name'    => $key,
                'message' => $value,
            ];
        }
        return $errors;
    }
}
