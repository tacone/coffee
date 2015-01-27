<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\StringableTrait;

class Outputtable
{
    use StringableTrait;
    /**
     * @var CompositeOutputtable
     */
    public $before;
    public $content;
    /**
     * @var CompositeOutputtable
     */
    public $after;

    public function __construct($content)
    {
        $this->content = new Attribute($content);
        $this->before = new CompositeOutputtable();
        $this->after = new CompositeOutputtable();
    }

    protected function render()
    {
        return $this->before
        .$this->content()
        .$this->after;
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

//    protected function content()
//    {
//        if (is_safe_callable($this->content)) {
//            $func = $this->content;
//
//            return $func();
//        } else {
//            return $this->content;
//        }
//    }
}
