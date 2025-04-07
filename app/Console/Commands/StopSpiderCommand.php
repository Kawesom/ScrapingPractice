<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StopSpiderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stop-spider-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->good_times) {
            $this->error('Bad times');
            // $this->exit();
            // throw new RuntimeException('Bad times');
            return self::FAILURE;
        }

        // ...
        return self::SUCCESS;
    }
}
