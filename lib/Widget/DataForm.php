<?php

namespace Tacone\Coffee\Widget;

use App;
use Illuminate\Cache\ArrayStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\Field\Field;


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
    }

    public function save($model = null, $prev = [])
    {

        $model = $this->source->unwrap();
        $modelRelations = $this->source->relations($model);

        // Confused?
        //
        // - a female belongsTo a male
        // - each male hasOneOrMany females
        // - males have a $localKey, females don't and rely on a
        //   external foreignKey
        // - females need an pivot table to associate among them
        //
        // a model can be male and female at the same time
        //
        // we need to save females first, then the current model,
        // then each male

        foreach ($modelRelations as $key => $mr)
        {
            $daughter = $mr['model'];
            $relation = $mr['relation'];

            if ($relation instanceof BelongsTo)
            {
                $daughter->save();
                $relation->associate($daughter);
            }
        }
        $model->save();
        foreach ($modelRelations as $key => $model)
        {
            $son = $mr['model'];
            $relation = $mr['relation'];
            if ($relation instanceof HasOne)
            {
                $relation->save($son);
            }
        }
    }

    public function validate()
    {
        $arguments = func_get_args();
        return call_user_func_array([$this->fields, 'validate'], $arguments);
    }

}