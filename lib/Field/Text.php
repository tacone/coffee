<?php

namespace Tacone\Coffee\Field;

class Text extends Field
{
    public function content()
    {
        return \Form::text($this->htmlName(), $this->value, $this->buildHtmlAttributes());
    }
}
