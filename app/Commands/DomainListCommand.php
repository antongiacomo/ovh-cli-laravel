<?php

namespace App\Commands;

use App\Formatters\DumpFormatter;
use App\Formatters\TableFormatter;
use App\Formatters\TextFormatter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use React\EventLoop\Factory;

class DomainListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:list
                            {--search= : Whether we should filter out the domains}
                            {--order= : The record type to show by default(optional)}
                            {--format=dump : The record type to show by default(optional)}';


    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show info about a service';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $format = match ($this->option('format')) {
            'txt' => new TextFormatter(),
            'dump' => new DumpFormatter(),
            default => new TableFormatter(),
        };

        $search = $this->option('search');
        $order = $this->option('order');

        $results = collect(\App\Ovh::get("/domain"))
            ->filter(fn ($domain) => str_contains($domain, $search))
            ->values()
            ->toArray();

        $domains = $results;

        // TODO: Add cache for this requests

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


        $format->output($results);

        return 0;
    }
}
