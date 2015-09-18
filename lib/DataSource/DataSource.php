<?php

namespace Tacone\Coffee\DataSource;

use Tacone\Coffee\Base\DelegatedArrayTrait;

class DataSource
{
    public static function make($var)
    {
        return new ArrayDataSource($var);
    }
}
