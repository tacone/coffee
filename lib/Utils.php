<?php


namespace Tacone\Coffee;

class Utils
{
    public static function undot($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            array_set($result, $key, $value);
        }

        return $result;
    }

    public static function toHtmlNotation($key)
    {
        $segments = explode('.', $key);
        $result = array_shift($segments);
        foreach ($segments as $s) {
            $result .= "[$s]";
        }

        return $result;
    }
}
