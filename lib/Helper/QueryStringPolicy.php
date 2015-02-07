<?php
namespace Tacone\Coffee\Helper;

class QueryStringPolicy
{

    public static function action($value = null)
    {
        if (func_num_args()) {
            return 'coffee[action]=' . urlencode($value);
        }
        switch (\Request::method()) {
            case 'DELETE':
                return 'destroy';
        }
        $data = \Input::query('coffee');
        return array_get($data, 'action');
    }

    public static function id($value = null)
    {
        if (func_num_args()) {
            return 'coffee[id]=' . urlencode($value);
        }
        $data = \Input::query('coffee');
        return array_get($data, 'id');
    }
}