<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ServiceRenew extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'service:renew {serviceId}';

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
        $serviceId = $this->argument('serviceId');

        $services = \App\Ovh::get("/service/{$serviceId}/renew");

        $options = $services[0]['strategies'][0]['servicesDetails'];

        $choice = $this->choice('rewnew',collect($options)->map(function($item, $i){
            return ["$i" => $item['serviceType'] . ' / ' . $item['serviceName']];
        })->flatten()->toArray());

        dump($choice);
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
