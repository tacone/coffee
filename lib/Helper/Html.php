<?php


namespace Tacone\Coffee\Helper;

class Html
{
    /**
     * Converts a dotted string to the HTML array
     * notation.
     * (book.author.name will become book[author][name])
     *
     * @param $name
     * @return string
     */
    public static function undot($name)
    {
        $segments = explode('.', $name);
        $result = array_shift($segments) ?: '';
        foreach ($segments as $s) {
            $result .= "[$s]";
        }

        return $result;
    }

    /**
     * Build an HTML attribute string from an array.
     * (Credit goes to the Laravel developers https://github.com/laravel/laravel )
     *
     * @param  array  $attributes
     * @return string
     */
    public static function renderAttributes($attributes)
    {
        $html = array();

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array) $attributes as $key => $value) {
            $element = static::renderSingleAttribute($key, $value);

            if ( ! is_null($element)) $html[] = $element;
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     * (Credit goes to the Laravel developers https://github.com/laravel/laravel )
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected static function renderSingleAttribute($key, $value)
    {
        if (is_numeric($key)) $key = $value;

        if ( ! is_null($value)) return $key.'="'.e($value).'"';
    }
}
