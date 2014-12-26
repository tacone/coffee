<?php


namespace Tacone\Coffee\DataSource;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tacone\Coffee\Base\DelegatedArrayTrait;

/**
 * You neighbourly dot syntax data source.
 */
class DataSource implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;

    protected $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public static function make($source)
    {
        return new static($source);
    }

    public function unwrap()
    {
        return $this->getDelegatedStorage();
    }

    protected function getDelegatedStorage()
    {
        return $this->source;
    }

    public function offsetGet($offset)
    {
        $key = null;
        $source = $this->find($offset, $key);
        if (is_object($source)) {
            return $source->read($key);
        }
        throw new \LogicException("Last source must be object");
    }

    protected function find($offset, &$key)
    {
        $tokens = explode('.', $offset);
        $key = array_shift($tokens);

        if (!$tokens) {
            return $this;
        }
        $source = static::make($this->read($key));
        $offset = join('.', $tokens);
        return $source->find($offset, $key);
    }

    public function findRelations($offset, &$key)
    {
        $result = [];
        $result2 = [];

        $tokens = explode('.', $offset);
        $key = array_shift($tokens);

//        if (!$tokens) {
//            return [$offset, get_class($this->source)];
//        }
        $found = $this->read($key);
        if (is_object($found)) {
            $result = [$offset, get_class($found)];
            $source = static::make($found);
            $offset2 = join('.', $tokens);
            $result2 = $source->findRelations($offset2, $key);
        }
        if ($result2)
        {
            $result = array_merge($result, $result2);
        }
        return $result;
    }

    protected function read($key)
    {
        if (isset($this->source->$key)) {
            return $this->source->$key;
        }
        $result = $this->create($key);
        return $result;
    }

    protected function create($key)
    {
        if ($value = $this->source->$key) {
            return $value;
        }

        if (method_exists($this->source, $key)) {
            $relation = $this->source->$key();
            $model = $relation->getModel();
            switch (true) {
                case $relation instanceof HasOne:
                    $model->setAttribute($relation->getPlainForeignKey(), $relation->getParentKey());
                    break;
                case $relation instanceof BelongsTo:
                    $relation->associate($model);
                    break;
                default:
                    throw new \RuntimeException(
                        "Unsupported relation " . get_class($relation)
                        . "|" . get_class($model) . " found in " . get_class($this->source)
                        . "::" . $key);
            }
            $this->source->$key = $model;
            return $model;
        }
        return null;
        throw new \RuntimeException("Creation not supported for key: '$key'");
    }

    protected function write($key, $value)
    {
        $this->source->$key = $value;
    }

    function offsetSet($offset, $value)
    {

        $key = null;
        $source = $this->find($offset, $key);
        if (is_object($source)) {
            $source->write($key, $value);
            return;
        }
        throw new \LogicException("Last source must be object");

//        $tokens = explode('.', $offset);
//        $key = array_shift($tokens);
//
//        if (!$tokens) {
//            $this->write($key, $value);
//        }
//
//        $tokens = join('.', $tokens);
//
//
//
//        $source = static::make($this->read($token));
//        return $source[];


        $tokens = explode('.', $offset);
        $target = $this->source;
        while (count($tokens) > 1) {
            $key = array_shift($tokens);
            if (isset($target->$key)) {
                $target = $target->$key;
            } else {
                throw new \RuntimeException("Offset \"$offset\" not found");
            }
        }
        $key = array_shift($tokens);
        $target->$key = $value;

        return $this;
    }

    public function offsetExists($offset)
    {
        return (boolean)$this->offsetGet($offset);
//        $offset = explode('.', $offset);
//        $target = $this->source;
//        foreach ($offset as $key) {
//            if (isset($target->$key)) {
//                $target = $target->$key;
//            } else {
//                return false;
//            }
//        }
//        return true;
    }


}