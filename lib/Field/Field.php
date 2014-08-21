<?php namespace Tacone\Coffee\Field;

abstract class Field
{
    protected $name;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function name ($value = null)
    {
        if (!func_num_args()) return $this->name;
    }
    
    abstract public function output();
}