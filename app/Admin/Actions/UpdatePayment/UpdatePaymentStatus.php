<?php

namespace App\Admin\Actions\UpdatePayment;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use App\Models\UpdatePayment;
use Illuminate\Support\Facades\Http;

class UpdatePaymentStatus extends Action
{
    public $name = 'Update Transaction Status';

    protected $selector = '.search';

    public function handle(Request $request)
    {
        $date = $request->get('date');
        $status = $request->get('status');
        $payment_type = $request->get('payment_type');

        $data = \DB::connection('epic')->table('eps_transactions')
        ->where('eps_status', $status)
        ->whereDate('payment_datetime', $date)
        ->take(10)
        ->get();

        if($data){
            
            foreach($data as $epic){

                # generate receipt
                $url = env('EPAYMENT_REQUERY_URL').$epic->id;
                
                $response = Http::get($url);
                $response->throw();

                if($response){

                    $result = json_decode($response->body(),true);

                    if(isset($result['STATUS'])){

                        if($result['STATUS'] == '1'){

                            # post data to response page
                            if($result['agency'] == 'stom'){
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
                        }
                    }
                }
            }
        } else {
            return $this->response()->error('No transaction require update for this criteria')->refresh();
        }
    }

    public function form()
    {
        $this->date('date', 'Date');
        $this->select('status', __('Status'))->options(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default search">Update Transaction</a>
        HTML;
    }
}