<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Base\StringableTrait;

class CallbackOutputtable
{
    use StringableTrait;

    protected $value;

    public function __construct(callable $value)
    {
        if (!is_safe_callable($value)) {
            throw new \InvalidArgumentException(unsafe_callable_error_message($value));
        }
        $this->value = $value;
    }

    protected function render()
    {
        $func = $this->value;

        return $func($this);
    }
}
