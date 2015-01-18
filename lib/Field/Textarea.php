<?php

namespace Tacone\Coffee\Field;

class Textarea extends Field
{
    public function content()
    {
        return \Form::textarea($this->htmlName(), $this->value, $this->buildHtmlAttributes());
    }
}
