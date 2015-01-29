<?php

namespace Tacone\Coffee\Widget;


use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Helper\RouteHelper;
use Tacone\Coffee\Output\Tag;

class DataGrid extends Grid
{
    protected $editUrl;
    /**
     * @var Tag
     */
    public $createButton;


    public function __construct($source = null, $editUrl = null)
    {
        $this->editUrl = RouteHelper::toUrl('@anyEdit');
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
            ->css('padding', '15px 0');
        return $button;
    }

    protected function bindShortcuts()
    {
        parent::bindShortcuts();
        $this->createButton = $this->buildCreateButton($this->editUrl);
        $this->start->before->createButton = $this->createButton;
    }


}
