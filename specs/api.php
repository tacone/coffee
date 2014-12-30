<?php

trait exposeable
{

}

trait exposer
{

}


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
$d->set($key, $value);
$d->set($array);
$d->get($key);
$d->unset($key);
$d->remove($value);
$d->accept($key, $value);
$c->toArray();
$c->fromArray($array);

$field->attr($key);
$field->attr($value);
$field->removeAttr($value);
$field->unsetAttr($key);