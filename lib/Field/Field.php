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

    protected  $htmlId;

    protected $options = ['class' => 'form-control'];

    public function __construct($name, $label = null)
    {
        $htmlId = md5(microtime().rand(0,1e5));
        $this->options['id'] = $htmlId;
        $this->options['data-id'] = $name;

        $this->name = new Attribute($name);
        $this->value = new Attribute();
        $this->label = new Label($name, $label, $htmlId);
    }

    abstract public function control();

    public function output()
    {
        return '<div class="form-group">'
        . $this->label->output() . "\n"
        . $this->control() . "\n"
        . '</div>';
    }

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
            $result = call_user_func_array($this->$method, $parameters);
            // don't break field level chaining
            return is_object($result) ? $this : $result;
        }
        if (isset($this->$method)) {
            throw new \RuntimeException("No method named $method");
        }
    }


}
