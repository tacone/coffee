<?php

namespace Tacone\Coffee\DataSource;

class ArrayDataSource extends AbstractDataSource
{
    public function read($key)
    {
        return isset($this->getDelegatedStorage()[$key])
            ? $this->getDelegatedStorage()[$key]
            : null;
    }
}