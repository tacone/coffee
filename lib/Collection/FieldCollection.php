<?php

namespace Tacone\Coffee\Collection;

use Illuminate\Support\Contracts\ArrayableInterface;
use IteratorAggregate;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\FieldStorage;
use Tacone\Coffee\Field;
use Traversable;

class FieldCollection implements \Countable, \IteratorAggregate, \ArrayAccess, ArrayableInterface
{
    use DelegatedArrayTrait;

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
        if (is_object($name)) {
            $name = $name->name();
        }
        return $this->storage->offsetExists($name);
    }


    protected function getDelegatedStorage()
    {
        return $this->storage;
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


}