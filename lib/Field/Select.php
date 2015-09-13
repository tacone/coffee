<?php

namespace Tacone\Coffee\Field;

use Illuminate\Support\Contracts\ArrayableInterface;

class Select extends Field
{
    protected $options = [];
    protected $optionsLabel = '---';

    protected function selectedLabel()
    {
        $value = $this->value->output();
        if (isset($this->options[$value])) {
            return $this->options[$value];
        }

        return $value;
    }

    public function renderCompact()
    {
        $value = \Str::words(strip_tags($this->selectedLabel()));

        return $value ?: '&nbsp;';
    }
    public function renderShow()
    {
        return $this->selectedLabel() ?: '&nbsp;';
    }
    public function renderEdit()
    {
        $label = $this->optionsLabel === false || $this->optionsLabel === null ? [] : ['' => $this->optionsLabel];

        $name = $this->htmlName();
        if ($this->attr->has('multiple')) {
            $name .= '[]';
        }
        $value = $this->value();
        if ($value instanceof ArrayableInterface) {
            $value = $value->toArray();
        }

        return \Form::select($name, $label + $this->options, $value, $this->buildHtmlAttributes());
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
