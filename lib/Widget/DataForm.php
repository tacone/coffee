<?php

namespace Tacone\Coffee\Widget;

use App;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\Field\Field;


class DataForm
{
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
        $field = App::make("coffee.$name", $arguments);
        $this->fields->push($field);
        return $field;
    }
    
    public function fields()
    {
        return $this->fields;
    }
    public function field($name)
    {
        return $this->fields[$name];
        return $this->fields->get($name);
    }
}