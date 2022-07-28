<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scheduler;
use App\Models\Agency;
use Carbon\Carbon;

class MaintenanceSchedule extends Command
{
    protected $signature = 'maintenance:schedule';

    protected $description = 'This command is used to update maintenance schedule';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        $item = Scheduler::whereDate('start_at','>=',$now)->whereDate('end_at','>=',$now)->get();

        if($item)
        {
            foreach($item as $data)
            {
                $update = Agency::find($data['agency_id']);
                $update->enable = $data['status'];
                $update->save();
                echo 'Agency '.$data['agency_id'].' has been updated to status '.$data['status'];
            }
        } else {
            echo 'No matching schedule';
        }
    }
}
