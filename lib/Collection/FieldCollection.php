<?php

namespace Tacone\Coffee\Collection;

use Illuminate\Support\Collection;
use Tacone\Coffee\Field\Field;

class FieldCollection extends Collection
{

    public function __construct(array $items = array())
    {
        foreach ($this->getArrayableItems($items) as $field) $this->push($field);
    }

    public function push($field)
    {
        if ( !$field instanceof Field) throw new \InvalidArgumentException("Field subclass expected");
        if (!$field->name()) throw new \InvalidArgumentException("Cannot accept a field with an empty name");
        $this->items[$field->name()] = $field;
    }

    /**
     * Adds an item. $key is discarded in favour of item's name
     *
     * @param  mixed  $key
     * @param  mixed  $field
     * @return void
     */
    public function offsetSet($key, $field)
    {
        $this->push($field);
    }

    public function flatten()
    {
        return $this->values();
    }

    /**
     * Diff the collection with the given items.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
     * @return static
     */
    public function diff($items)
    {
        // avoid to string conversion of array_diff
        $me = $this;
        return new static(array_filter($this->items, function($v) use ($items, $me) {
                return !in_array($v, $this->getArrayableItems($items), true);
            }));
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Support\Contracts\ArrayableInterface|array  $items
     * @return static
     */
    public function intersect($items)
    {
        // avoid to string conversion of array_intersect
        $me = $this;
        return new static(array_filter($this->items, function($v) use ($items, $me) {
                return in_array($v, $this->getArrayableItems($items), true);
            }));
    }

    /**
     * WARNING: This method makes no sense with an object collection, it's implemented just to
     * stay compatbile with Illuminate\Support\Collection interface
     * 
     * Return only unique items from the collection array.
     *
     * @return static
     */
    public function unique()
    {
        // avoid to string conversion of array_unique by using SORT_REGULAR flag
        return new static(array_unique($this->items, SORT_REGULAR));
    }

    /**
     * WARNING: This method makes no sense with an object collection, it's implemented just to
     * stay compatbile with Illuminate\Support\Collection interface
     * 
     * Collapse the collection items into a single array.
     *
     * @return static
     */
    public function collapse()
    {
        return new static($this->items);
    }

    /**
     * WARNING: This method makes no sense. Will throw a BadMethodCallException
     */
    public function flip()
    {
        throw new \BadMethodCallException("flip() method is not supported");
    }

    /**
     * WARNING: This method makes no sense. Will throw a BadMethodCallException
     */
    public function chunk($size, $preserveKeys = false)
    {
        throw new \BadMethodCallException("flip() method is not supported");
    }

    /**
     * WARNING: TODO. Will throw a BadMethodCallException for now
     */
    public function splice($offset, $length = 0, $replacement = array())
    {
        throw new \BadMethodCallException("flip() method is not supported");
    }

    /**
     * WARNING: This method makes no sense. Will throw a BadMethodCallException
     */
    public function transform(\Closure $callback)
    {
        throw new \BadMethodCallException("flip() method is not supported");
    }

}
