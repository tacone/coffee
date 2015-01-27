<?php

namespace Tacone\Coffee\Widget;

use Illuminate\Database\Eloquent\Builder;
use Tacone\Coffee\DataSource\DataSourceCollection;
use Tacone\Coffee\Output\CallbackOutputtable;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Outputtable;
use Tacone\Coffee\Output\Tag;

class DataGrid extends DataForm
{
    /**
     * @var Row
     */
    public $prototype;

    /**
     * @var Rows
     */
    public $rows;
    /**
     * @var CallbackOutputtable
     */
    public $headers;

    public function __construct($source = null)
    {
        $arguments = func_get_args();
        call_user_func_array('parent::__construct', $arguments);
        $this->headers = new CallbackOutputtable([$this, 'renderHeaders']);
        $this->paginator = new Outputtable([$this, 'renderPaginator']);
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
                $this->source = $source;
                break;
            case $source instanceof Builder:
                $this->source = $source;
                break;
            default:
                $type = is_object($source) ? get_class($source) : gettype($source);
                throw new \RuntimeException("Source of type $type is not supported");
        }
        $this->source = new DataSourceCollection($this->source);

        $this->prototype = new Row();
        $this->rows = new Rows($this->source, $this->prototype, $this->fields);
    }

    protected function bindShortcuts()
    {
        parent::bindShortcuts();
        $this->paginate = $this->rows->paginate;
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
        . $this->headers
        . $this->rows
        . $this->end
        . $this->paginator;
    }
    public function renderPaginator()
    {
        $paginator = $this->rows->paginator;
        return $paginator ? (string)$paginator->links(): '';
    }
    public function renderHeaders()
    {
        $cells = new CompositeOutputtable();
        foreach ($this->fields as $field) {
            $cells[] = new Tag('th', $field->label());
        }
        $wrapper = new Tag('tr', $cells->output());
        $wrapper = new Tag('thead', $wrapper->output());

        return (string)$wrapper;
    }
}
