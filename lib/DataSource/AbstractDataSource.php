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
     *
     * @return AbstractDataSource
     */
    public static function make($var)
    {
        return DataSource::make($var);
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

        // strict comparison is very important as PHP casts zero
        // and '0' to false
        if ($path === '') {
            return $data;
        }

        // more hops to go, so we recurse
        $source = static::make($data);

        return $source[$path];
    }

    public function offsetExists($offset)
    {
        return !is_null($this->offsetGet($offset));
    }

    public function offsetUnset($offset)
    {
        return $this->unsets($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->recursiveWrite($offset, $value);
    }

    protected function recursiveWrite($offset, $value)
    {
        // First we get the key to retrieve and the eventual path
        // to get from it
        list($key, $path) = $this->splitOffset($offset);

        // strict comparison is very important as PHP casts zero
        // and '0' to false
        if ($path !== '') {
            $node = $this->read($key);
            if (is_null($node)) {
                $node = $this->createChild($key);
            }
            $value = DataSource::make($node)->recursiveWrite($path, $value);
        }

        $this->write($key, $value);

        return $this->unwrap();
    }
    // TODO
    protected function createChild($key)
    {
        $element = [];
        $this->write($key, $element);

        return $element;
    }

    public function unwrap()
    {
        return $this->getDelegatedStorage();
    }

    public function toArray()
    {
        $data = [];
        foreach ($this->arrayize() as $key => $value) {
            $data[$key] = is_scalar($value) ? $value : $value->toArray();
        }

        return $data;
    }

    abstract protected function read($key);

    abstract protected function write($key, $value);

    abstract protected function arrayize();

    abstract protected function unsets($key);
}
