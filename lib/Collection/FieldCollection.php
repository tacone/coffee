<?php

namespace Tacone\Coffee\Collection;

use Illuminate\Support\Contracts\ArrayableInterface;
use IteratorAggregate;
use Tacone\Coffee\Base\FieldStorage;
use Tacone\Coffee\Field;
use Traversable;

class FieldCollection implements \Countable, \IteratorAggregate, \ArrayAccess, ArrayableInterface
{
    protected $array;

    public function __construct()
    {
        $this->storage = new FieldStorage();
    }

    public function add($object)
    {
        return $this->storage[$object->name()] = $object;
    }

    public function get($name)
    {
        return $this->storage[$name];
    }

    public function contains($name)
    {
        if (is_object($name)) $name = $name->name();
        return $this->storage->offsetExists($name);
    }


    public function count()
    {
        return $this->storage->count();
    }

    public function offsetExists($name)
    {
        return $this->storage->offsetExists($name);
    }

    public function offsetSet($name, $field)
    {
        return $this->storage->offsetSet($name, $field);
    }

    public function offsetUnset($name)
    {
        return $this->storage->offsetUnset($name);
    }

    public function offsetGet($name)
    {
        return $this->storage->offsetGet($name);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->storage as $key => $field) {
            $array[$field->name()] = $field->value();
        }
        return $array;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->storage;
    }
}