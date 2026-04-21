<?php

namespace App\Console\Commands;

use App\Jobs\TrainForecastJob;
use Illuminate\Console\Command;

class TrainForecastNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecast:train-now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch training job manually';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        TrainForecastJob::dispatch();
        $this->info('Training job dispatched. Run php artisan queue:work to process it.');
        return 0;
    }
}
