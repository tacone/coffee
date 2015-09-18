<?php

namespace Tacone\Coffee\Base;

use Tacone\Coffee\Output\Tag;

/**
 * Reppresents an outputtable wrapped by another element.
 */
trait WrappableTrait
{
    /**
     * @var Outputtable
     */
    public $start;
    /**
     * @var Outputtable
     */
    public $end;

    /**
     * Switches the wrapper with another tag.
     *
     * @param string $tag
     *
     * @return object $this
     */
    public function wrap($tag)
    {
        list($this->start, $this->end) = Tag::createWrapper($tag);

        return $this;
    }

    /**
     * Call me from your constructor.
     */
    protected function initWrapper()
    {
        // override me
    }

    protected function render()
    {
        return $this->start
        .$this->content()
        .$this->end;
    }
}
