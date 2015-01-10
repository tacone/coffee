<?php

use Tacone\Coffee\Collection\FieldCollection;

$field = new TextField($name);

$field->value($newValue = null);
$field->label($customLabel = true); // '' means no label, true means auto
$field->help($helpText);
$field->filter($filterRules);
$field->rule($laravelRulesString);
$field->validate(); // --> true/false
$field->errors(); // --> validation errors
$field->output(); // --> prints the field as HTML
$field->assignTo($var); //--> assign to variable without breaking chaining

// -- html
$field->attr($name, $newValue = null); // html attribute on the main control
$field->css(); // css rules to go into the style attribute
$field->classes(); // class attribute
$field->wrap($before = '', $after = ''); // wraps the field into a container

// -- transformators
echo $field; // __toString() calls $field->output();

// ---------------------
// field type
// ---------------------

$f->text();
$f->textarea();
$f->select();
$f->checkbox();
$f->radio();
$f->multiselect();
$f->checkboxes();
$f->file();
$f->image();
$f->password();

// shortcuts
$f->email();
$f->number();
$f->integer();

// advanced
$f->color();
$f->map();

// ---------------------
// Collection
// ---------------------

$fields = new FieldCollection();
$fields->add($field);
$fields->remove($field);
$fields->validate();
$fields->errors();

// -- transformators
$fields->toArray();
$fields->toJson(); //or use json_encode($field);


// ---------------------
// Flow
// ---------------------

$outputtable = $field->output();
echo $outputtable->output();

// -- group outputtable

$outputtable->before('<span>hello</span>');

// -- outputtables

$form['title']->labelOutput(function ($outputtable) {});
$form['title']->labelReplaceOutput($newOutputtable);
$form['title']->labelMaskAddClass('col-xs-6');

$field->beforeStart('<span>lorem ipsum</span>');
$field->afterStart('<span>lorem ipsum</span>');
$field->beforeEnd('<span>lorem ipsum</span>');
$field->afterEnd('<span>lorem ipsum</span>');

// --- validation
$field->setValidatorCallback($callable);
$field->validateWith($validator);

// sample array

[
    'title' => 'my meeting',
    'host_id' => 54,
    'info' => ['id' => 4, 'address' => 'lorem ipsum'],
    'attendants' => [
        [ 'id' => 3, 'name' => 'christoff'],
        [ 'id' => 12, 'name' => 'laura'],
        [ 'id' => '', 'name' => '%$weoij'],
    ]
];
