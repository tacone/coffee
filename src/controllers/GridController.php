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

    public function anyIndex()
    {
        $grid = new DataGrid(new Article());
        $grid->text('id');
        $grid->text('title');
        $grid->text('author.fullname');
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

    public function anyCallback()
    {
        $grid = new DataGrid(new Article());
        $grid->text('id');
        $grid->text('title');
        $grid->text('author.firstname');
        $grid->text('author.lastname');
        $grid->text('categories.0.name');
        $grid->start->before[] = '<em>Customized with a row callback</em>';
        $colors = ['success', 'warning', 'info', 'danger'];
        $counter = 0;
        $grid->prototype->output(function ($row) use (&$counter, $colors) {
            $row->class($colors[$counter]);
            if ($colors[$counter] == 'danger') {
                $row->end->after[] = '<tr><td colspan="1000" class="text-danger danger">
Warning: this item has been rejected by the moderators
</td></tr>';
            }
            $counter = ($counter + 1) % count($colors);
        });

        return View::make("coffee::demo.grid-automatic", compact('grid'));
    }
}
