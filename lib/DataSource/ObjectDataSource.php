<?php

namespace Tacone\Coffee\DataSource;

class ObjectDataSource extends AbstractDataSource
{
    public function __construct(\stdClass &$var)
    {
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
}
