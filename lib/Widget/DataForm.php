<?php

namespace Tacone\Coffee\Widget;

use App;
use Illuminate\Support\Collection;


class DataForm
{
    /**
     * @var Collection 
     */
    protected $fields;
    
    public function __construct()
    {
        $this->fields = new Collection();
    }
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
}