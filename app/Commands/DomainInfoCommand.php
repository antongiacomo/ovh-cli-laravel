<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use React\EventLoop\Factory;

class DomainInfoCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:info
                            {--name= : The name of the domain}
                            {--more : Whether to fetch more details}
                            {--filter= : Whether we should filter out the domains}
                            {--type=A : The record type to show by default}
                            {--order= : The record type to show by default(optional)}
                            {--format=dump : The record type to show by default(optional)}';

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
        $more = $this->option('more');
        $filter = $this->option('filter');
        $order = $this->option('order');

        if ($name) {
            $results = \App\Ovh::get("/domain/$name/serviceInfos");
            dump($results);
            return;
        }

        $results = \App\Ovh::get("/domain");

        $results = collect($results)
            ->filter(fn ($domain) => strpos($domain, $filter) !== false)
            ->values()
            ->toArray();

        if ($more) {
            $domains = $results;
            $results = [];
            $loop = Factory::create();
            $bar = $this->output->createProgressBar(count($domains));
            $bar->start();

            foreach($domains as $domain) {
                $loop->addTimer(0, function() use ($domain, &$results, $bar) {
                    $results[] = \App\Ovh::get("/domain/$domain/serviceInfos");
                    $bar->advance();
                });
            }

            $loop->run();
            $bar->finish();

            echo PHP_EOL;

            $rows = array_map(function($res) {
                ksort($res);
                return array_map(function($el) {
                    return is_array($el) ? implode('',$el) : $el;
                }, $res);
            },$results);

            if (!empty($order) && array_key_exists($order, $rows[0])) {
                $orderCol = array_column($rows, $order);
                array_multisort($orderCol, SORT_ASC, SORT_NATURAL, $rows);
            }

            $rows = array_map('array_filter', $rows);

            if ($this->option('format') == 'txt') {
                foreach($rows as $row) {
                    echo $row['domain'] . PHP_EOL;
                }
            }else {
                $this->table(
                    array_map(fn ($key) => ucwords(preg_replace('/(?<!\ )[A-Z]/', ' $0', $key)), array_keys($rows[0])),
                    $rows,
                );
            }
        }else {
            if ($this->option('format') == 'txt') {
                foreach($results as $res) {
                    echo $res . PHP_EOL;
                }
            } else {
                dump($results);
            }
        }
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
