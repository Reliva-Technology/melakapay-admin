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

        $grid->column('id', __('ID'));
        $grid->column('payment_type', __('Payment Type'));
        $grid->column('receipt_no', __('Receipt No.'));
        $grid->column('created_at', __('Created at'));

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->equal('payment_type', 'Payment Type')->radio(
                [
                    '' => 'All',
                    'Cukai Tanah' => 'Cukai Tanah',
                    'Cukai Petak' => 'Cukai Petak',
                    'Carian Persendirian' => 'Carian Persendirian'
                ]
            );
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

        $show->field('id', __('ID'));
        $show->field('payment_type', __('Payment Type'));
        $show->field('receipt_no', __('Receipt No.'));
        $show->field('request', __('Request'))->json();
        $show->field('response', __('Response'))->json();
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }
}
