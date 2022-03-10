<?php

namespace App\Admin\Controllers;

use App\Models\Feedback;
use App\Models\Agency;
use App\Models\User;
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
        $grid->model()
            ->whereNotIn('status',['1'])
            ->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('title', __('Title'));
        $grid->column('message', __('Message'));
        $grid->column('user.name', __('User Name'));
        $grid->column('status', __('Status'))->using([
            'unread' => 'Pending',
            '0' => 'Pending',
            '1' => 'Completed',
            '2' => 'In Progress'
        ]);
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
            $filter->equal('status', 'Status')->radio(
                [
                    '' => 'All',
                    '0' => 'Pending',
                    '1' => 'Completed',
                    '2' => 'In Progress'
                ]
            );
            $filter->equal('user_id', __('User ID'))->select(User::all()->pluck('name','id'));
        
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

        $show->field('id', __('ID'));
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

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->select('agency_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'));
        $form->text('title', __('Title'));
        $form->textarea('message', __('Message'));
        $form->select('user_id', __('User ID'))->options(User::all()->pluck('name','id'));
        $form->radio('status', __('Status'))->options([
            '0' => 'Pending',
            '1' => 'Completed',
            '2' => 'In Progress'
        ]);

        return $form;
    }
}
