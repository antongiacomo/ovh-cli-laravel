<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use LaravelZero\Framework\Commands\Command;
use React\EventLoop\Factory;

class ServiceOldList extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'service:old-list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all services to renew';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $services = \App\Ovh::get("/services");

        $loop = Factory::create();
        $bar = $this->output->createProgressBar(count($services));
        $bar->start();

        $results = Cache::get('service:list');

        if ($results === null) {
            $results = [];
            foreach($services as $service) {
                $loop->addTimer(0, function() use ($service, &$results, $bar) {
                    $results[] = \App\Ovh::get("/services/$service");
                    $bar->advance();
                });
            }
        }

        $loop->run();
        $bar->finish();

        Cache::put('service:list', $results);

        $this->table(
            ['Name', 'Expiration date', 'Status', 'Creation Date'],
            collect($results)->map(function ($result) {

                if (!isset($result['route']['url'])) {
                    return null;
                }

                return [
                    $result['resource']['displayName'] . ' - (' . $result['route']['url'] . ')',
                    Carbon::parse($result['billing']['expirationDate'])->format('Y-m-d'),
                    $result['billing']['lifecycle']['current']['state'] ? 'Active' : 'Expired',
                    Carbon::parse($result['billing']['lifecycle']['current']['creationDate'])->diffForHumans(),
                ];
            })->filter()->values()->sortBy(function($result, $key){
                return $result[0];
            }, SORT_NATURAL),
        );
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
