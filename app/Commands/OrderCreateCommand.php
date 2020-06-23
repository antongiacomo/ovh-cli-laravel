<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class OrderCreateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'order:create {names}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a cart with all domain names';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $domains = explode( ',', $this->argument('names'));

        $cart = \App\Ovh::post("/order/cart", [
            'ovhSubsidiary' => 'IT'
        ]);

        $results = [];
        foreach ($domains as $domain) {
            $results[] = \App\Ovh::post("/order/cart/{$cart['cartId']}/domain", [
                'domain' => $domain
            ]);
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
