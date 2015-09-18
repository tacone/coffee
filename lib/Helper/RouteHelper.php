<?php

namespace Tacone\Coffee\Helper;

class RouteHelper
{
    public static function fixAction($action, $defaultMethod = null)
    {
        $action = explode('@', $action);
        $current = explode('@', \Route::currentRouteAction());
        if (count($current) != 2) {
            throw new \RuntimeException('Could not get current route action');
        }
        $action[0] = $action[0] ?: $current[0];
        $action[1] = !empty($action[1]) ?  $action[1] : $defaultMethod;

        return implode('@', $action);
    }

    public static function toUrl($url, $defaultMethod = null)
    {
        switch (true) {
            case !$url || strpos($url, '@') !== false:
                return \URL::action(static::fixAction($url, $defaultMethod));
        }

        return $url;
    }
}
