<?php

namespace Tacone\Coffee\DataSource;

class ScalarDataSource extends AbstractDataSource
{
    protected function read($key)
    {
        return;
    }

    protected function write($key, $value)
    {
        throw new \RuntimeException(sprintf(
            "A scalar value can't be a datasource ("
            .'key: %s, type: %s, value: %s | '
            .'parentType: %s, parentValue: %s ',
            $key, get_type_class($value), $value,
            get_type_class($this->getDelegatedStorage()), $this->getDelegatedStorage()
        ));
    }

    protected function arrayize()
    {
        throw new \RuntimeException("You can't convert a scalar to array");
    }

    protected function unsets($key)
    {
        throw new \RuntimeException('Scalars does not support unsets');
    }

    protected function createChild($key)
    {
        throw new \RuntimeException('Scalars does not support createChild()');
    }
}
