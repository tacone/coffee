<?php

namespace Tacone\Coffee;

use App;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Str;

class CoffeeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('tacone/coffee');
    }

    protected function registerFields()
    {
        $namespace = "\\Tacone\\Coffee\\Field";
        $fields = ['text'];
        foreach ($fields as $class) {
            App::bind("coffee.$class",
                      function($app, $arguments) use($class, $namespace) {
                $class = Str::studly($class);
                $reflect = new ReflectionClass($namespace . "\\$class");
                $instance = $reflect->newInstanceArgs($arguments);
                return $instance;
            });
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFields();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
