<?php

namespace Tacone\Coffee\DataSource;

class ArrayDataSource extends AbstractDataSource
{
    protected $plainArray = false;
    public function __construct(array &$var)
    {
        if (is_array($var)) {
            // getIterator() would complain otherwise
            $this->plainArray = true;
            $var = new \ArrayObject($var);
        }
        $this->storage = $var;
    }

    protected function read($key)
    {
        return isset($this->getDelegatedStorage()[$key])
            ? $this->getDelegatedStorage()[$key]
            : null;
    }

    protected function write($key, $value)
    {
        $this->getDelegatedStorage()->offsetSet($key, $value);
    }

    protected function unsets($key)
    {
        if ($this->offsetExists($key)) {
            return $this->getDelegatedStorage()->offsetUnset($key);
        }
    }

    public function unwrap()
    {
        return $this->plainArray
            ? $this->getDelegatedStorage()->getArrayCopy()
            : $this->getDelegatedStorage();
    }

    protected function arrayize()
    {
        return $this->getDelegatedStorage()->getArrayCopy();

        return to_array($this->unwrap());
    }

    protected function createChild($key)
    {
        $element = [];

        return $element;
    }
}
