<?php

namespace Tacone\Coffee\DataSource;

use Tacone\Coffee\Base\DelegatedArrayTrait;

abstract class AbstractDataSource implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;

    protected $storage;

    public function __construct($var)
    {
        $this->storage = $var;
    }

    public function getDelegatedStorage()
    {
        return $this->storage;
    }

    /**
     * @param $var
     *
     * @return AbstractDataSource
     */
    protected function makeAndBind($var, $key = null)
    {
        return DataSource::make($var);
    }

    protected function splitOffset($offset)
    {
        $tokens = explode('.', $offset);

        return [array_shift($tokens), implode('.', $tokens)];
    }

    public function offsetGet($offset)
    {
        // First we get the key to retrieve and the eventual path
        // to get from it
        list($key, $path) = $this->splitOffset($offset);

        // We retrieve the key value, and if it's null there's no
        // value in continuing the recursion
        $data = $this->read($key);
        if (is_null($data)) {
            return $data;
        }

        return $this->wrapAndRecurse($path, $data);
    }

    // TODO add exception for EloquentCollection
    protected function wrapAndRecurse($path, $data)
    {
        // strict comparison is very important as PHP casts zero
        // and '0' to false, and at the same time, arrays are zero based
        // which means that a path like "helloworld.0" would be considered
        // as simply "helloworld"
        if ($path === '') {
            return $data;
        }

        // more hops to go, so we recurse
        $source = DataSource::make($data);

        return $source[$path];
    }

    public function offsetExists($offset)
    {
        return !is_null($this->offsetGet($offset));
    }

    public function offsetUnset($offset)
    {
        return $this->unsets($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->recursiveWrite($offset, $value);
    }

    protected function recursiveWrite($offset, $value)
    {
        // First we get the key to retrieve and the eventual path
        // to get from it
        list($key, $path) = $this->splitOffset($offset);

        // use strict checking here, '0' is a valid key
        if ($path !== '') {
            $node = $this->read($key);

            // we use count, because it works well either for NULL and EloquentCollections
            if (!count($node)) {
                // TODO empty many-relations are not null
                $node = $this->createChild($key);
                $this->throwIfCreateChildIsNull($key, $node, $path);
            }
            // let's write immediately, so we can cache the relation when using eloquent
            $this->write($key, $node);

            // use strict comparison because '0' should be valid value
            $value = $this->makeAndBind($node, $key)->recursiveWrite($path, $value);
        }
        $this->write($key, $value);

        return $this->unwrap();
    }

//    /**
//     * Evaluate whether we need to create a new child to replace an empty
//     * value.
//     *
//     * @param $key
//     * @param $child
//     *
//     * @return mixed
//     */
//    protected function shouldCreateChild($key, $child)
//    {
//        if (is_null($child)) {
//            // TODO empty many-relations are not null
//            $child = $this->createChild($key);
//            $this->throwIfCreateChildIsNull($key, $child);
////            $this->write($key, $child);
//        }
//
//        return $child;
//    }

    protected function throwIfCreateChildIsNull($key, $node, $path)
    {
        if (is_null($node)) {
            throw new \LogicException(sprintf(
                'createChild returned NULL (parent: %s, key: %s, path: %s)',
                get_type_class($this->getDelegatedStorage()), $key, $path
            ));
        }
    }

    public function unwrap()
    {
        return $this->getDelegatedStorage();
    }

    public function toArray()
    {
        $data = [];
        foreach ($this->arrayize() as $key => $value) {
            $data[$key] = !$value instanceof self ? $value : $value->arrayize();
        }

        return $data;
    }

    /**
     * Get a child.
     *
     * @param $key
     *
     * @return mixed
     */
    abstract protected function read($key);

    /**
     * Replace a child with another.
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    abstract protected function write($key, $value);

    /**
     * Shallowly cast the storage to array.
     *
     * @return mixed
     */
    abstract protected function arrayize();

    /**
     * Remove a child.
     *
     * @param $key
     *
     * @return mixed
     */
    abstract protected function unsets($key);

    /**
     * Add an empty child at the specified key offset.
     *
     * @param $key
     *
     * @return mixed
     */
    abstract protected function createChild($key);
}
