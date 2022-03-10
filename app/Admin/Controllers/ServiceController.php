<?php

namespace App\Admin\Controllers;

use App\Models\Service;
use App\Models\Agency;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ServiceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Services';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Service());

        $grid->column('agency.agency_name', __('Agency'))->sortable();
        $grid->column('category', __('Category'))->sortable();
        $grid->column('sub_category', __('Search Type'))->sortable();
        $grid->column('api_url', __('API URL'));
        $grid->column('status', __('Enabled'))->bool();
        $grid->column('ordering', __('Order'));
        $grid->column('created_at')->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
            $filter->equal('category', 'Category')->select(Service::all()->pluck('category','category'));
            $filter->equal('sub_category', 'Search Type')->select(Service::all()->pluck('sub_category','sub_category'));

        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
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
        $show = new Show(Service::findOrFail($id));

        $show->field('category', __('Category'));
        $show->field('sub_category', __('Search Type'));
        $show->field('api_url', __('API URL'));
        $show->field('parameter', __('Parameter'));
        $show->field('payment_url', __('Payment URL'));
        $show->field('payment_parameter', __('Payment Parameter'));
        $show->field('ordering', __('Ordering'));
        $show->field('status', __('Enabled'))->using(['0' => 'No', '1' => 'Yes']);

        $show->agency('Agency Details', function ($agency) {

            $agency->setResource('/admin/agencies');
            $agency->agency();
            $agency->agency_name();
            $agency->enable()->using(['0' => 'No', '1' => 'Yes']);

            $agency->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });
        });

        $show->panel()->tools(function ($tools) {
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
        $form = new Form(new Service());

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->select('agency_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'))->required();
        $form->text('category', __('Category'))->required();
        $form->text('sub_category', __('Sub Category'));
        $form->text('api_url', __('API URL'));
        $form->number('ordering', __('Ordering'));
        $form->text('parameter', __('Parameter'));
        $form->text('payment_url', __('Payment URL'));
        $form->text('payment_parameter', __('Payment Parameter'));
        $status = [
            'on'  => ['value' => 1, 'text' => 'Enable', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'Disable', 'color' => 'danger'],
        ];
        $form->switch('status', __('Enabled'))->states($status);

        return $form;
    }
}