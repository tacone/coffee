<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\StringableTrait;

class ArrayAttribute
{
    use StringableTrait;
    use DelegatedArrayTrait;

    protected $value = [];
    protected $callback = null;

    public function __construct($value = [])
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

    public function get()
    {
        if (is_callable($this->callback)) {
            $func = $this->callback;

            return $func($this->value);
        }

        return $this->value;
    }

    public function set($value)
    {
        if (is_callable($value)) {
            $this->callback = $value;

            return $this;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Expecting an array, got a ' . gettype($value));
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Required by StringableTrait, must return a string;
     * @return string
     */
    protected function render()
    {
        foreach ($this->get() as $value) {
            return $value;
        }
    }

    /**
     * Required by DelegatedArrayTrait, must return the
     * storage array
     */
    public function getDelegatedStorage()
    {
        return $this->value;
    }
}
