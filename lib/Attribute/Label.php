<?php


namespace Tacone\Coffee\Attribute;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Tacone\Coffee\Base\StringableTrait;

class Label
{
    use StringableTrait;

    var $name = '';
    var $value = true;
    var $options = ['class' => 'control-label'];

    public function __construct($name, $label = null)
    {
        if ($label) $this->value = $label;
        $this->name = (string)$name;
    }

    public function __invoke()
    {
        $arguments = func_get_args();
        if (!count($arguments)) return $this->get();
        return call_user_func_array([$this, 'set'], $arguments);
    }

    public function get()
    {
        if ($this->value === true) {
            return $this->guess();
        }
        return (string)$this->value;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function output()
    {
        return \Form::label($this->name, $this->get(), $this->options);
    }

    protected function guess()
    {
        $value = (string)$this->name;
        $value = str_replace(['-', '_'], '.', $value);
        $words = explode('.', $value);

        return ucfirst(join(' ', $words));
    }
} 