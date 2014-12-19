<?php


namespace Tacone\Coffee\DataSource;


use Tacone\Coffee\Base\DelegatedArrayTrait;

/**
 * You neighbourly dot syntax data source.
 */

class DataSource  implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;

    protected $source;

    public function __construct(&$source)
    {
        $this->source =& $source;
    }

    public static function make(&$source)
    {
        return new static($source);
    }

    protected function getDelegatedStorage()
    {
        return $this->source;
    }

    public function offsetExists($offset)
    {
        $offset = explode('.', $offset);
        $target = $this->source;
        foreach ($offset as $key) {
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                return false;
            }
        }
        return true;
    }

    public function offsetGet($offset)
    {
        $offset = explode('.', $offset);
        $target = $this->source;
        foreach ($offset as $key) {
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                return null;
            }
        }
        return $target;
    }

    function offsetSet($offset, $value)
    {
        $offset = explode('.', $offset);
        $target = $this->source;
        while (count($offset) > 1) {
            $key = array_shift($offset);
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                die ("offset $offset not found");
            }
        }
        $key = array_shift($offset);
        $target->$key = $value;

        return $this;
    }
}