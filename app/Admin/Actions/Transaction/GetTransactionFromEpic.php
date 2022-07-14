<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon as Carbon;
use App\Models\User;
use App\Models\Profile;
use App\Models\UpdatePayment;
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

                    if(isset($data['STATUS'])){

                        if($data['STATUS'] == '1'){

                            # post data to response page
                            if($data['agency'] == 'stom'){
                                $data['source'] = 'admin';
                                $update = Http::asForm()->post(env('MELAKAPAY_URL').'stom/response', $data);
                            } else {
                                $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $data);
                            }

                            # log attempt in DB
                            UpdatePayment::updateOrCreate([
                                "eps_id" => $epic->id,
                                "transaction_id" => $epic->merchant_trans_id,
                                "eps_status" => $epic->eps_status,
                                "response" => $update->body()
                            ]);
                            
                            return $this->response()->success($update->body())->refresh();
                            
                        } else {
                            return $this->response()->warning('Error retrieving this data from EPIC.');
                        }
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