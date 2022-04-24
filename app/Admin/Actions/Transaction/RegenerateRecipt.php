<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Messages\MailMessage;

class RegenerateRecipt extends RowAction
{
    public $name = 'Regenerate Receipt';

    public function handle(Model $model)
    {
        $epic = DB::connection('epic')->table('eps_transactions')->where('merchant_trans_id', $model->id)->first();

        if($epic){

            # update transaction
            DB::table('transaction_details')
                ->where('id', $model->id)
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

                    # delete rexisting receipt
                    unlink('/var/www/html/e-bayar-api/storage/app/public/rasmi-'.$model->id.'.pdf');

                    # post data to response page
                    $update = Http::asForm()->post(env('MELAKAPAY_URL').'payment/fpx/response', $data);

                    # send new receipt to user
                    $mail = new MailMessage;
                    $mail->subject(__('MelakaPay Transaction Receipt'))
                        ->attach(storage_path('app/public/').'rasmi-'.$model->id.'.pdf', [
                            'as' => 'MelakaPay-Receipt-'.$model->id.'.pdf',
                            'mime' => 'text/pdf',
                        ])
                        ->markdown('email.new-receipt');

                    return $this->response()->success($update->body())->refresh();
                    
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