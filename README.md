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

- as few lines of code as possible for basic cases
- let developers use one generic view for all the CRUDs
- customization via public methods/attributes and callbacks
rather than class extension
- make it easy to create custom field types
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

Then add the coffe provider to the providers list in
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

## Contributing

I welcome pull requests. Please try to send PSR-2 compliant code.

The suggested method to conform to PSR-2 is installing 
[PSR Police](https://github.com/tacone/psr-police) and using it
to set a pre-commit git hook.

## License

You are permitted to use this library under the terms of the MIT license.

Some code comes from third-party libraries:

- the Documenter class comes from the [Rapyd](https://github.com/zofe/rapyd-laravel) package by Felice Ostuni
- some methods taken from the [Laravel framework](https://github.com/laravel/laravel)