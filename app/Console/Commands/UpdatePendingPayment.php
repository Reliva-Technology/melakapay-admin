<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UpdatePayment;
use Illuminate\Support\Facades\Http;
use DB;
use Log;
use Exception;
use Carbon\Carbon;
use App\Models\Transaction;

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
        $transactions = Transaction::where('status', 3) // pending
            ->whereDate('date_payment', '>', Carbon::today()->subDays(7))
            ->get();

        if($transactions){

            Log::info('Got '.$transactions->count().' transactions to be update');
        
            foreach($transactions as $transaction){

                $epic = DB::connection('epic')
                    ->table('eps_transactions')
                    ->where('merchant_trans_id', $transaction->id)
                    ->first();

                if($epic){

                    # generate receipt
                    if($epic->receipt_no != NULL)
                    {
                        $url = env('EPAYMENT_REQUERY_URL').$epic->id;
                        
                        $response = Http::get($url);
                        $response->throw();

                        if($response){

                            $data = json_decode($response->body(),true);

                            if(isset($data['STATUS'])){

                                if($data['STATUS'] == '1'){

                                    # post data to response page
                                    if($data['agency'] == 'stom'){
                                        $result['source'] = 'admin';
                                        $update = Http::asForm()->post(env('MELAKAPAY_URL').'stom/response', $result);
                                    } else {
                                        $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $result);
                                    }

                                    # log pending in DB
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
                } else {
                    Log::info('No matching transaction for ID '.$transaction->id);
                }
            }
        } else {
            Log::info('No pending payment transaction require status update');
        }
    }
}