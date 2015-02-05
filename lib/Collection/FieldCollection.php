<?php

namespace Tacone\Coffee\Collection;

use Illuminate\Support\Contracts\ArrayableInterface;
use IteratorAggregate;
use Tacone\Coffee\Base\CompositeTrait;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\FieldStorage;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Field;
use Tacone\Coffee\Helper\ArrayHelper;

class FieldCollection implements \Countable, \IteratorAggregate, \ArrayAccess, ArrayableInterface
{
    use DelegatedArrayTrait;
    use StringableTrait;
    use CompositeTrait;

    public function __construct()
    {
        $this->storage = new FieldStorage();
    }

    protected function compositeTraitGetChildren()
    {
        $children = [];
        foreach ($this as $name => $field) {
            $children[$name] = $field;
        }
        return $children;
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
     * Get the fields value as an associative array.
     * By default a nested array is returned.
     * Passing true as the first parameter, a flat
     * array will be returned, with dotted offsets
     * as the keys.
     *
     * @param  bool  $flat
     * @return array
     */
    public function toArray($flat = false)
    {
        $array = $this->value();
        if ($flat) {
            return $array;
        }

        return ArrayHelper::undot($array);
    }

//    public function rules()
//    {
//        die('ds');
//        $rules = [];
//        foreach ($this as $name => $field) {
//            $rules[$name] = $field->rules->toArray();
//        }
//
//        return $rules;
//    }

    protected function render()
    {
        return $this->dispatch('output');
    }

    public function validate()
    {
        $validator = \Validator::make(
            $this->toArray(true),
            $this->rules()
        );
        $names = array();
        foreach ($this as $field) {
            $names[$field->name()] = '"'.$field->label().'"';
        }
        $validator->setAttributeNames($names);
        foreach ($validator->errors()->getMessages() as $name => $messages) {
            $this[$name]->errors($messages);
        }

        return !$validator->fails();
    }

    public function populate()
    {
        $dataSources = func_get_args();
        foreach ($this as $name => $field) {
            foreach ($dataSources as $source) {
                if (isset($source[$name])) {
                    $field->value($source[$name]);
                }
            }
        }
    }
}
