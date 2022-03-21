<?php

namespace App\Admin\Controllers;

use App\Models\Transaction;
use App\Models\AgencyEbayar;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EbayarTransactionController extends AdminController
{
    protected $title = 'eBayar Transactions';

    protected function grid()
    {
        $grid = new Grid(new Transaction());
        $grid->model()->where('agency','NOT LIKE','%-app')->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('epx_trns_no', __('EPS Trans. ID'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->column('user_id', __('User ID'));
        $grid->column('ebayar.agency_name', __('Agency'));
        $grid->column('status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $grid->column('account_id', __('Account ID'));
        $grid->column('payment_type', __('FPX'))->using(['fpx' => 'Individual', 'fpx1' => 'Corporate']);
        $grid->amount()->display(function ($amount) {
            return number_format($amount,2);
        });

        $grid->filter(function($filter){
        
            // Add a column filter
            $filter->equal('epx_trns_no', 'EPS Trans. ID');
            $filter->equal('receipt_no', 'Receipt No.');
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
            $filter->equal('agency_id', __('Agency'))->select(AgencyEbayar::all()->pluck('agency_name','id'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit()->disableDelete();
        });

        $grid->disableCreateButton();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Transaction::findOrFail($id));

        $show->field('id', __('Merchant Transaction ID'));
        $show->field('epx_trns_no', __('EPS Transaction No.'));
        $show->field('receipt_no', __('Receipt No.'));
        $show->field('user_id', __('User ID'));
        $show->field('agency', __('Agency'));
        $show->field('status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $show->field('account_id', __('Account ID'));
        $show->field('date_payment', __('Date Payment'));
        $show->field('payment_type', __('Payment Type'));
        $show->field('amount', __('Amount'))->display(function ($amount) {
            return number_format($amount,2);
        });
        $show->field('modified', __('Modified'));

        return $show;
    }

}
