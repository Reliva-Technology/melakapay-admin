<?php

namespace App\Admin\Controllers;

use App\Models\Transaction;
use App\Models\Agency;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use DB;
use Carbon\Carbon;
use App\Admin\Actions\Transaction\GetTransactionFromEpic;
use Illuminate\Support\Facades\Http;
Use Encore\Admin\Admin;

class TransactionController extends AdminController
{
    protected $title = 'Transaction';

    protected function grid()
    {
        $grid = new Grid(new Transaction());

        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $grid->model()
            ->where('agency','LIKE','%-app')
            //->whereBetween('modified',[$start,$end])
            ->orderBy('id', 'desc');
        
        $grid->column('modified', __('Payment Date/Time'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('epx_trns_no', __('EPS Transaction ID'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->amount()->display(function ($amount) {
            return number_format($amount,2);
        })->totalRow();
        $grid->column('status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $grid->column('payment_type', __('FPX'))->using(['fpx' => 'Individual', 'fpx1' => 'Corporate']);

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

    protected function detail($id)
    {
        $show = new Show(Transaction::findOrFail($id));

        $show->receipt(__('FPX Details'), function ($receipt){
            $receipt->field('ic_no', __('IC No'));
            $receipt->field('account_no', __('Account No'));
            $receipt->field('payment_type', __('Payment Type'));
            $receipt->field('transaction_date_time', __('Transaction Date/Time'));
            $receipt->field('payment_from', __('Payment From'));
            $receipt->field('fpx_charge', __('FPX Charge'));
            $receipt->field('buyer_bank', __('Buyer Bank'));
            $receipt->field('fpx_transaction_no', __('FPX Transaction No'));
            $receipt->field('seller_order_no', __('Seller Order No'));

            $receipt->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });
        });

        $show->field('id', __('ID'));
        $show->field('agency_id', __('Agency ID'));
        $show->field('account_id', __('Account ID'));
        $show->amount()->as(function ($amount) {
            return number_format($amount,2);
        });
        $show->field('payment_type', __('Payment mode'));
        $show->field('epx_trns_no', __('EPS Transaction ID'));
        $show->field('receipt_no', __('Receipt No'));
        $show->field('modified', __('Created at'));

        $receipt = DB::table('receipts')->where('merchant_transaction_id', $id)->first();

        if($receipt){

            if($show->status()->using(['0' => 'Failed', '1' => 'Success', '2' => 'Cancelled', '3' => 'Pending']) == '1'){

                $show->id(__('Action'))->unescape()->as(function ($data) {
                    return '<a href="'.env('MELAKAPAY_URL').'storage/rasmi-'.$data.'.pdf" class="btn btn-sm btn-primary" title="View Receipt" target="_blank">View Receipt</a>';
                });

            } else {

                $show->id(__('Action'))->unescape()->as(function ($data) {
                    return '<a href="'.env('MELAKAPAY_URL').'storage/'.$data.'.pdf" class="btn btn-sm btn-primary" title="View Proof of Payment" target="_blank">View Proof of Payment</a>';
                });

            }

        } else {

            $show->id(__('Action'))->unescape()->as(function ($data) {
                $epic = DB::connection('epic')->table('eps_transactions')->where('merchant_trans_id', $data)->first();
                return '<a href="'.env('EPAYMENT_URL').'/eps/response/'.base64_encode($epic->id).'" class="btn btn-sm btn-success" title="Retrieve latest status" target="_blank">Retrieve latest status</a>';
            });
        }

        $show->user(__('User'), function ($user){
            $user->setResource('/users');
            $user->name();
            $user->username();
            $user->email();
            $user->id('User ID');

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