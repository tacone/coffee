<?php
namespace Tacone\Coffee\Demo\Controllers;

use Tacone\Coffee\Demo\Models\Article;
use Tacone\Coffee\Widget\DataGrid;
use View;

class GridController extends DemoController
{
    /**
     * A very simple grid
     */

    public function anyIndex($view = 'grid')
    {
        $grid = new DataGrid(new Article());
        $grid->text('id');
        $grid->text('title');
        $grid->text('author.firstname');
        $grid->text('author.lastname');
        $grid->text('categories.0.name');
        $grid->select('Publish in')->options([
            'home' => 'Frontpage',
            'blog' => 'Blog',
            'magazine' => 'Magazine',
            'Other destinations' => [
                'newsletter' => 'Newsletter',
                'sponsor' => 'Main sponsor website',
                'drafts' => 'Draft box',
            ],
        ]);
        $grid->start->before[] = '<em>This is a very simple grid</em>';

        return View::make("coffee::demo.grid-automatic", compact('grid'));
    }
}
