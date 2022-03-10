<?php

namespace App\Admin\Controllers;

use App\Models\Payment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PaymentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Payment';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Payment());
        $grid->model()->orderBy('id', 'desc');
        
        $grid->column('id', __('Payment ID'));
        $grid->column('payment_datetime', __('Payment Date/time'));
        $grid->column('buyer_name', __('Name'));
        $grid->column('buyer_bank', __('Bank'));
        $grid->column('payment_transaction_id', __('Payment Trans ID'));
        $grid->column('merchant_order_no', __('Order No'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();
        $grid->column('amount')->totalRow(function ($amount) {
            return "<span class='text-bold'>RM ".number_format($amount,2)." </span>";
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
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
        $show = new Show(Payment::findOrFail($id));

        $show->field('id', __('Payment ID'));
        $show->field('amount', __('Amount'));
        $show->field('status_code', __('Status code'));
        $show->field('status_message', __('Status message'));
        $show->field('payment_transaction_id', __('Payment transaction id'));
        $show->field('payment_datetime', __('Payment datetime'));
        $show->field('buyer_bank', __('Buyer bank'));
        $show->field('buyer_name', __('Buyer name'));
        $show->field('merchant_order_no', __('Merchant order no'));

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
        $form = new Form(new Payment());

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->text('amount', __('Amount'))->default('double');
        $form->text('status_code', __('Status code'));
        $form->text('status_message', __('Status message'));
        $form->text('payment_transaction_id', __('Payment transaction id'));
        $form->datetime('payment_datetime', __('Payment datetime'))->default(date('Y-m-d H:i:s'));
        $form->text('buyer_bank', __('Buyer bank'));
        $form->text('buyer_name', __('Buyer name'));
        $form->text('merchant_order_no', __('Merchant order no'));

        return $form;
    }
}