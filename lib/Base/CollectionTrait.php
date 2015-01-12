<?php

namespace Tacone\Coffee\Base;

/**
 * Handy collection methods for DelegatedArray implementations
 */

trait CollectionTrait
{
    public function add($value)
    {
        return $this[] = $value;
    }

    public function has($value)
    {
        foreach ($this->getIterator() as $v) {
            if ($v == $value) {
                return true;
            }
        }

        return false;
    }
    public function set($value)
    {
        if (is_string($value)) {
            $value = explode($this->separator, $value);
        }
        if (!$value) {
            $value = [];
        }

        return static::set($value);
    }
    public function removeAll()
    {
        $this->getDelegatedStorage()->exchangeArray([]);

        return $this;
    }
    public function remove($value)
    {
        $array = $this->toArray();
        foreach ($array as $k => $v) {
            if ($v == $value) {
                unset($array[$k]);
            }
        }
        $this->getDelegatedStorage()->exchangeArray($array);

        return $this;
    }
}
