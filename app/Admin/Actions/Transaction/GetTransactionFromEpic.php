<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon as Carbon;

class GetTransactionFromEpic extends RowAction
{
    public $name = 'Get Transaction from EPIC';

    public function handle(Model $model)
    {
        $epic = DB::connection('epic')->table('eps_transactions')->where('merchant_trans_id', $model->id)->first();

        if($epic){

            # update transaction
            $transaction = \DB::table('transaction_details')
                ->where('id', $model['id'])
                ->update([
                    'status' => $epic->eps_status,
                    'epx_trns_no' => $epic->gateway_id1,
                    'receipt_no' => $epic->receipt_no,
                    'modified' => Carbon::now()
                ]);

            # generate receipt
            if($epic->receipt_no != NULL){
                
                $url = env('EPAYMENT_URL').'/eps/response/'.base64_encode($epic->id);
                $response = Http::get($url);
                
                if($response->body() == 'Successful'){
                    return $this->response()->success('Successfully get transaction details from EPIC.');
                } else {
                    return $this->response()->error('Cannot generate receipt in EPIC. Only successful transaction can generate receipt or this receipt already exist.');
                }
            }

        } else {
            return $this->response()->warning('No such transaction records exist in EPIC.');
        }
    }
}