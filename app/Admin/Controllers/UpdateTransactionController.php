<?php

namespace App\Admin\Controllers;

use App\Models\UpdateTransaction;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UpdateTransactionController extends AdminController
{
    protected $title = 'Update Transaction Log';

    protected function grid()
    {
        $grid = new Grid(new UpdateTransaction());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('Id'));
        $grid->column('payment_type', __('Payment Type'));
        $grid->column('receipt_no', __('Receipt no'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('payment_type', __('Payment Type'));
            $filter->between('updated_at', 'Date')->date();
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->disableCreateButton()->disableColumnSelector();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(UpdateTransaction::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('payment_type', __('Payment type'));
        $show->field('receipt_no', __('Receipt no'));
        $show->field('request', __('Request'));
        $show->field('response', __('Response'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }
}
