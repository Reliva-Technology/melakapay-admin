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
        $grid->model()->orderBy('id', 'desc');
        
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('epx_trns_no', __('EPS Transaction ID'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->amount()->display(function ($amount) {
            return number_format($amount,2);
        });
        $grid->column('status', __('Status'))->using(['0' => 'Failed', '1' => 'Success', '2' => 'Cancelled', '3' => 'Pending']);
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
                    1 => 'Success',
                    0 => 'Failed',
                    2 => 'Cancelled',
                    3 => 'Pending'
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

        $grid->header(function ($query) {
            $method = $query->select(DB::raw('count(payment_type) as count, payment_type'))
                ->groupBy('payment_type')->get()->pluck('count', 'payment_type')->toArray();
            $doughnut = view('admin.charts.payment-mode', compact('method'));
            //return new Box('Payment Mode', $doughnut);
        });

        $grid->actions(function ($actions) {
            //$actions->disableEdit();
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
        $show->id(__('Action'))->unescape()->as(function ($id) {
            return '<a href="https://melakapay.melaka.gov.my/storage/rasmi-'.$id.'.pdf" class="btn btn-sm btn-primary" title="View Receipt" target="_blank">View Receipt</a>';
        });

        $show->user(__('User'), function ($user){
            $user->setResource('/users');
            $user->name();
            $user->email();
            $user->id(__('Action'))->unescape()->as(function ($user_id) {
                return '<a href="'.url('/carian-persendirian/add-carian-persendirian').'/'.$user_id.'" class="btn btn-sm btn-success" title="Carian Persendirian">Carian Persendirian</a>';
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