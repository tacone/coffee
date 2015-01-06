<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Attribute\CssAttribute;
use Tacone\Coffee\Attribute\DictionaryAttribute;
use Tacone\Coffee\Attribute\JoinedArrayAttribute;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Helper\Html;

class Tag
{
    use StringableTrait;

    public $attr;
    public $class;

    protected $tagName;
    protected $content;
    protected $close;

    public function __construct($tagName, $content = '', $close = true)
    {
        $this->attr = new DictionaryAttribute();
        $this->class = new JoinedArrayAttribute([], ' ');
        $this->css = new CssAttribute();
        $this->tagName = $tagName;
        $this->content = $content;
        $this->close = $close;
    }

    protected function render()
    {
        $attributes = Html::renderAttributes($this->buildHtmlAttributes());
        $output = "<{$this->tagName} $attributes";
        $output .= !$this->content && $this->close ? '>' : '/>';
        $output .= $this->content ?: '';
        $output .= $this->content && $this->close ? "</{$this->tagName}>" : '';

        return $output;
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
