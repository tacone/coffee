<?php
namespace Tacone\Coffee\Demo\Controllers;

use DB;

use Schema;
use Tacone\Coffee\Demo\Documenter;
use Tacone\Coffee\Demo\Models\Article;
use Tacone\Coffee\Widget\DataForm;
use View;

class DemoController extends \Controller
{

    public $views = [];

    public function __construct()
    {
        error_reporting(-1);

        $me = $this;
        app()['events']->listen(
            'composing:*',
            function ($view) use ($me) {
                $me->views[] = $view->getPath();
            }
        );

        \View::share('demo', $this);
    }

    public function source()
    {
        list($controller, $method) = explode('@', \Route::current()->getAction()['controller']);

        $source = Documenter::showMethod($controller, [$method]);
        foreach ($this->views as $v) {
            if (basename($v) != 'master.blade.php') {
                $source .= Documenter::showCode($v);
            }
        }

        return $source;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function anyIndex($view = 'simple')
    {

        $model = Article::findOrNew(1);

        $form = new DataForm($model);

        // for testing purpouses, will switch to get
        $form->attr('method', 'get');

        $form
            ->addCss('border', '1px solid red')
            ->css('border', '1px dashed #ccc')// override the previous line
            ->addCss('padding', '30px')
            ->css('background', 'red')
            ->css('!background'); // try the abbreviated remove syntax

        $form->text('title')->value('Tommy')->value(function ($v) {
            return ucwords($v);
        })->rules('required|max:10');
        $form->text('author.firstname', 'Author\'s first name')->rules('required');
        $form->text('author.lastname')->addCss('background', '#ddeeff');
        $form->textarea('detail.note');
        $form->textarea('body')
            ->addAttr('disabled', 'disabled')//            ->class('aaa sdd')
        ;

        // you can access the single fields using the array notation
        $form['title']->addAttr('autofocus', 'autofocus')
            ->addClass('input-lg');

//        $form->select('author_id')->options(\Author::get()->lists('fullname', 'id'))->rules('required');
//        $form->text('random', 'Tries')->value(function () {
//            return Input::get('random') + 1;
//        });

        $form->populate();
        $form->writeSource();

        if ($form->submitted() && $form->validate()) {
            $form->save();
        }
//        if ($view == 'custom') {
//
//            \App::terminate(\Request::instance()  , \Redirect::to('/'));
//        }
        return View::make("coffee::demo.$view", compact('form', 'model'));
    }

    public function example()
    {
        $form = new DataEdit(new Article());
        $form->html('Write your book review using this form:');
        $form->text('title');
        $form->textarea('body')->attr('rows', '10');
        $form->text('product_code')
            ->filter('trim|strtoupper')
            ->rule('required|max:10')
            ->message('the product code format is incorrect, check the label twice');
        $form->image('picture')->move('/uploads/{{$id}}');
        $form->select('author')->options('--- choose author ---', Users::lists('id', 'name'));
        $form->radio('layout', 'Page layout')
            ->options(['full', 'sidebar'])
            ->help('hint: select full to enjoy a full page view');
        $form->date('published_on')->rule('required')->addClass('calendar');
        // group and change order
        $form->fields('published_on', 'author', 'layout')->tag('sidebar');
        $form->html('Double check everything before saving');
        // custom view
        $form->view('admin/custom-view');

        $form->text('notes')->control(function ($control) {
            if (!App::user()->isAdmin) {
                return '--- disabled ---';
            } else {
                return ['<div>Write a note on this item</div>', $control, ':-)))'];
            }
        });

        $form->submit('save!');
        $normalFields = $form->tagged(null);
    }

    public function stupid()
    {
        // url policy
        "/admin/articles/?main-list(title:'a*',comments:4 or less)";
        // abbreviated class syntax
        $form->date('published_on')->rule('required')->class('calendar',
            '!form-control'); //add calendar, remove form-control
        // easy grouping
        $step2 = $form->tag('step2', [
            $form->text('title'),
            $form->textarea('body')->attr('rows', '10'),
            $form->image('picture')->move('/uploads/{{$id}}'),
            $form->select('author')->options('--- choose author ---', Users::lists('id', 'name'))
        ])->class('step2-field');

        if ($step1->hasErrors()) {
            $step2->hide();
            // ....

        }
    }

    public function getWipe()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table("demo_users")->truncate();
        DB::table("demo_articles")->truncate();
        DB::table("demo_article_detail")->truncate();
        DB::table("demo_comments")->truncate();
        DB::table("demo_categories")->truncate();
        DB::table("demo_article_category")->truncate();
        DB::statement("SET foreign_key_checks=1");
    }

    public function getSetup()
    {
        Schema::dropIfExists("demo_users");
        Schema::dropIfExists("demo_articles");
        Schema::dropIfExists("demo_article_detail");
        Schema::dropIfExists("demo_comments");
        Schema::dropIfExists("demo_categories");
        Schema::dropIfExists("demo_article_category");

        //create all tables
        Schema::table("demo_users", function ($table) {
            $table->create();
            $table->increments('id');
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->timestamps();
        });
        Schema::table("demo_articles", function ($table) {
            $table->create();
            $table->increments('id');
            $table->integer('author_id')->unsigned();
            $table->string('title', 200);
            $table->text('body');
            $table->string('photo', 200);
            $table->boolean('public');
            $table->timestamp('publication_date');
            $table->timestamps();
        });
        Schema::table("demo_article_detail", function ($table) {
            $table->create();
            $table->increments('id');
            $table->integer('article_id')->unsigned();
            $table->text('note');
            $table->string('note_tags', 200);
        });
        Schema::table("demo_comments", function ($table) {
            $table->create();
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('article_id')->unsigned();
            $table->text('comment');
            $table->timestamps();
        });
        Schema::table("demo_categories", function ($table) {
            $table->create();
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->string('name', 100);
            $table->timestamps();
        });
        Schema::table("demo_article_category", function ($table) {
            $table->create();
            $table->integer('article_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->timestamps();
        });

        //populate all tables
        $users = DB::table('demo_users');
        $users->insert(array('firstname' => 'Jhon', 'lastname' => 'Doe'));
        $users->insert(array('firstname' => 'Jane', 'lastname' => 'Doe'));

        $categories = DB::table('demo_categories');
        for ($i = 1; $i <= 5; $i++) {
            $categories->insert(array(
                    'name' => 'Category ' . $i
                )
            );
        }
        $articles = DB::table('demo_articles');
        for ($i = 1; $i <= 20; $i++) {
            $articles->insert(array(
                    'author_id' => rand(1, 2),
                    'title' => 'Article ' . $i,
                    'body' => 'Body of article ' . $i,
                    'publication_date' => date('Y-m-d'),
                    'public' => true,
                )
            );
        }
        $categories = DB::table('demo_article_category');
        $categories->insert(array('article_id' => 1, 'category_id' => 1));
        $categories->insert(array('article_id' => 1, 'category_id' => 2));
        $categories->insert(array('article_id' => 20, 'category_id' => 2));
        $categories->insert(array('article_id' => 20, 'category_id' => 3));

        $comments = DB::table('demo_comments');
        $comments->insert(array(
                'user_id' => 1,
                'article_id' => 2,
                'comment' => 'Comment for Article 2'
            )
        );

        $files = glob(public_path() . '/uploads/demo/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        echo 'All set!';
    }

}
