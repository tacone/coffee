<?php

trait exposeable
{

}

trait exposeableParser
{

}

$a = new attribute();
$a->get();
$a->set($value);
$field->value();
$field->value($newValue);

// collection API

$c = new collection();
$c->add($value);
$c->remove($value);
$c->get($value);
$c->toArray();
$c->fromArray($array);
$c->accept($value);
$c->exposes(); //add, remove

$field->class('one', 'two', 'three');
$field->addClass('one');
$field->removeClass('one');
$field->toggleClass('one', true);

// dictionary API

$d = new dictionary();
$d->set($array);
$d->get($key);
$d->add($key, $value);
$d->remove($value);
$d->unset($key);
$d->accept($key, $value);
$d->toArray();
$d->fromArray($array);

$field->attr($key);
$field->attr($key, $value);
$field->addAttr($value);
$field->removeAttr($value);
$field->unsetAttr($key);


// ----
$o->exposes() == [
    'accessors' == [],
    'others' == []
];

