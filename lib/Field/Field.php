<?php

namespace Tacone\Coffee\Field;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Attribute\ErrorsAttribute;
use Tacone\Coffee\Attribute\JoinedArrayAttribute;
use Tacone\Coffee\Attribute\Label;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\HtmlAttributesTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Helper\Html;
use Tacone\Coffee\Output\Tag;

abstract class Field
{
    use StringableTrait;
    use HtmlAttributesTrait;

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

    /**
     * @var Tag
     */
    public $start;
    /**
     * @var Outputtable
     */
    public $end;

    public function __construct($name, $label = null)
    {
        list($this->start, $this->end) = Tag::createWrapper('div');
        $this->start->class('form-group');
        // a dirty trick to force the update of the wrapper class
        $this->start->content(with(function () {
            if ($this->errors->count()) {
                $this->start->class('has-error');
            }

            return '';
        })->bindTo($this, $this));

        $this->initHtmlAttributes();
        $this->attr['id'] = md5(microtime().rand(0, 1e5));
        $this->attr['data-id'] = $name;
        $this->class('form-control');

        $this->errors = new ErrorsAttribute();
        $this->label = new Label($name, $label, $this->attr['id']);
        $this->name = new Attribute($name);
        $this->rules = new JoinedArrayAttribute(null, '|');
        $this->value = new Attribute();
    }

    abstract public function control();

    protected function render()
    {
        return $this->start
        .$this->label."\n"
        .$this->control()."\n"
        .$this->errors."\n"
        .$this->end;
    }

    /**
     * Implements a jQuery-like interface
     *
     * @param  string $method
     * @param  array  $parameters
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
