<?php


namespace Tacone\Coffee\Attribute;

class Attribute
{
    var $value = true;

    public function __construct($value)
    {
        $this->set($value);
    }

    public function __invoke()
    {
        $arguments = func_get_args();
        if (!count($arguments)) return $this->get();
        return call_user_func_array([$this, 'set'], $arguments);
    }

    public function get()
    {
        return $this->value;
    }

    public function set($value)
    {
        $this->value = $value;
        return $this;
    }
    public function __toString(){
        return (string)$this->get();
    }
} 