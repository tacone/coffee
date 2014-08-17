<?php

namespace Tacone\Coffee\Field;

class Text extends Field
{

    public function output()
    {
        return \Form::text($this->name);
    }

}
