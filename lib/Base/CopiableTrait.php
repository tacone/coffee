<?php
namespace Tacone\Coffee\Base;

use DeepCopy\DeepCopy;

trait CopiableTrait
{
    public function copy()
    {
        $copy = new DeepCopy();

        return $copy->copy($this);
    }
}
