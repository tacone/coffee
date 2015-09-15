<?php

/*
 * global namespace functions
 */
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Contracts\ArrayableInterface;
use Tacone\Coffee\Helper\RouteHelper;

/**
 * Redirect the user no matter what. No need to use a return
 * statement. Also avoids the trap put in place by the Blade Compiler.
 *
 * @param string $url
 * @param int    $code http code for the redirect (should be 302 or 301)
 */
function redirect_now($url, $code = 302)
{
    try {
        \App::abort($code, '', ['Location' => $url]);
    } catch (\Exception $exception) {
        // the blade compiler catches exceptions and rethrows them
        // as ErrorExceptions :(
        //
        // also the __toString() magic method cannot throw exceptions
        // in that case also we need to manually call the exception
        // handler
        $previousErrorHandler = set_exception_handler(function () {
        });
        restore_error_handler();
        call_user_func($previousErrorHandler, $exception);
        die;
    }
}

/**
 * Check if a callback is a safe callback.
 * Strings callback are insecure as they may come from user input.
 *
 * Forbidden callables are:
 * - string only (i.e. 'strtoupper' or 'unlink')
 * - arrays with string obj params (i.e. ['App', 'abort'])
 *
 * @param $callable
 *
 * @return bool
 */
function is_safe_callable($callable)
{
    return (
        is_callable($callable)
        && !is_string($callable)
        && (!is_array($callable) || count($callable) && is_object($callable[0]))
    );
}

function unsafe_callable_error_message($value)
{
    switch (gettype($value)) {
        case 'string':
            return "Strings are not safe callables (got: '$value')";
        case 'array':
            return 'String-only arrays are not safe callables (got: '.json_encode($value).')';
    }
    throw new LogicException('String or array expected, got: '.gettype($value));
}

function missing_method_message($object, $methodName)
{
    // the method does not exist or it hasn't been exposed
    return 'Method \''.get_class($object)."::$methodName' does not exist";
}

function quick_url($url)
{
    return RouteHelper::toUrl($url);
}

function to_array($array)
{
    switch (true) {
        case $array instanceof ArrayableInterface:
        case $array instanceof Model:
            return $array->toArray();

        case $array instanceof EloquentBuilder:
        case $array instanceof QueryBuilder:
            return $array->get()->toArray();

        case $array instanceof \ArrayIterator:
        case $array instanceof \ArrayObject:
            return $array->getArrayCopy();

        case is_null($array):
            return [];
    }

    throw new \LogicException(sprintf(
        'to_array() does not supports type: %s%s',
        gettype($array),
        is_object($array) ? ' - '.get_class($array) : ''
    ));
}
