<?php

namespace App\Commands;

use App\Template;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use SplFileInfo;

class TemplateRunCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'template:run {name} {domains*}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'run template';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domains = $this->argument('domains');

        // dd($name, $domains);
        $template = collect(File::allFiles(base_path('templates')))
            ->map(function(SplFileInfo $file) {
                $path = $file->getPathname();
                return include $path;
            })->filter(function($config) use ($name) {
                return array_key_exists('id',$config) && $config['id'] == $name;
            })->first();

        foreach($domains as $domain) {
            Template::run($template, $domain);
        }

        $this->info("Done!");
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
