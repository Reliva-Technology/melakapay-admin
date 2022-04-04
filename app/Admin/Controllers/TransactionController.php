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
        
        $grid->column('id')->display(function ($title) {
            if(\File::exists(env('MELAKAPAY_STORAGE').'rasmi-'.$title.'.pdf')){
                return "<span class='label label-success'>$title</span>";
            } else {
                return "<span class='label label-danger'>$title</span>";
            }
        
        });
        $grid->column('modified', __('Payment Date/Time'))->filter('range', 'date');
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->amount()->display(function ($amount) {
            return number_format($amount,2);
        });
        $grid->column('status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $grid->column('payment_type', __('FPX'))->using(['fpx' => 'Individual', 'fpx1' => 'Corporate']);

        $grid->filter(function($filter){
        
            // Add a column filter
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
            $actions->add(new GetTransactionFromZakat);
        });

        $grid->disableCreateButton();

        $grid->footer(function ($query) {
        
            return "<span class='label label-success'>xxx</span> Receipt generated<br><span class='label label-danger'>xxx</span> No receipt found. Use Check Status action to retrieve latest transaction details";
        });

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
        $show->field('agency.agency_name', __('Agency'));
        $show->field('account_id', __('Account ID'));
        $show->amount()->as(function ($amount) {
            return number_format($amount,2);
        });
        $show->field('payment_type', __('Payment Mode'))->using([
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

            if(\File::exists(env('MELAKAPAY_STORAGE').$id.'.pdf')){

                $show->id(__('Proof of Payment'))->unescape()->as(function ($proof) {
                    return '<a href="'.env('MELAKAPAY_URL').'storage/'.$proof.'.pdf" class="btn btn-sm btn-primary" title="View Proof of Payment" target="_blank">View Proof of Payment</a>';
                });

            }

            if(\File::exists(env('MELAKAPAY_STORAGE').'rasmi-'.$id.'.pdf')){

                $show->id(__('Receipt'))->unescape()->as(function ($resit) {
                    return '<a href="'.env('MELAKAPAY_URL').'storage/rasmi-'.$resit.'.pdf" class="btn btn-sm btn-success" title="View Receipt" target="_blank">View Receipt</a>';
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
}