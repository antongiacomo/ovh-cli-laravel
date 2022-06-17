<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Termwind\ask;
use function Termwind\render;

class DomainUpdateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:update
                            {name* : The name of the domain (required)}
                            {--value= : The name of the domain (required)}
                            {--type=A : Type of record to inspect (required)}
                            {--subdomain=,www : Subdomain to inspect (required)}
                            {--dry : Run command in dry mode}
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
        $domains = $this->argument('name');
        $value = $this->option('value');
        $type = $this->option('type');
        $subdomains = explode(',', $this->option('subdomain'));
        $dry = $this->option('dry');

        if ($dry) {
           render("<i class='text-yellow-100'>Dry mode enabled</i><br>");
        }
        render('');

        foreach ($domains as $domain) {
            foreach ($subdomains as $subdomain) {
                $sub = empty($subdomain) ? '' : $subdomain . '.';

                if (! preg_match('/^y|^$/i', ask("<span>Updating <i class='text-lime-300'>$sub$domain</i> record <strong class='text-lime-300'>$type</strong> to <strong  class='text-lime-300'>$value</strong>. Ok? (yes/no) [<span class='text-orange-300'>yes</span>]:</span>") )) {
                    render('<i class="text-red-300">Aborted</i>');
                    return;
                }

                try {
                    $results = [];

                    if ($dry) {
                        render('<i class="text-yellow-300">Skipping fetching of record...</i>');
                    }

                    $this->task('Fetching record details', function () use (&$results, $type, $subdomain, $domain, $dry) {
                        if (! $dry) {
                            $results = \App\Ovh::get("/domain/zone/$domain/record", [
                                'fieldType' => $type,
                                'subDomain' => $subdomain,
                            ]);
                        } else {
                            sleep(2);
                        }
                    });

                    if ($dry) {
                        render('<i class="text-yellow-300">Skipping update...</i>');
                    }

                    $this->task('Updating record settings', function () use ($domain, $results, $value, $dry) {
                        if (! $dry) {
                            $results = \App\Ovh::put("/domain/zone/$domain/record/$results[0]", [
                                'target' => $value
                            ]);
                        } else {
                            sleep(2);
                        }
                    });

                    render('');
                } catch (\Throwable $e) {
                    render($e->getMessage());
                    exit;
                }
            }

            $this->task("Refreshing $domain zone", fn () => \App\Ovh::post("/domain/zone/$domain/refresh"));
            render('');
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
