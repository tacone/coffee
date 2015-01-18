<?php

namespace Tacone\Coffee\Widget;

use Tacone\Coffee\Output\Tag;

class Row extends DataForm
{
    protected function initWrapper()
    {
        list($this->start, $this->end) = Tag::createWrapper('tr');
    }

    /**
     * Renders the form as an HTML string.
     * This method is also called by __toString().
     * @return string
     */
    protected function render()
    {
        return $this->start
        .$this->fields
        .$this->end;
    }
}
