<?php

namespace Tacone\Coffee\DataSource;

class DataSource
{
    /**
     * @param $var
     *
     * @return AbstractDataSource
     */
    public static function make($var)
    {
        switch (true) {
            case is_scalar($var) || is_null($var):
                return new ScalarDataSource($var);
            case is_array($var):
                return new ArrayDataSource($var);
            case is_object($var):
                return new ObjectDataSource($var);
            default:
                throw new \LogicException(
                    'Datasource does not support type '.get_type_class($var)
                );
        }
    }
}
