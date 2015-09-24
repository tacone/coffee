<?php

namespace Tacone\Coffee\Test;

use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('customers', function (Blueprint $table) {
            $table->create();
            $table->increments('id');
            $table->string('name');
            $table->string('surname');
            $table->timestamps();
        });

        Schema::table('customer_details', function (Blueprint $table) {
            $table->create();
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('biography');
            $table->boolean('accepts_cookies');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->create();
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('code');
            $table->string('shipping');
            $table->timestamps();
        });
    }

    protected function includeModels()
    {
        foreach (glob(__DIR__.'/models/*.php') as $file) {
            require_once $file;
        }
    }

    public function createModels($className, $data)
    {
        (new $className())->truncate();

        foreach ($data as $record) {
            $model = new $className();
            foreach ($record as $key => $value) {
                $model->$key = $value;
            }
            $model->save();
        }
    }
}
