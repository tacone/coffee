<?php

namespace Tacone\Coffee\Base;

/**
 * Quick implementation of \Countable, \IteratorAggregate, \ArrayAccess
 *
 * You need to implement a getDelegatedStorage() method that returns the
 * actual object.
 */

trait DelegatedArrayTrait
{
    public function count()
    {
        return $this->getDelegatedStorage()->count();
    }

    public function offsetExists($name)
    {
        return $this->getDelegatedStorage()->offsetExists($name);
    }

    public function offsetSet($name, $field)
    {
        return $this->getDelegatedStorage()->offsetSet($name, $field);
    }

    public function offsetUnset($name)
    {
        return $this->getDelegatedStorage()->offsetUnset($name);
    }

    public function offsetGet($name)
    {
        return $this->getDelegatedStorage()->offsetGet($name);
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
        return $this->getDelegatedStorage();
    }

} 