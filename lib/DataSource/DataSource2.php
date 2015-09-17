<?php

namespace Tacone\Coffee\DataSource;

class DataSource2
{
    protected $storage;

    public function __construct($array)
    {
        $this->storage = $array;
    }

    protected function unshiftOffset($offset)
    {
    }

    public function offsetGet($offset)
    {
        $key = null;
        $source = $this->find($offset, $key);
        if (is_object($source)) {
            return $source->read($key);
        }
        throw new \LogicException('Last source must be object');
    }
}
