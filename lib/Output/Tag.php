<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\HtmlAttributesTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Helper\Html;

class Tag extends Outputtable
{
    use StringableTrait;
    use HtmlAttributesTrait;

    /**
     * @var string
     */
    protected $closeMe;
    /**
     * @var Attribute
     */
    protected $tagName;

    public function __construct($tagName, $content = '', $closeMe = true)
    {
        parent::__construct($content);
        $this->initHtmlAttributes();

        $this->tagName = new Attribute($tagName);
        $this->closeMe = $closeMe;

    }

    public static function createWrapper($tagName)
    {
        $start = new static ($tagName, false, false);
        $end = new Outputtable([$start, 'closeTag']);

        return [$start, $end];
    }

    protected function content()
    {
        $content = parent::content();

        $attributes = Html::renderAttributes($this->buildHtmlAttributes());
        $output = "<{$this->tagName} $attributes";
        $output .= !$content && $this->closeMe ? '>' : '/>';
        $output .= $content ?: '';
        $output .= $content && $this->closeMe ? $this->closeTag() : '';

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
}
