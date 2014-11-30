<?php


namespace Tacone\Coffee\Attribute;

class Label
{
    var $hint = '';
    var $value = true;

    public function __construct($hint, $label = null)
    {
        if ($label) $this->value = $label;
        $this->hint = (string)$hint;
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
        return '<label>' . \HTML::escape($this->get()) . '</label>';
    }

    protected function guess()
    {
        $value = (string)$this->value;
        $value = str_replace(['-', '_'], '.', $value);
        $words = explode('.', $value);

        return ucfirst(join(' ', $words));
    }
} 