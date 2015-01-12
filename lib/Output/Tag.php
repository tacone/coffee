<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Attribute\Attribute;
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
    public $tagName;

    protected $content;
    protected $close;

    public function __construct($tagName, $content = '', $close = true)
    {
        $this->attr = new DictionaryAttribute();
        $this->class = new JoinedArrayAttribute([], ' ');
        $this->css = new CssAttribute();
        $this->tagName = new Attribute($tagName);
        $this->content = $content;
        $this->close = $close;
    }
    public static function createWrapper($tagName)
    {
        $start = new static ($tagName, false, false);
        $end = new Outputtable([$start,'closeTag']);

        return [$start, $end];
    }
    protected function render()
    {
        $attributes = Html::renderAttributes($this->buildHtmlAttributes());
        $output = "<{$this->tagName} $attributes";
        $output .= !$this->content && $this->close ? '>' : '/>';
        $output .= $this->content ?: '';
        $output .= $this->content && $this->close ? $this->closeTag() : '';

        return $output;
    }
    public function closeTag()
    {
        return "</{$this->tagName}>";
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
