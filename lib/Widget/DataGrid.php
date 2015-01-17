<?php

namespace Tacone\Coffee\Widget;

use Tacone\Coffee\DataSource\DataSource;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Tag;

class DataGrid extends DataForm
{
    protected $paginate = 10;

    public $prototype;
    public $rows;

    protected function initWrapper()
    {
        list($this->start, $this->end) = Tag::createWrapper('table');
        $this->start->class('table table-bordered table-striped');
    }

    protected function initSource($source = null)
    {
        if ($source instanceof \Eloquent) {
            //            $this->source = $source->paginate($this->paginate)->getCollection();
//            $this->source = new DataSource($this->source);
            $this->source = new DataSource([]);
//            xxx($this->source instanceof \Traversable);
        }
        $this->prototype = new CompositeOutputtable();
        $this->rows = new Rows($this->source, $this->prototype, $this->fields);
    }

    public function rows()
    {
        return $this->rows;
    }

    /**
     * Renders the widget as an HTML string.
     * This method is also called by __toString().
     * @return string
     */
    protected function render()
    {
        return $this->start
        .$this->headers()
        .$this->rows
        .$this->end;
    }

    protected function headers()
    {
        $cells = new CompositeOutputtable();
        foreach ($this->fields as $field) {
            //            $name = $field->name();
//            $value = !empty($record[$name]) ? $record[$name] : '';
            $cells[] = new Tag('th', $field->label());
        }
        $wrapper = new Tag('tr', $cells->output());

        return $wrapper;
    }
}
