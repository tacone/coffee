<?php

namespace Tacone\Coffee\Test;

use Illuminate\Foundation\Application;
use Schema;

class BaseTestCase extends \Illuminate\Foundation\Testing\TestCase
{
    // see: https://github.com/sebastianbergmann/phpunit/issues/856
    // and: https://github.com/sebastianbergmann/phpunit/issues/314
    protected $preserveGlobalState = false;

    public function __construct()
    {
        error_reporting(-1);
        $args = func_get_args();

        $this->includeModels();
        $this->registerMethods();

        return call_user_func_array('parent::__construct', $args);
    }

    public function createApplication()
    {

//        require $this->getF . '/phpunit/Framework/Assert/Functions.php';
        $unitTesting = true;
        $testEnvironment = 'testing';

        /** @var Application $app */
        $app = require __DIR__.'/../../../../bootstrap/start.php';

        \Config::set('database.default', 'sqlite');
        \Config::set('database.connections', [
            'sqlite' => array(
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ),
        ]);

        $this->createDatabase();

        return $app;
    }

    protected function createDatabase()
    {
        Schema::dropIfExists('customers');

        //create all tables
        Schema::table('customers', function ($table) {
            $table->create();
            $table->increments('id');
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->timestamps();
        });
    }

    protected function includeModels()
    {
        foreach (glob(__DIR__.'/models/*.php') as $file) {
            require_once $file;
        }
    }

    protected function registerMethods()
    {
        // I don't know how to include Framework/Assert/Functions.php
        // and I don't give a dime.

        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'assert') === 0) {
                if (!function_exists($method)) {
                    eval("
                    function $method()
                    {
                        return call_user_func_array(
                            'PHPUnit_Framework_Assert::$method',
                            func_get_args()
                        );
                    }
                    ");
                }
            }
        }
    }
}
