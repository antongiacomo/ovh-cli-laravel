<?php

namespace App\Formatters;

class TextFormatter
{
    public function output($results)
    {
        echo PHP_EOL;

        foreach ($results as $res) {

            $output = is_array($res) ? $this->recursiveImplode($res) : $res;

            echo $output . PHP_EOL;
        }
    }

    public function recursiveImplode($input): string
    {
        $string = '';

        ksort($input);

        foreach ($input as $i => $a) {
            if (is_array($a)) {
                $string .= $this->recursiveImplode($a) . ', ';
            } else {
                $string .= $a . ', ';
            }
        }

        return trim($string, ', ');
    }
}
