<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;

class ActivateCitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activate:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate mashhad and ahvaz';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mashhad = City::where('name', 'مشهد')->firstOrFail();
        $ahvaz = City::where('name', 'اهواز')->firstOrFail();

        if (env('APP_ENV') == "local") {
            $host = "http://142.44.150.155:8008/";
        } else {
            $host = "file.chaarpaye.ir/";
        }

        $mashhad->update([
            'is_active' => true,
            'image' => $host . 'cities/mashhad.jpg'
        ]);

        $ahvaz->update([
            'is_active' => true,
            'image' => $host . 'cities/ahvaz.jpg'
        ]);
        $this->info('Mashhad and Ahvaz successfully activated!');
    }
}
