<?php

namespace App\Admin\Controllers;

use App\Models\LogSms;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SmsLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'SMS Logs';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LogSms());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('user.name', __('User'));
        $grid->column('phone_number', __('Phone number'));
        $grid->column('sessionid', __('Session ID'));
        $grid->column('messageid', __('Message ID'));
        $grid->column('created_at', __('Created at'));

        $grid->filter(function ($filter) {

            // Remove the default id filter
            $filter->disableIdFilter();

            $filter->like('phone_number', 'Phone Number');
            // Sets the range query for the created_at field
            $filter->between('created_at', 'Sent Date')->datetime();
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete()->disableEdit();
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
        $show = new Show(LogSms::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user.name', __('User'));
        $show->field('phone_number', __('Phone number'));
        $show->field('sessionid', __('Sessionid'));
        $show->field('messageid', __('Messageid'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LogSms());

        $form->number('user_id', __('User id'));
        $form->text('phone_number', __('Phone number'));
        $form->text('sessionid', __('Sessionid'));
        $form->text('messageid', __('Messageid'));

        return $form;
    }
}
