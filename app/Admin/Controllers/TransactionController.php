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

        $grid->model()
            ->app()
            ->orderBy('id', 'desc')
            ->take(1000);
        
        $grid->column('id', __('ID'));
        $grid->column('modified', __('Payment Date/Time'))->filter('range', 'date');
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('epx_trns_no', __('EPS Transaction ID'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->amount()->display(function ($amount) {
            return number_format($amount,2);
        });
        $grid->column('status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $grid->column('payment_type', __('FPX'))->using(['fpx' => 'Individual', 'fpx1' => 'Corporate']);

        $grid->filter(function($filter){
        
            // Add a column filter
            $filter->equal('epx_trns_no', 'EPS Transaction ID');
            $filter->equal('receipt_no', 'Receipt No');
            $filter->equal('status', 'Status')->radio(
                [
                    '' => 'All',
                    '0' => 'Attempt Payment',
                    '1' => 'Successful',
                    '2' => 'Failed',
                    '3' => 'Pending'
                ]
            );
            $filter->equal('payment_type', 'Payment Type')->radio(
                [
                    '' => 'All',
                    'fpx' => 'FPX Individual',
                    'fpx1' => 'FPX Corporate'
                ]
            );
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit()->disableDelete();
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
        $show->field('payment_type', __('Payment mode'))->using([
            'fpx' => 'FPX Individual',
            'fpx1' => 'FPX Corporate'
        ]);
        $show->field('epx_trns_no', __('EPS Transaction ID'));
        $show->field('receipt_no', __('Receipt No'));
        $show->field('status', __('Status'))->using([
            '0' => 'Attempt Payment',
            '1' => 'Successful',
            '2' => 'Failed',
            '3' => 'Pending'
        ]);
        $show->field('date_payment', __('Payment Date/Time'));

        $receipt = DB::table('receipts')->where('merchant_transaction_id', $id)->first();

        if($receipt){

            $test = \File::exists(env('MELAKAPAY_STORAGE').'rasmi-'.$id.'.pdf');

            if(\File::exists(env('MELAKAPAY_STORAGE').'rasmi-'.$id.'.pdf')){

                $show->id(__('Action'))->unescape()->as(function ($data) {
                    return '<a href="'.env('MELAKAPAY_URL').'storage/rasmi-'.$data.'.pdf" class="btn btn-sm btn-primary" title="View Receipt" target="_blank">View Receipt</a>';
                });

            }

            if(\File::exists(env('MELAKAPAY_STORAGE').$id.'.pdf')){

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

            $user->id(__('Action'))->unescape()->as(function ($data) {
                return '<a href="'.env('MELAKAPAY_URL').'impersonate/'.$data.'" class="btn btn-sm btn-danger" title="View Receipt" target="_blank">Login as this user</a>';
            });

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

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

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