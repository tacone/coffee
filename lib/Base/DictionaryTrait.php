<?php

namespace Tacone\Coffee\Base;

/**
 * Handy collection methods for DelegatedArray implementations
 */

trait DictionaryTrait
{
    public function add($key, $value)
    {
        $this[$key] = $value;
    }

    public function has($key)
    {
        return isset($this[$key]);
    }

    public function remove($key)
    {
        unset($this[$key]);
    }

}