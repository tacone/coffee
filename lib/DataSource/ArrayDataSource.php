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
    public function read($key)
    {
        return isset($this->getDelegatedStorage()[$key])
            ? $this->getDelegatedStorage()[$key]
            : null;
    }
    public function write($key, $value)
    {
        $this->getDelegatedStorage()->offsetSet($key, $value);
    }

    public function unwrap()
    {
        return $this->plainArray
            ? $this->getDelegatedStorage()->getArrayCopy()
            : $this->getDelegatedStorage();
    }
    public function arrayize() {
        return $this->getDelegatedStorage()->getArrayCopy();
        return to_array($this->unwrap());
    }
}