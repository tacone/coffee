<?php

namespace Tacone\Coffee\Field;

class Text extends Field
{

    public function control()
    {
        return \Form::text($this->name, $this->value, $this->options);
    }

}
