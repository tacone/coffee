<?php

namespace Tacone\Coffee\Field;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Attribute\ErrorsAttribute;
use Tacone\Coffee\Attribute\JoinedArrayAttribute;
use Tacone\Coffee\Attribute\Label;
use Tacone\Coffee\Base\Exposeable;
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

    public $rules;

    protected $htmlId;

    protected $attr = ['class' => 'form-control'];

    public function __construct($name, $label = null)
    {
        $htmlId = md5(microtime() . rand(0, 1e5));
        $this->attr['id'] = $htmlId;
        $this->attr['data-id'] = $name;

        $this->name = new Attribute($name);
        $this->value = new Attribute();
        $this->rules = new JoinedArrayAttribute('|');
        $this->label = new Label($name, $label, $htmlId);
        $this->errors = new ErrorsAttribute();
    }

    abstract public function control();

    protected function render()
    {
        $errors = $this->errors->output();
        $class = $errors ? ' has-error' : '';

        return '<div class="form-group'.$class.'">'
        . $this->label->output() . "\n"
        . $this->control() . "\n"
        . $errors . "\n"
        . '</div>';
    }

    /**
     * Implements a jQuery-like interface
     *
     * @param  string $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return Exposeable::handleExposeables($this, $method, $parameters);
//        return
//        if (is_callable($this->$method)) {
//            $result = call_user_func_array($this->$method, $parameters);
//            // don't break field level chaining
//            return is_object($result) ? $this : $result;
//        }
//        if (isset($this->$method)) {
//            throw new \RuntimeException("No method named $method");
//        }
    }

}
