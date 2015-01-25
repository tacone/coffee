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

    protected function rawGet()
    {
        return $this->value === true ? $this->guess() : $this->value;
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
