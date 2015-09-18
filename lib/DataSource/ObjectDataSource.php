<?php

namespace Tacone\Coffee\DataSource;

class ObjectDataSource extends AbstractDataSource
{
    public function __construct($var)
    {
        if (!is_object($var)) {
            throw new \RuntimeException(
                'Argument 1 passed to '.get_class($this)
                .' must be an object, var of type '.gettype($var).' given'
            );
        }
        $this->storage = $var;
    }

    public function read($key)
    {
        return isset($this->getDelegatedStorage()->$key)
            ? $this->getDelegatedStorage()->$key
            : null;
    }

    protected function write($key, $value)
    {
        $this->getDelegatedStorage()->$key = $value;
    }

    protected function unsets($key)
    {
        if ($this->offsetExists($key)) {
            unset($this->getDelegatedStorage()->$key);
        }
    }

    public function unwrap()
    {
        return $this->getDelegatedStorage();
    }

    protected function arrayize()
    {
        return (array) $this->unwrap();
    }

    protected function createChild($key)
    {
        $element = new \stdClass();
        $this->write($key, $element);

        return $element;
    }
}
