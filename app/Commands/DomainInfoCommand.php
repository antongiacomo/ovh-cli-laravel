<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

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
                            {--type=A : The record type to show by default}';

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
            foreach($domains as $domain) {
                $results[] = \App\Ovh::get("/domain/$domain/serviceInfos");
                echo '.';
            }
        }

        dump($results);
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
