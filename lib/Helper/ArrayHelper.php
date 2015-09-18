<?php

namespace Tacone\Coffee\Helper;

class ArrayHelper
{
    public static function undot($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            array_set($result, $key, $value);
        }

        return $result;
    }
}
