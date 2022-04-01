<?php

namespace App\Formatters;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table;

class TableFormatter
{
    public function output($rows)
    {
        $table = new Table(new \Symfony\Component\Console\Output\ConsoleOutput);

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        if (count($rows) == 0) {
            return;
        }

        $rows = collect($rows)
            ->map(function ($row) {
                return collect($row)
                    ->sortKeys()
                    ->mapWithKeys(function ($value, $key) {
                        $value = is_array($value) ? implode(', ', $value) : $value;
                        return [$key => $value];
                    })->toArray();
            });

        $table->setHeaders(array_keys($rows[0]));

        $rows = $rows
            ->map(fn ($item) => array_values($item))
            ->sortBy(fn($result, $key) => $result[0], SORT_NATURAL)
            ->toArray();

        $table->addRows($rows);

        $table->render();
    }
}
