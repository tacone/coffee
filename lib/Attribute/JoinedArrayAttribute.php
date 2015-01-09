<?php


namespace Tacone\Coffee\Attribute;

class JoinedArrayAttribute extends CollectionAttribute
{

    protected $separator;

    public function __construct($value = [], $separator = ' ')
    {
        // assign separator first, because it's needed by the set function
        // invoked by the parent constructor
        $this->separator = $separator;
        parent::__construct((array) $value);
    }

    /**
     * Adds an item or an array of items to the collection.
     * Strings will be split using the separator.
     * You can also pass the items using multiple parameters.
     *
     * If you prepend an item with a ! all the occurrence of that
     * item will be removed from the collection.
     *
     * @param $value
     * @return $this
     */
    public function add($value)
    {
        $values = func_get_args();
        foreach ($this->flattenValue($values) as $val) {
            if (\Str::startsWith($val, '!')) {
                if ($this->has(substr($val, 1))) {
                    $this->remove(substr($val, 1));
                }
            } else {
                parent::add($val);
            }
        }

        return $this;
    }

    public function __invoke()
    {
        if (!func_num_args()) {
            return parent::__invoke();
        }
        $arguments = func_get_args();

        return $this->add($arguments);
    }

    /**
     * Flatten a multi dimensional array and split all the strings
     * using the separator
     * @param $value
     * @return array
     */
    protected function flattenValue($value)
    {
        $value = (array) $value;
        $return = array();
        $separator = $this->separator;
        array_walk_recursive($value, function ($item) use (&$return, $separator) {
            if (is_string($item)) {
                $item = explode($separator, $item);
            }
            if (!is_array($item) && $item instanceof \Traversable) {
                $item = iterator_to_array($item);
            }
            if (is_array($item)) {
                $return = array_merge($return, $item);
            } else {
                $return[] = $item;
            }
        });
        $return = array_filter($return);

        return $return;
    }

    public function set($value)
    {
        $this->removeAll();
        $this->add($value);
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
