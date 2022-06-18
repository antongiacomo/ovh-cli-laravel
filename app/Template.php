<?php

namespace App;

use Illuminate\Support\Str;

class Template
{

    public static function parse($template, string $domain)
    {
        Template::run([
            'id' => Str::random(12),
            'zone' => array_map(fn ($row) =>
                array_combine(['host', 'type', 'value'], str_getcsv($row)), $template)
        ], $domain);
    }

    public static function run(array $template, string $domain)
    {
        $records = $template['zone'];

        foreach($records as $record) {
            $host = $record['host'];
            $type = $record['type'];
            $value = $record['value'];

            echo ("Updating $domain: $host $type $value") . PHP_EOL;

            $results = \App\Ovh::get("/domain/zone/$domain/record", [
                'fieldType' => $type,
                'subDomain' => $host,
            ]);

            if(count($results) > 0) {
                $results = \App\Ovh::put("/domain/zone/$domain/record/$results[0]", [
                    'target' => $value
                ]);
            } else {
                $results = \App\Ovh::post("/domain/zone/$domain/record", [
                    'fieldType' => $type,
                    'subDomain' => $host,
                    'target' => $value
                ]);
            }

            $results = \App\Ovh::post("/domain/zone/$domain/refresh");
        }
    }
}
