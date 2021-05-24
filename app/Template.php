<?php

namespace App;

use Illuminate\Console\Command;

class Template
{
    public static function run(array $template, string $domain)
    {
        $records = $template['zone'];

        foreach($records as $record) {
            $host = $record['host'];
            $type = $record['type'];
            $value = $record['value'];

            echo ("Updating $domain $type record to $value") . PHP_EOL;

            $results = \App\Ovh::get("/domain/zone/$domain/record", [
                'fieldType' => $type,
                'subDomain' => $host,
            ]);

            if(count($results) > 0) {
                $results = \App\Ovh::put("/domain/zone/$domain/record/$results[0]", [
                    'target' => $value
                ]);
                $results = \App\Ovh::post("/domain/zone/$domain/refresh");
            } else {
                dump($results);
            }
        }
    }
}
