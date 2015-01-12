<?php

namespace Tacone\Coffee\Collection;

use Illuminate\Support\Contracts\ArrayableInterface;
use IteratorAggregate;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\FieldStorage;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Field;
use Tacone\Coffee\Helper\ArrayHelper;

class FieldCollection implements \Countable, \IteratorAggregate, \ArrayAccess, ArrayableInterface
{
    use DelegatedArrayTrait;
    use StringableTrait;

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
        $array = [];
        foreach ($this->storage as $key => $field) {
            $array[$field->name()] = $field->value();
        }
        if ($flat) {
            return $array;
        }

        return ArrayHelper::undot($array);
    }

    public function rules()
    {
        $rules = [];
        foreach ($this as $name => $field) {
            $rules[$name] = $field->rules->toArray();
        }

        return $rules;
    }

    protected function render()
    {
        $output = '';
        foreach ($this as $field) {
            $output .= $field->output() . "\n";
        }

        return $output;
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
        foreach ($this as $field) {
            $name = $field->name();
            foreach ($dataSources as $source) {
                if (isset($source[$name])) {
                    $field->value($source[$name]);
                }
            }
        }
    }
}
