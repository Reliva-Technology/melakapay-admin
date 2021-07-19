<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Agency;
use Carbon\Carbon;

class AgencyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Agencies';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Agency);

        $grid->column('id', __('Agency ID'))->sortable();
        $grid->column('agency', __('Code'))->sortable();
        $grid->column('agency_name', __('Name'))->sortable();
        $grid->column('enable', __('Enabled'))->using(['0' => 'No', '1' => 'Yes', '2' => 'Temporary']);
        $grid->updated_at()->display(function ($updated_at) {
            return Carbon::parse($updated_at)->diffForHumans();
        });
        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('agency', 'Code');
            $filter->like('name', 'Name');
        
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Agency::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('agency', __('Code'));
        $show->field('agency_name', __('Name'));
        $show->field('enable', __('Enabled'))->using(['0' => 'No', '1' => 'Yes', '2' => 'Temporary']);
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->services('Services', function ($services) {

            $services->resource('/admin/services');
        
            $services->id();
            $services->category();
            $services->sub_category();
            $services->status()->bool();

            $services->disableFilter();
            $services->disableCreateButton();
            $services->disablePagination();
            $services->disableExport();
            $services->disableActions();
            $services->disableColumnSelector();
            $services->disableRowSelector();
        
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
        $form = new Form(new Agency);

        $form->display('id', __('ID'));
        $form->text('agency', __('Code'))->required();
        $form->text('agency_name', __('Name'))->required();
        $status = [
            '0' => 'Disabled',
            '1' => 'Enabled',
            '2' => 'Temporary Disabled'
        ];
        
        $form->radio('enable', __('Enabled'))->options($status)->required();

        return $form;
    }
}