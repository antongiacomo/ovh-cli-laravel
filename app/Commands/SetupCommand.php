<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class SetupCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup your keys';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('1. Go to https://docs.ovh.com/gb/en/customer/first-steps-with-ovh-api/' . PHP_EOL);
        $this->info('2. Follow the istructions to create your 3 keys'. PHP_EOL);
        $this->info('3. Then setup your .env accordingly'. PHP_EOL);
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
