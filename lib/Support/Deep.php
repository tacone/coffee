<?php

namespace Tacone\Coffee\Support;

/**
 * You neighbourly dot array getter.
 */
class Deep
{
    protected $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function has($keys)
    {
        $keys = explode('.', $keys);
        $target = $this->source;
        foreach ($keys as $key) {
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                return false;
            }
        }
        return true;
    }

    public function get($keys)
    {
        $keys = explode('.', $keys);
        $target = $this->source;
        foreach ($keys as $key) {
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                return null;
            }
        }
        return $target;
    }

    function set($keys, $value)
    {
        $keys = explode('.', $keys);
        $target = $this->source;
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                die ("$key not found");
            }
        }
        $key = array_shift($keys);
        $target->$key = $value;

        return $this;
    }
} 