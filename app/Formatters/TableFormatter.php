<?php

namespace App\Formatters;

use Illuminate\Support\Collection;

use function Termwind\render;

class TableFormatter
{
    protected Collection $columns;

    public function __construct(Collection $columns = new Collection())
    {
        $this->columns = $columns->filter();
    }

    public function output(Collection $rows)
    {
        if ($rows->count() == 0) {
            return;
        }

        if ($this->columns->count() == 0) {
            $rows = $rows
                ->map(function ($row) {
                    return collect($row)
                        ->sortKeys()
                        ->mapWithKeys(function ($value, $key) {
                            $value = is_array($value) ? implode(', ', $value) : $value;
                            return [$key => $value];
                        });
                });

            $this->columns = $rows[0]->keys();
        }

        render(<<<HTML
            <table style="borderless">
                <thead>
                    <tr>
                       {$this->columns->map(fn ($column) => "<th class='text-lime-300'>{$column}</th>")->implode('')}
                    </tr>
                </thead>
                <tr>
                    {$this->rows($rows)}
                </tr>
            </table>
        HTML);
    }

    protected function rows(Collection $rows) {
        return $rows->map(function ($row, $index) {
            $class = $index % 2 == 0 ? 'text-blue-200' : 'text-gray-200';
            return "<tr>" . $this->columns->map(function ($col) use ($row, $class) {
                $value = $row[$col] ?? 'N/A';
                return "<td class='$class'>$value</td>";
            })->implode('') . '</tr>';
        })
        ->implode("\n");
    }
}
