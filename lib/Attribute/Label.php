<?php


namespace Tacone\Coffee\Attribute;

class Label extends Attribute
{
    public $name = '';
    public $value = true;
    public $options = ['class' => 'control-label'];
    /**
     * @var null
     */
    private $htmlId;

    public function __construct($name, $label = null, $htmlId = null)
    {
        $this->name = (string) $name;
        $this->htmlId = $htmlId;

        parent::__construct($label);
    }

    public function get()
    {
        $value = ($this->value === true) ? $this->guess() : $this->value;

        if (is_safe_callable($this->callback)) {
            $func = $this->callback;

            return $func($value);
        }

        return $value;
    }

    protected function render()
    {
        return \Form::label($this->htmlId, $this->get(), $this->options);
    }

    protected function guess()
    {
        $value = (string) $this->name;
        $value = str_replace(['-', '_'], '.', $value);
        $words = explode('.', $value);

        return ucfirst(implode(' ', $words));
    }
    public function exposes()
    {
        return [
            'others' => ['output']
        ];
    }
}
