<?php

namespace Tacone\Coffee\Field;

class Textarea extends Field
{
    public function control()
    {
        $name = to_html_array_notation($this->name);

        return \Form::textarea($name, $this->value, $this->attr);
    }

}
