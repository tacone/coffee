<?php

namespace Tacone\Coffee\Widget;

use App;
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
     * @param  string $name
     * @param  array  $arguments
     * @return Field
     */
    public function __call($name, $arguments)
    {
        $binding = "coffee.$name";

        $field = App::make($binding, $arguments);
        $this->fields->add($field);

        return $field;
    }

    /**
     * Collection containing all the fields in the form
     * @return FieldCollection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Get a field by name (dotted offset).
     *
     * (you can also use array notation like:
     * <code>$form['author.name']</code>
     *
     * @param string $name
     * @return Field
     */
    public function field($name)
    {
        return $this->fields->get($name);
    }

    /**
     * Get the fields value as an associative array.
     * By default a nested array is returned.
     * Passing true as the first parameter, a flat
     * array will be returned, with dotted offsets
     * as the keys.
     *
     * @param bool $flat
     * @return array
     */
    public function toArray($flat = false)
    {
        return $this->fields()->toArray($flat);
    }

    /**
     * Required by DelegatedArrayTrait
     * @return FieldCollection
     */
    protected function getDelegatedStorage()
    {
        return $this->fields;
    }

    /**
     * Renders the form as an HTML string.
     * This method is also called by __toString().
     * @return string
     */
    protected function render()
    {
        return $this->before
        . $this->fields
        . $this->after;
    }

    /**
     * Whether the form has been submitted or not.
     * @return bool
     */
    public function submitted()
    {
        return (boolean) \Input::get('__submit');
    }

    /**
     * Sets the fields values back to the models
     */
    public function writeSource()
    {
        foreach ($this->fields as $field) {
            $name = $field->name();
            $this->source[$name] = $field->value();
        }
    }

    /**
     * Fills the form with the values coming from the DB
     * and HTTP input.
     *
     * @return void
     */
    public function populate()
    {
        $inputData = array_dot(\Input::all());

        return call_user_func_array([$this->fields, 'populate'], [
            $this->source,
            $inputData
        ]);
    }

    /**
     * Saves the models back to the database.
     */
    public function save()
    {
        return $this->source->save();
    }

    /**
     * Validates the form, then sets eventual errors on each field.
     * @return mixed
     */
    public function validate()
    {
        $arguments = func_get_args();

        return call_user_func_array([$this->fields, 'validate'], $arguments);
    }

}
