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
        $grid->text('categories.0.name', 'In category');

        $grid->start->before[] = '<p><em>This is a very simple grid</em></p>';

        return View::make("coffee::demo.grid-automatic", compact('grid'));
    }
//        $grid->select('Publish in')->options([
//            'home' => 'Frontpage',
//            'blog' => 'Blog',
//            'magazine' => 'Magazine',
//            'Other destinations' => [
//                'newsletter' => 'Newsletter',
//                'sponsor' => 'Main sponsor website',
//                'drafts' => 'Draft box',
//            ],
//        ]);
    public function anyCallback()
    {
        $grid = new DataGrid(new Article());
        $grid->text('id');
        $grid->text('title');
        $grid->text('author.firstname');
        $grid->text('author.lastname');
        $grid->text('categories.0.name', 'In category');
        $grid->start->before[] = '<p><em>Customized with a row callback</em></p>';
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
