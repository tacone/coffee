<?php

namespace Tacone\Coffee\DataSource;

class ScalarDataSource extends AbstractDataSource
{
    public function read($key)
    {
        return null;
    }

    public function write($key, $value)
    {
        throw new \RuntimeException("You can't overwrite a scalar with a composite");
    }

    public function arrayize() {
        throw new \RuntimeException("You can't convert a scalar to array");
    }

}