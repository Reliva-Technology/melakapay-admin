<?php

namespace App\Admin\Controllers;

use App\Models\Visitor;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VisitorController extends AdminController
{
    protected $title = 'Visitor';

    protected function grid()
    {
        $grid = new Grid(new Visitor());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('ip', __('IP Address'));
        $grid->column('date', __('Date'));

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('date', 'Date')->date();
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });

        $grid->disableCreateButton()->disableColumnSelector();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Visitor::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('ip', __('IP Address'));
        $show->field('date', __('Date'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }
}
