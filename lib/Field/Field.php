<?php

namespace Tacone\Coffee\Field;

use Illuminate\Support\Fluent;
use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Attribute\Label;
use Tacone\Coffee\Base\StringableTrait;

abstract class Field
{
    use StringableTrait;
    /**
     * @var Attribute
     */
    public $name;
    /**
     * @var
     */
    public $label;
    /**
     * @var
     */
    public $value;

    protected $options = ['class' => 'form-control'];

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



}
