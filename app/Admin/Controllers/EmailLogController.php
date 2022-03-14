<?php

namespace App\Admin\Controllers;

use App\Models\EmailLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EmailLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'EmailLog';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EmailLog());

        $grid->column('id', __('Id'));
        $grid->column('date', __('Date'));
        $grid->column('from', __('From'));
        $grid->column('to', __('To'));
        $grid->column('subject', __('Subject'));

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
        $show = new Show(EmailLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('date', __('Date'));
        $show->field('from', __('From'));
        $show->field('to', __('To'));
        $show->field('cc', __('Cc'));
        $show->field('bcc', __('Bcc'));
        $show->field('subject', __('Subject'));
        $show->field('body', __('Body'))->unescape();
        $show->field('headers', __('Headers'))->unescape();
        $show->field('attachments', __('Attachments'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
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
