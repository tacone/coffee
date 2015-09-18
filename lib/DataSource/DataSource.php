<?php

namespace Tacone\Coffee\DataSource;

use Tacone\Coffee\Base\DelegatedArrayTrait;

class DataSource implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;

    protected $storage;

    public function __construct($var)
    {
        $this->storage = $var;
    }

    public function getDelegatedStorage()
    {
        return $this->storage;
    }

    public static function make($var)
    {
        return new static($var);
    }

    protected function splitOffset($offset)
    {
        $tokens = explode('.', $offset);

        return [array_shift($tokens), implode('.', $tokens)];
    }

    public function offsetGet($offset)
    {
        list($key, $path) = $this->splitOffset($offset);
        $data = $this->read($key);
        switch (true) {
            case $path:
                // more hops to go
                //if (is_null($data) || is_scalar($data)) {
                //    return $data;
                //}
                $source = static::make($data);

                return $source[$path];
            default:
                // look: a leaf
                return $this->read($key);
        }
    }

    public function read($key)
    {
        return isset($this->getDelegatedStorage()[$key])
            ? $this->getDelegatedStorage()[$key]
            : null;
    }
}
