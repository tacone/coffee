<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\StringableTrait;

class Label
{
    use StringableTrait;

    public $name = '';
    public $value = true;
    public $options = ['class' => 'control-label'];
    /**
     * @var null
     */
    private $htmlId;

    public function __construct($name, $label = null, $htmlId = null)
    {
        if ($label) $this->value = $label;
        $this->name = (string) $name;
        $this->htmlId = $htmlId;
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

        return (string) $this->value;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function output()
    {
        return \Form::label($this->htmlId, $this->get(), $this->options);
    }

    protected function guess()
    {
        $value = (string) $this->name;
        $value = str_replace(['-', '_'], '.', $value);
        $words = explode('.', $value);

        return ucfirst(join(' ', $words));
    }
}
