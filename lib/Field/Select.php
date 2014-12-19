<?php

namespace Tacone\Coffee\Field;

class Select extends Field
{
    protected $options = [];
    protected $optionsLabel = "---";

    public function control()
    {
        $name = to_html_array_notation($this->name);
        $label = $this->optionsLabel === false || $this->optionsLabel === null ? [] : ["" => $this->optionsLabel];

        return \Form::select($name, $label + $this->options, $this->value, $this->attr);
    }

    public function options($options, $label = null)
    {
        $this->options = $options;
        if (func_num_args() == 2) {
            $this->optionsLabel = $label;
        }
        return $this;
    }

}
