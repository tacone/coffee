<?php


namespace Tacone\Coffee\Base;

trait StringableTrait
{
    public function __toString()
    {
        try {
            return $this->output();

        } catch (\Exception $exception) {
            $previousErrorHandler = set_exception_handler(function () {
            });
            restore_error_handler();
            call_user_func($previousErrorHandler, $exception);
            die;
        }
    }
}
