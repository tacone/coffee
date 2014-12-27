<?php

namespace Tacone\Coffee\Widget;

use App;
use Illuminate\Cache\ArrayStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\DataSource;
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
    protected $after = '<button type="submit" name="__submit" value="1" class="btn btn-primary">Submit</button></form>';
    /**
     * @var DataSource
     */
    protected $source;



    public function __construct(\Eloquent $source = null)
    {
        $this->fields = new FieldCollection();
        $this->source = DataSource::make($source);

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

    public function submitted()
    {
        return (boolean)\Input::get('__submit');
    }

    public function writeSource()
    {
        foreach ($this->fields as $field) {
            $name = $field->name();
            $this->source[$name] = $field->value();
        }
    }

    public function populate()
    {
        $inputData = array_dot(\Input::all());
        return call_user_func_array([$this->fields, 'populate'], [
            $this->source,
            $inputData
        ]);

//        $inputData = array_dot(\Input::all());
//        foreach ($this->fields as $field) {
//            $name = $field->name();
//            if (isset($this->source[$name])) {
//                $field->value($this->source[$name]);
//            }
//            if (isset($inputData[$name])) {
//                $field->value($inputData[$name]);
//            }
//        }
    }

    public function save($model = null, $prev = [])
    {

        $model = $this->source->unwrap();

//        xxx($model);
        $modelRelations = $this->source->relations($model);
//        xxx($modelRelations);
//        die;
        foreach ($modelRelations as $key => $relation)
        {
            if ($relation instanceof HasOne)
            {
//                var_dump($key); die;
                $model->$key->save();
            }
        }
        $model->push();

//        foreach ($this->fields as $field)
//        {
//            $null = null;
//            $relations[] = $this->source->findRelations($field->name(), $null);
//        }
//            $relations = array_filter($relations);
//var_dump($relations); die;

//        if (!func_num_args()) {
//            $model = $this->source->unwrap();
//        }
//        $prev[] = $model;
//        foreach ($model->getAttributes() as $key) {
//            if ($key instanceof Model) {
//                $this->save($key, $prev);
//                unset ($model->$key);
//            }
//        }
//        if ( func_num_args() && !array_intersect($model->getRelations(), $prev))
//        {
//            var_dump(get_class($model));
//            var_dump($prev);
//            $model->save();
//        }
//        if (!func_num_args()) {
//
//            die;
//            $model->push();
//        }
    }

    public function validate()
    {
        $arguments = func_get_args();
        return call_user_func_array([$this->fields, 'validate'], $arguments);
    }

}