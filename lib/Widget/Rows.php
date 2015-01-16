<?php

namespace Tacone\Coffee\Widget;

use DeepCopy\DeepCopy;
use Iterator;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Tag;

class Rows implements \OuterIterator
{
    use StringableTrait;
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

    public function __construct(/*DataSource*/ $source, $prototype, FieldCollection $fields)
    {
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
        if (!$record) {
            return '';
        }
        $copy = new DeepCopy();
        $cells = $copy->copy($this->prototype);
        foreach ($this->fields as  $field) {
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
        $source = new DataSource($this->iterator->current());

        return $this->makeRow($source);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        return $this->iterator->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *                 Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        return $this->iterator->rewind();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Returns the inner iterator for the current entry.
     * @link http://php.net/manual/en/outeriterator.getinneriterator.php
     * @return Iterator The inner iterator for the current entry.
     */
    public function getInnerIterator()
    {
        return $this->source;
    }
}
