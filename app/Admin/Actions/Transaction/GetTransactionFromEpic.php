<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon as Carbon;
use App\Models\User;
use App\Models\Profile;

class GetTransactionFromEpic extends RowAction
{
    public $name = 'Get Transaction from EPIC';

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

            $transaction = \DB::table('transaction_details')->find($model['id']);

            # clone user if required
            $user = User::find($transaction->user_id);
            $ebayar_user_details = Profile::where('id_no', $user->username)->first();

            if(!$ebayar_user_details){

                $ebayar_details = [
                    'user_id' => $user->id,
                    'full_name' => $user->name,
                    'id_type' => 'MyKad Number',
                    'id_no' => $user->username,
                    'email' => $user->email,
                    'modified' => now()
                ];
    
                DB::table('user_details')->insert($ebayar_details);
            }

            # generate receipt
            if($epic->receipt_no != NULL)
            {
                return $this->response()->success('Successfully get transaction details from EPIC.');
                
            } else {
                return $this->response()->warning('Cannot retrieve transaction records from EPIC.');
            }

        } else {
            return $this->response()->warning('No such transaction records exist in EPIC.');
        }
    }
}