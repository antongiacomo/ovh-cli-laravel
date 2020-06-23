<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class OrderInfoCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'order:info {id}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Info a cart with all domain names';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        if ($id) {
            $carts = \App\Ovh::get("/order/cart/$id/item");
        }else {
            $carts = \App\Ovh::get("/order/cart");
        }

        dump($carts);
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
