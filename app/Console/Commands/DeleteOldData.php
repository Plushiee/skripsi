<?php

namespace App\Console\Commands;

use App\Models\TabelArusAirModel;
use App\Models\TabelPingModel;
use App\Models\TabelTempHumModel;
use App\Models\TabelPompaModel;
use App\Models\TabelTDSModel;
use App\Models\TabelPHModel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:delete-old-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete data from the previous month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $seminggu = Carbon::now()->subDays(8);
        $sehari = Carbon::now()->subDays(1);

        TabelPHModel::where('created_at', '<', $seminggu)->delete();
        TabelArusAirModel::where('created_at', '<', $seminggu)->delete();
        TabelTDSModel::where('created_at', '<', $seminggu)->delete();
        TabelPompaModel::where('created_at', '<', $sehari)->delete();
        TabelTempHumModel::where('created_at', '<', $seminggu)->delete();
        TabelPingModel::where('created_at', '<', $seminggu)->delete();
    }
}
