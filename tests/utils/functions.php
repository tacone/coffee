<?php

function assertModelArrayEqual($expected, $actual, $message = '')
{
    $args = func_get_args();
    foreach ($args[1] as $k => $v) {
        unset($args[1][$k]['id']);
        unset($args[1][$k]['created_at']);
        unset($args[1][$k]['updated_at']);
    }

    return call_user_func_array(
        'PHPUnit_Framework_Assert::assertEquals',
        $args
    );
}
