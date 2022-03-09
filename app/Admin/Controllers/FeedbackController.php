<?php

namespace App\Admin\Controllers;

use App\Models\Feedback;
use App\Models\Agency;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon as Carbon;

class FeedbackController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Feedback';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Feedback());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('title', __('Title'));
        $grid->column('message', __('Message'));
        $grid->column('user.name', __('User Name'));
        $grid->column('status', __('Status'));
        $grid->created_at()->display(function ($created) {
            return Carbon::parse($created)->diffForHumans();
        });
        $grid->updated_at()->display(function ($updated) {
            return Carbon::parse($updated)->diffForHumans();
        });

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('title', 'Title');
            $filter->like('message', 'Message');
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
        
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
        $show = new Show(Feedback::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('agency.agency_name', __('Agency'));
        $show->field('title', __('Title'));
        $show->field('message', __('Message'));
        $show->field('user.name', __('User'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Feedback());

        $form->text('agency_id', __('Agency id'));
        $form->text('title', __('Title'));
        $form->textarea('message', __('Message'));
        $form->number('user_id', __('User id'));
        $form->text('status', __('Status'));

        return $form;
    }
}
