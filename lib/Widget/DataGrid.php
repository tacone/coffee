<?php

namespace Tacone\Coffee\Widget;

use Illuminate\Database\Eloquent\Builder;
use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\DataSource\DataSourceCollection;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Tag;

class DataGrid extends DataForm
{

    public $prototype;
    public $rows;

    public function __construct($source = null)
    {
        $this->paginate = new Attribute(200);

        $arguments = func_get_args();
        call_user_func_array('parent::__construct', $arguments);
    }


    protected function initWrapper()
    {
        list($this->start, $this->end) = Tag::createWrapper('table');
        $this->start->class('table table-bordered table-striped');
    }

    protected function initSource($source = null)
    {
        switch (true) {
            case $source instanceof \Eloquent:
                $this->source = $source
                    ->paginate($this->paginate->get())->getCollection();
                break;
            case $source instanceof Builder:
                $this->source = $source
                    ->paginate($this->paginate->get())->getCollection();
                break;
            default:
                $type = is_object($source) ? get_class($source) : gettype($source);
                throw new \RuntimeException("Source of type $type is not supported");
        }
        $this->source = new DataSourceCollection($this->source);

        $this->prototype = new Row();
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
        . $this->headers()
        . $this->rows
        . $this->end;
    }

    protected function headers()
    {
        $cells = new CompositeOutputtable();
        foreach ($this->fields as $field) {
            $cells[] = new Tag('th', $field->label());
        }
        $wrapper = new Tag('tr', $cells->output());
        $wrapper = new Tag('thead', $wrapper->output());

        return $wrapper;
    }
}
