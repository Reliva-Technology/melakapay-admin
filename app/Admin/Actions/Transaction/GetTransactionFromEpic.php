<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon as Carbon;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Http;

class GetTransactionFromEpic extends RowAction
{
    public $name = 'Check Status';

    public function handle(Model $model)
    {
        $epic = DB::connection('epic')->table('eps_transactions')->where('merchant_trans_id', $model->id)->first();

        if($epic){

            # update transaction
            \DB::table('transaction_details')
                ->where('id', $model['id'])
                ->update([
                    'status' => $epic->eps_status,
                    'epx_trns_no' => $epic->gateway_id1,
                    'receipt_no' => $epic->receipt_no,
                    'modified' => Carbon::now()
                ]);

            # generate receipt
            if($epic->receipt_no != NULL)
            {
                $url = env('EPAYMENT_REQUERY_URL').$epic->id;
                $response = Http::get($url);
                $response->throw();

                if($response){

                    $data = json_decode($response->body(),true);

                    if($data['STATUS'] == '1'){

                        # post data to response page
                        $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $data);

                        if($update->body() === 'Successful'){
                            return $this->response()->success('Successfully update transaction ID '.$data['TRANS_ID'])->refresh();
                        } else {
                            return $this->response()->warning('No update required for transaction ID '.$data['TRANS_ID']);
                        }
                    } else {
                        return $this->response()->warning('Error retrieving this data from EPIC.');
                    }
                } else {
                    return $this->response()->warning('No response from EPIC.');
                }
            } else {
                return $this->response()->warning('Cannot retrieve transaction records from EPIC');
            }
        } else {
            return $this->response()->warning('No such transaction records exist in EPIC.');
        }
    }
}