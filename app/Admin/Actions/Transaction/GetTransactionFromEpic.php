<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use DB;

class GetTransactionFromEpic extends RowAction
{
    public $name = 'Get Transaction from EPIC';

    public function handle(Model $model)
    {
        $epic = DB::connection('epic')->table('eps_transactions')->where('merchant_trans_id', $model->id)->first();

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

        return $this->response()->success('Successfully get transaction details from EPIC.');
    }
}