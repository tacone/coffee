<?php

namespace Tacone\Coffee\Field;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Attribute\CssAttribute;
use Tacone\Coffee\Attribute\DictionaryAttribute;
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

    public $attr;
    public $class;

    public function __construct($name, $label = null)
    {
        $this->attr = new DictionaryAttribute();
        $this->attr['id'] = md5(microtime() . rand(0, 1e5));
        $this->attr['data-id'] = $name;

        $this->class = new JoinedArrayAttribute(['form-control'], ' ');
        $this->css = new CssAttribute();
        $this->errors = new ErrorsAttribute();
        $this->label = new Label($name, $label, $this->attr['id']);
        $this->name = new Attribute($name);
        $this->rules = new JoinedArrayAttribute(null, '|');
        $this->value = new Attribute();
    }

    abstract public function control();

    protected function render()
    {
        $errors = $this->errors->output();
        $class = $errors ? ' has-error' : '';

        return '<div class="form-group' . $class . '">'
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
    }

    protected function buildHtmlAttributes()
    {
       return array_merge(
           $this->attr->toArray(),
           ['class' => $this->class->output()],
           ['style' => $this->css->output()]
       );
}
}
