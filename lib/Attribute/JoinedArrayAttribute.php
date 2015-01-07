<?php


namespace Tacone\Coffee\Attribute;

class JoinedArrayAttribute extends CollectionAttribute
{

    protected $separator;

    public function __construct($value = [], $separator = ' ')
    {
        parent::__construct((array) $value);
        $this->separator = $separator;
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
