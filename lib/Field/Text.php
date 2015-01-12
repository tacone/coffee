<?php

namespace Tacone\Coffee\Field;

class Text extends Field
{
    public function control()
    {
        return \Form::text($this->htmlName(), $this->value, $this->buildHtmlAttributes());
    }
}
