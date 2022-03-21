<?php

namespace App\Admin\Controllers;

use App\Models\EmailLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EmailLogController extends AdminController
{
    protected $title = 'Email Log';

    protected function grid()
    {
        $grid = new Grid(new EmailLog());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('date', __('Date'));
        $grid->column('from', __('From'));
        $grid->column('to', __('To'));
        $grid->column('subject', __('Subject'));
        
        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('to', __('Receipient E-mail'));
            $filter->between('date', 'Date Sent')->date();
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
        $show = new Show(EmailLog::findOrFail($id));

        $show->field('date', __('Date'));
        $show->field('from', __('From'));
        $show->field('to', __('To'));
        $show->field('subject', __('Subject'));
        $show->field('body', __('Body'))->unescape();
        $show->field('headers', __('Headers'))->json();
        $show->field('attachments', __('Attachments'))->json();

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }

    protected function form()
    {
        $form = new Form(new EmailLog());

        $form->datetime('date', __('Date'))->default(date('Y-m-d H:i:s'));
        $form->text('from', __('From'));
        $form->text('to', __('To'));
        $form->text('cc', __('Cc'));
        $form->text('bcc', __('Bcc'));
        $form->text('subject', __('Subject'));
        $form->textarea('body', __('Body'));
        $form->textarea('headers', __('Headers'));
        $form->textarea('attachments', __('Attachments'));

        return $form;
    }
}
