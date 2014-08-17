<?php namespace Tacone\Coffee\Field;

abstract class Field
{
    protected $name;
    
    function __construct($name)
    {
        $this->name = $name;
    }
    
    abstract public function output();
}