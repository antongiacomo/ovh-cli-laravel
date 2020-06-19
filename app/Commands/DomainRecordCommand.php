<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DomainRecordCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:record
                            {name : The name of the domain (required)}
                            {--type= : Type of record to inspect (optional)}
                            {--subdomain= : Subdomain to inspect (optional)}
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
        $domain = $this->argument('name');

        $type = $this->option('type');
        $subdomain = $this->option('subdomain');

        $results = \App\Ovh::get("/domain/zone/$domain/record", [
            'fieldType' => $type,
            'subDomain' => $subdomain,
        ]);

        $results = \App\Ovh::get("/domain/zone/$domain/record/$results[0]");

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
