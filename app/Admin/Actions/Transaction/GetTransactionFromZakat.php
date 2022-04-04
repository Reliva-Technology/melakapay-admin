<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon as Carbon;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Http;

class GetTransactionFromZakat extends RowAction
{
    public $name = 'Check Status Zakat';

    public function handle(Model $model)
    {
        $receipt = \DB::table('receipts')->where('merchant_transaction_id', $model->id)->first();

        # generate receipt
        if($receipt['receipt_no'] != NULL){
            $url = 'https://api.izakat.com/epay/eps/get_transaction/F'.$model->id;
            $response = Http::get($url);
            $response->throw();

            if($response){
                $data = json_decode($response->body(),true);
                if($data['STATUS'] == '1'){
                    # post data to response page
                    $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $data);
                    return $this->response()->success($update->body())->refresh();
                } else {
                    return $this->response()->warning('Error retrieving this data from EPIC Zakat Melaka.');
                }
            } else {
                return $this->response()->warning('No response from EPIC Zakat Melaka.');
            }
        } else {
            return $this->response()->warning('Cannot retrieve transaction records from EPIC Zakat Melaka.');
        }
    }
}