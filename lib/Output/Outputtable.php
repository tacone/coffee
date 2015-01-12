<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Attribute\CssAttribute;
use Tacone\Coffee\Attribute\DictionaryAttribute;
use Tacone\Coffee\Attribute\JoinedArrayAttribute;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\StringableTrait;

class Outputtable
{
    use StringableTrait;

    public $attr;
    public $class;

    public $control;

    public function __construct($control)
    {
        $this->attr = new DictionaryAttribute();
        $this->class = new JoinedArrayAttribute([], ' ');
        $this->css = new CssAttribute();
        $this->control = $control;
    }

    protected function render()
    {
        if (is_safe_callable($this->control)) {
            $func = $this->control;

            return $func($this);
        } else {
            return $this->control;
        }
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
