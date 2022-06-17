<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

use function Amp\asyncCall;
use function React\Promise\all;
use function Termwind\render;

use App\Formatters\DumpFormatter;
use App\Formatters\TableFormatter;
use App\Formatters\TextFormatter;
use React\Promise\Deferred;
use React\Promise\Promise;

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
                            {--format=table : The record type to show by default(optional)}
                            {--columns= : The record type to show by default(optional)}';


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
        $search = $this->option('search');
        $order = $this->option('order');

        $format = match ($this->option('format')) {
            'txt' => new TextFormatter(),
            'dump' => new DumpFormatter(),
            default => new TableFormatter(collect(explode(',', $this->option('columns')))),
        };

        /** @var Collection $results */
        $domains = collect(\App\Ovh::get("/domain"))
            ->filter(fn ($domain) => str_contains($domain, $search))
            ->values();

        $bar = $this->output->createProgressBar(count($domains));
        $bar->start();

        $results = $domains
            ->map(function ($domain) use ($bar) {
                return new Promise(function (callable $resolve, callable $reject, callable $notify) use ($domain, $bar) {
                    try {
                        $details = \App\Ovh::get("/domain/$domain/serviceInfos");
                        $bar->advance();
                        return $resolve($details);
                    } catch (\Throwable $e) {
                        $bar->advance();
                        render("<span class='text-red-300'>Error</span>: Failed to fetch $domain details", $e->getMessage());
                        $resolve();
                    }
                });
            })
            ->toArray();

        $promise = all($results);

        $promise->then(function ($results) use ($bar, $order, $format) {
            $bar->finish();

            $results = collect($results)->values();

            if (! empty($order)) {
                $results = $results
                    ->sortBy([
                        fn ($a, $b) => $a[$order] <=> $b[$order],
                    ], SORT_NATURAL)
                    ->values();
            }

            render('');
            render('');

            $format->output($results);
        });

        return 0;
    }
}
