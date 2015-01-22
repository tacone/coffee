<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Base\CopiableTrait;
use Tacone\Coffee\Base\StringableTrait;

class CallbackOutputtable
{
    use StringableTrait;
    use CopiableTrait;

    public $value;
    public $object;
    public $method;

    public function __construct($value, $method = null)
    {
        if (func_num_args() == 2) {
            $this->object = $value;
            $this->method = $method;
            if (!is_safe_callable([$this->object, $this->method])) {
                throw new \InvalidArgumentException(unsafe_callable_error_message($value));
            }

            return;
        }
        if (!is_safe_callable($value)) {
            throw new \InvalidArgumentException(unsafe_callable_error_message($value));
        }
        $this->value = $value;
    }

    protected function render()
    {
        if ($this->object) {
            return call_user_func([$this->object, $this->method]);
        }
        $func = $this->value;

        return $func($this);
    }
}
