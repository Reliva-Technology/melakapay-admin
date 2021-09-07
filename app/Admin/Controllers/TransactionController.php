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

        $grid->model()->whereNotNull('receipt_no')->orderBy('id', 'desc');
        $grid->column('epx_trns_no', __('EPS Transaction ID'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('amount', __('Amount'));
        $grid->column('status', __('Status'))->bool();
        $grid->column('created_at', __('Created at'))->hide();

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('epx_trns_no', 'EPS Transaction ID');
            $filter->like('receipt_no', 'Receipt No');
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->disableCreateButton();

        $grid->header(function ($query) {
            $method = $query->select(DB::raw('count(payment_type) as count, payment_type'))
                ->groupBy('payment_type')->get()->pluck('count', 'payment_type')->toArray();
            $doughnut = view('admin.charts.user', compact('method'));
            return new Box('Payment Menthod', $doughnut);
        });

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
        $show->field('amount', __('Amount'));
        $show->field('payment_type', __('Payment mode'));
        $show->field('status', __('Status'))->using(['0' => 'Failed', '1' => 'Success']);
        $show->field('epx_trns_no', __('EPS Transaction ID'));
        $show->field('receipt_no', __('Receipt No'));
        $show->field('modified', __('Created at'));

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

        $show->panel()->tools(function ($tools) {
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

        $form->number('user_id', __('User id'));
        $form->number('service_id', __('Service id'));
        $form->text('amount', __('Amount'))->default('double');
        $form->text('payment_mode', __('Payment mode'))->default('string');
        $form->text('payment_method', __('Payment method'))->default('string');
        $form->switch('status', __('Status'))->default(2);
        $form->text('receipt_no', __('Receipt no'));
        $form->number('payment_id', __('Payment id'));

        return $form;
    }
}