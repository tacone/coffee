<?php

namespace Tacone\Coffee\DataSource;

class ScalarDataSource extends AbstractDataSource
{
    protected function read($key)
    {
        return null;
    }

    protected function write($key, $value)
    {
        throw new \RuntimeException("You can't overwrite a scalar with a composite");
    }

    protected function arrayize()
    {
        throw new \RuntimeException("You can't convert a scalar to array");
    }

    protected function unsets($key)
    {
        throw new \RuntimeException("Scalars does not support unsets");
    }

}