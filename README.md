# Coffee

Coffee is a one-stop laravel CRUD solution.

This library is inspired by the [Rapyd](https://github.com/zofe/rapyd-laravel)
package and aims to become a widget library decoupled from pre-made
backends, to allow a developer to quickly build record listings
and forms with creation, editing and deletion in few lines
of code.

For the most basic cases, a generic view and two controller methods 
would suffice for a single crud: one for the record list, one for the
editing form.

Eloquent relations will be fully supported with the possible
exception of polymorphic relations (still to be seen).

## Principles

- require as few lines of code as possible for basic cases
- let developers use one generic container view for all the CRUDs
- customization via public methods/attributes and callbacks
rather than class extension
- fractal (nested) automation/overrides
- make it easy to create custom field types that work
- jump off the ajax bandwagon where possible. As less javascript
as possible. Allow ajax-form and instantclick use for those
interested
- little or no configuration
- allow the use blade and twig, don't depend on them

## Requirements

- Laravel 4.2 (untested on 4.1)
- PHP 5.4+ (we use traits extensively)

## Installation

Add the package to composer `composer.json`:

```
"tacone/coffee": "dev-master"  
```

Then add the coffee provider to the providers list in
 `app/config/app.php`:
   
```
'Tacone\Coffee\CoffeeServiceProvider',
```

then run: `composer update tacone/coffee`.

To try the demos add this line to `routes.php`:

```php
Route::controller('/demo', 'Tacone\Coffee\Demo\Controllers\DemoController');
```

Then point your browser to `http://<publicurl>/demo`. You will then be able
to setup the required tables by following the `setup` link.

## Usage

Coffee is made up of a handfew standalone widgets.

### DataForm

The dataform is a stateless form-builder that accepts a datasource 
as argument.

You can use it to build, validate and save forms with ease, while 
retaining control over each phase of the life-cycle.

A sample contact form may look like the example below:

```php
// definition
$model = new Message(); //an eloquent model
$form = new DataForm($model);
$form->text('title')->rule('required');
$form->text('mail', 'Your mail address')->rule('required|email');
$form->textarea('message')->rule('min:20');

// read the user submission from Input
$form->populate();

// set the value of the fields in the model
$form->writeSource();

// validate only when form has been submitted
if ($form->submitted() && $form->validate()) {
    // save the message in the database
    $form->save();
    // do something, for example send it via mail
    // then redirect to the homepage
    return Response::redirect('/');
}
return View::make("contact-us", compact('form'));
```

As you see very few lines of code are needed. To save even more
typing, you can use the DataEdit, which has a similar syntax, but
automates everything but the building part.

You can then simply print your form inside `contact-us.blade.php`:

```
{{ $form }}
```

But you could instead customize it more by printing each component
separately:

```
{{ $form->start }}
<div class="alert alert-success">My custom message :))</div>
{{ $form->fields }}
{{ $form->end }}
```

Or even more!

```
{{ $form->start }}
<!-- Look ma', customization! -->
<p><em>This is a simple form printed in a custom view.</em></p>

@foreach($form as $field)
    @if ($field->name() == 'title'
        <div class="form-group my-title-field {{ count($field->errors)? 'has-error':'' }}">
            {{ $field->label }}
            <p><em>Try to choose a nice title :)</em></p>
            {{ $field->control() }}
            {{ $field->errors() }}
        </div>
    @else
        {{ $field }}
    @endif
@endforeach
<p> Click the button! </p>
{{ $form->end }}
```

The bottom line is you can choose the level of customization without
unnecessary logic duplication.

## Contributing

I welcome pull requests. Please try to send PSR-2 compliant code.

The suggested method to conform to PSR-2 is installing 
[PSR Police](https://github.com/tacone/psr-police) and using it
to set a pre-commit git hook.

## Unit tests

There are no tests at the moment, as I feel it's too soon and the
design of this package may change sensibly as the development goes on.

Hopefully, as the API becomes stable, I will write some.

## License

You are permitted to use this library under the terms of the MIT license.

Some code comes from third-party libraries:

- the Documenter class and the demo models/migrations/seeds are derived from the
[Rapyd](https://github.com/zofe/rapyd-laravel) package by Felice Ostuni
- some methods taken from the [Laravel framework](https://github.com/laravel/laravel)