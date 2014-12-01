<?php

namespace Tacone\Coffee\Widget;

use App;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\Field\Field;


class DataForm implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;

    /**
     * @var FieldCollection
     */
    protected $fields;

    public function __construct()
    {
        $this->fields = new FieldCollection();
    }

    /**
     *
     * @param string $name
     * @param array $arguments
     * @return Field
     */
    public function __call($name, $arguments)
    {
        $binding = "coffee.$name";

        $field = App::make($binding, $arguments);
        $this->fields->add($field);
        return $field;
    }

    public function fields()
    {
        return $this->fields;
    }

    public function field($name)
    {
//        return $this->fields[$name];
        return $this->fields->get($name);
    }

    public function toArray()
    {
        return $this->fields()->toArray();
    }

    protected function getDelegatedStorage()
    {
        return $this->fields;
    }
}