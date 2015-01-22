<?php

namespace Tacone\Coffee\Field;

class Select extends Field
{
    protected $options = [];
    protected $optionsLabel = "---";

    public function renderEdit()
    {
        $label = $this->optionsLabel === false || $this->optionsLabel === null ? [] : ["" => $this->optionsLabel];

        return \Form::select($this->htmlName(), $label + $this->options, $this->value, $this->buildHtmlAttributes());
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
