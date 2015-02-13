<?php

namespace Tacone\Coffee\Widget;

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
    /**
     * @var Tag
     */
    public $editButton;
    /**
     * @var Tag
     */
    public $deleteButton;

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

        $this->createButton = $this->buildCreateButton($this->editUrl);
        $this->editButton = $button->copy()
            ->class('btn-default')
            ->content('Edit');
        $this->deleteButton = $button->copy()
            ->class('btn-danger')
            ->content('Delete');

    }

    public function buttons($show)
    {
        if ($show) {
            $this->start->before->createButton = $this->createButton;
            $actions = new CompositeOutputtable([
                'start' => '<td>',
                'editButton' => $this->editButton,
                'separator' => '&nbsp;',
                'deleteButton' => $this->deleteButton,
                'end' => '</td>',
            ]);
            $that = $this;
            $this->prototype->end->before->actions = new CallbackOutputtable(function () use ($actions, $that) {
                $u = $that->url;
                $current = $that->rows->getInnerIterator()->current();
                $id = $current->getKey();
                $actions->editButton->attr('href', $this->editUrl . "?" . $u->action('edit') . '&' . $u->id($id));
                $actions->deleteButton->attr('href', $this->editUrl . "?" . $u->action('delete') . '&' . $u->id($id));
                return (string)$actions;
            });
        } else {
            $this->start->before->remove('createButton');
            $this->prototype->end->before->remove('actions');
        }
    }
}
