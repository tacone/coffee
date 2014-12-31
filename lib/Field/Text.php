<?php

namespace Tacone\Coffee\Field;

class Text extends Field
{

    public function control()
    {
        $name = to_html_array_notation($this->name);

        return \Form::text($name, $this->value, $this->buildHtmlAttributes());
    }

}
