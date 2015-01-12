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
    /**
     * @var CompositeOutputtable
     */
    public $before;
    /**
     * @var CompositeOutputtable
     */
    public $after;

    protected $content;
    protected $closeMe;

    public function __construct($tagName, $content = '', $closeMe = true)
    {
        $this->attr = new DictionaryAttribute();
        $this->class = new JoinedArrayAttribute([], ' ');
        $this->css = new CssAttribute();
        $this->tagName = new Attribute($tagName);
        $this->content = $content;
        $this->closeMe = $closeMe;
        $this->before = new CompositeOutputtable();
        $this->after = new CompositeOutputtable();
    }

    public static function createWrapper($tagName)
    {
        $start = new static ($tagName, false, false);
        $end = new Outputtable([$start, 'closeTag']);

        return [$start, $end];
    }

    protected function render()
    {
        return $this->before
        . $this->control()
        . $this->after;
    }

    protected function control()
    {
        $attributes = Html::renderAttributes($this->buildHtmlAttributes());
        $output = "<{$this->tagName} $attributes";
        $output .= !$this->content && $this->closeMe ? '>' : '/>';
        $output .= $this->content ?: '';
        $output .= $this->content && $this->closeMe ? $this->closeTag() : '';

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
