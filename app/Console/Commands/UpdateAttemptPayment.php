<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UpdatePayment;
use Illuminate\Support\Facades\Http;
use DB;
use Log;
use Exception;
use Carbon\Carbon;

class UpdateAttemptPayment extends Command
{
    protected $signature = 'update:attempt';

    protected $description = 'Periodically update attempt payment transaction';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $data = DB::connection('epic')
            ->table('eps_transactions')
            ->where('eps_status', 0) // attempt
            ->where('update_status', 0) // belum update
            ->whereBetween('payment_datetime', 
                [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
            )
            ->get();
            
        } catch (Exception $e) {
            Log::error($e);
        }

        if($data){
            
            foreach($data as $epic){

                # check if attempt already logged
                $logged = UpdatePayment::where('eps_id', $epic->id)->first();

                if($logged){
                    Log::info('No attempt payment transaction require update for EPS ID:'.$epic->id);
                } else {

                    # generate receipt
                    $url = env('EPAYMENT_REQUERY_URL').$epic->id;
                    
                    $response = Http::get($url);
                    $response->throw();

                    if($response){

                        $data = json_decode($response->body(),true);

                        if(isset($data['STATUS'])){

                            if($data['STATUS'] == '1'){

                                # post data to response page
                                if($data['agency'] == 'stom'){
                                    $update = Http::asForm()->post(env('MELAKAPAY_URL').'stom/response', $result);
                                } else {
                                    $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $result);
                                }

                                # log attempt in DB
                                UpdatePayment::updateOrCreate([
                                    "eps_id" => $epic->id,
                                    "transaction_id" => $epic->merchant_trans_id,
                                    "eps_status" => $epic->eps_status,
                                    "response" => $update->body()
                                ]);

                                Log::info('Save or create log update for EPS ID:'.$epic->id);

                                sleep(10);
                            }
                            
                        } else {
                            Log::info('Status '.$epic->eps_status.' for EPS ID:'.$epic->id);
                        }
                    } else {
                        Log::info('No response from EPIC.');
                    }
                }
            }
        } else {
            Log::info('No attempt payment transaction require status update');
        }
    }
}
