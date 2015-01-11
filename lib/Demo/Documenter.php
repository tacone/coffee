<?php namespace Tacone\Coffee\Demo;

/**
 * This class was grabbed from the Rapyd package.
 *
 * To find out more about Rapyd see the following links:
 *
 *   Main Website: [rapyd.com](http://www.rapyd.com)
 *   Demo: [rapyd.com/demo](http://www.rapyd.com/demo)
 *   Documentation: [Wiki](https://github.com/zofe/rapyd-laravel/wiki)
 *
 * Rapyd is licensed under the [MIT license](http://opensource.org/licenses/MIT)
 */

use ReflectionClass;
use ReflectionMethod;

class Documenter
{
    public static function showCode($filepath)
    {
        $code = file_get_contents($filepath);
        $code = preg_replace("#{{ Documenter::show(.*) }}#Us", '', $code);
        $code = e($code);

        return "<pre class='prettyprint'>\n" . $code . "\n</pre>";
    }

    public static function showMethod($class, $methods)
    {
        $rclass = new ReflectionClass($class);
        $definition = implode("", array_slice(file($rclass->getFileName()), $rclass->getStartLine()-1, 1));

        $code = array();
        $code[] = "\n".$definition."\n//...";

        if (!is_array($methods))
            $methods = array($methods);

        foreach ($methods as $method) {
            $method = new ReflectionMethod($class, $method);
            $filename = $method->getFileName();
            $start_line = $method->getStartLine()-1;
            $end_line = $method->getEndLine();
            $length = $end_line - $start_line;
            $source = file($filename);
            $content = implode("", array_slice($source, $start_line, $length));

            $code[] .= $content;
        }

        $code = join("\n\n", $code);
        $code = e($code);

        return "<pre class=\"prettyprint\">\n" . $code . "\n</pre>";
    }
}
