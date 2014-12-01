<?php


namespace Tacone\Coffee\Base;


trait StringableTrait
{
    public function __toString()
    {
        return $this->output();
    }
} 