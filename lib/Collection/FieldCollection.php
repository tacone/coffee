<?php

namespace Tacone\Coffee\Collection;

use Illuminate\Support\Collection;


class FieldCollection extends Collection
{
    public function push($field)
    {
        if ( !$field->name()) throw new \InvalidArgumentException("Cannot accept a field with an empty name");
        $this->items[$field->name()] = $field;
    }

}

