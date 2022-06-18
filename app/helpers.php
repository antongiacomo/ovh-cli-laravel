<?php

/**
 * If you need something safer or more complete see https://github.com/lstrojny/functional-php,
 * in particular, https://github.com/lstrojny/functional-php/blob/master/src/Functional/FlatMap.php
 *
 * @param Callable $fn Mapping function that returns an array
 * @param array $array Data over which $fn will be mapped
 * @return array
 */
function array_flatmap(callable $fn, $array)
{
    return array_merge(...array_map($fn, $array));
}

function str_srv($string): array
{
    preg_match('/(?<priority>\d+)\s(?<weight>\d+)\s(?<port>\d+)\s(?<data>.*?)$/', $string, $matches);

    return $matches;
}

if (! function_exists('getEnvPath')) {
    function getEnvPath()
    {
        return getenv('HOME') .
             DIRECTORY_SEPARATOR . '.config' .
             DIRECTORY_SEPARATOR . 'ovhcli';
    }
}
