<?php

namespace App\Helpers;

class StrHelper
{
    /**
     * @param $value
     *
     * @return bool
     */
    public static function isObjectId($value):bool
    {
        if ($value instanceof \MongoDB\BSON\ObjectID) {
            return true;
        }
        try {
            new \MongoDB\BSON\ObjectID($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
