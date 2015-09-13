<?php

namespace Tacone\Coffee\Widget;

use App;
use ArrayAccess;
use Countable;
use Illuminate\Support\Contracts\ArrayableInterface;
use IteratorAggregate;
use Tacone\Coffee\Base\CopiableTrait;
use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\HtmlAttributesTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Base\WrappableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\Field\Field;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Tag;

class DataForm implements Countable, IteratorAggregate, ArrayAccess, ArrayableInterface
{
    use DelegatedArrayTrait;
    use StringableTrait;
    use HtmlAttributesTrait;
    use CopiableTrait;
    use WrappableTrait;

    /**
     * @var FieldCollection
     */
    public $fields;

    /**
     * @var DataSource
     */
    public $source;
    /**
     * @var Tag
     */
    public $submitButton;

    protected $key;

    public function __construct($source = null)
    {
        $this->fields = new FieldCollection();
        $this->initSource($source);
        $this->initWrapper();
        $this->bindShortcuts();
    }

    protected function initSource($source = null)
    {
        $this->source = DataSource::make($source);
    }

    protected function initWrapper()
    {
        $this->wrap('form');
        $this->start->addAttr('method', 'post');

//        $button = '<button type="submit" name="__submit" value="1" class="btn btn-primary">Submit</button>';
        $this->submitButton = (new Tag('button', 'Submit'))
            ->attr('type', 'submit')
            ->attr('name', '__submit')
            ->attr('value', 1)
            ->class('btn btn-primary');
        $this->end->before->actions = new CompositeOutputtable();
        $this->end->before->actions->submit = $this->submitButton;
    }

    protected function bindShortcuts()
    {
        $this->attr = $this->start->attr;
        $this->class = $this->start->class;
        $this->css = $this->start->css;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return Field|static|mixed
     */
    public function __call($name, $arguments)
    {
        // Is it a field name?
        try {
            $binding = "coffee.$name";
            $field = App::make($binding, $arguments);
            $this->fields->add($field);

            return $field;
        } catch (\ReflectionException $e) {
            // not a field name
        }

        // is it an attribute method ?
        try {
            return Exposeable::handleExposeables($this, $name, $arguments);
        } catch (\BadMethodCallException $exception) {
            // not an attribute method
        }

        // is it a method to be called on all the fields?
        try {
            return call_user_func_array([$this->fields, $name], $arguments);
        } catch (\BadMethodCallException $exception) {
            // not a field method
        }

        // oh, well, then ...
        throw new \BadMethodCallException(missing_method_message($this, $name));
    }

    /**
     * Collection containing all the fields in the form.
     *
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
     *
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
     *
     * @return array
     */
    public function toArray($flat = false)
    {
        return $this->fields()->toArray($flat);
    }

    /**
     * Required by DelegatedArrayTrait.
     *
     * @return FieldCollection
     */
    protected function getDelegatedStorage()
    {
        return $this->fields;
    }

    /**
     * Renders the form as an HTML string.
     * This method is also called by __toString().
     *
     * @return string
     */
    protected function render()
    {
        return $this->start
        .$this->fields
        .$this->end;
    }

    /**
     * Whether the form has been submitted or not.
     *
     * @return bool
     */
    public function submitted()
    {
        return (boolean) \Input::get('__submit');
    }

    /**
     * Sets the fields values back to the models.
     */
    public function writeSource()
    {
        foreach ($this->fields as $name => $field) {
            $this->source[$name] = $field->value();
        }
    }

    /**
     * Fills the form with the values coming from the DB
     * and HTTP input.
     */
    public function populate()
    {
        $inputData = array_dot(\Input::all());

        return call_user_func_array([$this->fields, 'populate'], [
            $this->source,
            $inputData,
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
     *
     * @return mixed
     */
    public function validate()
    {
        $arguments = func_get_args();

        return call_user_func_array([$this->fields, 'validate'], $arguments);
    }
}
