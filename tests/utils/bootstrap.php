<?php

require_once __DIR__.'/../../../../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/autoload.php';

// I don't know how to include Framework/Assert/Functions.php
// and I don't give a dime.
foreach (get_class_methods(\Tacone\Coffee\Test\BaseTestCase::class) as $method) {
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

require_once 'functions.php';
