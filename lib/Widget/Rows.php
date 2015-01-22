<?php

namespace Tacone\Coffee\Widget;

use Tacone\Coffee\Base\OuterIteratorTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Base\WrappableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\Output\CompositeOutputtable;

class Rows implements \OuterIterator
{
    use StringableTrait;
    use OuterIteratorTrait;
    use WrappableTrait;
    /**
     * @var
     */
    protected $source;
    /**
     * @var Row
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
        $this->initWrapper();

        $this->source = $source;
        $this->iterator = new \IteratorIterator($source);
        $this->prototype = $prototype;
        $this->fields = $fields;
    }

    protected function initWrapper()
    {
        $this->wrap('tbody');
    }

    public function content()
    {
        $rows = new CompositeOutputtable();
        foreach ($this as $row) {
            $rows[] = $row->output();
        }

        if (!$rows->count()) {
            $rows[] = '<tr><td colspan="1000" class="empty-placeholder">
No data yet.
</td></tr>';
        }

        return implode("\n", $rows->toArray());
    }

    protected function make($record)
    {
        /** @var Row $row */
        $row = $this->prototype->copy();
        foreach ($this->fields as $field) {
            $row->fields->add(
                $field->copy()
                    ->value(!empty($record[$field->name()]) ? $record[$field->name()] : '')
                    ->name('rows.'.$this->key().'.'.$field->name())
                    ->wrap('td')
                    ->outputLabel(false)
                    ->setMode('compact')
            );
        }

        return $row;
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

        return $this->make($source);
    }
}
