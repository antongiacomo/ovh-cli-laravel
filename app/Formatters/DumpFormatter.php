<?php

namespace App\Formatters;

class DumpFormatter
{
    public function output(string|array $results)
    {
        if (!is_array($results)) {
            dump($results);
        }

        foreach ($results as $row) {
            dump($row);
        }
    }
}
