<?php

namespace Tacone\Coffee\Widget;


use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Helper\RouteHelper;
use Tacone\Coffee\Output\CallbackOutputtable;
use Tacone\Coffee\Output\CompositeOutputtable;
use Tacone\Coffee\Output\Tag;

class DataGrid extends Grid
{
    protected $editUrl;
    /**
     * @var Tag
     */
    public $createButton;

    public function __construct($source = null, $editUrl = '@anyEdit')
    {
        $this->editUrl = RouteHelper::toUrl($editUrl);
        $this->makeButtons();
        parent::__construct($source);
    }

    protected function buildCreateButton($url)
    {
        $button = new Tag('a', 'Create');
        $button->attr('href', $url)
            ->class('btn btn-primary pull-right');
        $button->wrap('div');
        $button->before->wrapper
            ->class('clearfix')
            ->css('padding', '0 0 15px');
        return $button;
    }

    protected function bindShortcuts()
    {
        parent::bindShortcuts();
        $this->buttons(true);
    }

    protected function makeButtons()
    {
        $button = new Tag('a', 'Create');
        $button->class('btn btn-sm');
        $button->attr('href', $this->editUrl);

        $this->createButton = $this->buildCreateButton($this->editUrl);
        $this->editButton = $button->copy()->class('btn-default')->content('Edit');
        $this->deleteButton = $button->copy()->class('btn-danger')->content('Delete');

    }

    public function buttons($show)
    {
        if ($show) {
            $this->start->before->createButton = $this->createButton;
            $actions = new CompositeOutputtable([
                '<td>',
                $this->editButton,
                '&nbsp;',
                $this->deleteButton,
                '</td>',
            ]);
            $this->prototype->end->before->actions = $actions;
        } else {
            $this->start->before->remove('createButton');
            $this->prototype->end->before->remove('actions');
        }
    }
}
