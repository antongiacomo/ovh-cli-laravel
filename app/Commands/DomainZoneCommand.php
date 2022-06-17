<?php

namespace App\Commands;

use App\Formatters\TableFormatter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use React\EventLoop\Factory;

class DomainZoneCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:zone
                            {name : The name of the domain (required)}
                            {--filter= : Filter domains}
                            {--type=A : Type of record to inspect (optional)}
                            {--subdomain= : Subdomain to inspect (optional)}
                            {--order= : Order by column (optional)}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show info about a domain';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->option('type');
        $subdomain = $this->option('subdomain');
        $order = $this->option('order');

        $results = [];

        try {
            $ids = \App\Ovh::get("/domain/zone/$name/record");
        } catch (\Throwable $e) {
            $this->warn($e->getMessage());
            exit;
        }

        $results[] = collect($ids)->map(function ($id) use ($name) {
            try {
                return \App\Ovh::get("/domain/zone/$name/record/$id");
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
                exit;
            }
        })->toArray();


        echo PHP_EOL;

        $results = $results[0];
        $headers = $results[0];

        // save json to file
        $file = storage_path("app/$name.json");

        $data = collect($results)->map(fn ($record) => [
            'host' => $record['subDomain'],
            'type' => $record['fieldType'],
            'data' => $record['fieldType'] == 'SRV' ? str_srv($record['target'])['data'] : $record['target'],
            'ttl' => $record['ttl'],
            'priority' => $record['fieldType'] == 'SRV' ? (int)str_srv($record['target'])['priority'] : 0,
            'weight' => $record['fieldType'] == 'SRV' ? (int)str_srv($record['target'])['weight'] : 0,
            'port' => $record['fieldType'] == 'SRV' ? (int)str_srv($record['target'])['port'] : 0,
        ])->toArray();

        $rows = array_map(function ($res) {
            ksort($res);
            return array_map(function ($el) {
                return is_array($el) ? implode('', $el) : $el;
            }, $res);
        }, $results);

        if (!empty($order) && array_key_exists($order, $rows[0])) {
            $orderCol = array_column($rows, $order);
            array_multisort($orderCol, SORT_ASC, SORT_NATURAL, $rows);
        }

        $rows = collect(array_map('array_filter', $rows));

        $formatter = new TableFormatter(
            collect(array_keys($rows[0]))
        );

        $formatter->output($rows);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
