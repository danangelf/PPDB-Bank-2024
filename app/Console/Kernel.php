<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // synch siswa kota solok 086400
        $schedule->command('app:synch-kabkota 086400')->dailyAt('00:00');

        // synch siswa kota sawahlunto 086300
        $schedule->command('app:synch-kabkota 086300')->dailyAt('00:10');

        // synch siswa kota padang panjang 0862000
        $schedule->command('app:synch-kabkota 086200')->dailyAt('00:20');

        // synch siswa kota padang 086100
        $schedule->command('app:synch-kabkota 086100')->dailyAt('00:30');

        // synch siswa kota pariaman 086600
        $schedule->command('app:synch-kabkota 086600')->dailyAt('01:00');

        // synch siswa kota payakumbuh 086500
        $schedule->command('app:synch-kabkota 086500')->dailyAt('01:10');

        // synch siswa kota bukittinggi 086000
        $schedule->command('app:synch-kabkota 086000')->dailyAt('01:20');

        // synch siswa kab pasaman barat 081300
        $schedule->command('app:synch-kabkota 081300')->dailyAt('01:30');

        // synch siswa kab dharmasraya 081200
        $schedule->command('app:synch-kabkota 081200')->dailyAt('01:50');

        // synch siswa kab solok selatan 081100
        $schedule->command('app:synch-kabkota 081100')->dailyAt('02:10');

        // synch siswa kab kepulauan mentawai 081000
        $schedule->command('app:synch-kabkota 081000')->dailyAt('02:30');

        // synch siswa kab sijunjung 080800
        $schedule->command('app:synch-kabkota 080800')->dailyAt('02:50');

        // synch siswa kab tanah datar 080700
        $schedule->command('app:synch-kabkota 080700')->dailyAt('03:10');

        // synch siswa kab pesisir selatan 080600
        $schedule->command('app:synch-kabkota 080600')->dailyAt('03:30');

        // synch siswa kab padang pariaman 080500
        $schedule->command('app:synch-kabkota 080500')->dailyAt('03:50');

        // synch siswa kab solok 080400
        $schedule->command('app:synch-kabkota 080400')->dailyAt('04:10');

        // synch siswa kab lima puluh kota 080300
        $schedule->command('app:synch-kabkota 080300')->dailyAt('04:30');

        // synch siswa kab pasaman 0802000
        $schedule->command('app:synch-kabkota 080200')->dailyAt('04:50');

        // synch siswa kab agam 080100
        $schedule->command('app:synch-kabkota 080100')->dailyAt('05:10');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
