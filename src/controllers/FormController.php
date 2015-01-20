<?php
namespace Tacone\Coffee\Demo\Controllers;

use Tacone\Coffee\Demo\Models\Article;
use Tacone\Coffee\Widget\DataForm;
use View;

class FormController extends DemoController
{
    /**
     * A very simple form that handles relations.
     * Nothing fancy, just validation and a custom label.
     */
    public function anySimple()
    {
        // load the data
        $model = Article::findOrNew(1);

        // instantiate the form
        $form = new DataForm($model);

        // define the fields
        $form->text('title')->rules('required|max:10');
        $form->text('author.firstname', 'Author\'s first name')
            ->rules('required');
        $form->text('author.lastname');
        $form->textarea('detail.note');
        $form->textarea('body');

        // read the POST data, if any
        $form->populate();

        // write new data back to the model
        $form->writeSource();

        // if the form has been sent, and has no errors
        if ($form->submitted() && $form->validate()) {
            // we will save the data in the database
            $form->save();
        }

        // we just need to pass the $form instance to the view
        return View::make("coffee::demo.form-automatic", compact('form'));
    }

    /**
     * An heavily customized form, to show-off the output
     * manipulation capabilities of Coffee
     */
    public function anyIndex($view = 'automatic')
    {
        $model = Article::findOrNew(1);

        $form = new DataForm($model);

        // for testing purposes, will switch to the GET method
        // you can customize any HTML attribute of your form via
        // the attr() method.
        $form->attr('method', 'get');

        // you can also add whatever markup you wish before or after
        // the opening tag. Just add it to $form->start->before|after
        $form->start->after->premise = '<div class="alert alert-info">
Coffee Forms lets you customize your forms programatically
without having to resort to custom views.
</div>';
        // the same is true for the closing end.
        $form->end->before->prepend('reminder', '<p>Think well before you click!</p>');

        // use the css() method to add css rules to the form, as you
        // do with jQuery
        $form->css('position', 'relative')->css('padding-top', '40px');

        // you can chain any mutator. And remove items with the !abbreviated
        // syntax
        $form
            ->addCss('border', '1px solid red')
            ->css('border', '1px dashed #ccc')// override the previous line
            ->addCss('padding', '30px')
            ->css('background', 'red')
            ->css('!background'); // try the abbreviated remove syntax

        // "Tommy" will be the default value of the field
        $form->text('title')->value('Tommy')->value(function ($v) {
            // let's enforce capitalization
            return ucwords($v);
        })->rules('required|max:10');

        // you can add a custom label to any field. Easy!
        $form->text('author.firstname', 'Author\'s first name')->rules('required');
        $form->text('author.lastname')->addCss('background', '#ddeeff');
        $form->textarea('detail.note');
        $form->textarea('body')
            ->addAttr('disabled', 'disabled')
            ->class('one two three');

        // you can access the single fields using the array notation
        $form['title']->attr('autofocus', 'autofocus')->class('input-lg');

//        $form->select('author_id')->options(\Author::get()->lists('fullname', 'id'))->rules('required');
//        $form->text('random', 'Tries')->value(function () {
//            return Input::get('random') + 1;
//        });

        // --- Here comes the action! ---

        // we populate the form from HTTP variables
        $form->populate();

        // and we write them back to the models
        $form->writeSource();

        // now we check if the form has been submitted
        // if so, we run the validation rules and see if it validates
        if ($form->submitted() && $form->validate()) {
            // if it does, we save the model data to the database,
            // including every related model!
            $form->save();
        }

        return View::make("coffee::demo.form-$view", compact('form'));
    }
}
