<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
            case $var instanceof Collection:
                return new EloquentCollectionDataSource($var);
            case $var instanceof Model:
                return new EloquentModelDataSource($var);
            case is_object($var):
                return new ObjectDataSource($var);
            case is_array($var):
                return new ArrayDataSource($var);
            case is_scalar($var) || is_null($var):
                return new ScalarDataSource($var);
            default:
                throw new \LogicException(
                    'Datasource does not support type '.get_type_class($var)
                );
        }
    }
}
