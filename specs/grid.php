<?php

use Tacone\Coffee\Widget\DataGrid;
use Tacone\Coffee\Widget\Row;

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

$grid->text(function (Row $row, DataGrid $grid) {

});

$grid->row(function ($row) {
    if ($row['author.isPaid']->value()) {
        $row->class('row-success');
        $row->actions->hideContent();
    }
});

$field->editMode();
$field->showMode();
$field->shortMode();

$field->editContent();
$field->showContent();
$field->shortContent();

$field->content->show();
