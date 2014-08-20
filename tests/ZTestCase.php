<?php

namespace Tacone\Coffee\Test;

class ZTestCase extends \Orchestra\Testbench\TestCase
{

    protected function getPackageProviders()
    {
        return array('Tacone\Coffee\CoffeeServiceProvider');
    }

}
