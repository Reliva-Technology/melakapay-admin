<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UpdatePayment;
use Illuminate\Support\Facades\Http;
use DB;
use Log;
use Exception;
use Carbon\Carbon;

class UpdatePendingPayment extends Command
{
    protected $signature = 'update:pending';

    protected $description = 'Periodically update pending payment transaction';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $data = DB::connection('epic')
            ->table('eps_transactions')
            ->where('eps_status', 2) // pending
            ->whereBetween('payment_datetime', 
                [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
            )
            ->get();
            
        } catch (Exception $e) {
            Log::error($e);
        }

        if($data){
            
            foreach($data as $epic){

                # check if pending already logged
                $logged = UpdatePayment::where('eps_id', $epic->id)->first();

                if($logged){
                    Log::info('No pending payment transaction require update for EPS ID:'.$epic->id);
                } else {

                    # generate receipt
                    $url = env('EPAYMENT_REQUERY_URL').$epic->id;
                    
                    $response = Http::get($url);
                    $response->throw();

                    if($response){

                        $data = json_decode($response->body(),true);

                        if($data['STATUS'] == '1'){

                            # post data to response page
                            $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $data);

                            # log pending in DB
                            UpdatePayment::updateOrCreate([
                                "eps_id" => $epic->id,
                                "transaction_id" => $epic->merchant_trans_id,
                                "eps_status" => $epic->eps_status,
                                "response" => $update->body()
                            ]);

                            Log::info('Save or create log update for EPS ID:'.$epic->id);

                            sleep(30);
                            
                        } else {
                            Log::info('Status '.$epic->eps_status.' for EPS ID:'.$epic->id);
                        }
                    } else {
                        Log::info('No response from EPIC.');
                    }
                }
            }
        } else {
            Log::info('No pending payment transaction require status update');
        }
    }
}
