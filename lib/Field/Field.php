<?php

namespace Tacone\Coffee\Field;

use Illuminate\Support\Fluent;

abstract class Field extends Fluent
{

    protected $attributes = [
        'name' => "",
        'label' => "",
    ];
    
    public function __construct($name, $label = null)
    {
        $this->name = $name;
        $this->label = $label ?: '';
    }

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
