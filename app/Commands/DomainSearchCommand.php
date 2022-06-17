<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use function Termwind\render;

class DomainSearchCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:search {query}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $search = $this->argument('query');

        $results = collect(\App\Ovh::get("/domain"))
            ->filter(fn ($domain) => str_contains($domain, $search))
            ->values();

        if (count($results) == 0) {
            render(<<<HTML
            <p class="text-yellow-300">Domain {$search} not found!</p>
            HTML);
        } else {
            $items = $results->filter()->map(fn ($domain) => "<li class='text-lime-300'>{$domain}</li>")->implode('');

            render(<<<HTML
            <ol class="">
                {$items}
            </ol>
            HTML);
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
