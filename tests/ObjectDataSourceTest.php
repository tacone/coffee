<?php

namespace Tacone\Coffee\Test;

use Tacone\Coffee\DataSource\DataSource;

class ObjectDataSourceTest extends DataSourceTest
{

    protected function make(array $var)
    {
        // First we convert the array to a json string
        $json = json_encode($var);
        // The we convert the json string to a stdClass()
        $object = json_decode($json);

        return $object;
    }
}
