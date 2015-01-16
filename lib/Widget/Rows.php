<?php

namespace Tacone\Coffee\Widget;

use DeepCopy\DeepCopy;
use Iterator;
use Tacone\Coffee\Base\OuterIteratorTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Tag;

class Rows implements \OuterIterator
{
    use StringableTrait;
    use OuterIteratorTrait;
    /**
     * @var
     */
    protected $source;
    /**
     * @var
     */
    protected $prototype;
    /**
     * @var FieldCollection
     */
    private $fields;

    public function __construct(/*DataSource*/
        $source,
        $prototype,
        FieldCollection $fields
    ) {
        $this->source = $source;
        $this->iterator = new \IteratorIterator($source);
        $this->prototype = $prototype;
        $this->fields = $fields;
    }

    public function render()
    {
        $rows = new CompositeOutputtable();
        foreach ($this as $row) {
            $rows[] = $row->output();
        }

        return implode("\n", $rows->toArray());
    }

    protected function makeRow($record)
    {
        // clone the prototype
        $copy = new DeepCopy();
        $cells = $copy->copy($this->prototype);
        foreach ($this->fields as $field) {
            $value = !empty($record[$field->name()]) ? $record[$field->name()] : '';
            $field = $copy->copy($field);
            $field->value($value);
            $field->name('rows.'.$this->key().'.'.$field->name());
            $cells[] = new Tag('td', $field->control());
        }
        $wrapper = new Tag('tr', $cells->output());

        return $wrapper;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        if (!$this->valid()) {
            return;
        }
        $source = new DataSource($this->getInnerIterator()->current());

        return $this->makeRow($source);
    }
}
