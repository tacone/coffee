<?php

namespace Tacone\Coffee\Field;

use Illuminate\Support\Fluent;
use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Attribute\Label;

abstract class Field
{

    protected $name;
    protected $label;
    protected $value;

    public function __construct($name, $label = null)
    {
        $this->name = new Attribute($name, '');
        $this->value = new Attribute(null);
        $this->label = new Label($name, $label);
    }

    abstract public function output();


    /**
     * Implements a jQuery-like interface
     *
     * @param  string $method
     * @param  array $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (is_callable($this->$method)) {
            return call_user_func_array($this->$method, $parameters);
        }
        if (isset($this->$method)) {
            throw new \RuntimeException("No method named $method");
        }
    }

//    /**
//     * Implements a jQuery-like interface
//     *
//     * @param  string $method
//     * @param  array $parameters
//     * @return $this
//     */
//    public function __call($method, $parameters)
//    {
//        if (isset($this->$method)) {
//            throw new \RuntimeException("No attribute named $method");
//        }
//        if (count($parameters) > 0) {
//            $this->$method->set($parameters[0]);
////            $this->attributes[$method] = $parameters[0];
//
//            return $this;
//        }
//
//        return $this->$method->get();
//
//    }

}
