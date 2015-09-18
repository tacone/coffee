<?php

namespace Tacone\Coffee\Widget;

use Illuminate\Support\Collection;
use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Base\OuterIteratorTrait;
use Tacone\Coffee\Base\StringableTrait;
use Tacone\Coffee\Base\WrappableTrait;
use Tacone\Coffee\Collection\FieldCollection;
use Tacone\Coffee\DataSource\OldDataSource;
use Tacone\Coffee\DataSource\DataSourceCollection;
use Tacone\Coffee\Output\CompositeOutputtable;

class Rows implements \OuterIterator
{
    use StringableTrait;
    use OuterIteratorTrait;
    use WrappableTrait;
    /**
     * @var DataSourceCollection
     */
    public $source;
    /**
     * @var Row
     */
    protected $prototype;
    /**
     * @var FieldCollection
     */
    private $fields;

    /**
     * @var \IteratorIterator
     */
    protected $iterator;

    /**
     * @var Attribute
     */
    public $paginate;

    public $paginator;

    /**
     * @var bool whether to namespace row fields with 'row.0'.
     */
    protected $namespace = true;

    public function __construct(/*DataSource*/
        $source,
        $prototype,
        FieldCollection $fields
    ) {
        $this->initWrapper();

        $this->source = $source;
        $this->prototype = $prototype;
        $this->fields = $fields;
        $this->paginate = new Attribute(15);
    }

    protected function initWrapper()
    {
        $this->wrap('tbody');
    }

    public function toArray()
    {
        $oldNamespace = $this->namespace;
        $this->namespace = false;

        $result = [];
        foreach ($this as $k => $v) {
            $result[$k] = $v->toArray();
        }

        $this->namespace = $oldNamespace;

        return $result;
    }

    public function content()
    {
        $rows = new CompositeOutputtable();
        foreach ($this as $row) {
            $rows[] = $row->output();
        }

        if (!$rows->count()) {
            $rows[] = '<tr><td colspan="'.count($this->fields).'" class="empty-placeholder">
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
                    ->name($this->namespaceFieldname($this->key(), $field->name()))
                    ->wrap('td')
                    ->outputLabel(false)
                    ->setMode('compact')
            );
        }

        return $row;
    }
    protected function namespaceFieldname($key, $name)
    {
        if (!$this->namespace) {
            return $name;
        }

        return "rows.$key.$name";
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element.
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        if (!$this->valid()) {
            return;
        }
        $source = new OldDataSource($this->getInnerIterator()->current());

        return $this->make($source);
    }

    public function getInnerIterator()
    {
        if (!$this->iterator) {
            $object = $this->source->unwrap();
            if (!$object instanceof Collection) {
                $paginate = $this->paginate;
                $this->paginator = $object->paginate($paginate());
                $this->iterator = new \IteratorIterator($this->paginator->getCollection());
            } else {
                $this->iterator = new \IteratorIterator($object);
            }
        }

        return $this->iterator;
    }
}
