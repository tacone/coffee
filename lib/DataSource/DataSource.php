<?php


namespace Tacone\Coffee\DataSource;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected function getDelegatedStorage()
    {
        return $this->source;
    }

    public function offsetGet($offset)
    {
//        $tokens = explode('.', $offset);
//        $token = array_shift($tokens);
//
//        if (!$tokens) {
//            return $this->read($token);
//        }
//        $source = static::make($this->read($token));
//        return $source[join('.', $tokens)];

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
        return $source->find($offset,$key );
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
        if (method_exists($this->source, $key))
        {
            $result = $this->source->$key();
            $model = $result->getModel();
//            var_dump (method_exists($this->source, 'associate')); die;
            switch(true){
                case $result instanceof BelongsTo:
//                    $this->source->associate($model);
                    $result->associate($model);
                    break;
                default:
                    echo "found method $key of type: ".get_class($result);
                    echo " leading to model: ".get_class($model);
                    die;
            }
            return $model;
//            var_dump ($result);
//            $result = new BelongsTo;

        }
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
            $source->write($key,$value);
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


}