<?php

namespace Tacone\Coffee\Widget;

use App;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\Field\Field;
use Tacone\Coffee\Support\Deep;


class DataForm implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;
    use StringableTrait;

    /**
     * @var FieldCollection
     */
    protected $fields;
    protected $before = '<form>';
    protected $after = '<button type="submit" class="btn btn-primary">Submit</button></form>';
    /**
     * @var \Eloquent
     */
    protected $model;

    public function __construct(\Eloquent $model = null)
    {
        $this->fields = new FieldCollection();
        $this->model = $model;
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

    public function toArray($flat = false)
    {
        return $this->fields()->toArray($flat);
    }

    protected function getDelegatedStorage()
    {
        return $this->fields;
    }

    public function output()
    {
        return $this->before
        . $this->fields
        . $this->after;
    }

    public function populate()
    {
        $inputData = array_dot(\Input::all());
        $modelData = new Deep($this->model);
        foreach ($this->fields as $field) {
            if ($modelData->has($field->name())) {
                $field->value($modelData->get($field->name()));
            }
            if (isset($inputData[$field->name()])) {
                $field->value($inputData[$field->name()]);
            }
        }
    }

    public function validate()
    {
        $validator = \Validator::make(
            $this->fields->toArray(true),
            $this->fields()->rules()
        );
        $names = array();
        foreach ($this->fields as $field)
        {
            $names[$field->name()] = '"'.$field->label().'"';
        }
        $validator->setAttributeNames($names);
        var_dump($validator->errors());
    }

}