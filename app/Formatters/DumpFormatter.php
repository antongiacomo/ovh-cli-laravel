<?php

namespace App\Formatters;

use Illuminate\Support\Collection;

class DumpFormatter
{
    public function output(Collection $results)
    {
        if (! is_iterable($results)) {
            dump($results);
        }

        foreach ($results as $row) {
            dump($row);
        }
    }
}
