<?php

namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\StringableTrait;

class Attribute
{
    use StringableTrait;
    use Exposeable;

    public $value = null;
    /** @var callable */
    public $callback = null;

    public function __construct($value = null)
    {
        if ($value !== null) {
            $this->set($value);
        }
    }

    public function __invoke()
    {
        $arguments = func_get_args();
        if (!count($arguments)) {
            return $this->get();
        }

        return call_user_func_array([$this, 'set'], $arguments);
    }

    protected function rawGet()
    {
        return $this->value;
    }

    public function get()
    {
        $value = $this->rawGet();
        if (is_safe_callable($this->callback)) {
            $func = $this->callback;

            return $func($value);
        }

        return $value;
    }

    public function set($value)
    {
        if (is_safe_callable($value)) {
            $this->callback = $value;

            return $this;
        }
        $this->value = $value;

        return $this;
    }

    /**
     * Required by StringableTrait, must return a string;.
     *
     * @return string
     */
    protected function render()
    {
        return (string) $this->get();
    }
}
