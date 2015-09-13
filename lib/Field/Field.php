<?php

namespace Tacone\Coffee\Field;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Attribute\ErrorsAttribute;
use Tacone\Coffee\Attribute\JoinedArrayAttribute;
use Tacone\Coffee\Attribute\Label;
use Tacone\Coffee\Base\CopiableTrait;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\HtmlAttributesTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Base\WrappableTrait;
use Tacone\Coffee\Helper\Html;
use Tacone\Coffee\Output\CallbackOutputtable;
use Tacone\Coffee\Output\ModalOutputtable;

abstract class Field
{
    use StringableTrait;
    use HtmlAttributesTrait;
    use CopiableTrait;
    use WrappableTrait;

    /**
     * @var Attribute
     */
    public $name;
    /**
     * @var
     */
    public $label;
    /**
     * @var
     */
    public $value;
    public $rules;
    public $errors;

    public function __construct($name, $label = null)
    {
        // we create a nice mask for this field output
        // wrapping it with a parent element, adding css classes etc

        $this->initWrapper();

        // we configure the standard HTML attributes for every field
        // so you can manipulate tag attributes, classes and inline
        // styles

        $this->initHtmlAttributes();

        // what is this useful for? For <label for=".."> and
        // in general for javascripts that need to get a unique
        // handler with no collisions

        $this->attr['id'] = md5(microtime().rand(0, 1e5));

        // this is useful for css rules so you can style your forms
        // with the [data-id=...] selector

        $this->attr['data-id'] = $name;
        $this->class('form-control');

        // Look! Every property is an object in itself. We do that
        // to make things more robust, reuse logic and move it outside
        // of this class

        $this->errors = new ErrorsAttribute();
        $this->label = new Label($name, $label, $this->attr['id']);
        $this->name = new Attribute($name);
        $this->rules = new JoinedArrayAttribute(null, '|');
        $this->value = new Attribute();

        // every field has several render modes. Here we define which
        // callback will be called by each mode.

        $this->content = new ModalOutputtable([
            'edit' => new CallbackOutputtable([$this, 'renderEdit']),
            'show' => new CallbackOutputtable($this, 'renderShow'),
            'compact' => new CallbackOutputtable($this, 'renderCompact'),
        ]);

        // the default render mode is "edit". By default the field will
        // render as an editable widget.

        $this->content->setMode('edit');
    }

    public function setMode()
    {
        $arguments = func_get_args();
        call_user_func_array([$this->content, 'setMode'], $arguments);

        return $this;
    }

    protected function initWrapper()
    {
        $this->wrap('div');
        $this->start->class('form-group');
        // a dirty trick to force the update of the wrapper class
        $this->start->content(with(function () {
            if ($this->errors->count()) {
                $this->start->class('has-error');
            }

            return '';
        })->bindTo($this, $this));
    }

    abstract public function renderEdit();

    public function renderShow()
    {
        return $this->value->output() ?: '&nbsp;';
    }

    public function renderCompact()
    {
        $value = $this->value->output();
        $value = \Str::words(strip_tags($value));

        return $value ?: '&nbsp;';
    }

    protected function render()
    {
        return $this->start
        .$this->label."\n"
        .$this->content."\n"
        .$this->errors."\n"
        .$this->end;
    }

    /**
     * Implements a jQuery-like interface.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return Exposeable::handleExposeables($this, $method, $parameters);
    }

    protected function htmlName()
    {
        return Html::undot($this->name());
    }
}
