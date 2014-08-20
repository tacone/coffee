<?php

namespace Tacone\Coffee\Widget;

use Tacone\Coffee\Field\Text;


class DataForm
{
    public function __call($name, $arguments)
    {
        return \App::make("coffee.$name", $arguments);
    }
}