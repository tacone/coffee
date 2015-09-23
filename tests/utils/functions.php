<?php

function assertModelArrayEqual($expected, $actual, $message = '')
{
    $args = func_get_args();
    foreach (range(0, 1) as $a) {
        foreach ($args[$a] as $k => $v) {
            unset($args[$a][$k]['id']);
            unset($args[$a][$k]['created_at']);
            unset($args[$a][$k]['updated_at']);
        }
    }

    return call_user_func_array(
        'PHPUnit_Framework_Assert::assertEquals',
        $args
    );
}
