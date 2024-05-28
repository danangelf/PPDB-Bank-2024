<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Data\SiswaController;

class SynchSiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:synch-kabkota {kode_kabkota}';

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
        //
        $siswaController = new SiswaController();
        $response = $siswaController->getAndStoreSiswaKabkota($this->argument('kode_kabkota'));
        if($response['success']){
            $this->info($response['message']);
        }
        else{
            $this->error($response['message']);
        }
    }
}
