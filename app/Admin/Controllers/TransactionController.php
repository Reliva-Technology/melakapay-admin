<?php

namespace App\Admin\Controllers;

use App\Models\Transaction;
use App\Models\Agency;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use DB;
use Carbon\Carbon;
use App\Admin\Actions\Transaction\GetTransactionFromEpic;
use Illuminate\Support\Facades\Http;

class TransactionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Transaction';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Transaction());

        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $grid->model()
            ->where('agency','LIKE','%-app')
            //->whereBetween('modified',[$start,$end])
            ->orderBy('id', 'desc');
        
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('epx_trns_no', __('EPS Transaction ID'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->amount()->display(function ($amount) {
            return number_format($amount,2);
        });
        $grid->column('status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $grid->column('payment_type', __('FPX'))->using(['fpx' => 'Individual', 'fpx1' => 'Corporate']);
        $grid->column('modified', __('Date'));

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->between('modified', 'Date Range')->date();
            $filter->like('epx_trns_no', 'EPS Transaction ID');
            $filter->like('receipt_no', 'Receipt No');
            $filter->equal('status', 'Status')->radio(
                [
                    '' => 'All',
                    '0' => 'Attempt Payment',
                    '1' => 'Successful',
                    '2' => 'Failed',
                    '3' => 'Pending'
                ]
            );
            $filter->like('payment_type', 'Payment Type')->radio(
                [
                    '' => 'All',
                    'fpx' => 'FPX Individual',
                    'fpx1' => 'FPX Corporate'
                ]
            );
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->add(new GetTransactionFromEpic);
        });

        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Transaction::findOrFail($id));

        $response = Http::get(env('MELAKAPAY_URL').'/api/transactions/'.$id);
        $details = json_decode($response->body(),true);

        if(isset($details['data']['fpx_details'])){
            foreach($details['data']['fpx_details'] as $a => $b){
                $show->field($b, $a);
            }
        }

        if(isset($details['data']['payment_details'])){
            foreach($details['data']['payment_details'] as $k => $v){
                $show->field($v, $k)->unescape();
            }
        }

        $show->field('id', __('ID'));
        $show->field('agency_id', __('Agency ID'));
        $show->field('account_id', __('Account ID'));
        $show->amount()->as(function ($amount) {
            return number_format($amount,2);
        });
        $show->field('payment_type', __('Payment mode'));
        $show->field('status', __('Status'))->using(['0' => 'Failed', '1' => 'Success', '2' => 'Cancelled', '3' => 'Pending']);
        $show->field('epx_trns_no', __('EPS Transaction ID'));
        $show->field('receipt_no', __('Receipt No'));
        $show->field('modified', __('Created at'));

        $receipt = DB::table('receipts')->where('merchant_transaction_id', $id)->first();

        if($receipt){

            $show->id(__('Action'))->unescape()->as(function ($data) {
                return '<a href="https://melakapay.melaka.gov.my/storage/rasmi-'.$data.'.pdf" class="btn btn-sm btn-primary" title="View Receipt" target="_blank">View Receipt</a>';
            });

        } else {

            $show->id(__('Action'))->unescape()->as(function ($data) {
                $epic = DB::connection('epic')->table('eps_transactions')->where('merchant_trans_id', $data)->first();
                return '<a href="'.env('EPAYMENT_URL').'/eps/response/'.base64_encode($epic->id).'" class="btn btn-sm btn-success" title="Retrieve latest status" target="_blank">Retrieve latest status</a> <a href="https://melakapay.melaka.gov.my/storage/rasmi-'.$data.'.pdf" class="btn btn-sm btn-primary" title="View Receipt" target="_blank">View Receipt</a>';
            });
        }

        $show->user(__('User'), function ($user){
            $user->setResource('/users');
            $user->name();
            $user->email();

            $user->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });
        });

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Transaction());

        $form->text('epx_trns_no', __('EPS Transaction ID'));
        $form->text('receipt_no', __('Receipt No'));
        
        $form->number('user_id', __('User ID'));
                
        $form->text('status', __('Status'));
        $form->text('account_id', __('Account No'));
        $form->datetime('date_payment', __('Payment Date'));
        $form->text('payment_type', __('Payment Type'));
        $form->text('amount', __('Amount'));

        return $form;
    }
}