<?php

/*
 * global namespace functions
 */

/**
 * Redirect the user no matter what. No need to use a return
 * statement. Also avoids the trap put in place by the Blade Compiler.
 *
 * @param string $url
 * @param int $code http code for the redirect (should be 302 or 301)
 */
function redirect_now($url, $code = 302)
{
    try {
        \App::abort(302, '', ['Location' => $url]);
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
            return    "Strings are not safe callables (got: '$value')";
        case 'array':
            return "String-only arrays are not safe callables (got: ".json_encode($value).")";
    }
    throw new LogicException('String or array expected, got: '.gettype($value));
}
