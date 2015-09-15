<?php

namespace Tacone\Coffee\Test;

use Illuminate\Foundation\Application;

class BaseTestCase extends \Illuminate\Foundation\Testing\TestCase
{
    // see: https://github.com/sebastianbergmann/phpunit/issues/856
    // and: https://github.com/sebastianbergmann/phpunit/issues/314
    protected $preserveGlobalState = false;

    public function __construct()
    {
        error_reporting(-1);
        $args = func_get_args();

        return call_user_func_array('parent::__construct', $args);
    }
    public function createApplication()
    {
        $unitTesting = true;
        $testEnvironment = 'testing';

        /** @var Application $app */
        $app = require_once __DIR__.'/../../../../bootstrap/start.php';
        \Config::set('database.default', 'sqlite');
        \Config::set('database.connections', [
            'sqlite' => array(
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ),
        ]);

        return $app;
    }
    public function testSomething()
    {
        $this->assertTrue(true);
    }
}
