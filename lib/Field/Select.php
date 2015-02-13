<?php

namespace Tacone\Coffee\Field;

use Illuminate\Support\Contracts\ArrayableInterface;

class Select extends Field
{
    protected $options = [];
    protected $optionsLabel = "---";

    public function renderEdit()
    {
        $label = $this->optionsLabel === false || $this->optionsLabel === null ? [] : ["" => $this->optionsLabel];

        $name = $this->htmlName();
        if ($this->attr->has('multiple')) {
            $name .='[]';
        }
        $value =  $this->value();
        If ($value instanceof ArrayableInterface) {
            $value = $value->toArray();
        }
        return \Form::select($name, $label + $this->options,$value, $this->buildHtmlAttributes());
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
