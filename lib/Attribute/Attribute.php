<?php


namespace Tacone\Coffee\Attribute;

class Attribute
{
    var $value = null;
    var $callback = null;
    public function __construct($value = null)
    {
        if ($value)
        {
            $this->set($value);
        }
    }

    public function __invoke()
    {
        $arguments = func_get_args();
        if (!count($arguments)) {
            return $this->get();
        }
        return call_user_func_array([$this, 'set'], $arguments);
    }

    public function get()
    {
        if (is_callable($this->callback))
        {
            $func = $this->callback;
            return $func($this->value);
        }
        return $this->value;
    }

    public function set($value)
    {
        if (is_callable($value))
        {
            $this->callback = $value;
            return $this;
        }
        $this->value = $value;
        return $this;
    }

    public function __toString()
    {
        return (string)$this->get();
    }
} 