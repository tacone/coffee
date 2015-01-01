<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\StringableTrait;

class JoinedArrayAttribute extends CollectionAttribute
{

    protected $separator;

    public function __construct($value = [], $separator = ' ')
    {
        $value = (array) $value;
        parent::__construct($value);
        $this->separator = $separator;
    }

    public function get()
    {
        if (is_callable($this->callback)) {
            $func = $this->callback;

            return $func($this->value->getArrayCopy());
        }

        return $this->value->getArrayCopy();
    }

    public function set($value)
    {
        if (is_string($value)) {
            $value = explode($this->separator, $value);
        }
        if (!$value) {
            $value = [];
        }

        return parent::set($value);
    }

    /**
     * Required by StringableTrait, must return a string;
     * @return string
     */
    protected function render()
    {
        return join($this->separator, $this->get());
    }

}
