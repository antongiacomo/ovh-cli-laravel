<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DomainUpdateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:update
                            {name : The name of the domain (required)}
                            {value : The name of the domain (required)}
                            {--type= : Type of record to inspect (required)}
                            {--subdomain= : Subdomain to inspect (required)}
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
        $value = $this->argument('value');
        $type = $this->option('type');
        $subdomain = $this->option('subdomain');

        $this->info("Updating $domain $type record to $value");

        $results = \App\Ovh::get("/domain/zone/$domain/record", [
            'fieldType' => $type,
            'subDomain' => $subdomain,
        ]);

        $this->info("Got domain id: $results[0]");

        $this->info("Updating $domain...");

        $results = \App\Ovh::put("/domain/zone/$domain/record/$results[0]", [
            'target' => $value
        ]);

        if (!is_null($results)) {
            dump($results);
        }

        $results = \App\Ovh::post("/domain/zone/$domain/refresh");

        if (is_null($results)) {
            $this->info("Done!");
        } else{
            dump($results);
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
