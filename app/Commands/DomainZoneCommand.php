<?php

namespace App\Commands;

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
                            {--name= : The name of the domain (optional)}
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
        $name = $this->option('name');
        $filter = $this->option('filter');
        $type = $this->option('type');
        $subdomain = $this->option('subdomain');
        $order = $this->option('order');


        if ($name) {
            $results = \App\Ovh::get("/domain/$name/serviceInfos");
            dump($results);
            return;
        }

        $domains = collect(\App\Ovh::get("/domain"))
            ->filter(fn ($domain) => strpos($domain, $filter) !== false)
            ->values()
            ->toArray();

        $results = [];
        $loop = Factory::create();
        $bar = $this->output->createProgressBar(count($domains));
        $bar->start();

        foreach($domains as $domain) {
            $loop->addTimer(0, function() use ($domain, $type, $subdomain, &$results, $bar) {
                $ids = \App\Ovh::get("/domain/zone/$domain/record", [
                    'fieldType' => strtoupper($type),
                    'subDomain' => $subdomain,
                ]);

                if (count($ids) > 0) {
                    $results[] = \App\Ovh::get("/domain/zone/$domain/record/$ids[0]");
                }

                $bar->advance();
            });
        }

        $loop->run();
        $bar->finish();

        echo PHP_EOL;

        $headers = $results[0];

        $rows = array_map(function($res) {
            ksort($res);
            return array_map(function($el) {
                return is_array($el) ? implode('',$el) : $el;
            }, $res);
        }, $results);

        if (!empty($order) && array_key_exists($order, $rows[0])) {
            $orderCol = array_column($rows, $order);
            array_multisort($orderCol, SORT_ASC, SORT_NATURAL, $rows);
        }

        $rows = array_map('array_filter', $rows);

        $this->table(
            array_map(fn ($key) => ucwords(preg_replace('/(?<!\ )[A-Z]/', ' $0', $key)), array_keys($rows[0])),
            $rows,
        );
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
