<?php

namespace Tacone\Coffee\Field;

use Illuminate\Support\Fluent;

abstract class Field extends Fluent
{

    protected $attributes = [
        'name' => ""
    ];

//    protected $name;
//    
    public function __construct($name)
    {
        $this->name = $name;
    }

//    public function name ($value = null)
//    {
//        if (!func_num_args()) return $this->name;
//    }

    abstract public function output();
    
    /**
	 * Implements a jQuery-like interface
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return $this
	 */
	public function __call($method, $parameters)
	{
        if (  count($parameters) > 0 )
        {
            $this->attributes[$method] = $parameters[0];

            return $this;
        }
        if (isset($this->attributes[$method])) return $this->attributes[$method];
        throw new \RuntimeException("No attribute named $method");
	}
}
