<?php

namespace Tacone\Coffee\Field;

class Textarea extends Field
{
    public function control()
    {
        return \Form::textarea($this->htmlName(), $this->value, $this->buildHtmlAttributes());
    }
}
