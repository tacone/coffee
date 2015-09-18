<?php

namespace Tacone\Coffee\DataSource;

use Tacone\Coffee\Base\DelegatedArrayTrait;

abstract class AbstractDataSource implements \Countable, \IteratorAggregate, \ArrayAccess
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

    /**
     * @param $var
     * @return AbstractDataSource
     */
    public static function make($var)
    {
        switch (true) {
            case is_scalar($var) || is_null($var):
                return new ScalarDataSource($var);
            case is_array($var):
                return new ArrayDataSource($var);
        }
    }

    protected function splitOffset($offset)
    {
        $tokens = explode('.', $offset);

        return [array_shift($tokens), implode('.', $tokens)];
    }

    public function offsetGet($offset)
    {
        // First we get the key to retrieve and the eventual path
        // to get from it
        list($key, $path) = $this->splitOffset($offset);

        // We retrieve the key value, and if it's null there's no
        // value in continuing the recursion
        $data = $this->read($key);
        if (is_null($data)) {
            return $data;
        }
        if ($path) {
            // more hops to go, so we recurse
            $source = static::make($data);
            return $source[$path];
        }
        return $data;
    }

    public function offsetExists($offset)
    {
        return !is_null($this->offsetGet($offset));
    }

    abstract public function read($key);
}
