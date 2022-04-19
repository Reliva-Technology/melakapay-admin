<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\UpdatePayment;
use DB;
use Log;
use Exception;

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
            ->where('merchant_trans_id', '>', 496072) // only melakapay transaction
            ->where('eps_status', 0) // attempt
            ->where('update_status', 0) // belum update
            ->whereNotNull('receipt_no') // takde resit
            ->get();
            
        } catch (Exception $e) {
            Log::error($e);
        }

        if($data){
            
            foreach($data as $epic){

                # check if attempt already logged
                $logged = UpdatePayment::all();

                if($logged){
                    if(isset($logged['eps_id'])){
                        if($logged['eps_id'] != $epic->id){

                            # generate receipt
                            $url = env('EPAYMENT_REQUERY_URL').$epic->id;
                            
                            $response = Http::get($url);
                            $response->throw();

                            if($response){

                                $data = json_decode($response->body(),true);

                                if($data['STATUS'] == '1'){

                                    # post data to response page
                                    $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $data);

                                    # log attempt in DB
                                    UpdatePayment::updateOrCreate([
                                        "eps_id" => $epic->id,
                                        "transaction_id" => $epic->merchant_trans_id,
                                        "eps_status" => $epic->eps_status,
                                        "response" => $update->body()
                                    ]);

                                    Log::info('Save or create log update for EPS ID:'.$epic->id);

                                    sleep(30);
                                    
                                } else {
                                    Log::info('Error retrieving this data from EPIC.');
                                }
                            } else {
                                Log::info('No response from EPIC.');
                            }
                        }
                    } else {
                        Log::info('No record for EPS ID:'.$epic->id);
                    }
                } else {
                    Log::info('No attempt payment transaction require update for EPS ID:'.$epic->id);
                }
            }
        } else {
            Log::info('No attempt payment transaction require status update');
        }
    }
}
