<?php

namespace App\Commands;

use App\Formatters\TableFormatter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use function Termwind\render;

class DomainCheckCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:check {search}';

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
        $search = $this->argument('search');

        $results = collect(\App\Ovh::get("/domain"))
            ->filter(fn ($domain) => str_contains($domain, $search))
            ->values()
            ->toArray();

        if (count($results) == 0) {
            render(<<<HTML
            <div class="px-1">
                <p class="text-yellow-200">Domain {$search} not found!</p>
            </div>
            HTML);
        } else {
            render('<h1 class="p-1 text-gray-500 font-bold">This is what I found:</h1>');
            foreach ($results as $key => $domain) {
                render(<<<HTML
                <div class="px-1 text-green-500">
                    {$domain}
                </div>
                HTML);
            }
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
