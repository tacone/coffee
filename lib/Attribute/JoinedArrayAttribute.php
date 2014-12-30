<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\StringableTrait;

class JoinedArrayAttribute
{
    use StringableTrait;
    use DelegatedArrayTrait;
    use Exposeable;

    protected $value = [];
    protected $callback = null;
    protected $separator;

    public function __construct($separator, $value = [])
    {
        if ($value !== null) {
            $this->set($value);
        }
        $this->separator = $separator;
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
            if (!$value) {
                $value = [];
            } else {
                $value = explode($this->separator, $value);
            }
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
        return (string) $this->get();
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
