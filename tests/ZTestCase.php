<?php

namespace Tacone\Coffee\Test;

class ZTestCase extends \Orchestra\Testbench\TestCase
{
    public function __construct()
    {
        error_reporting(E_ALL);
        parent::__construct();
    }

    protected function getPackageProviders()
    {
        return array('Tacone\Coffee\CoffeeServiceProvider');
    }
}
